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


class b2c_checkout_stage
{
    //$params  member_id,addr_id,dlytype_id,payapp_id,cart_result，payapp_filter
    public function check($params)
    {
        $member_id = $params['member_id'];
        $addr_id = $params['addr_id'];
        $dlytype_id = $params['dlytype_id'];
        $payapp_id = $params['payapp_id'];
        $cart_result = $params['cart_result'];
        //支付方式过滤
        $payapp_filter = array(
            'status' => 'true',
            'platform_allow' => array(
                'pc',
            ),
        );
        if ($params['payapp_filter']) {
            $payapp_filter = array_merge($payapp_filter, $params['payapp_filter']);
        }
        $_return = array();
        $mdl_maddr = app::get('b2c')->model('member_addrs');
        $mdl_dltype = app::get('b2c')->model('dlytype');
        $mdl_payapps = app::get('ectools')->model('payment_applications');

        if ($member_addrs = $mdl_maddr->getList('*', array(
            'member_id' => $member_id,
        ), 0, -1, '`is_default` ASC,updatetime DESC,`addr_id`')) {
            $def_addr = $member_addrs[0]; //会员默认收货地址
            $member_addrs = utils::array_change_key($member_addrs, 'addr_id');
            $last_checkout = $this->get_default($member_id); //会员最后一次购物时选择的配送和支付方式
            if ($addr_id) {
                $member_addrs[$addr_id]['selected'] = 'true';
                $area = $member_addrs[$addr_id]['area'];
            } else {
                $area = $def_addr['area'];
                $member_addrs[$def_addr['addr_id']]['selected'] = 'true';
            }
            $area_id = array_pop(explode(':', $area));
            $_return['member_addrs'] = $member_addrs;
        }
        //根据地区获得送货方式
        $dlytypes = $mdl_dltype->getAvailable($area_id);
        $dlytypes = utils::array_change_key($dlytypes, 'dt_id');
        if ($dlytype_id = $dlytype_id ? $dlytype_id : $last_checkout['dlytype_id']) {
            if ($dlytypes[$dlytype_id]) {
                $dlytypes[$dlytype_id]['selected'] = 'true';
                if ($dlytypes[$dlytype_id]['has_cod'] == 'true') {
                    $is_cod = true; //为了货到付款锁定在线支付
                }
            } else {
                $dlytypes[key($dlytypes) ]['selected'] = 'true';
            }
        }
        if ($is_cod) {
            $payapp_filter['app_id'] = 'cod';
        }
        $paymentapps = $mdl_payapps->getList('*', $payapp_filter);
        $paymentapps = utils::array_change_key($paymentapps, 'app_id');
        if ($payapp_id = $payapp_id ? $payapp_id : $last_checkout['pay_app']) {
            if ($paymentapps[$payapp_id]) {
                $paymentapps[$payapp_id]['selected'] = 'true';
            } else {
                $paymentapps[key($paymentapps) ]['selected'] = 'true';
            }
        }
        $_return['dlytypes'] = $dlytypes;
        $_return['paymentapps'] = $paymentapps;
        if (!$params['ignore_return_cart_result']) {
            $_return['cart_result'] = $cart_result;
        }
        $_return['cart_md5'] = utils::array_md5($cart_result);
        $order_sdf_tmp = array(
            'consignee' => array(
                'area' => $area,
            ) ,
            'dlytype_id' => $dlytype_id,
        );
        $_return['total'] = vmc::singleton('b2c_order_total')->detail($order_sdf_tmp, $cart_result); //总价
        return $_return;
    }
    /*得到会员最后一次订单确认习惯数据*/
    private function get_default($member_id)
    {
        $mdl_orders = app::get('b2c')->model('orders');
        $last_order = $mdl_orders->getRow('pay_app,dlytype_id,member_id', array(
            'member_id' => $member_id,
        ), 'createtime DESC');
        if (!empty($last_order)) {
            return array(
                'pay_app' => $last_order['pay_app'],
                'dlytype_id' => $last_order['dlytype_id'],
            );
        }

        return false;
    }
}
