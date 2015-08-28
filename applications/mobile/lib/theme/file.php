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


class mobile_theme_file
{
    public function __construct()
    {
        $this->fileObj = vmc::singleton('mobile_theme_file_fsfile');
    }

    //获取单一模板文件路径，这里是图片和xml等
    public function get_src($theme, $uriname)
    {
        return $this->fileObj->get_src($theme, $uriname);
    }

    //是否有备份文件
    public function is_themme_bk($theme, $uriname)
    {
        return $this->fileObj->is_themme_bk($theme, $uriname);
    }

    //模板前缀
    public function preview_prefix($theme)
    {
        return $this->fileObj->preview_prefix($theme);
    }

    //保存备份文件
    public function bak_save($theme, $data)
    {
        return $this->fileObj->bak_save($theme, $data);
    }

    //返回模板real路径
    public function get_theme_dir($theme, $open_path)
    {
        return vmc_('mobile', 'theme_get_theme_dir', $theme, $open_path);
    }

    public function get_file($dir, $file_name)
    {
        return $this->fileObj->get_file($dir, $file_name);
    }

    public function get_content($file_content)
    {
        return $this->fileObj->get_content($file_content);
    }

    public function get_source_code($theme, $tmpl_type)
    {
        return $this->fileObj->get_source_code($theme, $tmpl_type);
    }

    public function check($theme, &$msg = '')
    {
        return $this->fileObj->check($theme, $msg);
    }

    public function get_theme_xml($theme, $uriname)
    {
        return $this->fileObj->get_theme_xml($theme, $uriname);
    }

    public function get_style_css($theme, $uriname)
    {
        return $this->fileObj->get_style_css($theme, $uriname);
    }

    public function get_tmpl_content($theme, $tmpl)
    {
        return $this->fileObj->get_tmpl_content($theme, $tmpl);
    }

    public function get_widgets_content($theme, $tpl, $widgets_app)
    {
        return $this->fileObj->get_widgets_content($theme, $tpl, $widgets_app);
    }

    public function get_func_phpcode($theme, $func_file, $widgets_app)
    {
        return $this->fileObj->get_func_phpcode($theme, $func_file, $widgets_app);
    }

    public function get_full_file_url($theme, $file_content, $open_path, $file_name)
    {
        return $this->fileObj->get_full_file_url($theme, $file_content, $open_path, $file_name);
    }

    public function get_widgets_code($theme, $app, $widgets_dir)
    {
        return $this->fileObj->get_widgets_code($theme, $app, $widgets_dir);
    }

    public function get_xml_content($theme, $sDir, $loadxml)
    {
        return $this->fileObj->get_xml_content($theme, $sDir, $loadxml);
    }
}
