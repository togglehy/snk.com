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



class base_application_prototype_filepath extends base_application_prototype_content{

    var $current;
    var $path;


    function init_iterator(){
        $tmp_core_dir = '';
        if( is_dir($this->target_app->app_dir.'/'.$this->path) ){
            $tmp_core_dir =  $this->target_app->app_dir.'/'.$this->path;
        }
        
        if($tmp_core_dir){
            $cong_files = array();
            if(defined('EXTENDS_DIR') && is_dir(EXTENDS_DIR.'/'.$this->target_app->app_id.'/'.$this->path)){
                $EXTENDS_DIR = new DirectoryIterator(EXTENDS_DIR.'/'.$this->target_app->app_id.'/'.$this->path); #从
                $EXTENDS_DIR = new NewCallbackFilterIterator($EXTENDS_DIR, function($current){
                    return  ! $current->isDot() ;
                });
                foreach($EXTENDS_DIR as $v){
                    $cong_files[] = $v->getFilename();
                }
            }
            $core_dir = new DirectoryIterator($tmp_core_dir);#主
            $core_dir = new NewCallbackFilterIterator($core_dir, function($current, $key, $iterator, $params){
                if(!$current->isDot() && !in_array($current->getFilename(),$params)){
                  return $current;
                }
            }, $cong_files);

            $iterator = new AppendIterator;
            if($EXTENDS_DIR){
                $iterator->append($EXTENDS_DIR);
            }
            $iterator->append($core_dir);

            return $iterator;
        }else{
            return new ArrayIterator(array());
        }
    }

    public function getPathname(){
        return $this->iterator()->getPathname();
    }

    public function current() {
        $this->key = $this->iterator()->getFilename();
        return $this;
    }

    function prototype_filter(){
        $filename = $this->iterator()->getFilename();
        if($filename{0}=='.'){
            return false;
        }else{
            return $this->filter();
        }
    }

    function last_modified($app_id){
        $info_arr = array();
        foreach($this->detect($app_id) as $item){
            //$modified = max($modified,filemtime($this->getPathname()));
            //todo: md5
            $filename = $this->getPathname();
            if(is_dir($filename)){
                foreach(utils::tree($filename) AS $k=>$v){
					if (is_dir($v)) continue;
                    $info_arr[$v] = md5_file($v);
                }
            }else{
                $info_arr[$filename] = md5_file($filename);
            }
        }
        ksort($info_arr);
        return md5(serialize($info_arr));
    }



}
