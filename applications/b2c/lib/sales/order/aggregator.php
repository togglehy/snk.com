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
 * order aggregator基类
 * $ 2010-05-09 19:39 $
 */
class b2c_sales_order_aggregator extends b2c_sales_basic_postfilter_aggregator
{
    public $default = 'b2c_sales_order_aggregator_combine'; // 默认处理
    protected $aggregator_apps = 'b2c_sales_order_aggregator_apps'; // aggregator servicelist
    protected $attribute_apps = 'b2c_sales_order_item_apps'; // item servicelist

    public function create_auto($ctl = 'admin_sales_order',$act = 'conditions') {
        return parent::create_auto('admin_sales_order');
    }
}
?>
