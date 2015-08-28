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



$db['goods_type_props_value']=array (
  'columns' =>
  array (
    'props_value_id' =>
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => ('属性值序号'),
      'width' => 110,
      
      'in_list' => true,
  ),
    'props_id' =>
    array (
      'type' => 'table:goods_type_props',
      'required' => true,
      'label' => ('属性序号'),
      'width' => 110,
      
      'in_list' => true,
      'default_in_list' => true,
  ),
    'name' =>
    array (
      'type' => 'varchar(100)',
      'required' => true,
      'default' => '',
      'label' => ('类型名称'),
      'is_title' => true,
      'width' => 150,
      'editable' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'order_by' => array(
        'type' => 'int',
        'required' => true,
        'default' => 0,
        'comment' => ('排序'),
    ),
    'alias' =>
    array (
      'type' => 'varchar(255)',
       'required' => true,
      'default' => '',
     
      'comment' => '别名',
    ),
    'lastmodify' =>
    array (
      'label' => ('最后更新时间'),
      'width' => 150,
      'type' => 'last_modify',
      'hidden' => 1,
      'in_list' => false,
    ),
  ),
  'comment' => ('商品属性值表'),
  'index' =>
  array (
    'ind_props_id' =>
    array (
      'columns' =>
      array (
        0 => 'props_id',
      ),
    ),
  ),
  'version' => '$Rev: 40654 $',
  'comment' => ('商品类型扩展属性值表'),
);
