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


$db['bind_tag'] = array(
    'columns' => array(
        'id' => array(
            'type' => 'number',
            'pkey' => true,
            'extra' => 'auto_increment',
            'comment' => 'ID' ,
        ) ,
        'tag_type' => array(
            'type' => array(
                'weixin' => '微信' ,
            ) ,
            'is_title' => true,
            'default' => 'weixin',
            'required' => true,
            'comment' => '绑定平台' ,
        ) ,
        'open_id' => array(
            'type' => 'varchar(100)',
            'comment' => '绑定平台唯一ID' ,
        ) ,
        'tag_name' => array(
            'type' => 'varchar(100)',
            'comment' => '绑定平台的昵称' ,
        ) ,
        'member_id' => array(
            'type' => 'varchar(32)',
            'required' => true,
            'comment' => '绑定会员' ,
        ) ,
        'disabled' => array(
            'type' => 'bool',
            'default' => 'false',
        ) ,
        'createtime' => array(
            'type' => 'time',
            'comment' => '创建时间' ,
        ) ,
    ) ,
    'index' => array(
        'open_id' => array(
            'columns' => array(
                'open_id',
            ) ,
            'prefix' => 'UNIQUE',
        ) ,
    ) ,
    'engine' => 'innodb',
    'comment' => '绑定第三方平台' ,
);
