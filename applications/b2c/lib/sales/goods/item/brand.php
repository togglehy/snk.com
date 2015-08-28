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


/**
 * brand_items
 * $ 2010-05-10 13:29 $
 */
class b2c_sales_goods_item_brand extends b2c_sales_goods_item {
    public function getItem() {
        return array(
            ///////////////////////  品牌属性 /////////////////////////
            'brand_brand_name' => array(
                'name' => ('品牌名称') ,
                'path' => 'brand_name',
                'type' => 'brand',
                'object' => 'b2c_sales_goods_item_brand',
                'operator' => array(
                    'equal',
                    'contain',
                    'contain1',
                    'null'
                )
            ) ,
            'brand_brand_url' => array(
                'name' => ('品牌网址') ,
                'path' => 'brand_url',
                'type' => 'brand',
                'object' => 'b2c_sales_goods_item_brand',
                'operator' => array(
                    'equal',
                    'contain',
                    'contain1',
                    'null'
                )
            ) ,
            'brand_brand_desc' => array(
                'name' => ('品牌描述') ,
                'path' => 'brand_desc',
                'type' => 'brand',
                'object' => 'b2c_sales_goods_item_brand',
                'operator' => array(
                    'equal',
                    'contain',
                    'contain1',
                    'null'
                )
            ) ,
            'brand_brand_keywords' => array(
                'name' => ('品牌别名') ,
                'path' => 'brand_keywords',
                'type' => 'brand',
                'object' => 'b2c_sales_goods_item_brand',
                'operator' => array(
                    'equal',
                    'contain',
                    'contain1',
                    'null'
                )
            ) ,
        );
    }
    public function getRefInfo() {
        return array(
            'ref_id' => 'brand_id', // 在主表的关联字段名
            'pkey' => 'brand_id', // vmc_b2c_goods_cat 里的主键
            'table' => 'vmc_b2c_brand',
        );
    }
}
?>
