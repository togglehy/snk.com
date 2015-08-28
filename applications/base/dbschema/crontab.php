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


$db['crontab'] = array(
    'columns' => array(
        'id' => array(
            'type' => 'varchar(100)',
            'pkey' => true,
            'required' => true,
            'label' => '定时任务ID',
            'editable' => false,
            'is_title' => true,
            'in_list' => true,
            'default_in_list' => false,
            'width' => 70,
            'order' => 10,
        ),

        'description' => array(
            'required' => true,
            'type' => 'varchar(255)',
            'label' => '描述',
            'in_list' => true,
            'default_in_list' => true,
            'order' => 15,
        ),

        'enabled' => array(
            'type' => 'bool',
            'default' => 'true',
            'label' => '开启',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'order' => 20,
        ),

        'schedule' => array(
            'type' => 'varchar(255)',
            'label' => '定时规则',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'order' => 30,
        ),
        'last' => array(
            'type' => 'time',
            'label' => '最后执行时间',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'app_id' => array(
            'type' => 'varchar(32)',
            'required' => true,
            'label' => 'app应用',
        ),
        'class' => array(
            'type' => 'varchar(100)',
            'required' => true,
            'label' => '定时任务类名',
        ),
        'type' => array(
            'type' => array(
                'custom' => '客户自定义',
                'system' => '系统内置', ),
            'label' => '定时器类型',
        ),
    ),
    'version' => '$Rev: 41137 $',
    'ignore_cache' => true,
    'comment' => '定时任务表',
);
