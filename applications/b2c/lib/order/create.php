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


class b2c_order_create
{
    /**
     * 构造方法.
     *
     * @param object app
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->obj_math = vmc::singleton('ectools_math');
    }
    /**
     * 订单标准数据生成.
     */
    public function generate(&$order_sdf, $cart_result = array(), &$msg = '')
    {
        $new_order_id = $order_sdf['order_id'] ? $order_sdf['order_id'] : app::get('b2c')->model('orders')->apply_id();
        $obj_total = new b2c_order_total();
        $total = $obj_total->detail($order_sdf, $cart_result);
        $sdf = array(
            'order_id' => $new_order_id, //订单唯一ID
            'weight' => $cart_result['weight'], //货品总重量
            'quantity' => $cart_result['goods_count'], //货品总数量
            'ip' => base_request::get_remote_addr() , //下单IP地址
            'memberlv_discount' => $cart_result['member_discount_amount'], //会员身份优惠总额
            'pmt_goods' => $cart_result['goods_promotion_discount_amount'], //商品级优惠促销总额
            'pmt_order' => $cart_result['order_promotion_discount_amount'], //订单级促销优惠总额
            'finally_cart_amount' => $cart_result['finally_cart_amount'], //购物车优惠后总额
            'score_g' => $total['gain_score'],//订单可得积分
            'order_total' => $total['order_total'], //订单应付当前货币总额
            'cost_tax' => $total['cost_tax'], //营业税
            'cost_protect' => $total['cost_protect'], //保价费
            'cost_payment' => $total['cost_payment'], //支付手续费
            'cost_freight' => $total['cost_freight'], //运费

        );
        $order_sdf = array_merge($order_sdf, $sdf);
        //发票参数处理
        if ($order_sdf['need_invoice'] != 'true') {
            $order_sdf['need_invoice'] = 'false';
            unset($order_sdf['invoice_title']);
        } else {
            if (!isset($order_sdf['invoice_addon']) || !is_array($order_sdf['invoice_addon'])) {
                $order_sdf['invoice_addon'] = array();
            }
            //发票未开出
            $order_sdf['invoice_addon'] = array_merge($order_sdf['invoice_addon'], array('invoice_out' => 'false'));
        }

        //组织订单明细-[商品]
        foreach ($cart_result['objects']['goods'] as $key => $object) {
            if ($object['disabled'] == 'true') {
                continue;
            }
            $product = $object['item']['product'];
            //has_many order_items
            $order_sdf['items'][] = array(
                'order_id' => $new_order_id,
                'product_id' => $product['product_id'],
                'goods_id' => $product['goods_id'],
                'bn' => $product['bn'],
                'name' => $product['name'],
                'spec_info' => $product['spec_info'],
                'price' => $product['price'],
                'member_lv_price' => $product['member_lv_price'],
                'buy_price' => $product['buy_price'],
                'amount' => $this->obj_math->number_multiple(array(
                    $product['buy_price'],
                    $object['quantity'],
                )) ,
                'nums' => $object['quantity'],
                'weight' => $this->obj_math->number_multiple(array(
                    $product['weight'],
                    $object['quantity'],
                )) ,
                'image_id' => $product['image_id'],
            );
            $cart_objects[$object['obj_ident']] = $object;
        }
        //组织订单明细-[商品促销规则]
        foreach ($cart_result['promotions']['goods'] as $key => $pmts) {
            foreach ($pmts as $value) {
                $order_sdf['promotions'][] = array(
                    'rule_id' => $value['rule_id'],
                    'order_id' => $new_order_id,
                    'product_id' => $cart_objects[$key]['item']['product']['product_id'],
                    'pmt_type' => 'goods',
                    'pmt_tag' => $value['tag'],
                    'pmt_description' => $value['desc'],
                    'pmt_solution' => $value['solution'],
                    'pmt_save' => $value['save'],
                );
            }
        }
        //组织订单明细-[订单促销规则]
        foreach ($cart_result['promotions']['order'] as $key => $value) {
            $order_sdf['promotions'][] = array(
                'rule_id' => $value['rule_id'],
                'order_id' => $new_order_id,
                'pmt_type' => 'order',
                'pmt_tag' => $value['tag'],
                'pmt_description' => $value['desc'],
                'pmt_solution' => $value['solution'],
                'pmt_save' => $value['save'],
            );
        }
        //TODO  优惠券数据
        // 订单创建前之行的方法
        $services = vmc::servicelist('b2c.order.create.before');
        if ($services) {
            foreach ($services as $service) {
                $flag = $service->exec($order_sdf, $cart_result, $msg);
                if (!$flag) {
                    return false;
                }
            }
        }

        return true;
    }
    /**
     * 订单保存.
     *
     * @param array order_sdf
     * @param string message
     *
     * @return bool
     */
    public function save(&$sdf, &$msg = '')
    {
        $mdl_order = $this->app->model('orders');
        //must Insert
        $result = $mdl_order->save($sdf, null, true);
        if (!$result) {
            $msg = ('订单未能保存成功');

            return false;
        } else {
            //订单创建时同步扩展服务
            foreach (vmc::servicelist('b2c.order.create.finish') as $service) {
                if (!$service->exec($sdf, $msg)) {
                    //记录日志，不中断
                    logger::error($sdf['order_id'].'创建出错！'.$msg);
                }
            }
            //订单相关业务异步处理队列
            system_queue::instance()->publish('b2c_tasks_order_related', 'b2c_tasks_order_aftercreate', $sdf);

            return true;
        }
    }
}
