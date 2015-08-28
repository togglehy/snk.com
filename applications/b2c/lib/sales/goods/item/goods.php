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



class b2c_sales_goods_item_goods extends b2c_sales_goods_item {
    public function getItem() {
        return array(
            'goods_goods_id' => array(
                'name' => ('具体商品') ,
                'path' => 'goods_id',
                'type' => 'goods',
                'object' => 'b2c_sales_goods_item_goods',
                'operator' => array(
                    'contain'
                ) ,
                'input' => 'objectgoods',
            ) ,
            'goods_brand_id' => array(
                'name' => ('品牌筛选') ,
                'path' => 'brand_id',
                'type' => 'goods',
                'object' => 'b2c_sales_goods_item_goods',
                'operator' => array(
                    'contain'
                ) ,
                'input' => 'checkbox',
                'options' => 'table:SELECT brand_id AS id,brand_name AS name FROM vmc_b2c_brand'
            ) ,
            'goods_cat_id' => array(
                'name' => ('分类筛选') ,
                'path' => 'cat_id',
                'type' => 'goods',
                'object' => 'b2c_sales_goods_item_goods',
                'operator' => array(
                    'contain'
                ) ,
                'input' => 'checkbox',
                'options' => 'table:SELECT cat_id AS id,cat_name AS name, parent_id AS pid FROM vmc_b2c_goods_cat'
            ) ,
            'goods_type_id' => array(
                'name' => ('类型筛选') ,
                'path' => 'type_id',
                'type' => 'goods',
                'object' => 'b2c_sales_goods_item_goods',
                'operator' => array(
                    'contain'
                ) ,
                'input' => 'checkbox',
                'options' => 'table:SELECT type_id AS id,name FROM vmc_b2c_goods_type'
            ) ,
            'goods_name' => array(
                'name' => ('商品名称') ,
                'path' => 'name',
                'type' => 'goods',
                'object' => 'b2c_sales_goods_item_goods',
                'operator' => array(
                    'equal',
                    'contain',
                    'contain1'
                )
            ) ,
            'goods_brief' => array(
                'name' => ('商品简介') ,
                'path' => 'brief',
                'type' => 'goods',
                'object' => 'b2c_sales_goods_item_goods',
                'operator' => array(
                    'equal',
                    'contain',
                    'contain1',
                    'null'
                )
            ) ,
            'goods_intro' => array(
                'name' => ('商品介绍') ,
                'path' => 'intro',
                'type' => 'goods',
                'object' => 'b2c_sales_goods_item_goods',
                'operator' => array(
                    'equal',
                    'contain',
                    'contain1',
                    'null'
                )
            ) ,

        );
    }
    // 和主表的关系
    public function getRefInfo() {
        return false; // 使用就是主表的

    }
}
?>
