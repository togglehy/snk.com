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


$db['goods_rate'] = array(
  'columns' => array(
    'goods_1' => array(
      'type' => 'number',
      'required' => true,
      'default' => 0,
      'pkey' => true,

      'comment' => ('关联商品ID'),
    ),
    'goods_2' => array(
      'type' => 'number',
      'required' => true,
      'default' => 0,
      'pkey' => true,

      'comment' => ('被关联商品ID'),
    ),
    'manual' => array(
      'type' => array(
        'left' => ('单向'),
        'both' => ('关联'),
      ),

      'comment' => ('关联方式'),
    ),
    'rate' => array(
      'type' => 'number',
      'default' => 1,
      'required' => true,

      'comment' => ('关联比例'),
    ),
  ),
  'comment' => ('相关商品表'),
);
