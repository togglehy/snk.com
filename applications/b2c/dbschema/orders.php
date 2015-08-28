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


$db['orders'] = array(
    'columns' => array(
        'order_id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'default' => 0,
            'pkey' => true,
            'label' => ('订单号') ,
            'is_title' => true,
            'searchtype' => 'has',
            'filtertype' => 'custom',

            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'createtime' => array(
            'type' => 'time',
            'label' => ('下单时间') ,
            'filtertype' => 'time',

            'in_list' => true,
            'default_in_list' => true,
            'orderby' => true,
        ) ,
        'last_modified' => array(
            'label' => ('最后更新时间') ,
            'type' => 'last_modify',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'status' => array(
            'type' => array(
                'active' => ('活动订单') ,
                'dead' => ('已作废') ,
                'finish' => ('已完成') ,
            ) ,
            'filtertype' => 'normal',
            'default' => 'active',
            'required' => true,
            'label' => ('订单状态') ,
        ) ,
        'confirm' => array(
            'type' => 'tinybool',
            'default' => 'N',
            'required' => true,
            'label' => ('确认状态') ,
        ) ,
        'pay_status' => array(
            'type' => array(
                0 => ('未支付') ,
                1 => ('已支付') ,
                2 => ('已付款至到担保方') ,
                3 => ('部分付款') ,
                4 => ('部分退款') ,
                5 => ('全额退款') ,
            ) ,
            'default' => '0',
            'required' => true,
            'label' => ('付款状态') ,
            'filtertype' => 'normal',
        ) ,
        'payed' => array(
            'type' => 'money',
            'default' => '0',
            'label' => ('订单已支付金额') ,
        ) ,
        'is_cod' => array(
            'type' => 'tinybool',
            'default' => 'N',
            'required' => true,
            'label' => ('是否是货到付款订单') ,
            'filtertype' => 'normal',
        ) ,
        'need_shipping' => array(
            'type' => 'tinybool',
            'default' => 'Y',
            'required' => true,
            'label' => ('是否需要运输\发货') ,
            'filtertype' => 'normal',
        ) ,
        'ship_status' => array(
            'type' => array(
                0 => ('未发货') ,
                1 => ('已发货') ,
                2 => ('部分发货') ,
                3 => ('部分退货') ,
                4 => ('已退货') ,
            ) ,
            'default' => '0',
            'required' => true,
            'label' => ('发货状态') ,
            'filtertype' => 'normal',
        ) ,
        'pay_app' => array(
            'type' => 'varchar(100)',
            'label' => ('支付方式') ,
            'filtertype' => 'yes',

            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'dlytype_id' => array(
            'type' => 'table:dlytype',
            'label' => ('配送方式') ,
            'filtertype' => 'yes',

            'in_list' => false,
        ) ,
        'member_id' => array(
            'type' => 'table:members',
            'label' => ('会员用户名') ,
            'filtertype' => 'yes',

            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'consignee_name' => array(
            'type' => 'varchar(50)',
            'label' => ('收货人') ,
            'sdfpath' => 'consignee/name',
            'searchtype' => 'head',
            'filtertype' => 'normal',

            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'consignee_area' => array(
            'type' => 'region',
            'sdfpath' => 'consignee/area',
            'label' => ('收货地区') ,
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'consignee_address' => array(
            'type' => 'text',
            'sdfpath' => 'consignee/addr',
            'label' => ('收货地址') ,
            'searchtype' => 'has',
            'width' => 180,
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
            'label' => ('收货地址') ,
        ) ,
        'consignee_zip' => array(
            'type' => 'varchar(20)',
            'sdfpath' => 'consignee/zip',
            'label' => ('收货地邮编') ,
        ) ,
        'consignee_tel' => array(
            'type' => 'varchar(50)',
            'sdfpath' => 'consignee/tel',
            'label' => ('收货人固话') ,
            'searchtype' => 'has',
            'filtertype' => 'normal',

            'in_list' => true,
            'default_in_list' => true,
            'label' => ('收货人固话') ,
        ) ,
        'consignee_email' => array(
            'type' => 'varchar(200)',
            'sdfpath' => 'consignee/email',
            'label' => ('收货人Email') ,
            'in_list' => true,
            'label' => ('收货人Email') ,
        ) ,
        'consignee_mobile' => array(
            'label' => ('收货人手机') ,

            'searchtype' => 'has',
            'type' => 'varchar(50)',
            'sdfpath' => 'consignee/mobile',
            'in_list' => true,
            'default_in_list' => true,
            'label' => ('收货人手机') ,
        ) ,
        'weight' => array(
            'type' => 'money',
            'label' => ('订单商品总重量（克）') ,
        ) ,
        'quantity' => array(
            'type' => 'number',
            'label' => ('订单包含商品数量') ,
        ) ,
        'need_invoice' => array(
            'type' => 'bool',
            'default' => 'false',
            'required' => true,
            'in_list' => true,
            'filtertype' => 'normal',
            'default_in_list' => true,
            'label' => ('是否要开发票') ,
            'label' => ('是否要开发票') ,
        ) ,
        'invoice_title' => array(
            'type' => 'varchar(255)',
            'label' => ('发票抬头') ,
        ) ,
        'invoice_addon' => array(
            'type' => 'longtext',
            'label' => ('发票扩展信息') ,
        ) ,
        'score_u' => array(
            'type' => 'money',
            'default' => '0',
            'required' => true,
            'label' => ('订单积分消耗') ,
        ) ,
        'score_g' => array(
            'type' => 'money',
            'default' => '0',
            'required' => true,
            'label' => ('订单获得积分') ,
        ) ,
        'finally_cart_amount' => array(
            'type' => 'money',
            'default' => '0',
            'required' => true,
            'label' => ('商品优惠后总金额') ,
        ) ,
        'cost_freight' => array(
            'type' => 'money',
            'default' => '0',
            'required' => true,
            'label' => ('配送费用') ,
            'filtertype' => 'number',
            'in_list' => true,
        ) ,
        'cost_protect' => array(
            'type' => 'money',
            'default' => '0',
            'required' => true,
            'label' => ('保价费') ,
        ) ,
        'cost_payment' => array(
            'type' => 'money',
            'label' => ('支付手续费') ,
        ) ,
        'cost_tax' => array(
            'type' => 'money',
            'default' => '0',
            'required' => true,
            'label' => ('订单营业税费') ,
        ) ,
        'currency' => array(
            'type' => 'varchar(8)',
            'default' => 'CNY',
            'label' => ('订单支付货币') ,
        ) ,
        'cur_rate' => array(
            'type' => 'decimal(10,4)',
            'default' => '1.0000',
            'label' => ('订单支付货币汇率') ,
        ) ,
        'memberlv_discount' => array(
            'type' => 'money',
            'label' => ('会员身份优惠金额') ,
            'filtertype' => 'normal',
        ) ,
        'pmt_goods' => array(
            'type' => 'money',
            'label' => ('商品促销优惠金额') ,
            'filtertype' => 'normal',
        ) ,
        'pmt_order' => array(
            'type' => 'money',
            'label' => ('订单促销优惠金额') ,
            'filtertype' => 'normal',
        ) ,
        'order_total' => array(
            'type' => 'money',
            'default' => '0',
            'required' => true,
            'label' => ('订单应付总金额') ,
            'filtertype' => 'number',
            'in_list' => true,
            'default_in_list' => true,
            'orderby' => true,
            'label' => ('订单当前货币应付总金额') ,
        ) ,
        'platform' => array(
            'type' => array(
                'pc' => ('PC') ,
                'mobile' => ('触屏H5') ,
                'wx' => ('微信') ,
                'ios' => ('iOS客户端') ,
                'android' => ('Android客户端') ,
                'other' => ('其他') ,
            ) ,
            'required' => false,
            'label' => ('平台来源') ,
            'width' => 110,
            'default' => 'pc',
            'in_list' => true,
            'default_in_list' => false,
            'filterdefault' => false,
            'filtertype' => 'yes',
        ) ,
        'memo' => array(
            'type' => 'longtext',
            'label' => ('订单创建时附言') ,
        ) ,
        'remarks' => array(
            'type' => 'longtext',
            'label' => ('订单管理员备注') ,
            'width' => 50,
            'filtertype' => 'normal',
            'in_list' => true,
        ) ,
        'addon' => array(
            'type' => 'longtext',
            'label' => ('订单附属信息(序列化)') ,
        ) ,
        'ip' => array(
            'type' => 'varchar(15)',
            'label' => ('IP地址') ,
            'label' => ('下单IP地址') ,
            'in_list' => true,
        ) ,
        'disabled' => array(
            'type' => 'bool',
            'default' => 'false',
        ) ,
    ) ,
    'index' => array(
        'ind_ship_status' => array(
            'columns' => array(
                0 => 'ship_status',
            ) ,
        ) ,
        'ind_pay_status' => array(
            'columns' => array(
                0 => 'pay_status',
            ) ,
        ) ,
        'ind_status' => array(
            'columns' => array(
                0 => 'status',
            ) ,
        ) ,
        'ind_disabled' => array(
            'columns' => array(
                0 => 'disabled',
            ) ,
        ) ,
        'ind_last_modified' => array(
            'columns' => array(
                0 => 'last_modified',
            ) ,
        ) ,
        'ind_createtime' => array(
            'columns' => array(
                0 => 'createtime',
            ) ,
        ) ,
    ) ,
    'engine' => 'innodb',
    'label' => ('订单主表') ,
);
