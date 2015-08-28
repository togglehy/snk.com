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

class b2c_ctl_mobile_checkout extends b2c_mfrontpage
{
    public $title = '订单确认';
    public function __construct(&$app)
    {
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
        $this->verify_member();
        //$this->app->member_id  已赋值
        $this->cart_stage = vmc::singleton('b2c_cart_stage');
        $this->cart_stage->set_member_id($this->app->member_id);
        $this->set_tmpl('checkout');
    }
    //checkout 主页
    public function index($fastbuy = false)
    {
        $blank_url = $this->gen_url(array(
            'app' => 'b2c',
            'ctl' => 'mobile_cart',
            'act' => 'blank',
        ));

        $filter = array();
        $member_id = $this->app->member_id;

        if ($fastbuy !== false) {
            $filter['is_fastbuy'] = 'true';
        }
        $cart_result = $this->cart_stage->result($filter);
        if ($fastbuy !== false) {
            if ($this->cart_stage->is_empty($cart_result) || $cart_result['objects']['goods'][0]['disabled'] || $cart_result['objects']['goods'][0]['warning']) {
                $this->splash('error', '', '立即购买失败.'.$cart_result['objects']['goods'][0]['warning']);
            }
        } else {
            if ($this->cart_stage->is_empty($cart_result)) {
                $this->splash('error', '', '购物车为空!');
            }
        }
        $this->pagedata = vmc::singleton('b2c_checkout_stage')->check(array(
            'member_id' => $member_id,
            'cart_result' => $cart_result,
        ));

        if ($fastbuy !== false) {
            $this->pagedata['is_fastbuy'] = 'is_fastbuy';
        }

        $available_coupons = vmc::singleton('b2c_coupon_stage')->get_member_couponlist($member_id, $my_coupons);

        foreach ($cart_result['promotions']['order'] as $p) {
            if ($p['rule_type'] == 'coupon' && $available_coupons[$p['coupon_code']]) {
                $available_coupons[$p['coupon_code']]['in_cart'] = 'true';
            }
        }
        $this->pagedata['my_coupons'] = $my_coupons;
        $this->pagedata['my_av_coupons'] = $available_coupons;

        $this->page('mobile/checkout/index.html');
    }
    /**
     * 快速购买.
     */
    public function fastbuy()
    {
        $this->index('is_fastbuy');
    }

    public function check($fastbuy = false)
    {
        $redirect = $this->gen_url(array(
            'app' => 'b2c',
            'ctl' => 'mobile_checkout',
        ));
        $filter = array();
        $member_id = $this->app->member_id;
        if ($fastbuy) {
            $filter['is_fastbuy'] = 'true';
        }
        $params = $this->_request->get_params(true);
        $cart_result = $this->cart_stage->result($filter);
        $params = array_merge($params, array(
            'member_id' => $member_id,
            'cart_result' => $cart_result,
            'ignore_return_cart_result' => true,
        ));
        $check_result = vmc::singleton('b2c_checkout_stage')->check($params);
        if ($params['cart_md5'] != $check_result['cart_md5']) {
            $this->splash('error', $redirect, '购物车发送变化！');
        }
        $this->splash('success', $redirect, $check_result);
    }
    public function payment($order_id, $flow_success = 0)
    {
        $redirect = $this->gen_url(array(
            'app' => 'b2c',
            'ctl' => 'mobile_member',
            'act' => 'orders',
        ));
        $order = $this->app->model('orders')->dump($order_id);
        if (!$order) {
            $this->splash('error', $redirect, '未知订单信息');
        }
        if ($this->app->member_id != $order['member_id']) {
            $this->splash('error', $redirect, '非法操作');
        }
        if ($order['pay_status'] == '1' || $order['pay_status'] == '2' || $order['payed'] == $order['order_total']) {
            $this->splash('success', $redirect, '订单已付款！');
        }
        $mdl_payapps = app::get('ectools')->model('payment_applications');
        $filter = array(
            'status' => 'true',
            'platform_allow' => array(
                'pc',
            ),
        );
        if ($order['is_cod'] == 'Y') {
            $filter['app_id'] = 'cod';
        }
        $payapps = $mdl_payapps->getList('*', $filter);
        $selected_payapp = $mdl_payapps->dump($order['pay_app']);
        $this->pagedata['order'] = $order;
        $this->pagedata['payapps'] = $payapps;
        $this->pagedata['selected_payapp'] = $selected_payapp;
        $this->pagedata['flow_success'] = $flow_success;
        //$this->set_tmpl('checkout');
        $this->page('mobile/checkout/payment.html');
    }
    /**
     * 准备跳转到支付平台.
     *
     * @param mixed $order_id 订单编号
     * @param $recursive 递归调用标记
     */
    public function dopayment($order_id, $recursive = false)
    {
        $redirect = $this->gen_url(array(
            'app' => 'b2c',
            'ctl' => 'mobile_member',
            'act' => 'orders',
        ));
        $obj_bill = vmc::singleton('ectools_bill');
        $mdl_bills = app::get('ectools')->model('bills');
        $order = $this->app->model('orders')->dump($order_id);
        if ($order['pay_status'] == '1' || $order['pay_status'] == '2') {
            $this->splash('success', $redirect, '已支付');
        }
        if (in_array($order['pay_app'], array(
            'cod',
            'offline',
        ))) {
            $this->splash('error', $redirect, '不是在线支付方式');
        }
        if ($this->app->member_id != $order['member_id']) {
            $this->splash('error', $redirect, '非法操作');
        }

        //未交互过的账单复用
        $bill_sdf = array(
            'order_id' => $order['order_id'],
            'bill_type' => 'payment',
            'pay_mode' => 'online',
            'pay_object' => 'order',
            'money' => $order['order_total'] - $order['payed'],
            'member_id' => $order['member_id'],
            'status' => 'ready',
            'pay_app_id' => $order['pay_app'],
            'pay_fee' => $order['cost_payment'],
            'memo' => $order['memo'],
        );
        $exist_bill = $mdl_bills->getRow('*',$bill_sdf);
        
        if ($exist_bill && !empty($exist_bill['bill_id'])) {
            $bill_sdf = array_merge($exist_bill, $bill_sdf);
        } else {
            $bill_sdf['bill_id'] = $mdl_bills->apply_id($bill_sdf);
        }
        $bill_sdf['return_url'] = $this->gen_url(array(
            'app' => 'b2c',
            'ctl' => 'mobile_checkout',
            'act' => 'payresult',
            'args' => array(
                $bill_sdf['bill_id'],
            ),
        ));

        //微信内支付时，需要静默授权，以获得用户openid
        if (base_mobiledetect::is_wechat() && $order['pay_app'] == 'wxpay' && empty($bill_sdf['payer_account'])) {
            $wxpay_setting = unserialize(app::get('ectools')->getConf('wechat_payment_applications_wxpay'));
            $wxpay_appid = $wxpay_setting['appid'];
            $wxpay_appsecret = $wxpay_setting['appsecret'];
            $auth_code = $_GET['code'];
            $auth_state = $_GET['state'];
            if (!$recursive) {
                $oauth_redirect = $this->gen_url(array(
                    'app' => 'b2c',
                    'ctl' => 'mobile_checkout',
                    'act' => 'dopayment',
                    'args' => array($order_id, 'recursive'),
                    'full' => 1,
                ));
                $oauth_redirect = urlencode($oauth_redirect);
                $oauth_action = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$wxpay_appid&redirect_uri=$oauth_redirect&response_type=code&scope=snsapi_base&state=$order_id#wechat_redirect";
                logger::debug('微信snsapi_base URL:'.$oauth_action);
                $this->redirect($oauth_action); //静默授权
            } elseif ($recursive && $auth_code && $auth_state == $order_id) {
                //把微信用户openid 记录到支付单据中
                $auth_token = vmc::singleton('base_httpclient')->get("https://api.weixin.qq.com/sns/oauth2/access_token?appid=$wxpay_appid&secret=$wxpay_appsecret&code=$auth_code&grant_type=authorization_code");
                $auth_token = json_decode($auth_token, 1);
                if (!$auth_token['openid']) {
                    logger::warning('微信静默授权失败!'.var_export($auth_token, 1));
                    $this->splash('error', $redirect, '暂无法进行微信内支付。');
                }
                $bill_sdf['payer_account'] = $auth_token['openid'];
            } else {
                logger::warning('微信静默授权失败!order_id:'.$order_id.'|'.var_export($_GET, 1));
            }
        }

        try {
            if (!$obj_bill->generate($bill_sdf, $msg)) {
                $this->splash('error', $redirect, $msg);
            }
        } catch (Exception $e) {
            $this->splash('error', $redirect, $e->getMessage());
        }
        $get_way_params = $bill_sdf;
        if (!vmc::singleton('ectools_payment_api')->redirect_getway($get_way_params, $msg)) {
            $this->splash('error', $redirect, $msg);
        }
        //here we go to the platform
    }

    /**
     * 监测订单支付状态.
     */
    public function paystatus($order_id)
    {
        $mdl_orders = app::get('b2c')->model('orders');
        $order = $mdl_orders->getRow('member_id,pay_status', array('member_id' => $this->app->member_id, 'order_id' => $order_id));
        if ($order['member_id'] != $this->app->member_id) {
            $this->splash('error', '', '非法操作!');
        }
        switch ($order['pay_status']) {
            case '1':
            case '2':
            //case '3':
                $this->splash('success', '', '已支付');
                break;

            default:
                $this->splash('error', '', '未支付');
                break;
        }
    }

    //支付回调
    public function payresult($bill_id)
    {
        $mdl_bills = app::get('ectools')->model('bills');
        $bill = $mdl_bills->dump($bill_id);
        if ($bill['member_id'] != $this->app->member_id) {
            $this->splash('error', $redirect, '非法操作');
        }
        $this->pagedata['bill'] = $bill;
        $this->pagedata['order'] = $this->app->model('orders')->dump($bill['order_id']);
        //$this->set_tmpl('checkout');
        $this->page('mobile/checkout/payresult.html');
    }
}
