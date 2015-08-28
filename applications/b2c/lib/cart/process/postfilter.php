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
 * 购物车处理 第三步 调用lib/cart/postfilter下的处理 订单促销规则过滤
 */
class b2c_cart_process_postfilter implements b2c_interface_cart_process {
    private $app;

    public function __construct(&$app){
        $this->app = $app;
        $this->omath = vmc::singleton('ectools_math');
    }

    public function get_order() {
        return 80;
    }

    public function process($filter,&$cart_result = array(),$config = array()){
        // servicelist('b2c_cart_postfilter_apps')=>
        // b2c_cart_postfilter_promotion
        //订单促销过滤计算

        //事先计算此时finally_cart_amount 购物车金额

        $cart_result['finally_cart_amount'] = $this->omath->number_minus(array(
            $cart_result['cart_amount'],
            $cart_result['member_discount_amount'],
            $cart_result['promotion_discount_amount']
        ));


        foreach(vmc::servicelist('b2c_cart_postfilter_apps') as $object) {
            if(!is_object($object)) continue;

            $object->filter($filter,$cart_result,$config);
            
        }

    }
}
?>
