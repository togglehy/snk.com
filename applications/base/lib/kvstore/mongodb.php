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



class base_kvstore_mongodb extends base_kvstore_abstract implements base_interface_kvstore_base
{
    static private $_mongodb = null;
    public $name = 'MONGODB';
    function __construct($prefix)
    {
        if(!isset(self::$_mongodb)){
            $server = defined('MONGODB_SERVER_CONFIG')?MONGODB_SERVER_CONFIG:"mongodb://localhost:27017";
            $option = defined('MONGODB_OPTION_CONFIG')?eval(MONGODB_OPTION_CONFIG):array("connect" => TRUE);

            $m = new MongoClient($server,$option);
            $db = $m->vmcshop_kvstore;
            self::$_mongodb = $db->selectCollection(base_kvstore::kvprefix());
        }
        $this->prefix = $prefix;
    }//End Function

    public function fetch($key, &$value, $timeout_version=null)
    {
        MongoCursor::$slaveOkay = true;
        $store = self::$_mongodb->findOne(array('key'=>$this->create_key($key)));
        if(!is_null($store) && $timeout_version < $store['dateline']){
            if($store['ttl'] > 0 && ($store['dateline']+$store['ttl']) < time()){
                return false;
            }
            $value = $store['value'];
            return true;
        }
        return false;
    }//End Function

    public function store($key, $value, $ttl=0)
    {
        $store['value'] = $value;
        $store['dateline'] = time();
        $store['ttl'] = $ttl;
        $store['key'] = $this->create_key($key);
        $store['prefix'] = $this->prefix;
		$res = self::$_mongodb->update(array('key'=>$store['key']), $store, array("upsert" => true));
        return $res;
    }//End Function

    public function delete($key)
    {
        return self::$_mongodb->remove(array('key'=>$this->create_key($key)));
    }//End Function

    public function recovery($record)
    {
        $key = $record['key'];
        $store['key'] = $this->create_key($key);
        $store['value'] = $record['value'];
        $store['dateline'] = $record['dateline'];
        $store['ttl'] = $record['ttl'];
		$res = self::$_mongodb->update(array('key'=>$store['key']), $store, array("upsert" => true));
        return $res;
    }//End Function

}//End Class
