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


 
$db['member_systmpl']=array (
  'columns' => 
  array (
    'tmpl_name' => array (
       'type' => 'varchar(100)',
        'pkey' => true,
      'required' => true,
       'comment' => ('模版名称'),
    ),
    'content' => array(
        'type'=>'longtext',
        'label' =>('内容'),
        'default' => 0,
        'comment' => ('模板内容'),
    ),
    'edittime' => array (
      'type' => 'int(10) ',
      'required' => true,
      'comment' => ('编辑时间'),
    ),
    'active' => array(
        'type'=>"enum('true', 'false')",
        'default' => 'true',
        'comment' => ('是否激活'),
    ),
   
  ),   
  'comment' => ('会员消息模版表'),
   'engine' => 'innodb',
   'version' => '$Rev$',
);
