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

class b2c_ctl_admin_delivery extends desktop_controller {
    function index() {
        $this->finder('b2c_mdl_delivery', array(
            'title' => ('发货单') ,
            'use_buildin_recycle' => false,
            'base_filter'=>array('delivery_type'=>'send')            
        ));
    }
    function addnew() {
        echo __FILE__ . ':' . __LINE__;
    }
}
