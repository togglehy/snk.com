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


 
$db['dlytype']=array (
  'columns' => 
  array (
    'dt_id' => 
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => ('配送ID'),
      'width' => 110,
      
      
      'in_list' => false,
    ),
    'dt_name' => 
    array (
      'type' => 'varchar(50)',
      'label' => ('配送方式'),
      'width' => 180,
      'editable' => true,
      'in_list' => true,
      'is_title' => true,
      'default_in_list' => true,
    ),
    'has_cod' => 
    array (
      'type' => 'bool',
      'default' => 'false',
      'required' => true,
      'label' => ('货到付款'),
      'width' => 110,
      
      'in_list' => true,
      'default_in_list' => true,
    ),
    'firstunit'=>
    array(
        'type' => 'number',
        
        'required' => true,
        'default' => 0,
        'comment' => ('首重'),
    ),
    'continueunit'=>array(
        'type' => 'number',
        
        'required' => true,
        'default' => 0,
        'comment' => ('续重'),
    ),
    'is_threshold'=>array(
        'type' => 
          array (
            '0' => ('不启用'),
            '1' => ('启用'),
          ),
        
        'default' => '0',
        'comment' => ('临界值'),
    ),
    'threshold'=>array(
      'type' => 'longtext',
      'label'=> ('临界值'),
      'required' => false,
      'default' => '',
      
      'comment' => ('临界值配置参数'),
    ),
    'protect' => 
    array (
      'type' => 'bool',
      'default' => 'false',
      'required' => true,
      'label' => ('物流保价'),
      'width' => 75,
      
      'in_list' => true,
      'default_in_list' => true,
    ),
    'protect_rate' => 
    array (
      'type' => 'float(6,3)',
      
      'comment' => ('报价费率'),
    ),
    'minprice' => 
    array (
      'type' => 'float(10,2)',
      'default' => '0.00',
      'required' => true,
      
      'comment' => ('保价费最低值'),
    ),
    'setting'=>array(
      'type' => 
      array (
        '0' => '指定配送地区和费用',
        '1' => '统一设置',
      ),
      
      'default' => '1',
      'comment' => ('地区费用类型'),
    ),

    'def_area_fee'=>array(
        'type'=>'bool',
        'default'=>'false',
        'label'=>('按地区设置配送费用时,是否启用默认配送费用'),
        'required' => false,
        
    ),

    'firstprice'=>array(
       'type' => 'float(10,2)',
      'default' => '0.00',
      'required' => false,
      
       'comment' => ('首重费用'),
    ),

    'continueprice'=>array(
      'type' => 'float(10,2)',
      'default' => '0.00',
      'required' => false,
      
      'comment' => ('续重费用'),
    ),


    'dt_discount'=>array(
      'type' => 'float(10,2)',
      'default' => '0.00',
      'required' => false,
      
      'comment' => ('折扣值'),
    ),
        
    'dt_expressions' => 
    array (
      'type' => 'longtext',
      
      'comment' => ('配送费用计算表达式'),
    ),
    'dt_useexp' => 
    array (
      'type' => 'bool',
      
      'default' => 'false',
      'comment' => ('是否使用公式'),
    ),

    'corp_id' => 
    array (
        'type' => 'number',
        
        'required' => false,
        'comment' => ('物流公司ID'),
    ),

    'dt_status' => 
    array (
      'type' => 
      array (
        '0' => ('关闭'),
        '1' => ('启用'),
      ),
      'label' => ('状态'),
      'width' => 75,
      
      'default' => '1',
      'in_list' => true,
      'default_in_list' => true,
      'comment' => ('是否开启'),
    ),

    'detail' => 
    array (
      'type' => 'longtext',
      
      'comment' => ('详细描述'),
    ),
    'area_fee_conf' => 
    array (
      'type' => 'longtext',
      'required' => false,
      'default' => '',
      
      'comment' => ('指定地区配置的一系列参数'),
    ),
    'ordernum' => 
    array (
      'type' => 'smallint(4)',
      'default' => 0,
      'label' => ('排序'),
      'width' => 110,
      'editable' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    
    'disabled' => 
    array (
      'type' => 'bool',
      'default' => 'false',
      
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
  ),
  'version' => '$Rev$',
  'comment' => ('商店配送方式表'),
);
