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


 
$db['hasrole']=array (
  'columns' => 
  array (
    'user_id' => 
    array (
      'type' => 'table:users',
      'required' => true,
      'pkey' => true,
      'comment' => '后台用户ID',
    ),
    'role_id' => 
    array (
      'type' => 'table:roles',
      'required' => true,
      'pkey' => true,
      'comment' => '角色ID',
    ),
  ),
  'version' => '$Rev: 40654 $',
  'comment' => '后台权限, 角色和用户关联表',
);

