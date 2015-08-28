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



$db['member_lv']=array (
  'columns' =>
  array (
    'member_lv_id' =>
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => 'ID',
      'width' => 110,
      
      'in_list' => false,
      'default_in_list' => false,
    ),
    'name' =>
    array (
      'type' => 'varchar(100)',
      'is_title' => true,
      'required' => true,
      'default' => '',
      'label' => ('等级名称'),
      'width' => 110,
      'editable' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'lv_logo' =>
    array (
      'type' => 'varchar(255)',
      'comment' => ('会员等级LOGO'),
      
      'label' => ('会员等级LOGO'),
      'in_list' => false,
      'default_in_list' => false,
    ),
    'dis_count' =>
    array (
      'type' => 'decimal(5,2)',
      'default' => '1',
      'required' => true,
      'label' => ('会员折扣率'),
      'width' => 110,
      'match' => '[0-9\\.]+',
      'editable' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'pre_id' =>
    array (
      'type' => 'mediumint',
      
      'comment' => ('前一级别ID'),
    ),
    'default_lv' =>
    array (
      'type' => 'intbool',
      'default' => '0',
      'required' => true,
      'label' => ('是否默认'),
      'width' => 110,
      
      'in_list' => true,
      'default_in_list' => true,
    ),
    'deposit_freeze_time' =>
    array (
      'type' => 'int',
      'default' => 0,
      
      'comment' => ('保证金冻结时间'),
    ),
    'deposit' =>
    array (
      'type' => 'int',
      'default' => 0,
      
      'comment' => ('所需保证金金额'),
    ),
    'more_point' =>
    array (
      'type' => 'int',
      'default' => 1,
      
      'comment' => ('会员等级积分倍率'),
    ),
    'lv_type' =>
    array (
      'type' =>
      array (
        'retail' => ('零售'),
        'wholesale' => ('批发'),
        'dealer' => ('代理'),
      ),
      'default' => 'retail',
      'required' => true,
      'label' => ('等级类型'),
      'width' => 110,
      
      'in_list' => false,
      'default_in_list' => false,
    ),
    'point' =>
    array (
      'type' => 'number',
      'default' => 0,
      'required' => true,
      'comment' => ('所需积分【暂未启用】'),
    ),

    'disabled' =>
    array (
      'type' => 'bool',
      'default' => 'false',
      
    ),
    'show_other_price' =>
    array (
      'type' => 'bool',
      'default' => 'true',
      'required' => true,
      
      'comment' => ('查阅其他会员等级价格'),
    ),
    'lv_remark' =>
    array (
      'type' => 'text',
      
      'comment' => ('会员等级备注'),
    ),
    'experience' =>
    array (
      'label' => ('所需经验值'),
      'type' => 'int(10)',
      'default' => 0,
      'required' => true,
      
      'in_list' => true,
      'default_in_list' => true,
      'comment' => ('经验值'),
    ),
    'expiretime' =>
    array (
      'type' => 'int(10)',
      'required' => true,
      'default' => 0,
      
      'comment' => ('积分过期时间'),
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
    'ind_name' =>
    array (
      'columns' =>
      array (
        0 => 'name',
      ),
      'prefix' => 'UNIQUE',
    ),

  ),
  'engine' => 'innodb',
  'version' => '$Rev: 44523 $',
  'comment' => ('会员等级表'),
);
