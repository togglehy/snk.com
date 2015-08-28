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

$db['goods_mark'] = array(
  'columns' => array(
    'mark_id' => array(
        'type' => 'number',
        'pkey' => true,
        'extra' => 'auto_increment',
        'label' => 'markID',
    ),
    'goods_id' => array(
        'type' => 'table:goods',
        'label' => ('相关商品'),
        
    ),
    'mark_type' => array(
        'pkey' => true,
        'type' => array(
            'normal' => '综合',
        ),
        'default' => 'normal',
        'comment' => '评分类型',
    ),
    'mark_star' => array(
        'type' => 'decimal(2,1)',
        'label' => ('分数'),
    ),
    'comment_id' => array(
        'type' => 'table:member_comment',
        'label' => ('相关评价'),
    )
  ),

   'engine' => 'innodb',
   'comment' => ('商品评分表'),
);
