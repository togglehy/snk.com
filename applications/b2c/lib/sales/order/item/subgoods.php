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



class b2c_sales_order_item_subgoods extends b2c_sales_order_item
{
    public function getItem() {
        return array(
                    // cart_item -> /
                    'subgoods_quantity'=> array(
                                            'name'=>('商品订购数量'),
                                            'path'=>'goods_count',
                                            'type'=>'subgoods',
                                            'object'=>'b2c_sales_order_item_subgoods',
                                            'operator'=>array('equal','equal1'),'vtype'=>'digits'
                                        ),
                    // price
                    'subgoods_subtotal'=> array(
                                            'name'=>('商品订购总金额'),
                                            'path'=>'cart_amount',
                                            'type'=>'subgoods',
                                            'object'=>'b2c_sales_order_item_subgoods',
                                            'operator'=>array('equal','equal1'),'vtype'=>'unsigned'
                                        ),
                    'subgoods_subtotal_weight'=> array(
                                                'name'=>('商品订购总重量'),
                                                'path'=>'weight',
                                                'type'=>'subgoods',
                                                'object'=>'b2c_sales_order_item_subgoods',
                                                'operator'=>array('equal','equal1'),'vtype'=>'unsigned'
                                            ),
        );
    }
}
?>
