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
 * goods_type_items
 * $ 2010-05-09 16:15 $
 */
class b2c_sales_goods_item_type extends b2c_sales_goods_item
{
    public function getItem() {
        return array(
             ///////////////////////  商品类型属性 /////////////////////////
             'type_name'=> array('name'=>('类型名称'),'path'=>'name','type'=>'type', 'object'=>'b2c_sales_goods_item_type', 'operator'=>array('equal','contain','contain1','null')),
        );
    }

    // 和主表的关系
    public function getRefInfo() {
        return array(
                  'ref_id' => 'type_id', // 在主表的关联字段名
                  'pkey'   => 'type_id',   // vmc_b2c_goods_type 里的主键
                  'table'  => 'vmc_b2c_goods_type',
               );
    }
}
?>
