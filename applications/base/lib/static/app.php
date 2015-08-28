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


class app
{
    private static $__instance = array();
    private static $__language = null;
    private $__render = null;
    private $__router = null;
    private $__define = null;
    private $__taskrunner = null;
    private $__checkVaryArr = array();
    private $__langPack = array();
    private $__installed = null;
    private $__actived = null;
    private $__setting = null;

    public function __construct($app_id)
    {
        $this->app_id = $app_id;
        $this->app_dir = APP_DIR.'/'.$app_id;
        $this->res_dir = PUBLIC_DIR;
        $this->res_url = vmc::get_app_statics_host_url();
        if(is_dir($this->res_dir.'/'.$app_id)){
            $this->res_dir = $this->res_dir.'/'.$app_id;
            $this->res_url = vmc::get_app_statics_host_url().'/'.$app_id;
        }
        $this->res_full_url = $this->res_url;
    }

    public static function get($app_id)
    {
        if (!isset(self::$__instance[$app_id])) {
            self::$__instance[$app_id] = new self($app_id);
        }

        return self::$__instance[$app_id];
    }

    public function render()
    {
        if (!$this->__render) {
            $this->__render = new base_render($this);
        }

        return $this->__render;
    }

    public function controller($controller)
    {
        return vmc::singleton($this->app_id.'_ctl_'.$controller, $this);
    }

    public function model($model)
    {
        return vmc::singleton($this->app_id.'_mdl_'.$model, $this);
    }

    public function router()
    {
        if (!$this->__router) {
            if (file_exists($this->app_dir.'/lib/router.php')) {
                $class_name = $this->app_id.'_router';
                $this->__router = new $class_name($this);
            } else {
                $this->__router = new base_router($this);
            }
        }

        return $this->__router;
    }

    public function setting()
    {
        if (!$this->__setting) {
            $this->__setting = new base_setting($this);
        }

        return $this->__setting;
    }

    public function base_url($full = false)
    {
        $c = $full ? 'full' : 'part';
        if (!$this->base_url[$c]) {
            $part = vmc::$app_url_map[$this->app_id];
            $this->base_url[$c] = vmc::base_url($full).vmc::url_prefix().$part.($part == '/' ? '' : '/');
        }

        return $this->base_url[$c];
    }

    public function get_parent_model_class()
    {
        $parent_model_class = $this->define('parent_model_class');

        return $parent_model_class ? $parent_model_class : 'base_db_model';
    }

    public function define($path = null)
    {
        if (!$this->__define) {
            if (is_dir($this->app_dir) && file_exists($this->app_dir.'/app.xml')) {
                $tags = array();
                $file_contents = file_get_contents($this->app_dir.'/app.xml');
                $this->__define = vmc::singleton('base_xml')->xml2array($file_contents, 'base_app');
            } else {
                $row = self::get('base')->model('apps')->getList('remote_config', array('app_id' => $this->app_id));
                $this->__define = $row[0]['remote_config'];
            }
        }
        if ($path) {
            $path = array_filter(explode('/',$path));
            $shift_path = array_shift($path);
            if($shift_path && is_array($shift_path)){
                $shift_path =key($shift_path);
            }
            $_return = $this->__define[$shift_path];
            while (count($path)>0 && isset($_return) && !empty($_return)) {
                $shift_path = array_shift($path);
                if($shift_path && is_array($shift_path)){
                    $shift_path =key($shift_path);
                }
                $_return = $_return[$shift_path];
            }
            return $_return;
        } else {
            return $this->__define;
        }
    }

    public function getConf($key,$default = false)
    {
        if (cachemgr::enable() && cachemgr::check_current_co_depth() > 0) {
            $this->check_expires($key, true);
        }//todo：如果存在缓存检查，进行conf检查
        $_return = $this->setting()->get_conf($key);
        if(!$_return && $default){
            $this->setConf($key,$default);
        }
        return $this->setting()->get_conf($key);
    }

    public function setConf($key, $value)
    {
        if ($this->setting()->set_conf($key, $value)) {
            $this->set_modified($key);

            return true;
        } else {
            return false;
        }
    }

    public function set_modified($key)
    {
        $vary_name = strtoupper(md5($this->app_id.$key));
        $now = time();
        $db = vmc::database();
        $exec_count = $db->exec('REPLACE INTO vmc_base_cache_expires (`type`, `name`, `app`, `expire`) VALUES ("CONF", "'.$vary_name.'", "'.$this->app_id.'", '.$now.')', true);
        if ($exec_count) {
            cachemgr::set_modified('CONF', $vary_name, $now);
            syscache::instance('setting')->set_last_modify();
        }
    }//End Function

    public function check_expires($key, $force = false)
    {
        if ($force || (cachemgr::enable() && cachemgr::check_current_co_depth() > 0)) {
            if (!isset($this->__checkVaryArr[$key])) {
                $this->__checkVaryArr[$key] = strtoupper(md5($this->app_id.$key));
            }
            if (!cachemgr::check_current_co_objects_exists('CONF', $this->__checkVaryArr[$key])) {
                cachemgr::check_expires('CONF', $this->__checkVaryArr[$key]);
            }
        }
    }//End Function

    public function runtask($method, $option = null)
    {
        if ($this->__taskrunner === null) {
            $this->__taskrunner = false;
            if (defined('EXTENDS_DIR') && file_exists(EXTENDS_DIR.'/'.$this->app_id.'/task.php')) {
                $taskDir = EXTENDS_DIR.'/'.$this->app_id.'/task.php';
            } else {
                $taskDir = $this->app_dir.'/task.php';
            }
            if (file_exists($taskDir)) {
                require $taskDir;
                $class_name = $this->app_id.'_task';
                if (class_exists($class_name)) {
                    $this->__taskrunner = new $class_name($this);
                }
            }
        }
        if (is_object($this->__taskrunner) && method_exists($this->__taskrunner, $method)) {
            return $this->__taskrunner->$method($option);
        } else {
            return true;
        }
    }

    public function status()
    {
        if (vmc::is_online()) {
            if (!vmc::database()->select('SHOW TABLES LIKE "'.vmc::database()->prefix.'base_apps"')) {
                return 'uninstalled';
            }else{
                $row = @vmc::database()->selectrow('select status from vmc_base_apps where app_id="'.$this->app_id.'"');
            }
            return $row ? $row['status'] : 'uninstalled';
        } else {
            return 'uninstalled';
        }
    }

    public function is_installed()
    {
        if (is_null($this->__installed)) {
            $this->__installed = ($this->status() != 'uninstalled') ? true : false;
        }

        return $this->__installed;
    }//End Function

    public function is_actived()
    {
        if (is_null($this->__actived)) {
            $this->__actived = ($this->status() == 'active') ? true : false;
        }

        return $this->__actived;
    }//End Function

    public function docs($dir = null)
    {
        $docs = array();
        if (!$dir) {
            $dir = $this->app_dir.'/docs';
        }
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file{0} != '.' && isset($file{5}) && substr($file, -4, 4) == '.t2t' && is_file($dir.'/'.$file)) {
                        $rs = fopen($dir.'/'.$file, 'r');
                        $docs[$file] = fgets($rs, 1024);
                        fclose($rs);
                    }
                }
                closedir($dh);
            }
        }

        return $docs;
    }

    public function _($s){
        return $s;
    }
}
