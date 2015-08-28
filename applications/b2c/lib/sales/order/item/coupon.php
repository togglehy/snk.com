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


class b2c_sales_order_item_coupon extends b2c_sales_order_item
{
    public function __construct()
    {
        parent::__construct();
        $this->o_coupon = app::get('b2c')->model('coupons');
    }
    /**
     * item validate 重载.
     *
     * @param array $objects
     * @param array $aCondition
     *
     * @return bool
     */
    public function validate($cart_result, $aCondition)
    {
    
        if (empty($aCondition['value'])) {
            return false;
        }
        if (empty($cart_result['objects']['coupon'])) {
            return false;
        }
        $couponsModel = $this->o_coupon;
        $value = $aCondition['value'];
        $couponFlag = $couponsModel->getFlagFromCouponCode($value);
        $_return = false;
        foreach ($cart_result['objects']['coupon'] as $coupon) {
            if('B' == $couponFlag){
                $couponPre = $couponsModel->getPrefixFromCouponCode($coupon['params']['code']);
                $_return = ($couponPre == $value);
            }else{
                $_return = ($coupon['params']['code'] == $value);
            }
            if($_return){
                break;
            }
        }
        return $_return;
    }
}
