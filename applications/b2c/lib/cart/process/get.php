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




/**
 * 购物车处理 第一步
 */

class b2c_cart_process_get implements b2c_interface_cart_process {
    private $app;

    public function __construct(&$app){
        $this->app = $app;
    }


    public function get_order() {
        return 99;
    }

    public function process($filter,&$cart_result = array(),$config = array()){
        if(empty($cart_result)) {
            $this->_cart_init($filter,$cart_result,$config);
        }

        if(!$filter['is_fastbuy'] && $filter['disabled_ident']){
            //非立即购买 & 有禁用项
            foreach($cart_result['objects']['goods'] as $k=>$item){
                if(in_array($item['obj_ident'],$filter['disabled_ident'])||$item['warning']){
                    $cart_result['objects']['goods'][$k]['disabled'] = 'true';
                }elseif(!$item['warning']){
                    $cart_result['objects']['goods'][$k]['disabled'] = 'false';
                }
            }
        }

        $this->_cart_count($cart_result);


    }



    // 购物车里的处理
    private function _cart_init($filter,&$cart_result,$config){
        // servicelist('b2c_cart_object_apps')=>
        // gift_cart_object_gift
        // b2c_cart_object_coupon
        // b2c_cart_object_goods
        foreach(vmc::servicelist('b2c_cart_object_apps') as $object) {
            if(!is_object($object)) continue;
            if( method_exists($object,'get_order') )
                $index = $object->get_order();
            else $index = 10;
            while(true) {
                if( !isset($tmp[$index]) )break;
                $index++;
            }
            $tmp[$index] = $object;
        }
        krsort($tmp);

        foreach($tmp as $object) {
            if( method_exists($object,'get_type') )
                $type_name = $object->get_type();
            else
                $type_name = array_pop(explode('_',get_class($object))); // 购物车项类型
            //购物车数据丰富首次填充
            $cart_result['objects'][$type_name] = $object->getAll(true,$filter['is_fastbuy']);
        }
    }
    //购物车首次小计计算
    private function _cart_count(&$cart_result){
        //初始化购物车小计项
        $amount_arr = array(
            'consume_score' => 0, //消费积分
            'gain_score' => 0, //获得积分
            'goods_count' => 0, //商品总量
            'object_count' => 0, //购物车项数
            'weight' => 0, //总重量
            'cart_amount' => 0.000, //购物车金额（优惠前）
            'member_discount_amount' => 0.000, //会员身份优惠小计
            'order_promotion_discount_amount' => 0.000, //订单级促销优惠
            'goods_promotion_discount_amount' => 0.000, //商品级促销优惠
            'promotion_discount_amount' => 0.000, //促销优惠合计（商品+订单\优惠券促销优惠合计）
            'finally_cart_amount' => 0.000, //购物车合计金额（所有优惠后扣除后）
            //finally_cart_amount  =  cart_amount - member_discount_amount - promotion_discount_amount
        );
        $cart_result = array_merge($cart_result, $amount_arr);
        foreach (vmc::servicelist('b2c_cart_object_apps') as $object) {
            if (!is_object($object)) continue;
            $object->count($cart_result); //$cart_result 引用传递
        }
    }

}
