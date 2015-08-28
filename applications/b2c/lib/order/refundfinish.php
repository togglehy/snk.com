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
 * 退款成功时.
 */
class b2c_order_refundfinish
{
    public function exec(&$bill,  &$msg = '')
    {
        $order_id = $bill['order_id'];
        if (!$order_id) {
            $msg = '未知订单ID';

            return false;
        }
        $omath = vmc::singleton('ectools_math');
        $mdl_orders = app::get('b2c')->model('orders');

        $order_sdf = $mdl_orders->dump($order_id);
        if (!$order_sdf) {
            $msg = '未知订单';

            return false;
        }

        $exist_refund_bill = app::get('ectools')->model('bills')->getList('*', array('bill_type' => 'refund', 'order_id' => $bill['order_id']));

        $sum_refund = 0;
        foreach ($exist_refund_bill as $rbill) {
            $sum_refund = $omath->number_plus(array(
                $sum_refund,
                $rbill['money'],
            ));
        }
        if ($sum_refund >= $order_sdf['payed']) {
            $update['pay_status'] = '5'; //全额退
            $log_msg_pad = '全额退款';
        } else {
            $update['pay_status'] = '4'; //部分退
            $log_msg_pad = '部分退款';
        }
        if (!$mdl_orders->update($update, array('order_id' => $order_id))) {
            $msg = '订单主单据信息更新失败!';

            return false;
        }
        //订单日志记录
        vmc::singleton('b2c_order_log')->set_operator(array(
            'ident' => $bill['op_id'],
            'model' => 'shopadmin',
            'name' => '操作员',
        ))->set_order_id($order_sdf['order_id'])->success('refund', $log_msg_pad.'成功', $bill);


        $pam_data = vmc::singleton('b2c_user_object')->get_pam_data('*', $order_sdf['member_id']);
        $env_list = array(
            'order_id'=>$bill['order_id'],
            'bill_id'=>$bill['bill_id'],
            'money'=>ectools_cur::format($bill['money']),
            'timestr'=>date('Y-m-d H:i:s',$bill['last_modify']),
        );
        vmc::singleton('b2c_messenger_stage')->trigger('orders-refund', $env_list, array(
            'mobile' => $pam_data['mobile'] ? $pam_data['mobile']['login_account'] : $order_sdf['consignee']['mobile'],
            'email' => $pam_data['email'] ? $pam_data['email']['login_account'] : $order_sdf['consignee']['email'],
            'member_id' => $order_sdf['member_id'],
        ));

        return true;
    }
}
