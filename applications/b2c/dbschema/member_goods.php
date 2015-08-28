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



$db['member_goods']=array (
  'columns' =>
  array (
    'gnotify_id' => array (
       'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => 'ID',
      'width' => 110,
      
      'default_in_list' => true,
      'id_title' => true,
    ),
    'goods_id' => array (
      'type' => 'table:goods',
      'required' => true,
      'label' => ('缺货商品名称'),
      'in_list' => true,
      'comment' => ('商品ID'),
    ),
    'member_id' => array(
        'type'=>'table:members',
        'in_list' => true,
         'label' => ('会员用户名'),
       'default_in_list' => true,
    ),
    'product_id' => array (
      'type' => 'table:products',
      'default' => null,
      'comment' => ('货品ID'),
    ),
    'goods_name' =>
    array (
        'type' => 'varchar(200)',
        'default' => '',
        'label' => ('商品名称'),
        'width' => 310,
    ),
    'goods_price' =>
    array (
        'type' => 'money',
        'default' => '0',
        'label' => ('销售价'),
        'width' => 75,
        
        'filtertype' => 'number',
        'orderby'=>true,
    ),
    'image_default_id' =>
    array (
        'type' => 'varchar(32)',
        'label' => ('默认图片'),
        'width' => 75,
        
        
    ),
    'email' => array(
        'type'=>'varchar(100)',
        'in_list' => true,
        'label' => 'Email',
        'default_in_list' => true,
        'comment' => ('邮箱'),
    ),
    'cellphone' => array(
        'type' => 'varchar(20)',
        'in_list' => true,
        'label' => ('手机号'),
        'default_in_list' => true,
    ),
    'status' => array (
      'type' => "enum('ready', 'send', 'progress')",
      'required' => true,
      'comment' => ('状态'),
    ),
    'send_time' =>
     array (
      'type' => 'time',
      'label' => ('发送时间'),
      'width' => 110,
      
      'filtertype' => 'time',
      
      'in_list' => true,
    ),
    'create_time' =>
    array (
      'type' => 'time',
      'label' => ('申请时间'),
      'width' => 110,
      
      'filtertype' => 'time',
      
      'in_list' => true,
    ),
    'disabled' => array (
      'type' => 'bool',
      'default'=>'false',
    ),
    'remark' => array (
      'type' => 'longtext',
      'default'=>'false',
      'comment' => ('备注'),
    ),
    'type' =>array(
        'type' =>  "enum('fav', 'sto')",
        'comment' => ('类型, 收藏还是缺货'),
        ),
     'object_type' =>array(
        'type' => 'varchar(100)',
        'default' => 'goods',
        'comment' => ('收藏的类型，goods'),
        ),
  ),
  'comment' => ('收藏/缺货登记'),
   'engine' => 'innodb',
   'version' => '$Rev$',
);
