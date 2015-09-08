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

$db['sellers'] = array(
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
            'type' => 'number',
            'required' => false,
			'default' => 0,
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
        'addr' => array(
            'type' => 'varchar(255)',
            'label' => ('地址') ,
            'sdfpath' => 'contact/addr',
            'editable' => true,
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => false,
        ) ,
        'area' => array(
            'label' => ('地区') ,
            'type' => 'region',
            'sdfpath' => 'contact/area',
            'filtertype' => 'yes',
            'filterdefault' => 'true',
            'in_list' => true,
            'default_in_list' => false,
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
			'default' => '1',
			'comment'=>'商家类型',
        ),        
         'reg_ip' => array(
            'type' => 'varchar(16)',
            'label' => ('注册IP') ,
            'in_list' => true,
            'comment' => ('注册时IP地址') ,
        ) ,
		 'regtime' => array(
            'label' => ('注册时间') ,
            'type' => 'time',
            'filtertype' => 'time',            
            'in_list' => true,
            'default_in_list' => true,
            'comment' => ('注册时间') ,
        ) ,
		'disabled' => array(
            'type' => 'bool',
            'default' => 'false',
        ) ,
        'login_count' => array(
            'type' => 'int(11)',
            'default' => 0,
            'required' => true,
        ) ,
        'experience' => array(
            'label' => ('经验值') ,
            'type' => 'int(10)',
            'in_list' => true,
        ),
		'status' => array(
            'label' => ('用户状态') ,
            'type' => array(
                0 => ('新用户'),
                1 => ('公司信息'),
				2 => ('联系人'),
				3 => ('基本资料'),
				4 => ('生产资料'),
            ),
            'default' => '0',
            'in_list' => false,
            'default_in_list' => false,
        ) ,
        'checkin' => array(
            'label' => ('审核') ,
            'type' => array(
                0 => ('待核'),
                1 => ('正常'),
				-1 => ('未通过'),
            ),
            'default' => '0',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
    ),
    'index' => array(
        'ind_email' => array(
            'columns' => array(
                0 => 'email',
            ) ,
        ) ,
        'ind_regtime' => array(
            'columns' => array(
                0 => 'regtime',
            ) ,
        ) ,
        'ind_disabled' => array(
            'columns' => array(
                0 => 'disabled',
            ) ,
        ) ,
    ) ,
    'version' => '$Rev$',
    'comment' => '商家表' ,
);
