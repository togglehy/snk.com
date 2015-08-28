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


$db['goods_type'] = array(
  'columns' => array(
    'type_id' => array(
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => ('类型序号'),
      'width' => 110,
      'in_list' => false,
    ),
    'name' => array(
      'type' => 'varchar(100)',
      'required' => true,
      'default' => '',
      'label' => ('类型名称'),
      'is_title' => true,
      'width' => 150,
      'editable' => true,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'params' => array(
      'type' => 'serialize',
      'comment' => ('参数表结构(序列化) array(参数组名=>array(参数名1=>别名1|别名2,参数名2=>别名1|别名2))'),
    ),
    'setting' => array(
      'type' => 'serialize',
      'comment' => ('类型设置'),
      'width' => 110,
      'label' => ('类型设置'),
    ),
    'disabled' => array(
      'type' => 'bool',
      'default' => 'false',
    ),
  ),
  'index' => array(
    'ind_disabled' => array(
      'columns' => array(
        0 => 'disabled',
      ),
    ),
  ),
  'comment' => ('商品类型表'),
);
