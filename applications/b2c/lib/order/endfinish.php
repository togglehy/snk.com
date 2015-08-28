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


class b2c_order_endfinish
{
    public function exec(&$order_sdf,  &$msg = '')
    {
        $pam_data = vmc::singleton('b2c_user_object')->get_pam_data('*',$order_sdf['member_id']);
        $env_list = array(
            'order_id'=>$order_sdf['order_id'],
            'timestr'=>date('Y-m-d H:i:s',$order_sdf['last_modify']),
        );
        vmc::singleton('b2c_messenger_stage')->trigger('orders-end',$env_list,array(
            'email'=>$pam_data['email']?$pam_data['email']['login_account']:$order_sdf['consignee']['email'],
            'mobile'=>$pam_data['mobile']?$pam_data['mobile']['login_account']:$order_sdf['consignee']['mobile'],
            'member_id'=>$order_sdf['member_id']
        ));
        return true;
    }
}
