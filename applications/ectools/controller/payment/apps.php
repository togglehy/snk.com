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


class ectools_ctl_payment_apps extends desktop_controller {

    public function __construct($app) {
        parent::__construct($app);
        //$this->app = app::get('ectools');
    }

    function index() {
        $mdl_pconf = app::get('ectools')->model('payment_applications');
        $payapp_list = $mdl_pconf->getList('*');
        $this->pagedata['list'] = $payapp_list;
        $this->page('payments/applications/index.html');
    }


    function setting($pay_app_class) {
        if (!$pay_app_class) {
            return false;
        }else{
            $pay_app_instance = new $pay_app_class();
            $setting = $pay_app_instance->setting();
        }
        if ($_POST['setting']) {
            $this->begin('index.php?app=ectools&ctl=payment_apps&act=index');
            foreach ($setting as $key => $value) {
                $conf[$key] = $_POST['setting'][$key];
            }
            $this->app->setConf($pay_app_class, serialize($conf));
            $this->end(true, '配置成功!');
        } else {
            if ($setting) {
                $render = $this->app->render();
                $render->pagedata['admin_info'] = $pay_app_instance->intro;
                $render->pagedata['app_name'] = $pay_app_instance->name;
                $render->pagedata['app_ver'] = $pay_app_instance->version;
                $render->pagedata['platform_allow'] = $pay_app_instance->platform_allow;
                $render->pagedata['settings'] = $setting;
                $render->pagedata['conf'] = unserialize($this->app->getConf($pay_app_class));
                $render->pagedata['classname'] = $pay_app_class;
                $render->display('payments/applications/cfgs.html');
            }
        }
    }
}
