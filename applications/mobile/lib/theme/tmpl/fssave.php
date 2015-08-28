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


class mobile_theme_tmpl_fssave
{
    public function get_default($type, $theme)
    {
    } //End Function
    public function set_default($type, $theme, $value)
    {
    } //End Function
    public function del_default($type, $theme)
    {
    } //End Function
    public function set_all_tmpl_file($theme)
    {
    } //End Function
    public function get_all_tmpl_file($theme)
    {
    } //End Function
    public function tmpl_file_exists($tmpl_file, $theme)
    {
    } //End Function
    public function get_edit_list($theme)
    {
    } //End Function
    public function install($theme)
    {
    } //End Function
    //
    public function insert_themes_file($data)
    {
    }
    public function insert($data)
    {
    } //End Function
    public function insert_tmpl($data)
    {
    } //End Function
    public function copy_tmpl($tmpl, $theme)
    {
    } //End Function
    public function delete_tmpl_by_theme()
    {
    } //End Function
    public function delete_tmpl($tmpl, $theme)
    {
    } //End Function
    private function __get_all_files($sDir, &$aFile, $loop = true)
    {
    }
    public function get_name()
    {
        $ctl = $this->__get_tmpl_list();

        return $ctl;
    }
    public function get_list_name($name)
    {
        $name = rtrim(strtolower($name), '.html');
        $ctl = $this->__get_tmpl_list();

        return $ctl[$name];
    } //End Function
    private function __get_tmpl_list()
    {
        $ctl = array(
            'index' => '首页' ,
            'default' => '默认页' ,
        );
        foreach (vmc::servicelist('mobile.mobile_theme_tmpl') as $object) {
            if (method_exists($object, '__get_tmpl_list')) {
                $arr = $object->__get_tmpl_list($ctl);
                if ($arr) {
                    foreach ($arr as $key => $val) {
                        if ($ctl[$key]) {
                            continue;
                        }
                        $ctl[$key] = $val;
                    }
                }
            }
        }

        return $ctl;
    }
    public function touch_theme_tmpl($theme)
    {
        vmc::singleton('mobile_theme_base')->set_theme_cache_version($theme);
        $cache_keys = vmc::database()->select('SELECT `prefix`, `key` FROM vmc_base_kvstore WHERE `prefix` IN ("cache/template", "cache/theme")');
        foreach ($cache_keys as $value) {
            base_kvstore::instance($value['prefix'])->get_controller()->delete($value['key']);
        }
        vmc::database()->exec('DELETE FROM vmc_base_kvstore WHERE `prefix` IN ("cache/template", "cache/theme")', true);
        cachemgr::init(true);
        cachemgr::clean($msg);
        cachemgr::init(false);

        return true;
    } //End Function
    public function touch_tmpl_file($tmpl, $time = null)
    {
        if (empty($time)) {
            $time = time();
        }
        $source = THEME_M_DIR.'/'.$tmpl;
        if (is_file($source)) {
            return @touch($source, $time);
        } else {
            return false;
        }
    } //End Function
    public function output_pkg($theme)
    {
        echo 'exit';
    }
} //End Class
