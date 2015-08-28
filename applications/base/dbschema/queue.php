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


 
$db['queue']=array (
  'columns' => 
  array (
    'queue_id' => 
    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => 'ID',
      
      'comment' => '序号',
    ),
    'queue_title' => 
    array (
      'type' => 'varchar(50)',
      'label'=>'队列名称',
      'required' => true,
      'is_title'=>true,
      'in_list'=>true,
      'width'=>200,
      'default_in_list'=>true,
    ),
    'status'=>array(
      'label'=>'状态',
        'type'=>array(
            'running'=>'运行中',
            'hibernate'=>'休眠中',
            'paused'=>'已暂停',
            'failure'=>'执行失败',
        ),
        'required' => true,
        'default' => 'hibernate',
      'in_list'=>true,
      'width'=>100,
      'default_in_list'=>true,
    ),
    'worker'=>array(
      'type' => 'varchar(200)',
      'required' => true,
      //'in_list'=>true,
      'width'=>200,
      //'default_in_list'=>true,
      'comment' => ('执行目标(classname.methodname)'),
    ),
    'start_time'=>array(
      'type' => 'time',
      'label'=>'任务产生时间',
      'required' => true,
      'in_list'=>true,
      'width'=>150,
//      'default_in_list'=>true,
    ),
    'worker_active'=>array(
      'type' => 'time',
      'label'=>'上次运行时间',
      'in_list'=>true,
      'width'=>150,
      'default_in_list'=>true,
    ),
    'total'=>array(
      'type' => 'number',
      'label'=>'队列长度',
      'in_list'=>true,
      'width'=>120,
      'default_in_list'=>true,
    ),
    'remaining'=>array(
      'type' => 'number',
      'label'=>'剩余任务',
      'in_list'=>true,
      'width'=>120,
      'default_in_list'=>true,
    ),
    'cursor_id'=>array(
      'type' => 'varchar(255)',
      'label'=>'执行游标',
      'default'=>0,
      'required' => true,
    ),
    'runkey'=>array(
      'type' => 'char(32)',
      'comment' => '任务唯一标识',
    ),
    'task_name'=>array(
      'type' => 'varchar(50)',
      'label'=>'永久任务名称',
    ),
    'params'=>array(
      'type' => 'serialize',
      'label'=>'参数',
      'required' => true,
      'comment'=>'参数，通常就是filter',
    ),
    'errmsg'=>array(
      'type' => 'varchar(255)',
      'default_in_list'=>true,
      'in_list'=>true,
      'width'=>200,
      'label'=>'错误信息',
    ),
  ),
  'index' => 
  array (
    'ind_worker' => 
    array (
      'columns' => 
      array (
        0 => 'worker',
      ),
    ),
    'ind_worker_active' => 
    array (
      'columns' => 
      array (
        0 => 'worker_active',
      ),
    ),
    'ind_status' => 
    array (
      'columns' => 
      array (
        0 => 'status',
      ),
    ),
  ),
  'engine' => 'innodb',
  'version' => '$Rev: 40912 $',
  'ignore_cache' => true,
  'comment' => '队列表',

);


//需要id从大到小的执行
