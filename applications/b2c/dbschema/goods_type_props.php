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


 
$db['goods_type_props']=array (
  'columns' => 
    array (
      'props_id' => array(
        'type' => 'number',
        'required' => true,
        'extra' => 'auto_increment',
        'label' => ('属性序号'),
        'width' => 110,
        
        'pkey' => true,
        'in_list' => true,
        'default_in_list' => true,
    ),
    'type_id' => 
    array (
      'type' => 'table:goods_type',
      'required' => true,
      'label' => ('类型序号'),
      'width' => 110,
      
      'in_list' => true,
      'default_in_list' => true,
    ),
    'type'=>array(
        'type'=>'varchar(20)',
        'required' =>true,
        'label' => ('展示类型')
    ),
    'search'=>array(
        'type'=>'varchar(20)',
        'required' => true,
        'label' =>('搜索方式'),
        'default' => 'select'
    ),
    'show' => array(
        'type' => 'varchar(10)',
        'required' => true,
        'default' => '',
        'in_list' => true,
        'comment' => ('是否显示'),
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
    'alias' => 
    array (
      'type' => 'longtext',
      
      'comment' => ('别名'),
    ),
    'goods_p'=>array(
        'type' => 'smallint',
        'label' => ('商品位置')
    ),
    'ordernum'=>array(
        'type' => 'int(10)',
        'default' => 0,
        'comment' => ('排序'),
    ),
    'lastmodify' => 
    array (
      'label' => ('供应商最后更新时间'),
      'width' => 150,
      'type' => 'last_modify',
      'hidden' => 1,
      'in_list' => false,
    ),
  ),
    'index' => 
  array (
    'ind_type_id' => 
    array (
      'columns' => 
      array (
        0 => 'type_id',
      ),
    ),
  ),
  'version' => '$Rev: 40654 $',
  'comment' => ('商品属性表'),  
);
