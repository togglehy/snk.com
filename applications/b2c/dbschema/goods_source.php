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


$db['goods_source'] = array(
  'columns' => array(
    'source_id' => array(
		'type' => 'number',
		'required' => true,
		'pkey' => true,
		'extra' => 'auto_increment',
		'label' => ('来源号'),
		'width' => 110,
		'in_list' => false,
    ),
    'name' => array(
		'type' => 'varchar(100)',
		'required' => true,
		'default' => '',
		'label' => ('名称'),
		'is_title' => true,
		'width' => 150,     
		'in_list' => true,
		'default_in_list' => true,
    ),
	'no' => array(
		'type' => 'varchar(50)',
		'required' => true,
		'default' => '',
		'label' => ('货号'),
		'is_title' => true,
		'width' => 100,
		'editable' => true,
		'in_list' => true,
		'default_in_list' => true,
    ),	
	'uptime' => array(
		'type' => 'time',
		'depend_col' => 'marketable:true:now',
		'label' => ('更新时间') ,
		'in_list' => true,
		'commit' => ('更新时间'),
		'orderby' => true,
		'default_in_list' => true,
	),
	'spec_desc' => array(
		'type' => 'serialize',		
		'comment' => ('货品规格序列化') ,
	),
	'params' => array(
		'type' => 'serialize',
		'comment' => ('参数表结构(序列化) array(参数组名=>array(参数名1=>别名1|别名2,参数名2=>别名1|别名2))'),
    ),
	'seller_id' =>	array (
		'type' => 'number',
		'label' => ('商家'),
		'comment' => ('商家'),
		'default' => 0,
		'width' => 110,
		'in_list' => true,
		'orderby' => true,
	),

	),
	'index' => array(
		'ind_seller' => array(
			'columns' => array(
				0 => 'seller_id',
			),
		),
	),
	'engine' => 'innodb',
	'comment' => ('货品仓库'),
);
