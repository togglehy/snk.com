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


//商品与商品促销规则关联表
$db['goods_promotion_ref'] = array(
    'columns' => array(
        'ref_id' => array(
            'type' => 'int(8)',
            'required' => true,
            'pkey' => true,
            'label' => 'id',
            'extra' => 'auto_increment',
            'comment' => ('商品与商品促销规则关联表'),
            ),
          'goods_id' => array(
            'type' => 'table:goods',
            'default' => 0,
            'required' => true,
            'comment' => ('商品ID'),
         ),
         'rule_id' => array(
            'type' => 'table:sales_rule_goods',
            'default' => 0,
            'required' => true,
            'comment' => ('优惠规则ID'),
         ),
        'description' => array(
            'type' => 'text',
            'label' => ('规则描述'),
            'required' => false,
            'default' => '',
            'in_list' => true,
            ),
        'member_lv_ids' => array(
            'type' => 'varchar(255)',
            'default' => '',
            'required' => false,
            'label' => ('会员级别集合'),
            ),
        'from_time' => array(
            'type' => 'time',
            'label' => ('起始时间'),
            'editable' => true,
            'in_list' => true,
            'default' => 0,
            'default_in_list' => true,
            ),
        'to_time' => array(
            'type' => 'time',
            'label' => ('截止时间'),
            'default' => 0,
            'editable' => true,
            'in_list' => true,
            'default_in_list' => true,
            ),
        'status' => array(
            'type' => 'bool',
            'default' => 'false',
            'required' => true,
            'label' => ('状态'),
            'in_list' => true,
            ),
        'stop_rules_processing' => array(
            'type' => 'bool',
            'default' => 'false',
            'required' => true,
            'label' => ('是否排斥其他规则'),
            'editable' => true,
            ),
        'sort_order' => array(
            'type' => 'int(10) unsigned',
            'default' => '0',
            'required' => true,
            'label' => ('优先级'),
            'editable' => true,
            ),
        'action_solution' => array(
            'type' => 'text',
            'default' => '',
            'required' => true,
            'label' => ('动作方案'),
            ),
        ),
    'comment' => ('商品促销规则缓存表'),
    );
