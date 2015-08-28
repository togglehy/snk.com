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


class b2c_order_total {
    /**
     * 生成订单总计
     */
    public function detail($sdf_order, $cart_result) {
        $f1 = app::get('ectools')->getConf('site_decimal_digit_count'); //小数位数
        $f2 = app::get('ectools')->getConf('site_decimal_type_count'); //进位方式
        $obj_math = vmc::singleton('ectools_math');
        $weight_amount = $cart_result['weight']; //商品总重量
        $cart_amount = $cart_result['cart_amount']; //购物车商品价格小计
        $member_discount_amount = $cart_result['member_discount_amount']; //购物车会员优惠小计
        $promotion_discount_amount = $cart_result['promotion_discount_amount']; //购物车促销优惠小计
        //购物车优惠后价格
        $finally_cart_amount = $cart_result['finally_cart_amount'] ? $cart_result['finally_cart_amount'] : $obj_math->number_minus(array(
            $cart_amount,
            $member_discount_amount,
            $promotion_discount_amount
        ));
        $cost_freight = 0; //物流费用
        $cost_protect = 0; //物流保价费用
        //物流费用计算
        if (!$cart_result['free_shipping'] && $sdf_order['dlytype_id']) {
            $mal_dlytype = app::get('b2c')->model('dlytype'); //配送方式
            $dlytype_info = $mal_dlytype->dump($sdf_order['dlytype_id'], '*');
            //物流保价费
            if ($dlytype_info['protect'] == 'true') {
                /** 保价费界定为商品的最原始价格 **/
                $cost_protect = $obj_math->number_multiple(array(
                    $cart_amount,
                    $dlytype_info['protect_rate']
                ));
                $cost_protect = ($cost_protect > $dlytype_info['minprice'] ? $cost_protect : $dlytype_info['minprice']); //保价费

            }
            if (!is_array($dlytype_info['area_fee_conf'])) $dlytype_info['area_fee_conf'] = unserialize($dlytype_info['area_fee_conf']);
            //不同收货地区不同配送费用
            if ($sdf_order['consignee']['area'] && $dlytype_info['setting'] != '1' && $dlytype_info['area_fee_conf'] && is_array($dlytype_info['area_fee_conf'])) {
                $area_id = array_pop(explode(':', $sdf_order['consignee']['area']));
                $mdl_regions = app::get('ectools')->model('regions');
                $region = $mdl_regions->getRow('region_path', array(
                    'region_id' => $area_id
                ));
                $root_area_id = reset(array_filter(explode(',', $region['region_path'])));
                foreach ($dlytype_info['area_fee_conf'] as $key => $conf) {
                    if (in_array($root_area_id, $conf['area'])) {
                        $cost_freight = $conf['firstprice'];
                    }
                }
            }
            $cost_freight = ($cost_freight > 0 ? $cost_freight : $dlytype_info['firstprice']);
            // @utils::cal_fee($dlytype_info['dt_expressions'], $items_weight, $cost_item, $dlytype_info['firstprice'], $dlytype_info['continueprice']); //TODO  首重续重下的配送费用

        }
        //TODO  开票营业税、支付手续费
        $order_total = $obj_math->number_plus(array(
            $finally_cart_amount,
            $cost_protect,
            $cost_freight
        ));



        $cart_amount = array(
            //'consume_score' => 0, //消费积分
            //'gain_score' => 0, //获得积分
            //'goods_count' => 0, //商品总量
            //'object_count' => 0, //购物车项数
            //'weight' => 0, //总重量
            'cart_amount' => 0, //购物车金额（优惠前）
            'member_discount_amount' => 0, //会员身份优惠小计
            'order_promotion_discount_amount' => 0, //订单级促销优惠
            'goods_promotion_discount_amount' => 0, //商品级促销优惠
            'promotion_discount_amount' => 0, //促销优惠合计
            //'finally_cart_amount' => 0, //购物车合计金额（所有优惠后）
            //finally_cart_amount 用到时 用 cart_amount - member_discount_amount - promotion_discount_amount

        );
        foreach ($cart_amount as $key => $value) {
            if ($cart_result[$key]) $cart_amount[$key] = $cart_result[$key];
        }
        $_return = array(

            'cost_protect' => $cost_protect, //保价费
            'cost_freight' => $cost_freight, //运费
            'cost_payment' => 0, //TODO //在线支付手续费
            'order_total' => $order_total, //*******订单最终应付总价
            'cost_tax' => 0, //TODO //税

        );
        $_return = array_merge($_return, $cart_amount);
        foreach ($_return as $key => $value) {
            //格式化价格数据，小数点位数、四舍五入规则等
            $_return[$key] = $obj_math->formatNumber($value, $f1, $f2);
            if($_return[$key]<0)$_return[$key] = $obj_math->formatNumber(0, $f1, $f2);;
        }
        //获得积分合计,四舍五入，取整
        $_return['gain_score'] = $obj_math->formatNumber($cart_result['gain_score'], 0 , $f2);

        return $_return;
    }
}
