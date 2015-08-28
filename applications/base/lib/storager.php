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


class base_storager
{
    public function __construct()
    {
        $this->base_url = vmc::base_url('full').'/';
        if (!defined('FILE_STORAGER')) {
            define('FILE_STORAGER', 'filesystem');
        }
        $this->class_name = ('base_storage_'.constant('FILE_STORAGER'));
        $this->worker = new $this->class_name();
        if (defined('HOST_MIRRORS')) {
            $host_mirrors = preg_split('/[,\s]+/', constant('HOST_MIRRORS'));
            if (is_array($host_mirrors) && isset($host_mirrors[0])) {
                $this->host_mirrors = &$host_mirrors;
                $this->host_mirrors_count = count($host_mirrors) - 1;
            }
        }
    }
    public function &parse($ident)
    {
        $ret = array();
        if (!$ident) {
            return false;
        } elseif (list($ret['url'], $ret['id'], $ret['storager']) = explode('|', $ident)) {
            return $ret;
        } else {
            $ret['url'] = &$ident;

            return $ret;
        }
    }
    public function save($file, $type = null, $addons = '')
    {
        if ($addons) {
            if (!is_array($addons)) {
                $addons = array(
                    $addons,
                );
            }
        } else {
            $addons = array();
        }
        if ($id = $this->worker->save($file, $url, $type, $addons)) {
            return $url.'|'.$id.'|'.$this->class_name;
        } else {
            return false;
        }
    }
    public function save_upload($file, $type = 'image', $addons = '', &$msg, $ext_name = '')
    {
        if (method_exists($this->worker, 'save')) {
            if ($type == 'file') {
                $ext_name = substr($file['name'], strrpos($file['name'], '.'));
                $file = $file['tmp_name'];
            }
            $addons = array(
                $file,
            );
            if ($id = $this->worker->save($file, $url, $type, $addons, $ext_name)) {
                $ident_data = $url.'|'.$id.'|'.substr($this->class_name, strrpos($this->class_name, '_') + 1);
                if ($type == 'file') {
                    $file_obj = app::get('base')->model('files');
                    $s_d = array(
                        'file_path' => $ident_data,
                        'file_type' => $_POST['_f_type'],
                    );
                    $file_obj->save($s_d);

                    return $s_d['file_id'];
                }

                return $ident_data;
            } else {
                return false;
            }
        }
    }
    public function replace($file, $ident, $type = 'image', $addons = '')
    {
        if (method_exists($this->worker, 'replace') && $ident) {
            $data = $this->parse($ident);
            if ($this->worker->replace($file, $data['id'])) {
                return $ident;
            } else {
                return false;
            }
        } else {
            if ($ident) {
                $this->remove($ident);
            }

            return $this->save($file, $type, $addons);
        }
    }
    public function remove($ident, $type = 'image')
    {
        $data = $this->parse($ident);
        if ($type == 'file') {
            $file_obj = app::get('base')->model('files');
            $s_d = array(
                'file_path' => $ident,
            );
            $file_obj->delete($s_d);
        }

        return $this->worker->remove($data['id']);
    }
    public function getFile($id)
    {
        $file_obj = app::get('base')->model('files');
        $t_d = $file_obj->dump(array(
            'file_id' => $id,
        ));
        $ident = $t_d['file_path'];
        if ($data = $this->parse($ident)) {
            return $this->worker->getFile($data['id'], $t_d['file_type']);
        } else {
            return false;
        }
    }
    public function getUrl($id)
    {
        $file_obj = app::get('base')->model('files');
        $t_d = $file_obj->dump(array(
            'file_id' => $id,
        ));
        $ident = $t_d['file_path'];
        if ($ident) {
            $libs = array(
                'http://' => 1,
                'https:/' => 1,
            );
            $data = $this->parse($ident);
            if ($this->host_mirrors) {
                return $this->host_mirrors[rand(0, $this->host_mirrors_count) ].'/'.$data['id'];
            }
            if (isset($libs[strtolower(substr($data['url'], 0, 7)) ])) {
                return $data['url'];
            } else {
                return $this->base_url.$data['url'];
            }
        } else {
            return false;
        }
    }
    private static $registed = false;
    public static function modifier($image_id, $size = '')
    {
        $size = strtolower($size);
        if (isset($image_id{31}) && !isset($image_id{32})) {
            return '%IMG_'.$image_id.'_S_'.$size.'_IMG%';
        } elseif(strstr($image_id,'//')!==false) {
            return $image_id;
        } else{
            //1x1 transparent gif
            return "data:image/gif;base64,R0lGODlhAQABAIAAAO/v7////yH5BAAHAP8ALAAAAAABAAEAAAICRAEAOw==";
        }
    }
    public static function image_path($image_id, $size = false)
    {
        //if(!$size)$size = 'L';
        $size = strtolower($size);
        $tmp = self::modifier($image_id, $size);

        return self::image_storage($tmp);
    }
    public static function image_storage($content)
    {
        $blocks = preg_split('/%IMG_([0-9a-f]{32})_S_([a-z0-9\:]*)_IMG%/', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
        $c = count($blocks);
        $imglib = array();
        $img = array();
        for ($i = 0;$i < $c;$i++) {
            switch ($i % 3) {
                case 1:
                    $image_id = $blocks[$i];
                    $img[$image_id][$i] = array(
                        $blocks[$i + 1],
                    );
                    $blocks[$i] = &$img[$image_id][$i][0];
                break;
                case 2:
                    $img[$image_id][$i - 1][1] = $blocks[$i];
                    $blocks[$i] = '';
                break;
            }
        }

        if ($img) {
            $db = vmc::database();
            foreach ($db->select($s = 'select image_id,url,xs_url,s_url,m_url,l_url,last_modified,width,height,storage from vmc_image_image where image_id in(\''.implode("','", array_keys($img)).'\')') as $r) {
                $imglib[$r['image_id']] = $r;
            }

            self::_process($img, $imglib);
        }

        return implode('', $blocks);
    }
    private static function _process(&$img, $imglib)
    {
        foreach ($img as $image_id => $sizes) {
            if (!isset($imglib[$image_id]['storage']) || empty($imglib[$image_id]['storage'])) {
                continue;//未知引擎,无法处理
            }
            if (file_exists(APP_DIR.'/base/lib/storager/'.$imglib[$image_id]['storage'].'.php') ||
            file_exists(EXTENDS_DIR.'/base/lib/storager/'.$imglib[$image_id]['storage'].'.php')) {
                $storager_class = 'base_storage_'.$imglib[$image_id]['storage'];
            } else {
                $storager_class = false;
            }

            foreach ($sizes as $i => $item) {
                if ($item[0] && $storager_class) {
                    $storager = new $storager_class();
                    if (method_exists($storager, 'modifier_process')) {
                        $url = $storager->modifier_process($imglib[$image_id]['url'], $item[0]);
                        $the_url_plus = implode('_', array(substr($imglib[$image_id]['last_modified'], -5), 'OW'.$imglib[$image_id]['width'], 'OH'.$imglib[$image_id]['height']));
                        $the_url = ($url && $url != '') ? ($url.'?'.$the_url_plus) : '';
                        $img[$image_id][$i][0] = $the_url;
                        continue;
                    }
                }
                switch ($item[0]) {
                    case 'xs':
                        $url = $imglib[$image_id]['xs_url'] ? $imglib[$image_id]['xs_url'] : $imglib[$image_id]['url'];
                    break;
                    case 's':
                        $url = $imglib[$image_id]['s_url'] ? $imglib[$image_id]['s_url'] : $imglib[$image_id]['url'];
                    break;
                    case 'm':
                        $url = $imglib[$image_id]['m_url'] ? $imglib[$image_id]['m_url'] : $imglib[$image_id]['url'];
                    break;
                    case 'l':
                        $url = $imglib[$image_id]['l_url'] ? $imglib[$image_id]['l_url'] : $imglib[$image_id]['url'];
                    break;
                    default:
                        $url = $imglib[$image_id]['url'];
                    break;
                }
                if ($url && !strpos($url, '://') && substr($url, 0, 2) != '//') {
                    $resource_host_url = vmc::get_resource_host_url();
                    $url = $resource_host_url.'/'.$url;
                }
                $the_url_plus = substr($imglib[$image_id]['last_modified'], -5).'_H'.$imglib[$image_id]['height'];
                $the_url = ($url && $url != '') ? ($url.'?'.$the_url_plus) : '';
                $img[$image_id][$i][0] = $the_url;
            }
        }
    }
}
