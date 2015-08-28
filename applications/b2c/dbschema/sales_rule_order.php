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


$db['sales_rule_order'] = array(
    'columns' => array(
        'rule_id' => array(
            'type' => 'int(8)',
            'required' => true,
            'pkey' => true,
            'label' => ('规则id'),
            'extra' => 'auto_increment',
            'in_list' => false,
            ),
        'name' => array(
            'type' => 'varchar(255)',
            'required' => true,
            'default' => '',
            'label' => ('规则名称'),
            'editable' => true,
            'in_list' => true,
            'default_in_list' => true,
            
            'is_title' => true,
            ),
        'description' => array(
            'type' => 'text',
            'label' => ('规则描述'),
            'required' => false,
            'default' => '',

            'in_list' => true,
            
            ),
        'from_time' => array(
            'type' => 'time',
            'label' => ('起始时间'),
            'default' => 0,
            'editable' => true,
            'in_list' => true,
            'default_in_list' => true,
            
            ),
        'to_time' => array(
            'type' => 'time',
            'label' => ('截止时间'),
            'default' => 0,
            'editable' => true,
            'in_list' => true,
            'default_in_list' => false,
            
            ),
        'member_lv_ids' => array(
            'type' => 'varchar(255)',
            'default' => '',
            'required' => true,
            'label' => ('会员级别集合'),

            ),
        'status' => array(
            'type' => 'bool',
            'default' => 'false',
            'required' => true,
            'label' => ('开启状态'),
            'in_list' => true,

            
            'default_in_list' => true,
            ),
        'conditions' => array(
            'type' => 'serialize',
            'default' => '',
            'required' => true,
            'label' => ('规则条件'),

            ),
        'action_conditions' => array(
            'type' => 'serialize',
            'default' => '',
            'label' => ('动作执行条件'),

            ),
        'stop_rules_processing' => array(
            'type' => 'bool',
            'default' => 'false',
            'required' => true,
            'label' => ('是否排斥'),
            'editable' => true,
            
            'in_list' => true,
            'default_in_list' => true,
            ),
        'sort_order' => array(
            'type' => 'int(10) unsigned',
            'default' => '0',
            'required' => true,
            'label' => ('优先级'),
            'editable' => true,
            'in_list' => true,
            'default_in_list' => true,
            ),
        'action_solution' => array(
            'type' => 'serialize',
            'default' => '',
            'required' => true,
            'label' => ('动作方案'),

            ),
       'rule_type' => array(
            'type' => array(
                'N' => ('普通规则'),
                'C' => ('优惠券规则'),
            ),
            'default' => 'N',
            'required' => true,
            ),
        'c_template' => array(
            'type' => 'varchar(100)',
            'label' => ('过滤条件模板'),
            ),
        's_template' => array(
            'type' => 'varchar(255)',
            'label' => ('优惠方案模板'),
            ),
        ),
    'comment' => ('订单促销规则'),
    );
