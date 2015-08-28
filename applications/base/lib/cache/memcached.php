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




class base_cache_memcached extends base_cache_abstract implements base_interface_cache
{
    static private $_cacheObj = null;
    public $name = 'Memcached高速内存级缓存';
    function __construct()
    {
        $this->connect();
        $this->check_vary_list();
    }//End Function

    public function connect()
    {
        if(!isset(self::$_cacheObj)){
            if(defined('CACHE_MEMCACHED_CONFIG') && constant('CACHE_MEMCACHED_CONFIG')){
                self::$_cacheObj = new Memcached;
                $config = explode(',', CACHE_MEMCACHE_CONFIG);
                foreach($config AS $row){
                    $row = trim($row);
                    if(strpos($row, 'unix://') === 0){

                    }else{
                        $tmp = explode(':', $row);
                        self::$_cacheObj->addServer($tmp[0], $tmp[1]);
                    }
                }
            }else{
                trigger_error('can\'t load CACHE_MEMCACHED_CONFIG, please check it', E_USER_ERROR);
            }
        }
    }//End Function

    public function fetch($key, &$result)
    {
        $result = self::$_cacheObj->get($key);
        if(self::$_cacheObj->getResultCode() == Memcached::RES_NOTFOUND){
            return false;
        }else{
            return true;
        }
    }//End Function

    public function store($key, $value)
    {
        return self::$_cacheObj->set($key, $value);
    }//End Function

    public function status()
    {
        $status = self::$_cacheObj->getStats();
        foreach($status AS $key=>$value){
            $return['服务器']   = $key;
            $return['缓存获取'] = $value['cmd_get'];
            $return['缓存存储'] = $value['cmd_set'];
            $return['可使用缓存'] = $value['limit_maxbytes'];
        }
        return $return;
    }//End Function

}//End Class
