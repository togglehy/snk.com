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



class b2c_sales_order_item_order extends b2c_sales_order_item
{
    public function getItem() {
        return array(
                'order_cart_amount'=> array(
                                        'name'=>('订单商品总金额'),
                                        'path'=>'finally_cart_amount',
                                        'type'=>'order',
                                        'object'=>'b2c_sales_order_item_order',
                                        'operator'=>array('equal','equal1'),
                                        'vtype'=>'unsigned',
                                        'unit'=> array('app'=>'ectools','model'=>'currency','func'=>'changer_odr'),
                                   ),
               'order_weight_amount'=> array(
                                        'name'=>('订单商品总重量'),
                                        'path'=>'weight',
                                        'type'=>'order',
                                        'object'=>'b2c_sales_order_item_order',
                                        'operator'=>array('equal','equal1'),
                                        'vtype'=>'unsigned',
                                   ),
                'order_goods_count'=> array(
                                    'name'=>('订单商品总数量'),
                                    'path'=>'goods_count',
                                    'type'=>'order',
                                    'object'=>'b2c_sales_order_item_order',
                                    'operator'=>array('equal','equal1'),
                                    'vtype'=>'digits',
                                    'unit'=>('个'),
                                    ),
                'order_object_count'=> array(
                                    'name'=>('订单项总数'),
                                    'path'=>'object_count',
                                    'type'=>'order',
                                    'object'=>'b2c_sales_order_item_order',
                                    'operator'=>array('equal','equal1'),
                                    'vtype'=>'digits',
                                    'unit'=>('项'),
                                    ),
        );
    }
}
?>
