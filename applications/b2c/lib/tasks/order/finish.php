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


class b2c_tasks_order_finish extends base_task_abstract implements base_interface_task
{
    public function exec($params = null)
    {
        
        $order_autocancel_day = app::get('b2c')->getConf('order_autofinish_day',9);//天
        $mdl_orders = app::get('b2c')->model('orders');
        $limit = 200; //每次最多处理200条
        $filter = array(
            'status' => 'active',//活动订单
            'pay_status' => '1',//已支付
            'ship_status' => '1',//已发货
            'createtime|lthan' => (time() - $order_autocancel_day*24*3600) ,
        );
        foreach ($mdl_orders->getList('order_id',$filter,0,$limit) as $key => $order) {
            $order['op_id'] = '-1';
            $order['op_name'] = '定时任务队列';
            if(!vmc::singleton('b2c_order_end')->generate($order,$msg)){
                logger::warning('订单自动完成队列异常。ORDER_ID：'.$order['order_id'].'。'.$msg);
            }
        }
        return false;

    }
}
