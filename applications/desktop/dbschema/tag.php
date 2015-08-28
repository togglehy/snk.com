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


$db['tag'] = array(
  'columns' => array(
    'tag_id' => array(
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',

      'comment' => 'tag ID',
    ),
    'tag_name' => array(
      'type' => 'varchar(20)',
      'required' => true,
      'default' => '',
      'label' => '标签名',
      'width' => 200,
      'editable' => true,
      'in_list' => true,
      'default_in_list' => true,
      'is_title' => true,
    ),
    'tag_mode' => array(
      'type' => array(
        'normal' => '普通标签',
        'filter' => '自动标签',
      ),
      'default' => 'normal',
      'label' => '标签类型',
      'required' => true,

      'in_list' => true,
      'default_in_list' => true,
    ),
    'app_id' => array(
      'type' => 'varchar(32)',
      'label' => '应用',
      'required' => true,
      'width' => 100,
      'in_list' => true,
      'comment' => 'app(应用)ID',
    ),
    'tag_type' => array(
      'type' => 'varchar(20)',
      'required' => true,
      'default' => '',
      'label' => '标签对应的model(表)',

      'in_list' => true,
    ),
    'tag_abbr' => array(
      'type' => 'varchar(150)',
      'required' => true,
      'default' => '',
      'label' => '标签备注',

      'in_list' => true,
    ),
    'tag_bgcolor' => array(
      'type' => 'varchar(7)',
      'required' => true,
      'default' => '',
      'label' => '标签背景颜色',

      'in_list' => true,
    ),
    'tag_fgcolor' => array(
      'type' => 'varchar(7)',
      'required' => true,
      'default' => '',
      'label' => '标签字体颜色',

      'in_list' => true,
    ),
    'tag_filter' => array(
      'type' => 'varchar(255)',
      'required' => true,
      'default' => '',
      'label' => '标签条件',

      'in_list' => false,
      'default_in_list' => false,
    ),
    'rel_count' => array(
      'type' => 'number',
      'default' => 0,
      'required' => true,

      'comment' => '关联的个数',
    ),
  ),
  'index' => array(
    'ind_type' => array(
      'columns' => array(
        0 => 'tag_type',
      ),
    ),
    'ind_name' => array(
      'columns' => array(
        0 => 'tag_name',
      ),
    ),
  ),
  'comment' => 'finder tag(标签)表',
);
