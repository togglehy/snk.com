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


class site_theme_install
{
    public function check()
    {
        $this->check_dir();
        $d = dir(THEME_DIR);
        while (false !== ($entry = $d->read())) {
            if (in_array($entry, array(
                '.',
                '..',
                '.svn',
            ))) {
                continue;
            }
            if (is_dir(THEME_DIR.'/'.$entry)) {
                $this->init_theme($entry);
            }
            //设置默认模板
            if (!vmc::singleton('site_theme_base')->get_default()) {
                vmc::singleton('site_theme_base')->set_default($entry);
            }
            $is_there[] = $entry;
        }
        if (!empty($is_there) && !in_array(vmc::singleton('site_theme_base')->get_default(), $is_there)) {
            vmc::singleton('site_theme_base')->set_default($is_there[0]);
        }
        $d->close();
        vmc::singleton('site_theme_base')->clean($is_there);
    } //End Function
    public function check_dir()
    {
        if (!is_dir(THEME_DIR)) {
            utils::mkdir_p(THEME_DIR);
        }
    } //End Function

    public function monitor_change($theme){
        $config_file_path = THEME_DIR.'/'.$theme.'/config.xml';
        $last_modify_time = filemtime($config_file_path);
        if(!$last_modify_time){
            return false;
        }
        $exist_last_time = app::get('site')->getConf($theme.'_theme_last_config');
        $exist_last_time = $exist_last_time?$exist_last_time:0;

        if($exist_last_time - $last_modify_time == 0){
            return true;
        }else{
            app::get('site')->setConf($theme.'_theme_last_config',$last_modify_time);
        }
        $config_xml_content = file_get_contents($config_file_path);
        if ($config_xml_content) {
            $theme_info = vmc::singleton('site_utility_xml')->xml2arrayValues($config_xml_content);
        }
        if (empty($theme_info)) {
            return false;
        }
        $config = $theme_info;

        $theme_sdf = array(
            'theme_id' => $config['theme']['id']['value'],
            'theme_dir'=>$theme,
            'name' => $config['theme']['name']['value'],
            'version' => $config['theme']['version']['value'],
            'info' => $config['theme']['info']['value'],
            'author' => $config['theme']['author']['value'],
            'config' => array() , //TODO
        );

        $theme_sdf = vmc_('site','theme_install_config',$theme_sdf,$config);
        if (!vmc::singleton('site_theme_base')->update_theme($theme_sdf)) {
            return false;
        }

        return $theme_sdf;

    }


    public function allow_upload(&$message)
    {
        if (!function_exists('gzinflate')) {
            $message = 'gzip';

            return $message;
        }
        if (!is_writable(THEME_DIR)) {
            $message = 'writeable';
            return $message;
        }

        return true;
    }
    public function remove_theme($theme_dir)
    {
        $dir = THEME_DIR.'/'.$theme_dir;
        $this->flush_theme($theme);
        if (is_dir($dir)) {
            $this->__remove_db_theme($theme); //删除theme_files的模板文件
            return $this->__remove_dir($dir);
        } else {
            return true;
        }
    } //End Function
    /**
     *
     */
    public function install($file, &$msg)
    {
        $this->check_dir();
        if (!$this->allow_upload($msg)) {
            return false;
        }
        $tar = vmc::singleton('base_tar');
        $handle = fopen($file['tmp_name'], 'r');
        $contents = file_get_contents($file['tmp_name']);
        preg_match('/\<id\>([a-zA-Z0-9]*)(.*?)\<\/id\>/', $contents, $tar_name);
        $filename = trim($tar_name[1] ? $tar_name[1] : time());
        if (is_dir(THEME_DIR.'/'.$filename)) {
            $msg = '已存在模板包：'.$filename;
            return false;
        }
        $new_theme_dir = $this->__build_dir(str_replace('\\', '/', THEME_DIR.'/'.$filename));
        if (!$tar->openTAR($file['tmp_name'], $new_theme_dir) ||!$tar->containsFile('config.xml') ) {
            $msg = '错误的模板包';
            return false;
        }

        foreach ($tar->files as $id => $file) {
            $fpath = $new_theme_dir.'/'.$file['name'];
            if (!is_dir(dirname($fpath))) {
                if (mkdir(dirname($fpath), 0755, true)) {
                    file_put_contents($fpath, $tar->getContents($file));
                } else {
                    $msg = '权限不允许';
                    return false;
                }
            } else {
                file_put_contents($fpath, $tar->getContents($file));
            }
        }
        $tar->closeTAR();
        if (!$config = $this->init_theme($filename)) {
            $this->__remove_dir($new_theme_dir);
            $msg = '模板包创建失败';
            return false;
        }

        return $config;

    }
    public function init_theme($theme)
    {
        return $this->monitor_change($theme);
    }

    private function __remove_db_theme($theme)
    {
        $filter = array(
            'theme' => $theme,
        );
        return app::get('site')->model('themes_file')->delete($filter);
    }
    private function __remove_dir($sDir)
    {
        if ($rHandle = opendir($sDir)) {
            while (false !== ($sItem = readdir($rHandle))) {
                if ($sItem != '.' && $sItem != '..') {
                    if (is_dir($sDir.'/'.$sItem)) {
                        $this->__remove_dir($sDir.'/'.$sItem);
                    } else {
                        if (!unlink($sDir.'/'.$sItem)) {
                            trigger_error('因权限原因，模板文件'.$sDir.'/'.$sItem.'无法删除', E_USER_NOTICE);
                        }
                    }
                }
            }
            closedir($rHandle);
            rmdir($sDir);

            return true;
        } else {
            return false;
        }
    }
    private function __build_dir($sDir)
    {
        if (file_exists($sDir)) {
            $aTmp = explode('/', $sDir);
            $sTmp = end($aTmp);
            if (strpos($sTmp, '(')) {
                $i = substr($sTmp, strpos($sTmp, '(') + 1, -1);
                $i++;
                $sDir = str_replace('('.($i - 1).')', '('.$i.')', $sDir);
            } else {
                $sDir .= '(1)';
            }

            return $this->__build_dir($sDir);
        } else {
            if (!is_dir($sDir)) {
                mkdir($sDir, 0755, true);
            }

            return $sDir.'/';
        }
    }
    private function __change_xml_array($aArray)
    {
        $aData = array();
        if (isset($aArray['attr'])) {
            $aArray = array(
                '0' => $aArray,
            );
        }
        if (is_array($aArray)) {
            foreach ($aArray as $i => $v) {
                unset($v['attr']);
                $aData[$i] = array_merge($v, $aArray[$i]['attr']);
            }
        }

        return $aData;
    }
    /*private function separatXml($theme){
        $workdir = getcwd();
        chdir(THEME_DIR.'/'.$theme);
        if(!is_file('info.xml')){
            $content=file_get_contents('theme.xml');
            $rContent=substr($content,0,strpos($content,'<widgets>'));
            file_put_contents('info.xml',$rContent.'</theme>');
        }
        chdir($workdir);
    }*/
    private function file_rename($source, $dest)
    {
        if (is_file($dest)) {
            if (PHP_OS == 'WINNT') {
                @copy($source, $dest);
                @unlink($source);
                if (file_exists($dest)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return @rename($source, $dest);
            }
        } else {
            return false;
        }
    }
    public function ini_get_size($sName)
    {
        $sSize = ini_get($sName);
        $sUnit = substr($sSize, -1);
        $iSize = (int) substr($sSize, 0, -1);
        switch (strtoupper($sUnit)) {
            case 'Y':
                $iSize *= 1024; // Yotta

            case 'Z':
                $iSize *= 1024; // Zetta

            case 'E':
                $iSize *= 1024; // Exa

            case 'P':
                $iSize *= 1024; // Peta

            case 'T':
                $iSize *= 1024; // Tera

            case 'G':
                $iSize *= 1024; // Giga

            case 'M':
                $iSize *= 1024; // Mega

            case 'K':
                $iSize *= 1024; // kilo

            break;
            default:
                $iSize = 5 * 1024 * 1024; //todo Default 2M

        };

        return $iSize;
    }
    public function get_file_ext($file_name)
    {
        $ftype = array(
            'html' => '模板文件' ,
            'gif' => '图片文件' ,
            'jpg' => '图片文件' ,
            'jpeg' => '图片文件' ,
            'png' => '图片文件' ,
            'bmp' => '图片文件' ,
            'css' => '样式表文件' ,
            'js' => '脚本文件' ,
            'xml' => 'theme.xml' ,
            'php' => '模板挂件' ,
        );
        if (strrpos($file_name, '.') === false) {
            return false;
        }
        $fext = strtolower(substr($file_name, strrpos($file_name, '.') + 1));
        if (!$ftype[$fext]) {
            return false;
        }

        return array(
            'ext' => $fext,
            'memo' => $ftype[$fext],
        );
    }
    public function initthemes()
    {
        if ($dh = opendir(THEME_DIR)) {
            while (($file = readdir($dh)) !== false) {
                if (substr($file, -4, 4) == '.tgz') {
                    //$filename_arr[] = $file;
                    $theme_file['tmp_name'] = THEME_DIR.'/'.$file;
                    $theme_file['name'] = $file;
                    $theme_file['type'] = 'application/octet-stream';
                    $theme_file['error'] = '0';
                    $theme_file['size'] = filesize(THEME_DIR.'/'.$file);
                    $res = $this->install($theme_file, $msg);
                }
            }
            closedir($dh);
        }
    }
} //End Class
