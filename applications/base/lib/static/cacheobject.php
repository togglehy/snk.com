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




class cacheobject
{

    /*
     * @var string $_instance
     * @access static private
     */
    static private $_instance = null;

    /*
     * @var string $_instance_name
     * @access static private
     */
    static private $_instance_name = null;

    /*
     * 初始化
     * @var boolean $with_cache
     * @access static public
     * @return void
     */
    static public function init($with_cache=true)
    {
        if(!WITHOUT_CACHE && $with_cache && defined('CACHE_ADAPTER') && constant('CACHE_ADAPTER')){
            self::$_instance_name = CACHE_ADAPTER;
        }else{
            self::$_instance_name = 'base_cache_nocache';    //todo：增加无cache类，提高无cache情况下程序的整体性能
        }
        self::$_instance = null;
    }//End Function


    /*
     * 获取cache_storage实例
     * @access static public
     * @return object
     */
    static public function instance()
    {
        if(is_null(self::$_instance)){
            self::$_instance = vmc::singleton(self::$_instance_name);
        }//使用实例时再构造实例
        return self::$_instance;
    }//End Function


    /*
     * 获取缓存
     * @var string $key
     * @var mixed &$return
     * @access static public
     * @return boolean
     */
    static public function get($key, &$return)
    {
        if(self::instance()->fetch(self::get_key($key), $data)){
            if($data['expirition'] > 0 && time() > $data['expirition']){
                return false;
            }
            $return = $data['content'];
            return true;
        }else{
            return false;
        }
    }//End Function

    /*
     * 设置缓存
     * @var string $key
     * @var mixed $content
     * @return boolean
     */
    static public function set($key, $content, $expirition = 0)
    {
        $data['expirition'] = ($expirition>0) ? $expirition : 0;       //todo: 设置过期时间
        $data['content'] = $content;
        return self::instance()->store(self::get_key($key), $data);
    }//End Function

    /*
     * 获取缓存key
     * @var string $key
     * @access static public
     * @return string
     */
    static public function get_key($key)
    {
        
        $kvprefix = (defined('KV_PREFIX')) ? KV_PREFIX : '';
        $key_array['key'] = $key;
        $key_array['kv_prefix'] = $kvprefix;
        $key_array['prefix'] = 'cacheobject';
        $key_array['version'] = cachemgr::get_cache_check_version();
        return md5(serialize($key_array));
    }//End Function


}//end
