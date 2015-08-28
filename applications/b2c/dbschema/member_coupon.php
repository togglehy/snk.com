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




$db['member_coupon']=array (
  'columns' =>
  array (
    'memc_code' =>
    array (
      'type' => 'varchar(255)',
      'required' => true,
      'default' => '',
      'pkey' => true,
      'comment' => ('优惠券code'),
    ),
    'cpns_id' =>
    array (
      'type' => 'number',
      'required' => true,
      'default' => 0,
      'comment' => ('优惠券主表ID'),
    ),
    'member_id' =>
    array (
      'type' => 'table:members',
      'required' => true,
      'default' => 0,
      'pkey'=>true,
      'comment' => ('会员ID'),
    ),
    'memc_gen_orderid' =>
    array (
      'type' => 'varchar(15)',
      'comment' => ('相关订单ID'),
    ),
    'memc_enabled' =>
    array (
      'type' => 'bool',
      'default' => 'true',
      'required' => true,
      'comment' => ('是否启用'),
    ),
    'memc_used_times' =>
    array (
      'type' => 'mediumint',
      'default' => 0,
      'comment' => ('已使用次数'),
    ),
    'memc_gen_time' =>
    array (
      'type' => 'time',
      'comment' => ('优惠券产生时间'),
    ),
    'disabled' =>
    array (
      'type' => 'bool',
      'default' => 'false',
      'comment' => ('无效'),
      'label' => ('无效'),
      'in_list' => false,
    ),
    'memc_isvalid' =>
    array (
      'type' => 'bool',
      'default' => 'true',
      'required' => true,
      'comment' => ('会员优惠券是否当前可用'),
    ),
  ),
  'index' =>
  array (
    'ind_memc_gen_orderid' =>
    array (
      'columns' =>
      array (
        0 => 'memc_gen_orderid',
      ),
    ),
  ),
  'comment' => ('用户优惠券表'),
);
