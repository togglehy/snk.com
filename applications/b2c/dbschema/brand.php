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



$db['brand']=array (
  'columns' =>
  array (
    'brand_id' =>
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => ('品牌id'),
      'comment' => ('品牌id'),
      
      'in_list' => false,
      'default_in_list' => false,
    ),
    'brand_name' =>
    array (
      'type' => 'varchar(50)',
      'label' => ('品牌名称'),
      'is_title' => true,
      'required' => true,
      'comment' => ('品牌名称'),
      'searchtype' => 'has',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'brand_initial' =>
    array (
      'type' => 'varchar(1)',
      'label' => ('拼音首字母'),
      'comment' => ('拼音首字母'),
      'searchtype' => 'has',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'brand_url' =>
    array (
      'type' => 'varchar(255)',
      'label' => ('品牌网址'),
      'comment' => ('品牌网址'),
      'in_list' => true,
      'default_in_list' => true,
    ),
    'brand_desc' =>
    array (
      'type' => 'longtext',
      'comment' => ('品牌介绍'),
      'label' => ('品牌介绍'),
    ),
    'brand_logo' =>
    array (
      'type' => 'varchar(255)',
      'comment' => ('品牌图片标识'),
      'label' => ('品牌图片标识'),
    ),
    'brand_setting' =>
    array(
        'type' => 'serialize',
        'label' => ('品牌设置'),
        'deny_export' => true,
    ),
    'disabled' =>
    array (
      'type' => 'bool',
      'default' => 'false',
      'comment' => ('失效'),
    ),
    'ordernum' =>
    array (
      'type' => 'number',
      'default' => 30,
      'label' => ('排名'),
      'comment' => ('排名'),
      'in_list' => true,
      'default_in_list' => true,
    ),
    'last_modify' =>
    array (
      'type' => 'last_modify',
      'label' => ('更新时间'),
      'width' => 110,
      
      'in_list' => true,
      'orderby' => true,
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
  'comment' => ('品牌表'),
);
