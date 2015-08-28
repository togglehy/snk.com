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

$db['seller'] = array(
    'columns' => array(
        'seller_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => '商家ID',			
            'pkey' => true,
            'extra' => 'auto_increment',
            'in_list' => true,
        ),    
		'company_id' => array(
            'type' => 'table:company',
            'required' => false,
			'default' => '',
            'label' => '公司',            
			'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
            'order' => '1',
			'comment' => '公司',
        ),
		'name' => array(
            'type' => 'varchar(50)',
            'required' => false,
			'default' => '',
            'label' => '联系人',            
			'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
            'order' => '1',
			'comment' => '联系人',
        ),
		'avatar' => array(
            'type' => 'char(32)',
            'label' => '头像' ,
            'in_list' => true,
            'default_in_list' => true,
        ),
		'mobile' => array(
            'type' => 'varchar(50)',
            'required' => true,
			'default' => '',
            'label' => '手机',            
			'searchtype' => 'has',          
            'in_list' => true,
            'default_in_list' => true,
            'order' => '1',
			'comment' => '商家ID',
        ),
		'email' => array(
            'type' => 'varchar(100)',
            'required' => false,
			'default' => '',
            'label' => 'Email',            
			'searchtype' => 'has',          
            'in_list' => true,
            'default_in_list' => true,
            'order' => '1',
			'comment' => 'Email',
        ),
		'phone' => array(
            'type' => 'varchar(50)',
            'required' => false,
			'default' => '',
            'label' => '电话',            
			'searchtype' => 'has',          
            'in_list' => true,
            'default_in_list' => false,
            'order' => '1',
			'comment' => '商家ID',
        ),
        'addr' => array(
            'type' => 'varchar(50)',
            'required' => false,
            'label' => '商家地址',
            'in_list' => true,
			'comment' => '商家ID',
        ),
		'status' => array(
            'type' => array(
                0 => ('待审'),
                1 => ('已审'),
				-1 => ('冻结'),
            ),
            'default' => 'false',
            'required' => false,
            'label' => '状态' ,
            'in_list' => true,
			'default_in_list' => true,
			'filtertype' => 'normal',
			'comment' => '状态',
        ),
		'type'=>array(
            'type' => array(
				0 => ('商家'),
                1 => ('线上分销商'),
                2 => ('线上专营服务商'),
				3 => ('授权销售商'),
				4 => ('神家客自营商'),				
            ),
			'label' => '商家类型' ,
			'filtertype' => 'normal',
			'default' => 1,
			'comment'=>'商家类型',
        ),
		'create_time' => array (
            'type' => 'time',
            'label' => ('注册时间'),            
            'in_list' => true,
            'default_in_list' => false,
			'filtertype' => 'time',
            'filterdefault'=>true,
        ),
		'updatetime'=>array(
            'type'=>'last_modify',
            'comment'=>'最后编辑时间'
        ),
    ),
    'index' => array(
        'ind_status' => array(
            'columns' => array(
                0 => 'status',
            ) ,            
        ) ,
    ) ,
    'version' => '$Rev$',
    'comment' => '商家表' ,
);
