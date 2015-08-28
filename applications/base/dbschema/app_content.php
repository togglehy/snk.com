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


 
$db['app_content']=array (
  'columns' => 
  array (
    'content_id'=>array(
      'type' => 'number',
      'pkey' => true,
      'extra' => 'auto_increment',
      'comment' => '序号',      
    ),
    'content_type' => 
    array (
      'type' => 'varchar(80)',
      'required' => true,
      'width' => 100,
      'in_list' => true,
      'default_in_list' => true,
      'comment' => 'service类型(service_category和service)',
    ),
    'app_id' => 
    array (
      'type' => 'table:apps',
      'required' => true,
      'width' => 100,
      'in_list' => true,
      'default_in_list' => true,
      'comment' => '应用的app_id',
    ),
    'content_name'=>array(
        'type'=>'varchar(80)',
        'comment' => 'service category name - service id',
    ),
    'content_title'=>array(
        'type'=>'varchar(100)',
        'is_title'=>true,
        'comment' => 'optname',
    ),
    'content_path'=>array(
        'type'=>'varchar(255)',
        'comment' => 'class name只有type为service才有',
    ),
	'ordernum' => 
    array (
      'type' => 'smallint(4)',
      'default' => 50,
      'label' => '排序',
    ),
	'input_time' =>
    array (
      'type' => 'time',
      'label' => '加载时间',
    ),
    'disabled'=>array(
        'type'=>'bool',
        'default'=>'false',
        'comment' => '是否有效',
    )
  ),
  'index' => 
  array (
      'ind_content_type' => 
      array (
          'columns' => 
          array (
              0 => 'content_type',
          ),
      ),
  ),

  'version' => '$Rev: 44008 $',
  'comment' => 'app资源信息表, 记录app的service信息',
);
