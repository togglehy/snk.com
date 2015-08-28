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

class b2c_ctl_admin_member_msg extends desktop_controller
{
    public function index()
    {
        $this->finder('b2c_mdl_member_msg', array(
            'title' => ('消息列表'),
            'use_buildin_recycle' => true,
            'actions' => array(
                array(
                    'icon' => 'fa-envelope',
                    'label' => ('新建消息通知'),
                    'href' => 'javascript:;',
                ),
            ),
        ));
    }

    public function send()
    {
    }
}
