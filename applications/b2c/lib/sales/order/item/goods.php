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


class b2c_sales_order_item_goods extends b2c_sales_order_item {
    public function getItem() {
        return array(
            'goods_goods_id' => array(
                'name' => ('具体商品') ,
                'path' => 'item/product/goods_id',
                'type' => 'goods',
                'object' => 'b2c_sales_order_item_goods',
                'operator' => array(
                    'contain'
                ) ,
                'input' => 'dialog',
                'table' => 'goods',
            ) ,
            'goods_product_id' => array(
                'name' => ('具体货品') ,
                'path' => 'item/product/product_id',
                'type' => 'goods',
                'object' => 'b2c_sales_order_item_goods',
                'operator' => array(
                    'contain'
                ) ,
                'input' => 'dialog',
                'table' => 'products',
            ) ,
            'goods_bn' => array(
                'name' => ('货号') ,
                'path' => 'item/product/bn',
                'type' => 'goods',
                'object' => 'b2c_sales_order_item_goods',
                'operator' => array(
                    'equal',
                    'contain',
                    'contain1'
                ) ,
                'vtype' => 'alphaint'
            ) ,
            'goods_name' => array(
                'name' => ('商品名称') ,
                'path' => 'item/product/name',
                'type' => 'goods',
                'object' => 'b2c_sales_order_item_goods',
                'operator' => array(
                    'equal',
                    'contain',
                    'contain1'
                ) ,
                'vtype' => 'alphanum'
            ) ,
            'goods_brand_id' => array(
                'name' => ('商品品牌') ,
                'path' => 'item/product/brand_id',
                'type' => 'goods',
                'object' => 'b2c_sales_order_item_goods',
                'operator' => array(
                    'equal',
                    'contain'
                ) ,
                'input' => 'checkbox',
                'options' => 'table:SELECT brand_id AS id,brand_name AS name FROM vmc_b2c_brand'
            ) ,
            'goods_cat_id' => array(
                'name' => ('商品分类') ,
                'path' => 'item/product/cat_id',
                'type' => 'goods',
                'object' => 'b2c_sales_order_item_goods',
                'operator' => array(
                    'equal',
                    'contain'
                ) ,
                'input' => 'checkbox',
                'options' => 'table:SELECT cat_id AS id,cat_name AS name FROM vmc_b2c_goods_cat'
            ) ,
            'goods_type_id' => array(
                'name' => ('商品类型') ,
                'path' => 'item/product/type_id',
                'type' => 'goods',
                'object' => 'b2c_sales_order_item_goods',
                'operator' => array(
                    'contain'
                ) ,
                'input' => 'checkbox',
                'options' => 'table:SELECT type_id AS id,name FROM vmc_b2c_goods_type'
            ) ,

        );
    }
}
?>
