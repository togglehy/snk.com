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

class b2c_ctl_site_order extends b2c_frontpage
{
    public $title = '我的订单';
    public function __construct(&$app)
    {
        parent::__construct($app);
        $this->_response->set_header('Cache-Control', 'no-store');
        $this->verify_member();
        //$this->app->member_id  已赋值
        $this->cart_stage = vmc::singleton('b2c_cart_stage');
        $this->cart_stage->set_member_id($this->app->member_id);
        $this->logger = vmc::singleton('b2c_order_log');
    }
    //PC端前台会员创建订单
    public function create($fastbuy = false)
    {
        $member_id = $this->app->member_id;
        //parent method
        //$member_info = $this->get_member_info($member_id);
        $this->logger->set_operator(array(
            'ident' => $member_id,
            'name' => '会员',
            'model' => 'members',
        ));
        $params = utils::_filter_input($_POST);
        //新订单标准数据
        $order_sdf = array(
            'member_id' => $member_id,
            'memo' => $params['memo'],
            'pay_app' => $params['payapp_id'],
            'dlytype_id' => $params['dlytype_id'],
            'createtime' => time() ,
            'need_invoice'=>$params['need_invoice'],
            'invoice_title'=>$params['invoice_title'],
            'platform' => 'pc',
        );
        $redirect_cart = $this->gen_url(array(
            'app' => 'b2c',
            'ctl' => 'site_cart',
        ), true);
        $redirect_checkout = $this->gen_url(array(
            'app' => 'b2c',
            'ctl' => 'site_checkout',
            'args' => array(
                $fastbuy,
            ),
        ), true);
        if (!$order_sdf['pay_app']) {
            $this->logger->fail('create', '未知支付方式', $params);
            $this->splash('error', $redirect_checkout, '未知支付方式');
        }
        if (!$order_sdf['dlytype_id']) {
            $this->logger->fail('create', '未知配送方式', $params);
            $this->splash('error', $redirect_checkout, '未知配送方式');
        }
        //COD FIX
        if ($order_sdf['pay_app'] == '-1' || $order_sdf['pay_app'] == 'cod') {
            $order_sdf['is_cod'] = 'Y';
        } else {
            $dlytype = app::get('b2c')->model('dlytype')->dump($params['dlytype_id']);
            if ($dlytype['has_cod'] == 'true') {
                $order_sdf['pay_app'] = 'cod';
                $order_sdf['is_cod'] = 'Y';
            }
        }

        if ($fastbuy) {
            $filter['is_fastbuy'] = 'true';
        }
        if (!$params['addr_id']) {
            $this->logger->fail('create', '无收货人信息', $params);
            $this->splash('error', $redirect_checkout, '无收货人信息');
        } else {
            $consignee = app::get('b2c')->model('member_addrs')->getRow('name,area,addr,zip,tel,mobile,email', array(
                'member_id' => $member_id,
                'addr_id' => $params['addr_id'],
            ));
            $order_sdf['consignee'] = $consignee;
        }
        //购物车数据
        $cart_result = $this->cart_stage->result($filter);
        if ($this->cart_stage->is_empty($cart_result)) {
            $this->logger->fail('create', '没有可结算商品', $params);
            $this->splash('error', $redirect_cart, '没有可结算商品');
        }
        if ($params['cart_md5'] != utils::array_md5($cart_result)) {
            $this->logger->fail('create', '购物车发生变化', $params);
            $this->splash('error', $redirect_cart, '购物车发生变化');
        }
        $db = vmc::database();
        //开启事务
        $this->transaction_status = $db->beginTransaction();
        $order_create_service = vmc::singleton('b2c_order_create');
        //&$order_sdf、&$msg
        if (!$order_create_service->generate($order_sdf, $cart_result, $msg)) {
            $db->rollback(); //事务回滚
            $msg = $msg ? $msg : '数据组织失败';
            $this->logger->fail('create', $msg, $params);
            $this->splash('error', $redirect_cart, $msg);
        }
        if (!$order_create_service->save($order_sdf, $msg)) {
            $db->rollback(); //事务回滚
            $msg = $msg ? $msg : '数据保存失败';
            $this->logger->fail('create', $msg, $order_sdf);
            $this->splash('error', $redirect_cart, $msg);
        }
        $db->commit($this->transaction_status); //事务提交
        $this->logger->set_order_id($order_sdf['order_id']);
        $this->logger->success('create', '订单创建成功', $params);

        /**
         * 优惠券冻结,优惠券使用记录
         * 未使用成功in_use!="true"的优惠券不做冻结处理，不做记录
         * @see /Applications/b2c/lib/postfilter/promotion.php line 200
         */
        foreach ($cart_result['objects']['coupon'] as $coupon) {
            if($coupon['params']['in_use']!='true')continue;
            $couponlog_data = array(
                'member_id'=>$member_id,
                'order_id'=>$order_sdf['order_id'],
                'cpns_id'=>$coupon['params']['cpns_id'],//优惠券ID
                'memc_code'=>$coupon['params']['code'],//优惠券号码
                'cpns_name'=>$coupon['params']['name'],//优惠券名称
                'coupon_save'=>$coupon['params']['save'],//优惠券在本次订购中抵扣的金额
                'order_total'=>$order_sdf['order_total']//订单应付金额
            );
            vmc::singleton('b2c_coupon_stage')->couponlog($couponlog_data,$msg);
            if($coupon['params']['cpns_type'] == '1'){
                //需冻结会员账户内的相关B类券
                vmc::singleton('b2c_coupon_stage')->freeze_member_coupon($member_id,$coupon['params']['code'],$msg);
            }
        }


        //清理购物车
        $this->cart_stage->clean($cart_result,$fastbuy);//只删除勾选结算项,对于优惠券，只删除触发促销的项

        $redirect_payment = $this->gen_url(array(
            'app' => 'b2c',
            'ctl' => 'site_checkout',
            'act' => 'payment',
            'args' => array(
                $order_sdf['order_id'],
                '1',
            ),
        ), true);

        $this->splash('success', $redirect_payment, '订单提交成功');
    }
    public function detail($order_id, $showlogistics = false)
    {
        $mdl_order = $this->app->model('orders');
        $bills = app::get('ectools')->model('bills');
        $mdl_order_log = $this->app->model('order_log');
        $mdl_delivery = $this->app->model('delivery');
        $order = $mdl_order->dump($order_id, '*', array(
            'items' => array(
                '*',
            ),
            'promotions' => array(
                '*',
            ),
            ':dlytype' => array(
                '*',
            ),
        ));
        if ($order['member_id'] != $this->app->member_id) {
            $this->splash('error', '非法操作!');
        }
        foreach ($mdl_order_log->getList('behavior,log_time', array(
            'order_id' => $order['order_id'],
            'result' => 'success',
            //会员端只显示成功日志

        )) as $log) {
            $order['process'][$log['behavior']] = $log['log_time'];
        }
        $this->pagedata['order'] = $order;
        $this->pagedata['payapp'] = app::get('ectools')->model('payment_applications')->dump($order['pay_app']);
        if ($order['ship_status'] != '0' && $order['need_shipping'] == 'Y') {
            $delivery_list = $mdl_delivery->getList('delivery_id', array(
                'member_id' => $this->app->member_id,
                'order_id' => $order['order_id'],
                'logistics_no|notin' => array(
                    '',
                    null,
                    'null',
                ),
            ));
            $this->pagedata['delivery_id'] = array_keys(utils::array_change_key($delivery_list, 'delivery_id'));
        }
        foreach ($order['promotions'] as $pitem) {
            switch ($pitem['pmt_type']) {
                case 'goods':
                    $this->pagedata['goods_pmt'][$pitem['product_id']][] = $pitem;
                break;
                case 'order':
                    $this->pagedata['order_pmt'][] = $pitem;
                break;
            }
        }
        $this->set_tmpl('order');
        $this->page('site/order/detail.html');
    }

    public function logistics_tracker($delivery_id)
    {
        $params = $this->_request->get_params(true);
        $delivery_id = ($delivery_id ? $delivery_id : $params['delivery_id']);
        $tracker_log = vmc::singleton('logisticstrack_puller')->pull($delivery_id, $errmsg);
        if (!$tracker_log || empty($tracker_log)) {
            $this->splash('error', $errmsg ? $errmsg : '查询物流状态失败,请稍后重试。');
        } else {
            $this->splash('success', null, $tracker_log);
        }
    }
}
