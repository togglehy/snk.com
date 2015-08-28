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


class b2c_tasks_order_cancel extends base_task_abstract implements base_interface_task
{
    public function exec($params = null)
    {

        $order_autocancel_time = app::get('b2c')->getConf('order_autocancel_time',86400);//秒
        $mdl_orders = app::get('b2c')->model('orders');
        $limit = 200; //每次最多处理200条
        $filter = array(
            'status' => 'active',//活动订单
            'pay_status' => '0',//未支付
            'ship_status' => '0',//未发货
            'is_cod'=>'N',//非货到付款
            'createtime|lthan' => (time() - $order_autocancel_time) ,
        );
        foreach ($mdl_orders->getList('order_id',$filter,0,$limit) as $key => $order) {
            $order['op_id'] = '-1';
            $order['op_name'] = '定时任务队列';
            if(!vmc::singleton('b2c_order_cancel')->generate($order,$msg)){
                logger::warning('订单自动关闭队列异常。ORDER_ID：'.$order['order_id'].'。'.$msg);
            }
        }
        return false;

    }
}
