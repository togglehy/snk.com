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


$db['order_pmt'] = array(
    'columns' => array(
        'pmt_id' => array(
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'comment' => ('促销记录ID') ,
        ) ,
        'rule_id' => array(
            'type' => 'int(8)',
            'required' => true,
            'label' => ('促销规则ID') ,
        ) ,
        'order_id' => array(
            'type' => 'table:orders',
            'required' => true,
            'comment' => ('订单ID') ,
        ) ,
        'product_id' => array(
            'type' => 'table:products',
            'comment' => ('促销类型') ,
        ) ,
        'pmt_type' => array(
            'type' => array(
                'order' => ('订单') ,
                'goods' => ('商品') ,
                'coupon' => ('优惠券') ,
            ) ,
            'default' => 'goods',
            'required' => true,
            'comment' => ('促销类型') ,
        ) ,
        'pmt_tag' => array(
            'type' => 'varchar(50)',
            'comment' => ('促销标签') ,
        ) ,
        'pmt_description' => array(
            'type' => 'text',
            'comment' => ('促销描述') ,
        ) ,
        'pmt_solution' => array(
            'type' => 'text',
            'comment' => ('促销兑现方案') ,
        ) ,
        'pmt_save' => array(
            'type' => 'money',
            'default' => '0',
            'required' => true,
            'comment' => ('促销节省金额') ,
        ) ,
    ) ,
    'engine' => 'innodb',
    'comment' => ('订单与商品促销规则的关联表') ,
);
