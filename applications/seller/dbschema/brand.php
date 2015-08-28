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
// | 商家品牌申请、授权
// +----------------------------------------------------------------------
$db['brand'] = array(
    'columns' => array(
        'id' => array(
            'type' => 'number',
            'required' => true,			
            'pkey' => true,
            'extra' => 'auto_increment',
            'in_list' => true,
			'comment' => 'id',
        ),    
		'seller_id' => array(
            'type' => 'table:seller',
            'required' => false,
			'default' => '',
            'label' => '商家',            
			'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
            'order' => '1',
			'comment' => '商家',
        ),		
		'status' => array(
            'type' => array(
                0 => ('待核'),
                1 => ('正常'),
				2 => ('未通过'),
            ),
            'default' => 'false',
            'required' => false,
            'label' => '状态' ,
            'in_list' => true,
			'default_in_list' => true,
			'filtertype' => 'normal',
			'comment' => '状态',
        ),		
		'create_time' => array (
            'type' => 'time',
            'label' => ('注册时间'),            
            'in_list' => true,
            'default_in_list' => false,
			'filtertype' => 'time',
            'filterdefault'=>true,
        ),
    ),
	'index' => array(
		'ind_status' => array(
			'columns' => array(
				0 => 'status',
			),
		),
	),
    'version' => '$Rev$',
    'comment' => '商家品牌表' ,
);
