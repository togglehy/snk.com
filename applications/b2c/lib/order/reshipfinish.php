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




class b2c_order_reshipfinish
{

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * 订单退货操作完成时
     * @params array - 退货单据数据SDF
     * @return boolean - 执行成功与否
     */
    public function exec($delivery_sdf,&$msg='')
    {

        $order_id = $delivery_sdf['order_id'];
        if (!$order_id) {
            $msg = '未知订单id';
            return false;
        }
        $all_send = true;
        $order_items = app::get('b2c')->model('order_items')->getList('*', array(
            'order_id' => $order_id
        ));
        $order_items = utils::array_change_key($order_items,'item_id');

        foreach ($delivery_sdf['delivery_items'] as $item) {
            $order_items[$item['order_item_id']]['sendnum']-= $item['sendnum'];
            if ($order_items[$item['order_item_id']]['sendnum']<0) {
                $msg = '退货时，造成订单明细数据异常';
                return false;
            }
            if ($order_items[$item['order_item_id']]['sendnum']!=0) {
                $all_send = false; //部分退货
            }
        }
        $order_sdf = array(
            'order_id' => $order_id,
            'items' => $order_items,
            'ship_status' => $all_send ? '4' : '3' //4：全退，3：部分退货

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
        if(!vmc::singleton('b2c_goods_stock')->returned($stock_data,$msg)){
            logger::error('库存回滚异常!ORDER_ID:'.$order_sdf['order_id'].'，'.$msg);
        }

        //订单日志记录
        vmc::singleton('b2c_order_log')->set_operator(array(
            'ident' => $delivery_sdf['op_id'],
            'model' => 'shopadmin',
            'name' => '操作员'
        ))->set_order_id($order_sdf['order_id'])->success('reship', '订单' . (!$all_send ? '部分' : '') . '退货成功!', $delivery_sdf);

        return true;
    }


}
