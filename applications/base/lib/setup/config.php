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



class base_setup_config{

    function __construct(){
        if(file_exists(ROOT_DIR.'/config/config.php')){
            $this->set_sample_file(ROOT_DIR.'/config/config.php');
        }else{
            $this->set_sample_file(ROOT_DIR.'/app/base/examples/config.php');
        }
    }

    function set_sample_file($file){
        $this->sample_file = $file;
    }

    function write($config){

        $this->sample_file = realpath($this->sample_file);

        $sample = file_get_contents($this->sample_file);

        foreach($config as $k=>$v){
            $arr['#(define\\s*\\(\\s*[\'"]'.strtoupper($k).'[\'"]\\s*,\\s*)[^;]+;#i'] = '\\1\''.str_replace('\'','\\\'',$v).'\');';
        }

        logger::info('Using sample :'.$this->sample_file);

        if(file_put_contents(ROOT_DIR.'/config/config.php',preg_replace(array_keys($arr),array_values($arr),$sample))) {
            $this->write_compat();
            logger::info('Writing config file... ok.');
            return true;
        }else{
            logger::info('Writing config file... fail.');
            return false;
        }
    }

    static function deploy_info(){
             return vmc::singleton('base_xml')->xml2array(
            file_get_contents(ROOT_DIR.'/config/deploy.xml'),'base_deploy');
    }

    function write_compat()
    {
        $file = ROOT_DIR.'/config/config.php';
        if(file_exists($file)){
            logger::info('Writing config compat... ok.');
            $sample = preg_replace('/('.preg_quote('/**************** compat functions begin ****************/', '/').')(.*)('.preg_quote('/**************** compat functions end ****************/', '/').')/isU', "\\1" .  "\r\n" . join("\r\n", $this->check_compat()) . "\r\n" . '\\3', file_get_contents($file));
            return file_put_contents($file, $sample);
        }else{
            logger::info('Writing config compat... failure.');
            return false;
        }
    }//End Function

    function check_compat()
    {
        $ret = array("#DON'T REMOVE!!!\n");
        $ret = array_merge($ret, (array)$this->check_json());
        return $ret;
    }//End Function

    function check_json()
    {
        if(!function_exists('json_encode')){
            $ret[] = file_get_contents(dirname(__FILE__) . '/compat/json_encode.txt');
        }
        if(!function_exists('json_decode')){
            $ret[] = file_get_contents(dirname(__FILE__) . '/compat/json_decode.txt');
        }
        return $ret;
    }//End Function
}
