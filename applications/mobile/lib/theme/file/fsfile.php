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




class mobile_theme_file_fsfile
{
    function get_src($theme, $uriname){
        $theme_url = vmc::get_themes_m_host_url();
        $preview_prefix = $theme_url.'/'.$theme;
        $src = $preview_prefix.'/'.$uriname;
        return $src;
    }

    function get_style_css($theme, $uriname){
        $src = vmc::base_url() . "/themes_m/" . $theme .'/'. $uriname;
        return $src;
    }

    function is_themme_bk($theme, $uriname){
        if(file_exists(THEME_M_DIR . '/' . $theme . '/'.$uriname)) {
            $is_theme_bk = 'true';
        }else{
            $is_theme_bk = 'false';
        }
        return $is_theme_bk;
    }

    function preview_prefix($theme){
		$theme_url = vmc::get_themes_m_host_url();
        $preview_prefix = $theme_url.'/'.$theme;
        return $preview_prefix;
    }

    function bak_save($theme,$data){
        if(file_put_contents(THEME_M_DIR . '/' . $theme . '/config_bak.xml', $data)) {
            $flag = true;
        } else {
            $flag = false;
        }
        return $flag;
    }

    function get_file($dir, $file_name){
        return $dir . '/' . $file_name;
    }

    function get_content($file_content){
        return $file_content;
    }

    function get_source_code($theme, $tmpl_type){
        return vmc_('mobile','theme_get_source_code',$theme,$tmpl_type);
    }

    function check($theme,&$msg=''){
        if(empty($theme)){
            $msg = '缺少参数';
            return false;
        }
        /** 权限校验 **/
        if($theme && preg_match('/(\..\/){1,}/', $theme)){
            $msg = '非法操作';
            return false;
        }
        $dir = THEME_M_DIR . '/' . $theme;
        if (!is_dir($dir)){
            $msg = '路径不存在';
            return false;
        }
        return true;
    }

    function get_theme_xml($theme, $uriname){
        $content = file_get_contents($uriname);
        return $content;
    }

    function get_tmpl_content($theme, $tmpl){
        $file_path = realpath(THEME_M_DIR . '/' . $theme . '/' . $tmpl);
        if(file_exists($file_path)){
            return file_get_contents($file_path);
        }else{
            trigger_error('compile file does\'s not exists ['.$file_path.']', E_USER_ERROR);
            return false;
        }
    }

    function get_widgets_content($theme, $tpl, $widgets_app){
        $file_path = realpath($tpl);
        if(file_exists($file_path)){
            return file_get_contents($file_path);
        }else{
            trigger_error('compile file does\'s not exists ['.$file_path.']', E_USER_ERROR);
            return false;
        }
    }

    function get_func_phpcode($theme, $func_file, $widgets_app){
        return 'require(\''.$func_file.'\');';
    }

    function get_full_file_url($theme, $file_content, $open_path, $file_name){
        return vmc::base_url(1) . rtrim(str_replace('//', '/', '/themes_m/' . $theme . '/' . str_replace(array('-','.'), array('/','/'), $open_path) . '/' . $file_name));
    }


    function get_xml_content($theme, $sDir, $loadxml){
        return file_get_contents($sDir . $loadxml);
    }

}//End Class
