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


 
$db['setting']=array (
  'columns' => 
  array (
    'app' => array(
        'type'=>'varchar(50)',
        'pkey' => true,
        'comment' => 'app名',
    ),
    'key' => array(
        'type'=>'varchar(255)',
        'pkey' => true,
        'comment' => 'setting键值',
        
    ),
    'value' => array(
        'type'=>'longtext',
        'comment' => 'setting存储值',
    ),
  ),
  'engine' => 'MyISAM',
  'version' => '$Rev: 41137 $',
  'ignore_cache' => true,
  'comment' => 'setting存储表',
);
