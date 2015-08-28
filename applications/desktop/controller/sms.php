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


class desktop_ctl_sms extends desktop_controller
{
    public function __construct($app)
    {
        parent::__construct($app);
    }
    public function setting()
    {
        $this->pagedata['sms_platform_name'] = $this->app->getConf('sms_platform_name');
        $this->pagedata['sms_platform_api_url'] = $this->app->getConf('sms_platform_api_url');
        $this->pagedata['sms_platform_api_params_tmpl'] = $this->app->getConf('sms_platform_api_params_tmpl');
        $this->pagedata['sms_platform_params_secret'] = $this->app->getConf('sms_platform_params_secret');
        $this->pagedata['sms_platform_api_access_token_action'] = $this->app->getConf('sms_platform_api_access_token_action');
        $this->pagedata['sms_sign'] = $this->app->getConf('sms_sign');
        $this->page('sms/config.html');
    }
    public function save_setting()
    {
        $this->begin('index.php?app=desktop&ctl=sms&act=setting');
        foreach ($_POST as $key => $value) {
            $this->app->setConf($key,$value);
        }
        $this->end(true,'保存成功');

    }
}
