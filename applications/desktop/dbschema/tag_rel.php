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


$db['tag_rel'] = array(
  'columns' => array(
    'tag_id' => array(
      'type' => 'table:tag',
      'sdfpath' => 'tag/tag_id',
      'required' => true,
      'default' => 0,
      'pkey' => true,

      'comment' => 'tag ID',
    ),
    'rel_id' => array(
      'type' => 'varchar(32)',
      'required' => true,
      'default' => 0,
      'pkey' => true,

      'comment' => '对象ID',
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
      'label' => '标签对象',

      'in_list' => true,
      'comment' => '标签对应的model(表)',
    ),
    'last_modify' => array(
      'type' => 'last_modify',
      'label' => ('更新时间'),
      'width' => 110,
      'in_list' => true,
      'orderby' => true,
    ),
  ),
  'comment' => 'tag和对象关联表',
);
