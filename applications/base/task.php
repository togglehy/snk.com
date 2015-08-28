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


class base_task
{


    public function pre_install($options)
    {
        vmc::set_online(false);
        if (file_exists(ROOT_DIR.'/config/config.php')) {
            require ROOT_DIR.'/config/config.php';
        }
    }
    public function post_install()
    {
        vmc::singleton('base_application_manage')->sync();
        vmc::set_online(true);
    }
    public function post_update($dbinfo)
    {

    } //End Function
}
