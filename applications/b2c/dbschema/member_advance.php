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


 
$db['member_advance']=array (
  'columns' => 
  array (
    'log_id' => 
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => ('日志id'),
      'width' => 110,
      'comment' => ('日志id'),
      
      
      'in_list' => true,
      'default_in_list' => true,
    ),
    'member_id' => 
    array (
      'type' => 'table:members@pam',
      'required' => true,
      'default' => 0,
      'label' => ('用户名'),
      'width' => 110,
      'comment' => ('用户id'),
      
      'in_list' => true,
      'default_in_list' => true,
    ),
    'money' => 
    array (
      'type' => 'money',
      'required' => true,
      'default' => 0,
      'label' => ('出入金额'),
      'width' => 110,
      'comment' => ('出入金额'),
      
      
      'in_list' => true,
    ),
    'message' => 
    array (
      'type' => 'varchar(255)',
      'label' => ('管理备注'),
      'is_title' => true,
      'width' => 110,
      'comment' => ('管理备注'),
      'editable' => true,
      'in_list' => true,
    ),
    'mtime' => 
    array (
      'type' => 'time',
      'required' => true,
      'default' => 0,
      'label' => ('交易时间'),
      'width' => 75,
      'comment' => ('交易时间'),
      
      'in_list' => true,
    ),
    'payment_id' => 
    array (
      'type' => 'varchar(20)',
      'label' => ('支付单号'),
      'width' => 110,
      'comment' => ('支付单号'),
      'searchtype' => 'has',
      
      'in_list' => true,
    ),
    'order_id' => 
    array (
      'type' => 'table:orders',
      'label' => ('订单号'),
      'width' => 110,
      'comment' => ('订单号'),
      'searchtype' => 'has',
      
      'in_list' => true,
    ),
    'paymethod' => 
    array (
      'type' => 'varchar(100)',
      'label' => ('支付方式'),
      'width' => 110,
      'comment' => ('支付方式'),
      
      'in_list' => true,
    ),
    'memo' => 
    array (
      'type' => 'varchar(100)',
      'label' => ('业务摘要'),
      'width' => 110,
      'comment' => ('业务摘要'),
      
      'in_list' => true,
      'default_in_list' => true,
    ),
    'import_money' => 
    array (
      'type' => 'money',
      'default' => '0',
      'required' => true,
      'label' => ('存入金额'),
      'width' => 110,
      'comment' => ('存入金额'),
      
      'in_list' => true,
    ),
    'explode_money' => 
    array (
      'type' => 'money',
      'default' => '0',
      'required' => true,
      'label' => ('支出金额'),
      'width' => 110,
      'comment' => ('支出金额'),
      
      'in_list' => true,
    ),
    'member_advance' => 
    array (
      'type' => 'money',
      'default' => '0',
      'required' => true,
      'label' => ('当前余额'),
      'width' => 110,
      'comment' => ('当前余额'),
      
      'in_list' => true,
    ),
    'shop_advance' => 
    array (
      'type' => 'money',
      'default' => '0',
      'required' => true,
      'label' => ('商店余额'),
      'width' => 110,
      'comment' => ('商店余额'),
      
      
      'in_list' => true,
    ),
    'disabled' => 
    array (
      'type' => 'bool',
      'default' => 'false',
      'required' => true,
      'comment' => ('失效'),
      
      'label' => ('失效'),
      'in_list' => true,
    ),
  ),
  'comment' => ('预存款历史记录'),
  'index' => 
  array (
    'ind_mtime' => 
    array (
      'columns' => 
      array (
        0 => 'mtime',
      ),
    ),
    'ind_disabled' => 
    array (
      'columns' => 
      array (
        0 => 'disabled',
      ),
    ),
  ),
  'engine' => 'innodb',
  'version' => '$Rev: 40912 $',
  'comment' => ('会员预存款日志表'),
);
