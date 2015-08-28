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




class base_status_system extends base_status_abstract{

    function get_cache_status(){
        $ret = array(
            'cache.engine'=>CACHE_ADAPTER,
            );

        if(method_exists(CACHE_ADAPTER,'status')){
            foreach(vmc::singleton(CACHE_ADAPTER) as $k=>$v){
                $ret['cache.'.$k] = $v;
            }
        }
        return $ret;
    }

    function get_kvstore_status(){
        $ret = array(
            'kvstore.engine'=>KV_ADAPTER,
            );

        if(method_exists(KV_ADAPTER,'status')){
            foreach(vmc::singleton(KV_ADAPTER) as $k=>$v){
                $ret['kvstore.'.$k] = $v;
            }
        }
        return $ret;
    }

    function get_mysql_status(){
        $aResult = array(
            'mysql.server_host'=>DB_HOST,
            'mysql.server_dbname'=>DB_NAME,
            'mysql.server_user'=>DB_USER,
        );
        foreach(vmc::database()->select("show status") as $row)
        {
            $aResult['mysql.'.strtolower($row["Variable_name"])] = $row["Value"];
        }
        return $aResult;
    }

}
