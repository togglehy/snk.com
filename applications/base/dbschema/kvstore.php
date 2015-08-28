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


$db['kvstore'] = array(
    'columns' => array(
        'id' => array(
            'type' => 'number',
            'pkey' => true,
            'extra' => 'auto_increment',
            'comment' => '序号' ,
        ) ,
        'prefix' => array(
            'type' => 'varchar(255)',
            'required' => true,
            'comment' => 'kvstore类型' ,
        ) ,
        'key' => array(
            'type' => 'varchar(255)',
            'required' => true,
            'comment' => 'kvstore存储的键值' ,
        ) ,
        'value' => array(
            'type' => 'serialize',
            'comment' => 'kvstore存储值' ,
        ) ,
        'dateline' => array(
            'type' => 'time',
            'comment' => '存储修改时间' ,
        ) ,
        'ttl' => array(
            'type' => 'time',
            'default' => 0,
            'comment' => '过期时间,0代表不过期' ,
        ) ,
    ) ,
    'index' => array(
        'ind_prefix' => array(
            'columns' => array(
                0 => 'prefix',
            ) ,
        ) ,
        'ind_key' => array(
            'columns' => array(
                0 => 'key',
            ) ,
        ) ,
    ) ,
    'engine' => 'innodb',
    'version' => '$Rev: 41137 $',
    'ignore_cache' => true,
    'comment' => 'kvstore存储表' ,
);
