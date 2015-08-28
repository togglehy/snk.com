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



$db['menus']=array (
  'columns' =>
  array (
    'menu_id'=>array(
      'type' => 'number',
      'pkey' => true,
      'extra' => 'auto_increment',
      'comment' => '后台菜单ID',
    ),
    'menu_type' =>
    array (
      'type' => 'varchar(80)',
      'required' => true,
      'width' => 100,
      'in_list' => true,
      'default_in_list' => true,
      'comment' => '菜单类型',
    ),
    'app_id' =>
    array (
      'type' => 'table:apps@base',
      'required' => true,
      'width' => 100,
      'in_list' => true,
      'default_in_list' => true,
      'comment' => '所属app(应用)ID',
    ),
    'workground'=>array(
        'type'=>'varchar(200)',
        'comment' => '顶级菜单',
    ),
     'menu_group'=>array(
        'type'=>'varchar(200)',
        'comment' => '菜单组',
     ),
    'menu_title'=>array(
        'type'=>'varchar(100)',
        'is_title'=>true,
        'comment' => '菜单标题',
    ),
    'menu_path'=>array(
        'type'=>'varchar(255)',
        'comment' => '菜单对应执行的url路径',
    ),
    'disabled'=>array(
        'type'=>'bool',
        'default'=>'false'
    ),
     'display'=>array(
        'type'=>"enum('true', 'false')",
        'default'=>'false',
        'comment' => '是否显示',
    ),
    'permission'=>array(
        'type'=>'varchar(80)',
        'comment' => '权限,有效显示范围',
    ),
    'addon'=>array(
        'type'=>'text',
        'comment' => '额外信息',
    ),
    'icon'=>array(
        'type'=>'varchar(100)',
        'comment' => 'WORKGROUND菜单ICON classname',
    ),
    'target'=>array(
        'type'=>'varchar(10)',
        'default'=>'',
        'comment' => '跳转',
    ),
    'menu_order'=>array(
        'type' => 'number',
        'default'=>'0',
        'comment' => '排序',
    ),
    'parent'=>array(
        'type' => 'varchar(255)',
        'default'=>'0',
        'comment' => '父节点',
    ),
  ),
  'index' =>
  array (
    'ind_menu_type' =>
    array (
      'columns' =>
      array (
        0 => 'menu_type',
      ),
    ),
    'ind_menu_path' =>
    array (
      'columns' =>
      array (
        0 => 'menu_path',
      ),
    ),
    'ind_menu_order' =>
    array (
      'columns' =>
      array (
        0 => 'menu_order',
      ),
    ),
  ),
  'version' => '$Rev: 44008 $',
  'unbackup' => true,
  'comment' => '后台菜单表',
);
