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


class b2c_order_sendfinish
{
    public function __construct($app)
    {
        $this->app = $app;
    }
    /**
     * 订单发货操作完成时.
     *
     * @delivery_sdf array - 发货单据SDF
     * @msg  异常消息
     *
     * @return bool - 执行成功与否
     */
    public function exec($delivery_sdf, &$msg = '')
    {
        $order_id = $delivery_sdf['order_id'];
        if (!$order_id) {
            $msg = '未知订单id';

            return false;
        }
        $all_send = true;
        $order_items = app::get('b2c')->model('order_items')->getList('*', array(
            'order_id' => $order_id,
        ));
        $order_items = utils::array_change_key($order_items, 'item_id');

        foreach ($delivery_sdf['delivery_items'] as $item) {
            $order_items[$item['order_item_id']]['sendnum'] += $item['sendnum'];
            if ($order_items[$item['order_item_id']]['sendnum'] > $order_items[$item['order_item_id']]['nums']) {
                $msg = '发货异常，超出应发数量';

                return false;
            }
            if ($order_items[$item['order_item_id']]['sendnum'] < $order_items[$item['order_item_id']]['nums']) {
                $all_send = false; //部分发货
            }
        }
        $order_sdf = array(
            'order_id' => $order_id,
            'items' => $order_items,
            'ship_status' => $all_send ? '1' : '2', //1：全部发货，2：部分发货

        );
        if (!app::get('b2c')->model('orders')->save($order_sdf)) {
            $msg = '订单状态修改失败！';

            return false;
        }

        //库存冻结释放，真实扣除库存
        $stock_data = array();
        foreach ($delivery_sdf['delivery_items'] as $key => $value) {
            $stock_data[] = array(
                'sku'=>$value['bn'],
                'quantity'=>$value['sendnum']
            );
        }
        if(!vmc::singleton('b2c_goods_stock')->unfreeze($stock_data,$msg)){
            logger::error('库存冻结释放异常!ORDER_ID:'.$order_sdf['order_id'].','.$msg);
        }
        if(!vmc::singleton('b2c_goods_stock')->delivery($stock_data,$msg)){
            logger::error('库存扣减异常!ORDER_ID:'.$order_sdf['order_id'].','.$msg);
        }

        //订单日志记录
        vmc::singleton('b2c_order_log')->set_operator(array(
            'ident' => $delivery_sdf['op_id'],
            'model' => 'shopadmin',
            'name' => '操作员',
        ))->set_order_id($order_sdf['order_id'])->success('shipment', '订单'.(!$all_send ? '部分' : '').'发货成功!', $delivery_sdf);

        /*
         * 消息通知
         * @args1 事件名称
         * @args2 消息模板数据填充
         * @args3 消息目标
         */
        $pam_data = vmc::singleton('b2c_user_object')->get_pam_data('*', $delivery_sdf['member_id']);
        logger::debug('pam_data'.var_export($pam_data,1));

        $dlycorp = app::get('b2c')->model('dlycorp')->dump($delivery_sdf['dlycorp_id']);
        $consignee_area = $delivery_sdf['consignee']['area'];
        $consignee_area = explode(':',$consignee_area);
        $consignee_area = $consignee_area[1];
        //消息模板参数
        $env_list = array(
            'order_id'=>$delivery_sdf['order_id'],
            'consignee_name'=>$delivery_sdf['consignee']['name'],
            'consignee_area'=>$consignee_area,
            'consignee_addr'=>$delivery_sdf['consignee']['addr'],
            'consignee_tel'=>$delivery_sdf['consignee']['tel'],
            'consignee_mobile'=>$delivery_sdf['consignee']['mobile'],
            'dlycorp_name'=>$dlycorp['name'],
            'dlycorp_code'=>$dlycorp['corp_code'],
            'dlycorp_website'=>$dlycorp['website'],
            'logistics_no'=>$delivery_sdf['logistics_no'],
            'timestr'=>date('Y-m-d H:i:s',$delivery_sdf['last_modify']),
        );
        vmc::singleton('b2c_messenger_stage')->trigger('orders-shipping', $env_list, array(
            'mobile' => $pam_data['mobile'] ? $pam_data['mobile']['login_account'] : $order_sdf['consignee']['mobile'],
            'email' => $pam_data['email'] ? $pam_data['email']['login_account'] : $order_sdf['consignee']['email'],
            'member_id' => $delivery_sdf['member_id'],
        ));

        return true;
    }
}
