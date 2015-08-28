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




class base_vcode {

    var $use_gd = true;

    function __construct(){
        if($this->use_gd){
            $this->obj = vmc::singleton('base_vcode_gd');
        }else{
            $this->obj = vmc::singleton('base_vcode_gif');
        }
        vmc::singleton('base_session')->start();
    }

    function length($len) {
        $this->obj->length($len);
        return true;
    }

    function verify_key($key){
        $sess_id = vmc::singleton('base_session')->sess_id();
        $key = $key.$sess_id;
        $ttl = 180;
        if(defined('WITHOUT_CACHE') && !constant('WITHOUT_CACHE')){
            cacheobject::set($key,$this->obj->get_code(),$ttl+time());
        }else{
            base_kvstore::instance('vcode')->store($key,$this->obj->get_code(),$ttl);
        }
    }

    static function verify($key,$value){
        $value = strtolower($value);
        $sess_id = vmc::singleton('base_session')->sess_id();
        $key = $key.$sess_id;
        if(defined('WITHOUT_CACHE') && !constant('WITHOUT_CACHE')){
            cacheobject::get($key,$vcode);
        }else{
            base_kvstore::instance('vcode')->fetch($key,$vcode);
        }
        if( $vcode == strval($value) ){
            return true;
        }

        return false;
    }

    function display(){
        $this->obj->display();
    }
}
