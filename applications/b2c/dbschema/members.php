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


$db['members'] = array(
    'columns' => array(
        'member_id' => array(
            'type' => 'number',
            'extra' => 'auto_increment',
            'pkey' => true,
            'label' => ('会员ID') ,
        ) ,
        'member_lv_id' => array(
            'required' => true,
            'default' => 0,
            'label' => ('会员等级') ,
            'sdfpath' => 'member_lv/member_group_id',

            'order' => 40,
            'type' => 'table:member_lv',
            'editable' => true,
            'filtertype' => 'bool',
            'filterdefault' => 'true',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'avatar' => array(
            'type' => 'char(32)',
            'label' => '头像' ,
            'in_list' => true,
            'default_in_list' => true,
        ) ,

        'name' => array(
            'type' => 'varchar(50)',
            'label' => ('姓名') ,

            'sdfpath' => 'contact/name',
            'searchtype' => 'has',
            'editable' => true,
            'filtertype' => 'normal',
            'filterdefault' => 'true',
            'in_list' => true,
            'is_title' => true,
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
        'mobile' => array(
            'type' => 'varchar(50)',
            'label' => ('手机') ,

            'sdfpath' => 'contact/phone/mobile',
            'searchtype' => 'head',
            'editable' => true,
            'filtertype' => 'normal',
            'filterdefault' => 'true',
            'in_list' => true,
            'default_in_list' => false,
        ) ,
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
            'label' => 'EMAIL',

            'sdfpath' => 'contact/email',
            'default' => '',
            'searchtype' => 'has',
            'editable' => true,
            'filtertype' => 'normal',
            'filterdefault' => 'true',
            'in_list' => true,
            'default_in_list' => false,
        ) ,
        'zip' => array(
            'type' => 'varchar(20)',
            'label' => ('邮编') ,

            'sdfpath' => 'contact/zipcode',
            'editable' => true,
            'filtertype' => 'normal',
            'in_list' => true,
        ) ,
        'order_num' => array(
            'type' => 'number',
            'default' => 0,
            'label' => ('订单数') ,
            'in_list' => true,
            'order' => 100,
            'default_in_list' => true,
        ) ,
        'refer_id' => array(
            'type' => 'varchar(50)',
            'label' => ('来源ID') ,
            'in_list' => false,
        ) ,
        'refer_url' => array(
            'type' => 'varchar(200)',
            'label' => ('推广来源URL') ,

            'in_list' => false,
        ) ,
        'b_year' => array(
            'label' => ('生年') ,
            'type' => 'smallint unsigned',

            'in_list' => false,
        ) ,
        'b_month' => array(
            'label' => ('生月') ,
            'type' => 'tinyint unsigned',

            
            'in_list' => false,
        ) ,
        'b_day' => array(
            'label' => ('生日') ,
            'type' => 'tinyint unsigned',

            
            'in_list' => false,
        ) ,
        'sex' => array(
            'type' => array(
                0 => ('女') ,
                1 => ('男') ,
                2 => '-',
            ) ,
            'sdfpath' => 'profile/gender',
            'default' => 2,
            'required' => true,
            'label' => ('性别') ,
            'editable' => true,
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'addon' => array(
            'type' => 'longtext',
            'comment' => ('会员额外序列化信息') ,
        ) ,
        'advance' => array(
            'type' => 'decimal(20,3) unsigned',
            'default' => '0.00',
            'required' => true,
            'label' => ('预存款') ,
            'sdfpath' => 'advance/total',
            'in_list' => true,
            'comment' => ('会员账户余额') ,
        ) ,
        'advance_freeze' => array(
            'type' => 'money',
            'default' => '0.00',
            'sdfpath' => 'advance/freeze',
            'required' => true,
            'comment' => ('会员预存款冻结金额') ,
        ) ,
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
        'cur' => array(
            'sdfpath' => 'currency',
            'type' => 'varchar(20)',
            'label' => ('货币') ,

            'in_list' => true,
            'comment' => ('货币(偏爱货币)') ,
        ) ,
        'disabled' => array(
            'type' => 'bool',
            'default' => 'false',
        ) ,
        'remark' => array(
            'label' => ('备注') ,
            'type' => 'text',

            'in_list' => true,
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
        ) ,
        'source' => array(
            'type' => array(
                'pc' => ('标准平台') ,
                'mobile' => ('手机触屏') ,
                'weixin' => ('微信') ,
                'app' => ('手机APP') ,
                'api' => ('API注册'),
            ) ,
            'required' => false,
            'label' => ('平台来源') ,
            'default' => 'pc',
            'in_list' => true,
            'default_in_list' => false,
            'filterdefault' => false,
            'filtertype' => 'normal',
        ) ,
    ) ,
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
    'engine' => 'innodb',
    'comment' => ('会员信息主表') ,
);
