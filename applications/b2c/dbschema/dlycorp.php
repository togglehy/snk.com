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



$db['dlycorp']=array (
  'columns' =>
  array (
    'corp_id' =>
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => ('物流公司ID'),
      'width' => 110,
      
      
    ),
	'corp_code' =>
    array (
      'type' => 'varchar(200)',
      'label' => ('物流公司代码'),
      'width' => 180,
      
      'default_in_list' => false,
      'in_list' => true,
    ),
    'name' =>
    array (
      'type' => 'varchar(200)',
      'label' => ('物流公司名称'),
      'width' => 180,
      'is_title'=>true,
      'editable' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'disabled' =>
    array (
      'type' => 'bool',
      'default' => 'false',
      
      'comment' => ('失效'),

    ),
    'ordernum' =>
    array (
      'type' => 'smallint(4) unsigned',
      'label' => ('排序'),
      
    ),
    'website' =>
    array (
      'type' => 'varchar(200)',
      'label' => ('物流公司官网'),
      'width' => 180,
      'editable' => true,
      'default_in_list' => true,
      'in_list' => true,
    ),
    'request_url' =>
    array (
      'type' => 'varchar(200)',
      'label' => ('包裹查询网址'),
      'width' => 180,
      'hidden'=>false,
      'editable' => true,
      'in_list' => true,
    ),
  ),
  'index' =>
  array (
    'ind_disabled' =>
    array (
      'columns' =>
      array (
        0 => 'disabled',
      ),
    ),
    'ind_ordernum' =>
    array (
      'columns' =>
      array (
        0 => 'ordernum',
      ),
    ),
  ),
  'version' => '$Rev$',
  'comment' => ('物流公司表'),
);
