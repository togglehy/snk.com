<?php
// +----------------------------------------------------------------------
// | VMCSHOP [V M-Commerce Shop]
// +----------------------------------------------------------------------
// | Copyright (c) vmcshop.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.vmcshop.com/licensed)
// +----------------------------------------------------------------------
// | Author: Shanghai ChenShang Software Technology Co., Ltd.
// +----------------------------------------------------------------------



class base_kvstore_redis extends base_kvstore_abstract implements base_interface_kvstore_base, base_interface_kvstore_extension {
    public $name = 'Redis';
    static private $_cacheObj = array(
        'm' => null,
        's' => array()
    );
    function __construct($prefix) {
        $this->connect();
        $this->prefix = $prefix;
    } //End Function
    public function connect() {
        if (empty(self::$_cacheObj['m'])) {
            if (defined('KVSTORE_REDIS_MASTER_CONFIG') && constant('KVSTORE_REDIS_MASTER_CONFIG')) {
                self::$_cacheObj['m'] = new Redis();
                $config = explode(':', KVSTORE_REDIS_MASTER_CONFIG);
                self::$_cacheObj['m']->pconnect($config[0], $config[1]);
            } else {
                trigger_error('Can\'t load KVSTORE_REDIS_MASTER_CONFIG, please check it', E_USER_ERROR);
            }
        }
        if (defined('KVSTORE_REDIS_SLAVE_CONFIG') && constant('KVSTORE_REDIS_SLAVE_CONFIG')) {
            $config_arr = explode(',', KVSTORE_REDIS_SLAVE_CONFIG);
            foreach ($config_arr as $k => $config) {
                if (!empty(self::$_cacheObj['s'][$k])) continue;
                //logger::alert( 'CS_'."\t".time() );
                $config = explode(':', KVSTORE_REDIS_SLAVE_CONFIG);
                self::$_cacheObj['s'][$k] = new Redis();
                self::$_cacheObj['s'][$k]->pconnect($config[0], $config[1]);
            }
        }
        if (empty(self::$_cacheObj['s']) || empty(self::$_cacheObj['s'][0])) {
            self::$_cacheObj['s'][0] = self::$_cacheObj['m'];
        }
    } //End Function
    public function fetch($key, &$value, $timeout_version = null) {
        $s_index = $this->_hashId(mt_rand() , count(self::$_cacheObj['s']));
        $ins = self::$_cacheObj['s'][$s_index];
        if (empty($ins)) return false;
        try {
            $store = $ins->get($this->create_key($key));
        }
        catch(Exception $e) {
            return false;
        }
        $store = json_decode($store, true);
        if ($store !== false) {
            if ($timeout_version < $store['dateline']) {
                if ($store['ttl'] > 0 && ($store['dateline'] + $store['ttl']) < time()) {
                    return false;
                }
                $value = $store['value'];
                return true;
            }
        }
        return false;
    } //End Function
    public function store($key, $value, $ttl = 0) {
        $store['value'] = $value;
        $store['dateline'] = time();
        $store['ttl'] = $ttl;
        if (intval($ttl) == 0) {
            return self::$_cacheObj['m']->set($this->create_key($key) , json_encode($store));
        } else {
            return self::$_cacheObj['m']->setex($this->create_key($key) , $ttl, json_encode($store));
        }
    } //End Function
    public function delete($key) {
        return self::$_cacheObj['m']->delete($this->create_key($key));
    } //End Function
    public function recovery($record) {
        $key = $record['key'];
        $store['value'] = $record['value'];
        $store['dateline'] = $record['dateline'];
        $store['ttl'] = $record['ttl'];
        return self::$_cacheObj['m']->set($this->create_key($key) , json_encode($store));
    } //End Function
    public function increment($key, $offset = 1) {
        $real_key = $this->create_key($key);
        return self::$_cacheObj['m']->incr($real_key, $offset);
    } //End Function
    public function decrement($key, $offset = 1) {
        $real_key = $this->create_key($key);
        return self::$_cacheObj['m']->decr($real_key, $offset);
    } //End Function
    private function _hashId($id, $m = 10) {
        $k = md5($id);
        $l = strlen($k);
        $b = bin2hex($k);
        $h = 0;
        for ($i = 0;$i < $l;$i++) {
            //相加模式HASH
            $h+= substr($b, $i * 2, 2);
        }
        $hash = ($h * 1) % $m;
        return $hash;
    }
} //End Class
