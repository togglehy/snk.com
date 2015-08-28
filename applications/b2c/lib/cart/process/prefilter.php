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
 * 购物车处理 第二步 调用lib/cart/prefilter下的处理 订单促销规则过滤
 */

class b2c_cart_process_prefilter implements b2c_interface_cart_process {
    private $app;

    public function __construct(&$app){
        $this->app = $app;
    }

    public function get_order() {
        return 90;
    }

    public function process($filter,&$cart_result = array(),$config = array()){
        // servicelist('b2c_cart_prefilter_apps')=>
        // b2c_cart_prefilter_promotion_goods

        //商品促销过滤计算
        foreach(vmc::servicelist('b2c_cart_prefilter_apps') as $object) {
            if(!is_object($object)) continue;
            $object->filter($cart_result,$config); //$cart_result 引用传递
        }



    }
}
?>
