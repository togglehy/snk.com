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


$db['contact'] = array(
    'columns' => array(
        'company_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => '联系人ID',
            'pkey' => true,
            'extra' => 'auto_increment',
            'in_list' => true,
			'comment' => '联系人ID',
        ),
		'name' => array(
            'type' => 'varchar(100)',
            'required' => true,
            'label' => '姓名',
            'in_list' => true,
			'default_in_list' => true,
			'comment' => '姓名',
        ),
		'department' => array(
            'type' => 'varchar(200)',
            'required' => true,
            'label' => '部门',
            'in_list' => true,
			'comment' => '部门',
        ),
        'tel' => array(
            'type' => 'varchar(50)',
            'label' => ('固定电话') ,
            'sdfpath' => 'contact/phone/telephone',
            'searchtype' => 'head',
            'editable' => true,
            'filtertype' => 'normal',
            'filterdefault' => 'true',
            'in_list' => true,
            'default_in_list' => false,
        ) ,
        'email' => array(
            'type' => 'varchar(200)',
            'sdfpath' => 'consignor/email',
            'label' => ('联系人Email') ,
            'comment' => ('联系人Email') ,
        ) ,
        
        'seller_id' => array(
            'type' => 'number',
            'required' => false,
			'default' => 0,
            'label' => '商家',
			'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
            'order' => '1',
			'comment' => '商家',
        ),
	),
	'index' => array(
	'ind_name' => array(
		'columns' => array(
			0 => 'name',
		) ,
		'prefix' => 'unique',
	) ,
    ) ,
    'version' => '$Rev$',
    'comment' => '公司表' ,
);
