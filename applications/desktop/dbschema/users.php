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


$db['users'] = array(
  'columns' => array(
    'user_id' => array(
      'type' => 'table:account@pam',
      'pkey' => true,
      'comment' => '后台用户ID',
    ),
    'avatar' => array(
      'type' => 'char(32)',
      'label' => '头像',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'status' => array(
      'type' => 'intbool',
      'default' => '0',
      'label' => '启用',
      'required' => true,
      'editable' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'name' => array(
      'type' => 'varchar(30)',
      'label' => '姓名',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'lastlogin' => array(
      'type' => 'time',
      'default' => 0,
      'required' => true,
      'label' => '最后登录时间',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'config' => array(
      'type' => 'serialize',
      'comment' => '配置信息',
    ),
    'favorite' => array(
      'type' => 'longtext',
      'comment' => '爱好',
    ),
    'super' => array(
      'type' => 'intbool',
      'default' => '0',
      'required' => true,
      'label' => '超级管理员',
      'in_list' => true,
      'default_in_list' => true,
    ),
    'lastip' => array(
      'type' => 'varchar(20)',
      'comment' => '上次登录ip',
    ),
    'logincount' => array(
      'type' => 'number',
      'default' => 0,
      'required' => true,
      'label' => '登录次数',
      'in_list' => true,
    ),
    'disabled' => array(
      'type' => 'bool',
      'default' => 'false',
      'required' => true,
    ),
    'op_no' => array(
      'type' => 'varchar(50)',
      'label' => '工号',
      'in_list' => true,
      'comment' => '工号',
    ),
    'memo' => array(
      'type' => 'text',
      'label' => '备注',
      'in_list' => true,
    ),
  ),
  'index' => array(
    'ind_disabled' => array(
      'columns' => array(
        0 => 'disabled',
      ),
    ),
  ),
  'engine' => 'innodb',
  'comment' => 'DESKTOP后台管理员表',
);
