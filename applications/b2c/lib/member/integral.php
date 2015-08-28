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


class b2c_member_integral
{


    public function __construct($app)
    {
        $this->app = $app;
        $this->mdl_member_integral = app::get('b2c')->model('member_integral');
    }

    // /**
    //  * 改变会员积分,并产生积分日志
    //  * @param $member_id 会员ID
    //  * @param $order_id 订单ID
    //  * @param $score 改变分值
    //  * @param $reason 改变理由
    //  *                  理由：
    //  *                  'order' => '下单',
    //  *                  'refund' => '退款',
    //  *                  'recharge' => '充值',
    //  *                  'exchange' => '兑换',
    //  *                  'deduction' => '抵扣',
    //  * @param $op_model 操作者类型、前台会员 or 后台管理员
    //  * @param $op_id  操作者账户ID
    //  * @param msg  错误消息
    //  */
    public function change($data, &$msg)
    {
        $mem_integral_amount = $this->amount($data['member_id']);
        if(!$mem_integral_amount){
            $mem_integral_amount = 0;
        }
        $data['balance'] = $balance = ($mem_integral_amount+$data['change']);
        $data['change_time'] = time();
        // if($balance<=0){
        //     $msg = '积分余额不足以扣减!';
        //     return false;
        // }

        return $this->mdl_member_integral->insert($data);
    }

    /**
     * 获得会员当前积分可用总额
     */
     public function amount($member_id){
          return $this->mdl_member_integral->amount($member_id);
     }


}
