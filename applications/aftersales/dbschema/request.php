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

$db['request'] = array(
  'columns' => array(
      'request_id' => array(
        'type' => 'bigint(20)',
        'required' => true,
        'pkey' => true,
        'in_list' => true,
        'default_in_list' => true,
        'searchtype' => 'has',
        'filtertype' => 'yes',
        'label'=>'售后编号'
      ),
    'order_id' => array(
      'type' => 'table:orders@b2c',
      'default' => '0',
      'required' => true,
      'default' => 0,
      'in_list' => true,
      'default_in_list' => true,
      'searchtype' => 'has',
      'filtertype' => 'yes',
      'label' => '订单号',
    ),
    'member_id' => array(
      'type' => 'table:members@b2c',
      'default' => '0',
      'required' => true,
      'in_list' => true,
      'default_in_list' => true,
      'label' => '申请人',
    ),
    'delivery_id'=>array(
      'type'=>'table:delivery@b2c',
      'default'=>'0',
      'label'=>'退货单'
    ),
    'bill_id'=>array(
      'type'=>'table:bills@ectools',
      'default'=>'0',
      'label'=>'退款单'
    ),
    'member_addr_id'=>array(
        'type' =>'table:member_addrs@b2c',
        'comment' => '会员收货地址'
    ),
    'subject' => array(
      'type' => 'varchar(200)',
      'required' => true,
      'in_list' => true,
      'default_in_list' => true,
      'searchtype' => 'has',
      'filtertype' => 'yes',
      'label' => '售后服务请求标题',
    ),
    'description' => array(
      'type' => 'longtext',
      'required' => true,
      'searchtype' => 'has',
      'filtertype' => 'yes',
      'label' => '问题描述',
    ),
    'remarks' => array(
        'type' => 'longtext',
        'label' => '管理员备注',
    ),
    'product' => array(
        'type' => 'serialize',
        'label' => '需售后服务货品',
    ),
    'req_type' => array(
        'type' => array(
            '1' => '退货',
            '2' => '更换',
            '3' => '维修',
            '4' => '投诉建议',
        ),
        'default' => '1',
        'required' => true,
        'comment' => '售后服务类型',
        'in_list' => true,
        'default_in_list' => true,
        'orderby' => true,
        'label' => '售后服务类型',
    ),
    'delivery_type'=>array(
        'type' => array(
            '0' =>'-',
            '1' => '快递至商家',
            '2' => '商家安排上门取件',
        ),
        'default' => '0',
        'comment' => '商品返回方式',
        'label' => '商品返回方式',
    ),
    'status' => array(
      'type' => array(
        '1' => '申请中',
        '2' => '接受申请',
        '3' => '申请被拒绝',
        '4' => '处理中',
        '5' => '处理完成',
      ),
      'default' => '1',
      'required' => true,
      'comment' => '售后申请状态',
      'in_list' => true,
      'default_in_list' => true,
      'orderby' => true,
      'label' => '售后申请状态',
    ),
    'createtime' => array(
        'type' => 'time',
        'label' => '创建时间',
        'in_list' => true,
        'default_in_list' => true,
    ),
    'last_modify' => array(
      'type' => 'last_modify',
      'label' => '更新时间',
      'in_list' => true,
      'default_in_list' => true,
      'orderby' => true,
    ),
  ),
  'engine' => 'innodb',
  'comment' => '售后申请',
);
