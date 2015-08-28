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


 
$db['cart_objects']=array (
  'columns' => 
  array (
    'obj_ident' => 
    array (
      'type' => 'varchar(255)',
      'pkey' => true,
      'required' => true,
      'label' => ('对象ident'),
      
      'in_list' => true,
      'default_in_list' => true,
      'is_title' => true,
    ),
    'member_ident' => 
    array (
      'type' => 'varchar(50)',
      'pkey' => true,
      'required' => true,
      'label' => ('会员ident'),
      'comment' => ('会员ident,会员信息和serssion生成的唯一值'),
      
      'in_list' => true,
      'default_in_list' => true,
    ),
    'member_id' => 
    array (
      'type' => 'int(8) ',
      'pkey' => true,
      'required' => true,
      'label' => ('会员 id'),
      
      'default' => -1,
    ),
    'obj_type' => 
    array (
      'type' => 'varchar(20)',
      'required' => true,
      'label' => ('购物车对象类型'),
      
      'in_list' => true,
    ),
    'params' => 
    array (
      'type' => 'serialize',
      'required' => true,
      'label' => ('购物车对象参数'),
      
      'in_list' => true,
    ),
    'quantity' => 
    array (
      'type' => 'float unsigned',
      'required' => true,
      'label' => ('数量'),
      
      'in_list' => true,
    ),
    'time' =>
    array(
      'type' => 'time',
      'label' => ('时间'),
    ),
  ),
  'index' => 
  array (
    'ind_member_id' => 
    array (
      'columns' => 
      array (
        0 => 'member_id',
      ),
    ),
  ),
  'engine' => 'innodb',
  'version' => '$Rev: 40912 $',
  'unbackup' => true,
  'ignore_cache' => true,
  'comment' => ('购物车'),  
);
