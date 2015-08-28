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



$db['coupons_issue']=array (
  'columns' =>
  array (
    'id' =>
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => ('id'),
      'comment' => ('已发行优惠券ID'),
    ),
    'cpns_prefix' =>
    array (
      'type' => 'varchar(50)',
      'required' => true,
      'default' => '',
      'label' => ('优惠券批次号'),
      'comment' => ('生成优惠券前缀'),
      'in_list' => true,
      'default_in_list' => true,
    ),
    'cpns_no' =>
    array (
      'type' => 'varchar(255)',
      'label' => ('优惠券号码'),
      'searchtype' => 'has',
      'comment' => ('优惠券号码'),
      'in_list' => true,
      'default_in_list' => true,
    ),
    'cpns_id' =>
    array (
      'type' => 'table:coupons',
      'comment' => ('优惠券批次ID'),
    ),
    'remark' =>
    array (
      'type' => 'varchar(255)',
      'comment' => ('发行备注'),
      'label' => ('发行备注'),
      'in_list' => true,
      'default_in_list' => true,
    ),
    'issue_op' =>
    array (
      'type' => 'varchar(255)',
      'comment' => ('发行者'),
      'label' => ('发行者'),
      'in_list' => true,
      'default_in_list' => true,
    ),
    'issue_date' =>
    array(
        'type'=>'last_modify',
        'label' => ('发行时间'),
        'in_list' => true,
        'default_in_list' => true,
        'orderby'=>true
    )
  ),

  'index' =>
  array (
    'ind_cpns_no' =>
    array (
      'columns' =>
      array (
        0 => 'cpns_no',
      ),
    ),
  ),
  'comment' => ('优惠券发行记录'),
);
