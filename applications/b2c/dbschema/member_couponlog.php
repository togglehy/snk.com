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


$db['member_couponlog'] = array(
    'columns' => array(
        'mcl_id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'label' => 'ID',
        ) ,
        'order_id' => array(
            'type' => 'table:orders@b2c',
            'required' => true,
            'default' => 0,
            'label' => '应用订单号' ,
            'searchtype' => 'has',
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'cpns_id' => array(
            'type' => 'number',
            'required' => true,
            'default' => 0,
            'label' => '优惠券ID' ,
        ) ,
        'cpns_name' => array(
            'type' => 'varchar(255)',
            'label' => '优惠券名称' ,
            'searchtype' => 'has',
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'usetime' => array(
            'type' => 'time',
            'label' => '使用时间' ,
            'filtertype' => 'time',
            
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'order_total' => array(
            'type' => 'money',
            'default' => '0',
            'required' => true,
            'label' => '订单金额' ,
            'in_list' => true,
            'searchtype' => 'has',
            'filtertype' => 'yes',
            'default_in_list' => true,
        ) ,
        'coupon_save' => array(
            'type' => 'money',
            'default' => '0',
            'required' => true,
            'label' => '优惠券抵扣金额' ,
            'in_list' => true,
            'searchtype' => 'has',
            'filtertype' => 'yes',
            'default_in_list' => true,
        ) ,
        'member_id' => array(
            'type' => 'table:members',
            'label' => '使用者' ,
            'searchtype' => 'has',
            'filtertype' => false,
            'filterdefault' => 'true',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'memc_code' => array(
            'type' => 'varchar(255)',
            'label' => '使用的优惠券号码' ,
            'searchtype' => 'has',
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ),
    ) ,
    'index' => array(
        'ind_cpnsid' => array(
            'columns' => array(
                0 => 'cpns_id',
            ) ,
        ) ,
        'ind_cpnscode' => array(
            'columns' => array(
                0 => 'memc_code',
            ) ,
        ) ,
        'ind_cpnsname' => array(
            'columns' => array(
                0 => 'cpns_name',
            ) ,
        ) ,
    ) ,
    'comment' => '优惠券使用记录'
);
