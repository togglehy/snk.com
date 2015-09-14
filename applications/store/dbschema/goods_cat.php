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

$db['goods_cat']=array (
  'columns' =>  array (
    'cat_id' =>    array (
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => ('分类ID'),
      'width' => 110,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'parent_id' =>    array (
      'type' => 'number',
      'label' => ('分类ID'),
      'width' => 110,
      'in_list' => true,
      'parent_id'=>true,
    ),
    'cat_path' =>    array (
      'type' => 'varchar(100)',
      'default' => ',',
      'label' => ('分类路径(从根至本结点的路径,逗号分隔,首部有逗号)'),
      'width' => 110,
      'in_list' => true,
    ),
    'is_leaf' =>    array (
      'type' => 'bool',
      'required' => true,
      'default' => 'false',
      'label' => ('是否叶子结点（true：是；false：否）'),
      'width' => 110,
      'in_list' => true,
    ),
    'cat_name' =>    array (
      'type' => 'varchar(100)',
      'required' => true,
      'is_title' => true,
      'default' => '',
      'label' => ('分类名称'),
      'width' => 110,
      'in_list' => true,
      'default_in_list' => true,
    ),
    'gallery_setting' =>    array(
        'type' => 'serialize',
        'label' => ('商品分类设置'),
        'deny_export' => true,
    ),
    'p_order' =>    array (
      'type' => 'number',
      'label' => ('排序'),
      'width' => 110,
      'default' => 0,
      'in_list' => true,
    ),
    'child_count' =>    array (
      'type' => 'number',
      'default' => 0,
      'required' => true,
      'comment' => ('子类别数量'),
    ),
    'addon' =>    array (
      'type' => 'longtext',
      'comment' => ('附加项'),
    ),
    'last_modify' =>    array (
      'type' => 'last_modify',
      'label' => ('更新时间'),
      'width' => 110,
      'in_list' => true,
      'orderby' => true,
    ),
    // 商家
	'store_id' => array (
	  'type' => 'number',
	  'label' => ('店铺ID'),
	  'comment' => ('店铺ID'),
	  'default' => 0,
	  'width' => 110,
	  'in_list' => false,
	  'orderby' => false,
	),
    'ismenu' => array (
      'type' => 'intbool',
      'label' => ('导航'),
      'comment' => ('导航'),
      'default' => '0',
      'width' => 110,
      'in_list' => false,
      'orderby' => false,
    ),
    'disabled' =>    array (
      'type' => 'bool',
      'default' => 'false',
      'required' => true,
      'label' => ('是否屏蔽（true：是；false：否）'),
      'width' => 110,
      'in_list' => true,
    ),
  ),
  'index' =>  array (
    'ind_cat_path' =>    array (
      'columns' =>      array (
        0 => 'cat_path',
      ),
    ),
    'ind_store' =>    array (
      'columns' =>      array (
        0 => 'store_id',
      ),
    ),
    'ind_disabled' =>    array (
      'columns' =>      array (
        0 => 'disabled',
      ),
    ),
    'ind_last_modify' =>    array (
      'columns' =>      array (
        0 => 'last_modify',
      ),
    ),
  ),
  'version' => '$Rev: 41329 $',
  'comment' => ('类别属性值有限表'),
);
