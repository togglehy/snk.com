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


class b2c_ctl_admin_reship extends desktop_controller{



    /**
     * 构造方法
     * @params object app object
     * @return null
     */
    public function __construct($app)
    {
        parent::__construct($app);

    }

    function index(){
        $this->finder('b2c_mdl_delivery',array(
            'title'=>('退货单'),
            'use_buildin_recycle'=>false,
            'use_view_tab'=>true,
            'base_filter'=>array('delivery_type'=>'reship')
            ));
    }


    function addnew(){
        echo __FILE__.':'.__LINE__;
    }

}
