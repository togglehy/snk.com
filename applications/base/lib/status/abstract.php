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


 

class base_status_abstract{

    function get_status(){
        $func = get_class_methods($this);
        $ret = array();
        foreach($func as $method){
            if(isset($method{10}) && substr($method,0,4)=='get_' && substr($method,-7,7)=='_status'){
                $ret = array_merge($ret,$this->$method());
            }
        }
        return $ret;
    }
    
}