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


class wechat_view_helper
{

    //会员等级名称
    public function modifier_qrcode($code_url,$is_mobile = false)
    {
        if($code_url){
            if($is_mobile!=flase && $is_mobile!='false'){
                $long_touch = true;
            }
            $long_touch = false;//TODO 暂时禁用
            $qrcode_image_url = vmc::singleton('wechat_qrcode')->create($code_url,$long_touch);
            $url = vmc::singleton('base_storager')->image_path($qrcode_image_url['image_id']);
            return "<img src='$url' />";
        }
    }


}
