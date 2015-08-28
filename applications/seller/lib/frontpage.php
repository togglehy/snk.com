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


class seller_frontpage extends site_controller {

    protected $seller = array();
    function __construct(&$app) {
        parent::__construct($app);
    }
    /**
     * 检测用户是否登陆
     *  
     */
    function verify() {
        $user_obj = vmc::singleton('b2c_user_seller');
        if ($this->app->seller_id = $user_obj->get_seller_id()) {
            $data = $user_obj->get_sellers_data(array(
                'sellers' => 'seller_id'
            ));
            if ($data) {
                //登录受限检测
                $res = $this->loginlimit($data['sellers']['seller_id'], $redirect);
                if ($res) {
                    $this->splash('error', $redirect, '登陆受限');
                } else {
                    return true;
                }
            }
        }
        $this->splash('error', $this->gen_url(array(
            'app' => 'b2c',
            'ctl' => 'site_passport',
            'act' => 'seller' //
        )) , '未登录');
    }
    /**
     * loginlimit-登录受限检测
     *
     * @param      none
     * @return     void
     */
    function loginlimit($mid, &$redirect) {
        $services = vmc::servicelist('loginlimit.check');
        if ($services) {
            foreach ($services as $service) {
                $redirect = $service->checklogin($mid);
            }
        }
        return $redirect ? true : false;
    } //End Function
    public function bind_seller($seller_id) {
        $columns = array(
            'account' => 'seller_id,login_account,login_password',
            'sellers' => 'seller_id',
        );
        $user_obj = vmc::singleton('b2c_user_seller');
        $cookie_expires = $user_obj->cookie_expires ? time() + $user_obj->cookie_expires * 60 : 0;
        $seller_data = $user_obj->get_sellers_data($columns,$seller_id);
        $login_name = $user_obj->get_seller_name($data['account']['login_name'],$seller_id);
        $this->cookie_path = vmc::base_url() . '/';
        $this->set_cookie('UNAME', $login_name, $cookie_expires);
        $this->set_cookie('SELLER_IDENT', $seller_id, $cookie_expires);
    }
    public function get_current_seller() {
        return vmc::singleton('b2c_user_seller')->get_current_seller();
    }
    function set_cookie($name, $value, $expire = false, $path = null) {
        if (!$this->cookie_path) {
            $this->cookie_path = vmc::base_url() . '/';
            #$this->cookie_path = substr(PHP_SELF, 0, strrpos(PHP_SELF, '/')).'/';
            $this->cookie_life = $this->app->getConf('system.cookie.life');
        }
        $this->cookie_life = $this->cookie_life > 0 ? $this->cookie_life : 315360000;
        $expire = $expire === false ? time() + $this->cookie_life : $expire;
        setcookie($name, $value, $expire, $this->cookie_path);
        $_COOKIE[$name] = $value;
    }
    function check_login() {
        vmc::singleton('base_session')->start();
        if ($_SESSION['account'][pam_account::get_account_type($this->app->app_id) ]) {
            return true;
        } else {
            return false;
        }
    }
   
    function setSeo($app, $act, $args = null) {
        $seo = vmc::singleton('site_seo_base')->get_seo_conf($app, $act, $args);
        $this->title = $seo['seo_title'];
        $this->keywords = $seo['seo_keywords'];
        $this->description = $seo['seo_content'];
        $this->nofollow = $seo['seo_nofollow'];
        $this->noindex = $seo['seo_noindex'];
    } //End Function

}
