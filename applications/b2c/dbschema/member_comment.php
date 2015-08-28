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

$db['member_comment'] = array(
  'columns' => array(
    'comment_id' => array(
        'type' => 'bigint(17)',
        'required' => true,
        'pkey' => true,
        'label' => 'ID',
        'comment' => ('ID'),
    ),
    'comment_type' => array(
        'type' => array(
            'comment'=>'评价',
            'consult'=>'咨询',
        ),
        'label' => ('类型'),
        'default' => 'comment',
        'required' => true,
    ),
    'for_comment_id' => array(
        'type' => 'bigint(17)',
        'label' => ('回复给'),
        'default' => 0,
    ),
    'goods_id' => array(
        'type' => 'table:goods',
        'label' => ('相关商品'),
        'in_list' => true,
        'default_in_list' => true,
    ),
    'product_id' => array(
        'type' => 'table:products',
        'label' => ('相关规格货品'),
        'default' => 0,
    ),
    'order_id' => array(
        'type' => 'table:orders',
        'label' => ('相关订单'),
        'searchtype' => 'has',
        'filtertype' => 'normal',
        'filterdefault' => 'true',
        'in_list' => true,
        'default_in_list' => true,
    ),
    'member_id' => array(
        'type' => 'mediumint(8)',
        'in_list' => false,
        'label' => ('相关会员'),
        'default' => 0,
    ),
    'author_name' => array(
        'type' => 'varchar(100)',
        'label' => ('发表人'),
        'searchtype' => 'has',
        'filtertype' => 'normal',
        'filterdefault' => 'true',
        'in_list' => true,
        'default_in_list' => true,
    ),
    'createtime' => array(
        'type' => 'time',
        'in_list' => true,
        'filtertype' => 'normal',
        'filterdefault' => 'true',
        'label' => ('创建时间'),
    ),
    'lastreply' => array(
        'type' => 'time',
        'label' => ('最后回复时间'),
        'filtertype' => 'normal',
        'filterdefault' => 'true',
        'in_list' => true,
        'default_in_list' => true,
    ),
     'title' => array(
        'type' => 'varchar(255)',
        'label' => ('标题'),
        'in_list' => true,
        'default_in_list' => true,
    ),
    'content' => array(
        'type' => 'longtext',
        'label' => ('内容'),
        'searchtype' => 'has',
        'filtertype' => 'normal',
        'filterdefault' => 'true',
    ),
    'display' => array(
        'type' => 'bool',
        'in_list' => true,
        'label' => ('是否公开'),
        'filtertype' => 'bool',
        'default' => 'false',
        'default_in_list' => true,
    ),
  ),
   'engine' => 'innodb',
   'comment' => ('咨询\评价表'),
   'index' => array(
       'index_for_comment_id' => array(
           'columns' => array(
               0 => 'for_comment_id',
           ),
       ),
   ),

);
