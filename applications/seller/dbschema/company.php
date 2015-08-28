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


$db['company'] = array(
    'columns' => array(
        'company_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => '公司ID',			
            'pkey' => true,
            'extra' => 'auto_increment',
            'in_list' => true,
			'comment' => '文章节点表',
        ),
		'name' => array(
            'type' => 'varchar(100)',
            'required' => true,
            'label' => '公司名称',			
            'in_list' => true,
			'default_in_list' => true,
			'comment' => '公司名称',
        ),
		'addr' => array(
            'type' => 'varchar(200)',
            'required' => true,
            'label' => '公司地址',            
            'in_list' => true,
			'comment' => '公司地址',
        ),
		'area' => array(
            'label' => ('地区') ,
            'type' => 'region',
            'sdfpath' => 'contact/area',
            'filtertype' => 'yes',
            'filterdefault' => 'true',
            'in_list' => true,
            'default_in_list' => false,
        ),
		'nature' => array(
            'label' => ('公司性质') ,
            'type' => array(
                1 => ('政府机关/事业单位') ,
                2 => ('国营') ,
                3 => ('私营') ,
                4 => ('中外合资') ,
                5 => ('外资') ,
				6 => ('其他') ,
            ) ,            
            'filtertype' => 'yes',
            'filterdefault' => 'true',
            'in_list' => true,
            'default_in_list' => false,
			'comment' => '公司性质',
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
