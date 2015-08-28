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



$db['recycle']=array (
  'columns' =>
  array (
    'item_id' =>
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      
      'comment' => '回收项ID',
    ),
    'item_title' =>
    array (
      'type' => 'varchar(200)',
      'label'=>'名称',
      'required' => false,
      'is_title'=>true,
      'in_list'=>true,
      'width'=>200,
      'filtertype' => 'yes',
      
      'default_in_list'=>true,
    ),
    'item_type'=>array(
      'label'=>'类型',
      'type' => 'varchar(80)',
      'required' => true,
      'in_list'=>true,
      'width'=>100,
      'filtertype' => 'yes',
      

      'default_in_list'=>true,
    ),
    'app_key'=>array(
      'label'=>'应用',
      'type' => 'varchar(80)',
      'required' => true,
      'in_list'=>true,
      'width'=>100,
      'default_in_list'=>true,
      'comment' => 'app(应用)ID',
    ),
    'drop_time'=>array(
      'type' => 'time',
      'label'=>'删除时间',
      'required' => true,
      'in_list'=>true,
      'width'=>150,
      'filtertype' => 'yes',
      
      'default_in_list'=>true,
    ),
    'item_sdf'=>array(
      'type' => 'serialize',
      'required' => true,
      'comment' => '数据',
    ),
    'permission'=>array(
      'type'=>'varchar(80)',
      'label'=>'相关权限',
      'in_list'=>false,
      'default_in_list'=>false,
    ),
  ),
  'engine' => 'innodb',
  'version' => '$Rev: 40912 $',
  'comment' => '回收站表',
);

//需要id从大到小的执行
