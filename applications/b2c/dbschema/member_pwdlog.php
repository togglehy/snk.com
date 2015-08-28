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


 
$db['member_pwdlog']=array (
  'columns' => 
  array (
    'pwdlog_id' => 
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => 'ID',
      'width' => 110,
      
      'in_list' => true,
      'default_in_list' => true,
    ),
    'member_id' => 
    array (
      'type' => 'table:members',
      'required' => true,  
      
      'in_list' => true,
      'default_in_list' => true,
      'comment' => ('会员ID'),
    ),
    'secret' => 
    array (
      'type' => 'varchar(100)',
      'required' => true,
      'default' => '',
      'width' => 110,
      'editable' => true,
      'in_list' => true,
      'comment' => ('临时秘钥'),
    ),
    'expiretime' => 
    array (
      'type' => 'time',
      
      'filtertype' => 'time',
      
      'in_list' => true,
      'comment' => ('过期时间'),
    ),
    'has_used' => 
    array (
      'type' => 'tinybool',
      'default' => 'N',
      'required' => true,
      
      'comment' => ('是否使用过, 如果使用过将失效'),
    ),
  ),
  'engine' => 'innodb',
  'version' => '$Rev: 40654 $',
  'comment' => ('忘记密码时临时秘钥表'),
  
);
