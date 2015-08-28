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
 * 购物车中央处理类
 * 此类完成购物车全部操作.
 *
 * @author litie@toggle.cn
 */
class b2c_cart_stage
{
    private $member_id = null;
    /**
     * 设置当前购物车操作的member_id.
     *
     * @param $member_id  有效会员ID
     */
    public function set_member_id($member_id)
    {
        $this->member_id = $member_id;
    }
    /**
     * 调用此方法前，需要先 调用 set_member_id,
     * 一般调用此方法时，set_member_id 已被调用，并且整个类实例为单例模式流程中！.
     */
    public function get_member()
    {
        $user_object = vmc::singleton('b2c_user_object');

        return $user_object->get_member_info($this->member_id);
    }
    /**
     * 向购物车加入内容，内部会自动判断应该加入到数据库还是临时购物车，还是快速购买购物车.
     *
     * @param $obj_type string  购物车内容类型。 e.g. goods \ coupon
     * @param $arr_data array  不同类型购物车内容，所需数据不同
     * @param &$msg string 错误提示，引用传递
     * @param $is_fastbuy boolean 是否是要进行快速购买
     */
    public function add($obj_type, $arr_data, &$msg, $is_fastbuy = false)
    {
        $ready_obj = false;
        foreach (vmc::servicelist('b2c_cart_object_apps') as $obj) {
            if ($obj_type && $obj_type == $obj->get_type()) {
                $ready_obj = $obj;
            }
        }
        if (!$ready_obj || !method_exists($ready_obj, 'add_object')) {
            $msg = '未知购物车内容类型';

            return flase;
        }
        //各类型对象内完成加入操作
        return $ready_obj->add_object($arr_data, $msg, true, $is_fastbuy);
    }
    /**
     * 删除购物车内容.
     *
     * @param $obj_type string  购物车内容类型。 e.g. goods \ coupon
     * @param $ident string  购物车内容obj_ident
     * @param $is_fastbuy 在快速购买过程中进行购物车内容删除，如在快速购买时，进行优惠券删除操作
     */
    public function delete($obj_type, $ident, $is_fastbuy = false)
    {
        foreach (vmc::servicelist('b2c_cart_object_apps') as $obj) {
            if ($obj_type == $obj->get_type()) {
                $obj->delete($ident, $is_fastbuy);
                break;
            }
        }
    }
    /**
     * 购物车商品购买数量更新.
     *
     * @param $obj_type string 购物车内容类型。 e.g. goods \ coupon
     * @param $ident string 购物车内容obj_ident
     * @param $quantity number 更新到数量
     * @param &$msg string 错误消息
     */
    public function update($obj_type, $ident, $quantity, &$msg)
    {
        foreach (vmc::servicelist('b2c_cart_object_apps') as $obj) {
            if ($obj_type == $obj->get_type()) {
                $obj->update($ident, $quantity, $msg);
                break;
            }
        }
        if ($msg) {
            return false;
        }

        return $this->result();
    }
    /**
     * 清除购物车内容 主要用于结算成功时.
     *
     * @param $cart_result array 指定清空的内容项
     * @param $is_fastbuy boolean 是否是在快速购买过程中操作
     */
    public function clean($cart_result = false, $is_fastbuy = false)
    {
        if ($cart_result) {
            foreach ($cart_result['objects'] as $type => $objects) {
                foreach ($objects as $key => $object) {
                    if ($object['disabled'] == 'true') {
                        continue;//不参与结算的购物车项不做清除
                    }
                    if($type == 'coupon' && $object['params']['in_use']!='true'){
                        continue;//没有使用优惠券不做清除
                    }
                    $this->delete($type, $object['obj_ident'], $is_fastbuy);
                }
            }
        } else {
            foreach (vmc::servicelist('b2c_cart_object_apps') as $obj) {
                if (!method_exists($obj, 'deleteAll')) {
                    continue;
                }
                $obj->deleteAll($is_fastbuy);
            }
        }
    }
    /**
     * 得到购物车当前详情信息.
     *
     * @param $filter array 过滤条件，e.g.  $filter = array('is_fastbuy'=>'true');
     */
    public function result($filter)
    {

        //排序
        foreach (vmc::servicelist('b2c_cart_process_apps') as $object) {
            if (!is_object($object)) {
                continue;
            }
            $processer[$object->get_order() ] = $object;
            // b2c_cart_process_get  购物车首次数据填充
            // b2c_cart_process_prefilter 商品促销计算
            // b2c_cart_process_postfilter 订单促销计算
        }

        if (!$_SESSION['CART_DISABLED_IDENT'] || !is_array($_SESSION['CART_DISABLED_IDENT'])) {
            $_SESSION['CART_DISABLED_IDENT'] = array();
        }
        if (count($_SESSION['CART_DISABLED_IDENT']) > 0) {
            //存在购物车禁用项
            $filter['disabled_ident'] = $_SESSION['CART_DISABLED_IDENT'];
        }
        krsort($processer);
        foreach ($processer as $p) {
            $p->process($filter, $cart_result, $config);
        }
        return $cart_result;
    }

    /**
     * 得到购物车当前货币化详情.
     *
     * @param $filter array 过滤条件，e.g.  $filter = array('is_fastbuy'=>'true');
     * @param $cart_result array 如果传递一个非货币化详情进来，就不要再操作数据库了
     */
    public function currency_result($filter, $cart_result = null)
    {
        if (!$cart_result) {
            $cart_result = $this->result($filter);
        }
        $f1 = app::get('ectools')->getConf('site_decimal_digit_count'); //小数位数
        $f2 = app::get('ectools')->getConf('site_decimal_type_count'); //进位方式
        $ecmath = vmc::singleton('ectools_math');
        foreach ($cart_result as $i => $v1) {
            switch ($i) {
                case 'cart_amount':
                case 'promotion_discount_amount':
                case 'member_discount_amount':
                case 'goods_promotion_discount_amount':
                case 'order_promotion_discount_amount':
                    if ($v1 > 0) {
                        $cart_result[$i] = $ecmath->formatNumber($v1, $f1, $f2);
                    }
                break;
                case 'objects':
                    foreach ($v1['goods'] as $j => $v2) {
                        foreach ($v2['item']['product'] as $k => $v3) {
                            switch ($k) {
                                case 'buy_price':
                                case 'member_lv_price':
                                case 'mktprice':
                                case 'mktprice':
                                    if ($v3 > 0) {
                                        $cart_result['objects']['goods'][$j]['item']['product'][$k] = $ecmath->formatNumber($v3, $f1, $f2);
                                    }
                                break;
                            }
                        }
                        $cart_result['objects']['goods'][$j]['amount'] = $ecmath->formatNumber($cart_result['objects']['goods'][$j]['item']['product']['buy_price'] * $cart_result['objects']['goods'][$j]['quantity'], $f1, $f2);
                    }
                break;
            }
        }
        $cart_result['finally_cart_amount'] = $ecmath->formatNumber($cart_result['cart_amount'] - $cart_result['member_discount_amount'] - $cart_result['promotion_discount_amount'], $f1, $f2);

        return $cart_result;
    }
    /**
     * 辅助判断购物车详情数组是否是没有商品信息的.
     *
     * @param $cart_result  购物车结果数组
     */
    public function is_empty($cart_result)
    {
        if (!is_array($cart_result)) {
            return true;
        }
        if (empty($cart_result['objects'])) {
            return true;
        }
        $keys = array_keys($cart_result['objects']);
        foreach ($keys as $key) {
            if ($key == 'coupon') {
                continue;
            }
            if (!empty($cart_result['objects'][$key])) {
                return false;
            }
        }

        return true;
    }
}
