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



class ectools_view_helper{

    function __construct($app){
        $this->app = $app;
    }
    function modifier_barcode($data){
        return vmc::singleton('ectools_barcode')->get($data);
    }

    function modifier_payname($data){
        return $this->app->model('payment_cfgs')->get_app_display_name($data);
    }
}
