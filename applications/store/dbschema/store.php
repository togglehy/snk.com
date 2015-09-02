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


$db['store'] = array(
    'columns' => array(
        'store_id' => array(
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'comment' => ('店铺') ,
        ) ,
        'store_name' => array(
            'type' => 'varchar(150)',
            'label' => ('店铺名称') ,
            'is_title' => true,
            'required' => true,
            'comment' => ('店铺名称') ,
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'store_bn' => array(
            'type' => 'varchar(100)',
            'label' => ('店铺编号') ,
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
            'comment' => ('店铺编号') ,
        ) ,
        'store_area' => array(
            'type' => 'region',
            'in_list' => true,
            'default_in_list' => true,
            'label' => ('所在地区') ,
            'comment' => ('所在地区') ,
        ) ,
        'store_address' => array(
            'type' => 'longtext',
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
            'comment' => ('详细地址/门牌') ,
            'label' => ('详细地址/门牌') ,
        ) ,
        'store_contact' => array(
            'type' => 'varchar(255)',
            'comment' => ('联系方式') ,
            'label' => ('联系方式') ,
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ) ,

		'seller_id' => array (
			'type' => 'number',
			'label' => ('商家'),
			'comment' => ('商家'),
			'default' => 0,
			'width' => 110,
			'in_list' => true,
			'orderby' => true,
		),
    ) ,
    'index' => array(
        'ind_bn' => array(
            'columns' => array(
                0 => 'store_bn',
            ) ,
        ) ,
    ) ,
    'comment' => ('店铺表') ,
);
