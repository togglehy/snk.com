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


class desktop_ctl_passport extends desktop_controller
{
    public $login_times_error = 3;
    public $certcheck = false;

    public function __construct($app)
    {
        parent::__construct($app);
        header('cache-control: no-store, no-cache, must-revalidate');
    }

    public function index()
    {
        $auth = pam_auth::instance(pam_account::get_account_type($this->app->app_id));
        $auth->set_appid($this->app->app_id);
        $auth->set_redirect_url($_SESSION['passport_redirect_url']);
        $this->pagedata['desktop_url'] = vmc::router()->app->base_url(1);
        foreach (vmc::servicelist('passport') as $k => $passport) {
            if ($auth->is_module_valid($k, $this->app->app_id)) {
                $this->pagedata['passports'][] = array(
                        'name' => $auth->get_name($k) ? $auth->get_name($k) : $passport->get_name(),
                        'html' => $passport->get_login_form($auth, 'desktop', 'basic-login.html', $pagedata),
                    );
            }
        }

        $this->display('login.html');
    }

    public function gen_vcode()
    {
        $vcode = vmc::singleton('base_vcode');
        $vcode->length(4);
        $vcode->verify_key($this->app->app_id);
        $vcode->display();
    }

    public function logout($backurl = 'index.php')
    {
        $this->begin('index.php?app=desktop&ctl=dashboard');
        $this->user->login();
        $this->user->logout();
        $auth = pam_auth::instance(pam_account::get_account_type($this->app->app_id));
        foreach (vmc::servicelist('passport') as $k => $passport) {
            if ($auth->is_module_valid($k, $this->app->app_id)) {
                $passport->loginout($auth, $backurl);
            }
        }
        vmc::singleton('base_session')->destory();
        $this->end('true', '成功登出');
    }

    /**
     * 定时触发器, 后端JS定期30秒触发.
     */
    public function status()
    {
        //set_time_limit(0);
        ob_start();
        $output = ob_get_contents();
        ob_end_clean();
        echo $output;

        vmc::singleton('base_session')->close(false);
    }
}
