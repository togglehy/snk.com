<?php

class wechat_qrcode
{
    public function create($intext, $long_touch)
    {
        $mdl_image = app::get('image')->model('image');
        if ($exits_image = $mdl_image->dump(md5($intext.$long_touch))) {
            return $exits_image;
        }
        $tmp_file = tempnam(TMP_DIR, 'qrcode');
        wechat_qrcode_QRcode::png($intext, $tmp_file, 'L', 8, 4);
        list($w, $h, $type) = getimagesize($tmp_file);
        if ($long_touch) {
            //加入额外图像
            $resize_tmp_file = tempnam(TMP_DIR, 'qrcode_resize');
            $image_tool = vmc::singleton('image_tools_gd');
            $image_tool->resize($tmp_file, $resize_tmp_file, $w, $h, $type, $w + 180, $h + 220, false, 20);
            $water_file = PUBLIC_DIR.'/misc/long_touch.gif';
            list($w_w, $w_h, $w_type) = getimagesize($water_file);
            $warermark_set = array(
                'wm_opacity' => 100,
                'watermark_width' => $w_w,
                'watermark_height' => $w_h,
                'type' => $w_type,
                'src_width' => $w + 180,
                'src_height' => $h + 180,
                'dest_x' => ($w + 180 - 120) / 2,
                'dest_y' => $h + 50,
            );
            $image_tool->watermark($resize_tmp_file, $water_file, $warermark_set);
        }

        $storager = new base_storager();
        if ($long_touch) {
            list($url, $ident, $storage) = explode('|', $storager->save_upload($resize_tmp_file, 'image', '', $msg, '.png'));
        } else {
            list($url, $ident, $storage) = explode('|', $storager->save_upload($tmp_file, 'image', '', $msg, '.png'));
        }

        $tmp_qrcode_image = array(
            'image_id' => md5($intext.$long_touch),
            'storage' => $storage,
            'image_name' => 'TMP_QRCODE',
            'ident' => $ident,
            'url' => $url,
            'width' => $w,
            'height' => $h,
            'last_modified' => time(),
        );
        $mdl_image->save($tmp_qrcode_image);
        unlink($tmp_file);
        if ($long_touch) {
            unlink($resize_tmp_file);
        }

        return $tmp_qrcode_image;
    }
}
