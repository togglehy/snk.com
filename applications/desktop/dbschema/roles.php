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



$db['roles']=array (
  'columns' =>
  array (
    'role_id' =>
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'label' => '角色ID',
      
      'extra' => 'auto_increment',
      'comment' => '管理员角色ID',
    ),
    'role_name' =>
    array (
      'type' => 'varchar(100)',
      'required' => true,
      'label' => '角色名',
      'width' => 310,
      'in_list' => true,
      'is_title' => true,
      'default_in_list' => true,
    ),
    'permissions' =>
    array (
      'label' => '角色拥有权限',
      'type' => 'text',
    ),
  ),
  'version' => '$Rev: 40654 $',
  'comment' => '管理员角色表',
);
