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


$db['order_items'] = array(
    'columns' => array(
        'item_id' => array(
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            
            'comment' => ('订单明细ID') ,
        ) ,
        'order_id' => array(
            'type' => 'table:orders',
            'required' => true,
            'default' => 0,
            
            'comment' => ('订单ID') ,
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
        'image_id' => array (
          'type' => 'table:image@image',
          'required' => true,
          'default' => 0,
          'comment' => '图片ID',
        ),
        'cost' => array(
            'type' => 'money',
            
            'comment' => ('明细商品的成本') ,
        ) ,
        'price' => array(
            'type' => 'money',
            'default' => '0',
            'required' => true,
            
            'comment' => ('销售价') ,
        ) ,
        'member_lv_price' => array(
            'type' => 'money',
            'default' => '0',
            'required' => true,
            
            'comment' => ('会员价') ,
        ) ,
        'buy_price' => array(
            'type' => 'money',
            'default' => '0',
            'required' => true,
            
            'comment' => ('成交价') ,
        ) ,
        'amount' => array(
            'type' => 'money',
            
            'comment' => ('明细商品总额(成交价x数量)') ,
        ) ,
        'score' => array(
            'type' => 'number',
            'label' => ('积分') ,
            'width' => 30,
            
            'comment' => ('明细商品积分') ,
        ) ,
        'weight' => array(
            'type' => 'number',
            
            'comment' => ('明细商品重量') ,
        ) ,
        'nums' => array(
            'type' => 'float',
            'default' => 1,
            'required' => true,
            
            'comment' => ('明细商品购买数量') ,
        ) ,
        'sendnum' => array(
            'type' => 'float',
            'default' => 0,
            'required' => true,
            
            'comment' => ('明细商品发货数量') ,
        ) ,
        'addon' => array(
            'type' => 'longtext',
            
            'comment' => ('明细商品的规格属性') ,
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
            
            'comment' => ('明细商品类型') ,
        ) ,
    ) ,
    'index' => array(
        'ind_item_bn' => array(
            'columns' => array(
                0 => 'bn',
            ) ,
            'type' => 'hash',
        ) ,
    ) ,
    'engine' => 'innodb',
    'comment' => ('订单明细表') ,
);
