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




class b2c_order_cancelfinish
{

    public function exec(&$order_sdf,  &$msg='')
    {

        $order_items = app::get('b2c')->model('order_items')->getList('bn,nums',array('order_id'=>$order_sdf['order_id']));

        //库存释放
        $unfreeze_data = array();
        foreach ($order_items as $key => $value) {
            $freeze_data[] = array(
                'sku'=>$value['bn'],
                'quantity'=>$value['nums']
            );
        }
        if(!vmc::singleton('b2c_goods_stock')->unfreeze($freeze_data,$msg)){
            logger::error('库存冻结释放异常!ORDER_ID:'.$order_sdf['order_id'].','.$msg);
        }

        //消息
        $pam_data = vmc::singleton('b2c_user_object')->get_pam_data('*',$order_sdf['member_id']);
        $env_list = array(
            'order_id'=>$order_sdf['order_id'],
            'timestr'=>date('Y-m-d H:i:s',$order_sdf['last_modify']),
        );
        vmc::singleton('b2c_messenger_stage')->trigger('orders-cancel',$env_list,array(
            'email'=>$pam_data['email']?$pam_data['email']['login_account']:$order_sdf['consignee']['email'],
            'mobile'=>$pam_data['mobile']?$pam_data['mobile']['login_account']:$order_sdf['consignee']['mobile'],
            'member_id'=>$order_sdf['member_id']
        ));
        return true;

    }


}

?>
