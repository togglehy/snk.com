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


class store_ctl_admin_pos extends desktop_controller {
    function index() {
        $this->page('admin/pos/index.html');
    }

    function product_filter() {
        $filter = $_POST['filter'];
        $f_value = array_values($filter);
        $f_value = $f_value[0];
        if (trim($f_value) == '') {
            die('');
        }
        $mdl_goods = app::get('b2c')->model('goods');
        $mdl_product = app::get('b2c')->model('products');
        $products = $mdl_product->getList('*', $filter, 0, 100);
        foreach ($products as $key => $value) {
            $item_group[$value['goods_id']][] = $value;
        }
        $gids = array_keys($item_group);
        $gimages = $mdl_goods->getList('goods_id,image_default_id', array(
            'goods_id' => $gids
        ));
        $this->pagedata['item_group'] = $item_group;
        $this->pagedata['gimages'] = utils::array_change_key($gimages, 'goods_id');
        $this->display('admin/pos/item_group.html');
    }

    function store_cart() {
        $products = $_POST['products'];
        $coupons = $_POST['coupons'];
        $member_id = $_POST['member_id'];
        $cart_stage = vmc::singleton('b2c_cart_stage');
        if($member_id){
            $cart_stage->set_member_id($member_id);
            $member_info = $cart_stage->get_member();
        }
        $cart_stage->clean(); //  每次都重置;
        //商品
        foreach ($products as $product_id => $num) {

            $cart_object['goods'][] = array(
                'goods' => array(
                    'product_id' => $product_id,
                    'num' => $num,
                )
            );
        }
        //优惠券
        foreach ($coupons as $k => $c) {
            $cart_object['coupon'][] = array(
                'coupon' => $c['code'],
            );
        }
        foreach ($cart_object as $key => $objects) {
            foreach ($objects as $object) {
                if(!$cart_stage->add($key,$object,$msg)){
                    $err_msg[] = array('warning'=>$msg,'object'=>$object[key($object)]);
                }
            }

        }

        if ($member_info) {
            $this->pagedata['member_info'] = $member_info;
        }
        $this->pagedata['err_msg'] = $err_msg;
        $this->pagedata['cart_info'] = $cart_stage->result();

        $this->display('admin/pos/cart.html');
    }

    function sel_coupon() {
        $this->display('admin/pos/sel_coupon.html');
    }

    function sel_delivery() {
        $this->display('admin/pos/sel_delivery.html');
    }

    function neworder() {
        $this->begin();
        $new_order = $_POST;

        $member_id = $new_order['member_id'];
        $cart_stage = vmc::singleton('b2c_cart_stage');
        if($member_id){
            $cart_stage->set_member_id($member_id);
            $member_info = $cart_stage->get_member();
        }


        if ($new_order['is_delivery'] == 'Y') {
            foreach ($_POST['delivery'] as $key => $value) {
                if (!$value || trim($value) == '') {
                    $this->end(false, '请完善收货人信息');
                }
            }
        }
        if (!$new_order['payment']['pay_app_id']) {
            $this->end(false, '未知的付款方式！');
        }

        $cart_result = $cart_stage->result();
        $is_empty = $cart_stage->is_empty($cart_result);
        if ($is_empty) {
            $this->end(false, '缺少货品信息，无法产生交易。');
        }
        $mdl_order = app::get('b2c')->model('orders');
        $new_order['order_id'] = $order_id = $mdl_order->gen_id();
        $obj_order_create = vmc::singleton("b2c_order_create");
        //预生成订单
        $new_order_data = $obj_order_create->generate($new_order, $member_indent, $msg, $cart_info);
        if (!$new_order_data) {
            $this->end(false, $msg);
        }
        $result = $obj_order_create->save($new_order_data, $msg); //保存订单

    }

    function printer($order_id){
        $this->display('ticket_print.html');
    }

    function edit($store_id) {
    }
}
