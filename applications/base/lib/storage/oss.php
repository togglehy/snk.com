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


define('OSS_API_PATH', dirname(__FILE__) . '/oss');
require_once OSS_API_PATH . '/sdk.class.php';
class base_storage_oss implements base_interface_storager {
    private $_tmpfiles = array();
    function __construct() {
        $this->oss_service = new ALIOSS();
        $this->oss_service->set_debug_mode(FALSE);
    } //End Function
    //上传文件到OSS
    public function save($file, &$url, $type, $addons, $ext_name = "") {
        if (defined('OSS_BUCKET') && defined('OSS_IMG_DOMAIN')) {
            $bucket = OSS_BUCKET;
        } else {
            //$bucket = '';
            //$this->oss_service->create_bucket($bucket,$acl);
            return false;
        }
        $filename = $this->_get_ident($file, $type, $addons, $url, $path, $ext_name);
        $object = $path . $filename;
        $response = $this->oss_service->upload_file_by_file($bucket, $object, $file);
        if ($response->status == 200) {
            $url = '//' . OSS_IMG_DOMAIN . '/' . $object;
            return $object . '@' . $bucket;
        } else {
            return false;
        }
    } //End Function
    // 生成object名字
    public function _get_ident($file, $type, $addons, $url, &$path, $ext_name = "") {
        $ident = md5(rand(0, 9999) . microtime());
        // 路径
        if (isset($addons['path']) && $addons['path']) {
            $path = $addons['path'];
        } else {
            $path = $ident{0} . $ident{5} . '/' . $ident{3} . $ident{1} . '/' . substr($ident, 3, 6);
        }
        // 文件名
        if (isset($addons['name']) && $addons['name']) {
            $fname = $addons['name'];
        } else {
            $fname = substr(md5(($addons ? $addons : $file) . microtime()) , 3, 6);
        }
        // 后缀
        if ($ext_name) {
            if (strrpos($fname, ".")) $fname = substr($fname, 0, strrpos($fname, ".")) . $ext_name;
            else $fname.= $ext_name;
        }
        return $fname;
    } // end function _get_ident
    //图片直接缩略服务
    public function image_rebuild($url, $size_k = false, $Q = 100, $ext = '.jpg') {
        if (!$size_k) return false;
        $image_conf = app::get('image')->getConf('size');
        $size = $image_conf[strtoupper($size_k) ];
        if (!$size) {
            $arr = explode('x', $size_k);
            if (is_numeric($arr[0]) && $arr[0] > 1 && is_numeric($arr[1]) && $arr[1] > 0) {
                $size['width'] = $arr[0];
                $size['height'] = $arr[1];
            }
        }
        return implode('@', array(
            $url,
            implode('_', array(
                ($size['width']?$size['width']:100) . 'w',
                ($size['height']?$size['height']:100) . 'h',
                $Q . 'Q'
            ))
        )) . $ext;
    }
    //图片占位符处理
    public function modifier_process($url, $size_k) {
        return $this->image_rebuild($url, $size_k);
    }
    //替换指定object@bucket
    public function replace($file, $id) {
        $o = explode('@', $id);
        $object = $o[0];
        $bucket = $o[1];
        if (!$object || !$bucket) return false;
        $response = $this->oss_service->upload_file_by_file($bucket, $object, $file);
        return ($response->status == 200);
    } //End Function
    //删除指定object@bucket
    public function remove($id) {
        if ($id) {
            $o = explode('@', $id);
            $object = $o[0];
            $bucket = $o[1];
            if (!$object || !$bucket) return false;
            $response = $this->oss_service->delete_object($bucket, $object);
            return ($response->status == 200);
        } else {
            return false;
        }
    } //End Function
    //拉取指定bucket 指定object
    public function getFile($id, $type) {
        $o = explode('@', $id);
        $object = $o[0];
        $bucket = $o[1];
        if (!$object || !$bucket) return false;
        $tmpfile = tempnam(TMP_DIR, 'osssystem');
        array_push($this->_tmpfiles, $tmpfile);
        $options = array(
            ALIOSS::OSS_FILE_DOWNLOAD => $tmpfile
        );
        $response = $this->oss_service->get_object($bucket, $object, $options);
        if ($response->status == 200) {
            return $tmpfile;
        } else {
            return false;
        }
    } //End Function
    //析构函数
    function __destruct() {
        foreach ($this->_tmpfiles AS $tmpfile) {
            @unlink($tmpfile);
        } //todo unlink tmpfiles;

    } //End Function

} //End Class
