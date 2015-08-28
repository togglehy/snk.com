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


$db['delivery_items'] = array(
    'columns' => array(
        'item_id' => array(
            'type' => 'int unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'comment' => ('序号') ,
        ) ,
        'delivery_id' => array(
            'type' => 'table:delivery',
            'required' => true,
            'default' => 0,

            'comment' => ('发货单号') ,
        ) ,
        'order_item_id' => array(
            'type' => 'table:order_items',
            'default' => 0,

            'comment' => ('原始订单明细项目编号') ,
        ) ,
        'item_type' => array(
            'type' => array(
                'product' => ('商品') ,
                'pkg' => ('捆绑商品') ,
                'gift' => ('赠品商品') ,
                'adjunct' => ('配件商品') ,
            ) ,
            'default' => 'product',
            'required' => true,

            'comment' => ('明细项货物类型') ,
        ) ,
        'product_id' => array(
            'type' => 'table:products',
            'required' => true,
            'default' => 0,

            'comment' => ('货品ID') ,
        ) ,
        'goods_id' => array(
            'type' => 'table:goods',
            'required' => true,
            'default' => 0,

            'comment' => ('商品ID') ,
        ) ,
        'bn' => array(
            'type' => 'varchar(40)',

            'is_title' => true,
            'comment' => ('明细商品货号') ,
        ) ,
        'name' => array(
            'type' => 'varchar(200)',

            'comment' => ('明细商品的名称') ,
        ) ,
        'spec_info' => array(
            'type' => 'varchar(200)',

            'comment' => ('商品规格描述') ,
        ) ,
        'image_id' => array(
            'type' => 'table:image@image',
            'required' => true,
            'default' => 0,
            'comment' => '图片ID',
        ) ,
        'weight' => array(
            'type' => 'number',

            'comment' => ('重量') ,
        ) ,
        'sendnum' => array(
            'type' => 'float',
            'default' => 0,
            'required' => true,

            'comment' => ('实际发货数量') ,
        ) ,
    ) ,
    'comment' => ('发货/退货单明细表') ,
);
