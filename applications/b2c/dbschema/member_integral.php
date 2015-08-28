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


$db['member_integral'] = array(
  'columns' => array(
    'id' => array(
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'comment' => '积分记录ID',
    ),
    'member_id' => array(
      'type' => 'table:members',
      'required' => true,
      'default' => 0,
      'comment' => '会员ID',
    ),
    'order_id' => array(
      'type' => 'table:orders',
      'default' => 0,
      'comment' => '订单ID',
    ),
    'balance' => array(
      'type' => 'int(10)',
      'required' => true,
      'default' => 0,
      'comment' => '余额',
    ),
    'change' => array(
      'type' => 'int(10)',
      'required' => true,
      'default' => '0',
      'comment' => '变动',
    ),
    'change_reason' => array(
      'type' => array(
          'order' => '下单',
          'refund' => '退款',
          'recharge' => '充值',
          'exchange' => '兑换',
          'deduction' => '抵扣',
          'sign' => '签到',
          'comment' => '评价'
          ),
      'required' => true,
      'default' => 'order',
      'comment' => '积分变动原因',
    ),
    'change_time' => array(
      'type' => 'time',
      'required' => true,
      'default' => 0,
      'comment' => '变动时间',
    ),
    'change_expire' => array(
      'type' => 'time',
      'required' => true,
      'default' => 0,
      'comment' => '积分变动有效期',
    ),
    'remark' => array(
      'type' => 'text',
      'default' => '',
      'comment' => '备注',
    ),
    'op_model' => array(
      'type' => array(
          'member'=>'会员',
          'shopadmin'=>'管理员'
      ),
      'comment' => '操作者类型',
    ),
    'op_id' => array(
      'type' => 'varchar(50)',
      'comment' => '操作者ID',
    ),
  ),
  'engine' => 'innodb',
  'comment' => '会员积分日志表',
);
