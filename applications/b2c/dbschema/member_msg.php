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


$db['member_msg'] = array(
  'columns' => array(
    'msg_id' => array(
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'label' => 'ID',
    ),
    'msg_type' => array(
        'type' => array(
            'normal' => '站内信',
            'sms' => '手机短信',
            'email' => '电子邮件',
        ),
        'label' => ('消息类型'),
        'default' => 'normal',
        'in_list' => true,
        'default_in_list' => true,
    ),
    'member_id' => array(
        'type' => 'table:members',
        'label' => ('会员'),
        'default' => 0,
    ),
    'target' => array(
        'type' => 'varchar(200)',
        'label' => ('发送目标'),
        'default' => 0,
        'in_list' => true,
        'default_in_list' => true,
    ),
    'subject' => array(
      'type' => 'varchar(255)',
        'label' => ('标题'),
        'is_title' => true,
        'in_list' => true,
        'default_in_list' => true,
    ),
    'content' => array(
      'type' => 'text',
      'label' => ('内容'),
      'required' => true,
    ),
    'createtime' => array(
      'type' => 'time',
      'label' => ('发送时间'),
      'filtertype' => 'time',
      
      'in_list' => true,
    ),
    'status' => array(
      'type' => array(
          'sent' => '已发出',
          'received' => '已达到\已阅读',
          'failure' => '未能发出',
      ),
      'required' => true,
      'default' => 'sent',
      'label' => ('当前状态'),
      
      'in_list' => true,
    ),
    'echo' => array(
      'type' => 'text',
      'label' => ('发送反馈'),
    ),
  ),
   'index' => array(
    'ind_member_id' => array(
      'columns' => array(
        0 => 'member_id',
      ),
    ),
  ),
   'engine' => 'innodb',
   'comment' => ('消息通知记录表'),
);
