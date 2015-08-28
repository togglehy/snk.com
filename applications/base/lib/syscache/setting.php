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


class base_syscache_setting extends base_syscache_abstract implements base_interface_syscache_farmer
{
    public function get_data()
    {
        $pri_settings = vmc::database()->select('select app, `key`, value from vmc_base_setting');
        $settings = array();

        foreach ($pri_settings as $setting) {
            $settings['setting/'.$setting['app'].'-'.$setting['key']] = unserialize($setting['value']);
        }

        return $settings;
    }
}
