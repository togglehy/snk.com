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



$db['goods_keywords']=array (
  'columns' =>
  array (
    'goods_id' =>
    array (
      'type' => 'table:goods',
      'required' => true,
      'default' => 0,
      'pkey' => true,
      
      'comment' => ('商品ID'),
    ),
    'keyword' =>
    array (
      'type' => 'varchar(40)',
      'default' => '',
      'required' => true,
      'pkey' => true,
      
      'is_title' => true,
      'comment' => ('搜索关键字'),
    ),
    'refer' =>
    array (
      'type' => 'varchar(255)',
      'default' => '',
      'required' => false,
      
      'comment' => ('来源'),
    ),
    'res_type' =>
    array (
      'type' => 'enum(\'goods\',\'article\')',
      'default' => 'goods',
      'required' => true,
      'pkey' => true,
      
      'comment' => ('搜索结果类型'),
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
  'version' => '$Rev: 40654 $',
  'comment' => ('商品搜索关键字表'),
);
