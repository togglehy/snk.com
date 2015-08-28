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


$datatypes = array(
    'money' => array(
        'sql' => 'decimal(20,3)',
        'searchparams' => array(
            'than' => '大于' ,
            'lthan' => '小于' ,
            'nequal' => '等于' ,
            'sthan' => '小于等于' ,
            'bthan' => '大于等于' ,
            'between' => '介于',
        ) ,
        'match' => '[0-9]{1,18}(\.[0-9]{1,3}|)',
    ) ,
    'email' => array(
        'sql' => 'varchar(255)',
        'searchparams' => array(
            'has' => '包含' ,
            'tequal' => '等于' ,
            'head' => '开头等于' ,
            'foot' => '结尾等于' ,
            'nohas' => '不包含',
        ) ,
    ) ,
    'bn' => array(
        'sql' => 'varchar(255)',
        'searchparams' => array(
            'has' => '包含' ,
            'tequal' => '等于' ,
            'nohas' => '不包含',
        ) ,
    ) ,
    'html' => array(
        'sql' => 'text',
    ) ,
    'bool' => array(
        'sql' => 'enum(\'true\',\'false\')',
        'searchparams' => array(
            'has' => '包含' ,
            'nohas' => '不包含',
        ) ,
    ) ,
    'time' => array(
        'sql' => 'integer(10) unsigned',
        'searchparams' => array(
            'than' => '晚于' ,
            'lthan' => '早于' ,
            'nequal' => '是' ,
            'between' => '介于',
        ) ,
    ) ,
    'cdate' => array(
        'sql' => 'integer(10) unsigned',
    ) ,
    'intbool' => array(
        'sql' => 'enum(\'0\',\'1\')',
    ) ,
    'region' => array(
        'sql' => 'varchar(255)',
    ) ,
    'password' => array(
        'sql' => 'varchar(32)',
    ) ,
    'tinybool' => array(
        'sql' => 'enum(\'Y\',\'N\')',
    ) ,
    'number' => array(
        'sql' => 'mediumint unsigned',
        'searchparams' => array(
            'than' => '大于' ,
            'lthan' => '小于' ,
            'nequal' => '等于' ,
            'sthan' => '小于等于' ,
            'bthan' => '大于等于' ,
            'between' => '介于',
        ) ,
    ) ,
    'float' => array(
        'sql' => 'float',
        'searchparams' => array(
            'than' => '大于' ,
            'lthan' => '小于' ,
            'nequal' => '等于' ,
            'sthan' => '小于等于' ,
            'bthan' => '大于等于' ,
            'between' => '介于',
        ) ,
    ) ,
    'gender' => array(
        'sql' => 'enum(\'male\',\'female\')',
    ) ,
    'ipaddr' => array(
        'sql' => 'varchar(20)',
    ) ,
    'serialize' => array(
        'sql' => 'longtext',
    ) ,
    'last_modify' => array(
        'sql' => 'integer(10) unsigned',
        'searchparams' => array(
            'than' => '大于' ,
            'lthan' => '小于' ,
            'nequal' => '等于',
        ) ,
    ) ,
);
