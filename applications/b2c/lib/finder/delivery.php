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


class b2c_finder_delivery {
    var $detail_delivery = '参数设置';
    function __construct($app) {
        $this->app = $app;
    }
    function detail_delivery($delivery_id) {
        $mdl_delivery = $this->app->model('delivery');
        $render = $this->app->render();
        $render->pagedata['delivery'] = $mdl_delivery->dump($delivery_id,'*','delivery_items');
        $render->pagedata['logistics_tracker'] = vmc::singleton('logisticstrack_puller')->pull($delivery_id,$msg);
        return $render->fetch('admin/order/delivery.html');
    }
}
