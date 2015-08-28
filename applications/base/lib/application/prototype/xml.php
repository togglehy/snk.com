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


 
class base_application_prototype_xml extends base_application_prototype_content{

    var $current;
    var $xml;
    var $xsd;
    var $path;
    static $__appinfo;

    public function init_iterator() {
        if(defined('EXTENDS_DIR') && file_exists(EXTENDS_DIR.'/'.$this->target_app->app_id.'/'.$this->xml)){
            $ident = $this->target_app->app_id.'-'.$this->xml;
            if(!isset(self::$__appinfo[$ident])){
                self::$__appinfo[$ident] = vmc::singleton('base_xml')->xml2array(
                    file_get_contents(EXTENDS_DIR.'/'.$this->target_app->app_id.'/'.$this->xml),$this->xsd);
            }            
            eval('$array = &self::$__appinfo[$ident]['.str_replace('/','][',$this->path).'];');
        }elseif(file_exists($this->target_app->app_dir.'/'.$this->xml)){
            $ident = $this->target_app->app_id.'-'.$this->xml;
            if(!isset(self::$__appinfo[$ident])){
                self::$__appinfo[$ident] = vmc::singleton('base_xml')->xml2array(
                    file_get_contents($this->target_app->app_dir.'/'.$this->xml),$this->xsd);
            }
            
            eval('$array = &self::$__appinfo[$ident]['.str_replace('/','][',$this->path).'];');
        }else{
            $array = array();
        }
        return new ArrayIterator((array)$array);
    }
    
    function last_modified($app_id){
        if(defined('EXTENDS_DIR') && file_exists(EXTENDS_DIR.'/'.app::get($app_id)->app_id.'/'.$this->xml)){
            $file = EXTENDS_DIR.'/'.app::get($app_id)->app_id.'/'.$this->xml;
        }else{
            $file = app::get($app_id)->app_dir.'/'.$this->xml;
        }
        
        if(file_exists($file)){
            //return filemtime($file);
            //todo: md5
            return md5_file($file);
        }else{
            return false;
        }
    }

}
