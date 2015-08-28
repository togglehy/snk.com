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


class b2c_messenger_msgbox
{
    public $name = '站内信'; //名称
    public $isHtml = true; //是否html消息
    public $hasTitle = true; //是否有标题

    public function __construct()
    {
    }

    public function send($target, $title, $content, $config)
    {
        logger::info(__CLASS__.var_export(func_get_args(), 1));
        if(!$member_id = $target['member_id']){
            return false;
        }
        $uname = vmc::singleton('b2c_user_object')->get_member_name(null, $member_id);
        $new_msg = array(
            //to
            'member_id'=>$member_id,
            'target'=>$uname,
            'subject'=>$title,
            'content'=>$content,
            'createtime'=>time(),
            'status'=>'sent'
        );
        return app::get('b2c')->model('member_msg')->save($new_msg);
    }
}
