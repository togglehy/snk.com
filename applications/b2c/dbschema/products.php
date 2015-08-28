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


$db['products'] = array(
    'columns' => array(
        'product_id' => array(
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'label' => ('货品ID') ,
        ) ,
        'goods_id' => array(
            'type' => 'table:goods',
            'default' => 0,
            'required' => true,
            'label' => ('商品ID') ,
        ) ,
        'barcode' => array(
            'type' => 'varchar(128)',
            'label' => ('条码') ,
            'in_list' => true,
        ) ,
        'bn' => array(
            'type' => 'varchar(30)',
            'label' => ('货号') ,
            'filtertype' => 'normal',
            
            'in_list' => true,
        ) ,
        'price' => array(
            'type' => 'money',
            'default' => '0',
            'required' => true,
            'label' => ('销售价格') ,
            'filtertype' => 'number',
            
            'in_list' => true,
        ) ,
        'mktprice' => array(
            'type' => 'money',
            'label' => ('市场价') ,
            'filtertype' => 'number',
            'in_list' => true,
        ) ,
        'name' => array(
            'type' => 'varchar(200)',
            'required' => true,
            'default' => '',
            'label' => ('货品名称') ,
            'searchtype' => 'has',
            'filtertype' => 'custom',
            
            'in_list' => true,
            'default_in_list' => true,
            'is_title' => true,
        ) ,
        'weight' => array(
            'type' => 'decimal(20,3)',
            'label' => ('单位重量') ,
            'filtertype' => 'number',
            
            'default' => 0,
            'in_list' => true,
        ) ,
        'unit' => array(
            'type' => 'varchar(20)',
            'label' => ('单位') ,
            'filtertype' => 'normal',
            'default' => '件',
            'in_list' => true,
        ) ,
        'spec_info' => array(
            'type' => 'text',
            'label' => ('规格') ,
            'default'=>'',
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => true,
            'searchtype' => 'has',
        ) ,
        'spec_desc' => array(
            'type' => 'serialize',
            'label' => ('规格值,序列化') ,
            'in_list' => true,
        ) ,
        'is_default' => array(
            'type' => 'bool',
            'required' => true,
            'default' => 'false',

        ) ,
        'image_id' => array(
            'type' => 'varchar(32)',
            'label' => ('相册图ID') ,
            
        ) ,
        'uptime' => array(
            'type' => 'time',
            'depend_col' => 'marketable:true:now',
            'label' => ('上架时间') ,
            'in_list' => true,
            'orderby' => true,
        ) ,
        'downtime' => array(
            'type' => 'time',
            'depend_col' => 'marketable:false:now',
            'label' => ('下架时间') ,
            'in_list' => true,
            'orderby' => true,
        ) ,
        'last_modify' => array(
            'type' => 'last_modify',
            'label' => ('最后修改时间') ,

            'in_list' => true,
        ) ,
        'disabled' => array(
            'type' => 'bool',
            'default' => 'false',

        ) ,
        'marketable' => array(
            'type' => 'bool',
            'default' => 'true',
            'required' => true,
            'label' => ('上架') ,
            'filtertype' => 'yes',

            'in_list' => true,
        ) ,
    ) ,
    'comment' => ('货品表') ,
    'index' => array(
        'ind_goods_id' => array(
            'columns' => array(
                0 => 'goods_id',
            ) ,
        ) ,
        'ind_disabled' => array(
            'columns' => array(
                0 => 'disabled',
            ) ,
        ) ,
        'ind_barcode' => array(
            'columns' => array(
                0 => 'barcode',
            ) ,
        ) ,
        'ind_bn' => array(
            'columns' => array(
                0 => 'bn',
            ) ,
            'prefix' => 'UNIQUE',
        ) ,
    ) ,
    'engine' => 'innodb',
    'comment' => ('商品货品表') ,
);
