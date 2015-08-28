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


$db['files'] = array(
    'columns' => array(
        'file_id' => array(
            'type' => 'number',
            'pkey' => true,
            'extra' => 'auto_increment',
            'comment' => '文件ID' ,
        ) ,
        'file_path' => array(
            'type' => 'varchar(255)',
            'comment' => '文件路径' ,
        ) ,
        'file_type' => array(
            'type' => array(
                'private' => '私有' ,
                'public' => '共有',
            ) ,
            'default' => 'public',
            'comment' => '文件类型' ,
        ) ,
        'last_change_time' => array(
            'type' => 'last_modify',
            'comment' => '最后更改时间' ,
        ) ,
    ) ,
    'version' => '$Rev: 41137 $',
    'comment' => '上传文件存放表, 非图片' ,
);
