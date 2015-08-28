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


class b2c_tasks_order_aftercreate extends base_task_abstract implements base_interface_task
{
    //订单创建成功时，相关处理业务
    public function exec($params = null)
    {
        $order_sdf = $params;
        $mdl_orders = app::get('b2c')->model('orders');
        $order_num = $mdl_orders->count(array(
            'member_id' => $order_sdf['member_id'],
        ));
        $mdl_members = app::get('b2c')->model('members');
        //更新会员订单数
        $mdl_members->update(array(
            'order_num' => $order_num,
        ), array(
            'member_id' => $order_sdf['member_id'],
        ));

        /* 订单金额为0 **/
        if ($order_sdf['order_total'] == '0') {
            // 生成支付账单
            $obj_bill = vmc::singleton('ectools_bill');
            $sdf = array(
                'bill_type' => 'payment',
                'pay_object' => 'order',
                'pay_mode' => (in_array($order_sdf['pay_app'], array(
                    '-1',
                    'cod',
                    'offline',
                )) ? 'offline' : 'online') ,
                'order_id' => $order_sdf['order_id'],
                'pay_app_id' => $order_sdf['pay_app'],
                'pay_fee' => $order_sdf['cost_payment'],
                'member_id' => $order_sdf['member_id'],
                'status' => 'succ',
                'money' => $order_sdf['order_total'],
                'memo' => '订单0元时自动生成',
            );
            if (!$obj_bill->generate($sdf, $msg)) {
                //TODO 自动支付失败,
                logger::error('订单0元时自动支付失败!'.$msg);

                return;
            }
        }

        return true;
    }
}
