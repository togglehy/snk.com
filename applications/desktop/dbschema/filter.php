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


$db['filter'] = array(
  'columns' => array(
    'filter_id' => array(
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',

      'comment' => 'finder过滤器ID',
    ),
    'filter_name' => array(
      'type' => 'varchar(20)',
      'required' => false,
      'label' => '筛选器名',
      'class' => 'span-3',
      'in_list' => true,
      'default_in_list' => true,

      'comment' => '过滤条件名称',
    ),
    'user_id' => array(
      'type' => 'number',
      'required' => true,
      'label' => '用户id',
      'width' => 110,

      
      'in_list' => true,
      'default_in_list' => true,
      'comment' => '过滤器所属后台用户ID',
    ),
    'model' => array(
      'type' => 'varchar(100)',
      'required' => true,
      'label' => '表',
      'class' => 'span-3',
      'in_list' => true,
      'default_in_list' => true,

      'comment' => '过滤器对应的model(表名)',
    ),
    'filter_query' => array(
      'type' => 'text',
      
      'label' => '筛选条件',
      'class' => 'span-4',
      'in_list' => true,

      'comment' => '过滤器对应的过滤条件',
    ),
    'ctl' => array(
      'type' => 'varchar(100)',
      'required' => true,
      'default' => '',
      'label' => '控制器',
      'class' => 'span-3',

      'comment' => '过滤器对应的controller(控制器)',
    ),
    'app' => array(
      'type' => 'varchar(50)',
      'required' => true,
      'default' => '',
      'label' => '应用',
      'class' => 'span-3',

      'comment' => '过滤器对应的app(应用)',
    ),
    'act' => array(
      'type' => 'varchar(50)',
      'required' => true,
      'default' => '',
      'label' => '方法',
      'class' => 'span-3',

      'comment' => '过滤器对应的act(方法)',
    ),
    'create_time' => array(
      'type' => 'time',
      'default' => 0,
      'required' => true,
      'label' => '建立时间',
      'width' => 110,

      'in_list' => true,
      'default_in_list' => true,
      'comment' => '过滤器创建时间',
    ),
  ),
  'comment' => '后台搜索过滤器表',
);
