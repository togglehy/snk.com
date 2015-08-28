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


define('UPYUN_API_PATH', dirname(__FILE__).'/upyun');
require_once UPYUN_API_PATH.'/sdk.class.php';
class base_storage_upyun implements base_interface_storager
{
    private $_tmpfiles = array();
    public function __construct()
    {
        if (defined('UPYUN_BUCKET') && defined('UPYUN_IMG_DOMAIN')) {
            try {
                $this->upyun_service = new UpYun(UPYUN_BUCKET, UPYUN_USER, UPYUN_PWD);
            } catch (UpYunException $e) {
                echo $e->__toString();
                exit;
            }
        } else {
            echo 'ERROR UNDEFINE BUCKET or IMG_DOMAIN';
            exit;
        }
    } //End Function
    //上传文件到UPYUN
    public function save($file, &$url, $type, $addons, $ext_name = '')
    {
        $filename = $this->_get_ident($file, $type, $addons, $url, $path, $ext_name);
        $object = $path.$filename;
        $response = $this->upyun_service->writeFile($object, file_get_contents($file), true);
        if ($response) {
            $url = '//'.UPYUN_IMG_DOMAIN.$object;

            return $object.'@'.UPYUN_BUCKET;
        } else {
            return false;
        }
    } //End Function
    // 生成object名字
    public function _get_ident($file, $type, $addons, $url, &$path, $ext_name = '')
    {
        $ident = md5(rand(0, 9999).microtime());
        // 路径
        if (isset($addons['path']) && $addons['path']) {
            $path = $addons['path'];
        } else {
            $path = '/'.$ident{0}
            .$ident{5}
            .'/'.$ident{3}
            .$ident{1}
            .'/'.substr($ident, 3, 6);
        }
        // 文件名
        if (isset($addons['name']) && $addons['name']) {
            $fname = $addons['name'];
        } else {
            $fname = substr(md5(($addons ? $addons : $file).microtime()), 3, 6);
        }
        // 后缀
        if ($ext_name) {
            if (strrpos($fname, '.')) {
                $fname = substr($fname, 0, strrpos($fname, '.')).$ext_name;
            } else {
                $fname .= $ext_name;
            }
        }

        return $fname;
    } // end function _get_ident
    //图片直接缩略服务
    public function image_rebuild($url, $size_k = false)
    {
        if (!$size_k) {
            return false;
        }

        return $url.'!'.strtolower($size_k);
    }
    //图片占位符处理
    public function modifier_process($url, $size_k)
    {
        return $this->image_rebuild($url, $size_k);
    }
    //替换指定object@bucket
    public function replace($file, $id)
    {
        $o = explode('@', $id);
        $object = $o[0];
        $bucket = $o[1];
        if (!$object || !$bucket) {
            return false;
        }
        $this->remove($id); //先删除
        $response = $this->upyun_service->writeFile($object, file_get_contents($file), true); //再新传
        return ($response);
    } //End Function
    //删除指定object@bucket
    public function remove($id)
    {
        if ($id) {
            $o = explode('@', $id);
            $object = $o[0];
            $bucket = $o[1];
            if (!$object || !$bucket) {
                return false;
            }
            $response = $this->upyun_service->delete($object);

            return ($response);
        } else {
            return false;
        }
    } //End Function
    //拉取指定bucket 指定object
    public function getFile($id, $type)
    {
        $o = explode('@', $id);
        $object = $o[0];
        $bucket = $o[1];
        if (!$object || !$bucket) {
            return false;
        }
        $tmpfile = tempnam(TMP_DIR, 'osssystem');
        array_push($this->_tmpfiles, $tmpfile);
        $response = $this->upyun_service->readFile($object, $tmpfile);
        if ($response) {
            return $tmpfile;
        } else {
            return false;
        }
    } //End Function
    //析构函数
    public function __destruct()
    {
        foreach ($this->_tmpfiles as $tmpfile) {
            @unlink($tmpfile);
        } //todo unlink tmpfiles;
    } //End Function
} //End Class
