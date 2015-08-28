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


 
$db['cache_expires']=array (
  'columns' => 
  array (
    'type' => array(
        'type' => 'varchar(20)',
        'pkey' => true,
        'required' => true,
        'comment' => '类型,目前两种conf和db两种',
    ),
    'name' => array(
        'type'=>'varchar(255)',
        'pkey' => true,
        'required'=>true,
        'comment' => '缓存名称',
    ),
    'expire' => array(
        'type'=>'time',
        'required' => true,
        'comment' => '最后更新时间',
    ),
    'app' => array(
        'type'=>'varchar(50)',
        'required'=>true,
        'comment' => '应用ID',
    ),
  ),
  'index' => 
  array (
    'ind_name' => 
    array (
      'columns' => 
      array (
        0 => 'name',
      ),
    ),
  ),
  'engine' => 'innodb',
  'version' => '$Rev: 41137 $',
  'ignore_cache' => true,
  'comment' => 'cache的过期判断-全页缓存',
);
