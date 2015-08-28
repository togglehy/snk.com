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


$db['coupons'] = array(
    'columns' => array(
        'cpns_id' => array(
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'label' => ('id') ,
            'comment' => ('优惠券方案id') ,

        ) ,
        'cpns_name' => array(
            'type' => 'varchar(255)',
            'label' => ('优惠券名称') ,
            'searchable' => true,
            'comment' => ('优惠券名称') ,

            'in_list' => true,
            'default_in_list' => true,
            'searchtype' => 'has',
            'filtertype' => 'yes',
        ) ,
        'cpns_prefix' => array(
            'type' => 'varchar(50)',
            'required' => true,
            'default' => '',
            'label' => ('优惠券批次号') ,
            'comment' => ('生成优惠券前缀') ,

            'in_list' => true,
            'default_in_list' => true,
            'searchtype' => 'has',
            'filtertype' => 'yes',
            
        ) ,
        'cpns_gen_quantity' => array(
            'type' => 'number',
            'default' => 0,
            'required' => true,
            'label' => ('已发行总量') ,
            'comment' => ('已发行总量') ,
            'in_list' => true,
            'default_in_list' => true,
            
            'orderby' => true
        ) ,
        'cpns_key' => array(
            'type' => 'varchar(20)',
            'required' => true,
            'default' => '',
            'comment' => ('优惠券生成的key') ,
        ) ,
        'cpns_status' => array(
            'type' => 'intbool',
            'default' => '1',
            'required' => true,
            'label' => ('是否启用') ,
            'comment' => ('优惠券方案状态') ,
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
        ) ,
        'cpns_type' => array(
            'type' => array(
                0 => ('暗号A型') ,
                1 => ('红包B型') ,
            ) ,
            'default' => '0',
            'required' => true,
            'label' => ('优惠券类型') ,
            'comment' => ('优惠券类型') ,

            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
            'orderby' => true
        ) ,
        'cpns_point' => array(
            'type' => 'number',
            'default' => NULL,
            'label' => ('兑换所需积分') ,
            'comment' => ('兑换优惠券积分') ,
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
            'orderby' => true
        ) ,
        'rule_id' => array(
            'type' => 'table:sales_rule_order',
            'sdfpath' => 'rule/rule_id',
            'default' => NULL,
            'comment' => ('相关的订单促销规则ID') ,
        ) ,
    ) ,
    'index' => array(
        'ind_cpns_prefix' => array(
            'columns' => array(
                0 => 'cpns_prefix',
            ) ,
        ) ,
    ) ,
    'comment' => ('优惠券表') ,
);
