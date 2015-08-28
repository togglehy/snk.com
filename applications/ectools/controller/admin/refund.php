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



class ectools_ctl_admin_refund extends desktop_controller{

    public function __construct($app)
    {
        parent::__construct($app);

    }

    public function index(){
        $this->finder('ectools_mdl_bills',array(
            'title'=>'应付款',
            'use_buildin_recycle'=>false,
            'base_filter'=>array('bill_type'=>'refund')
        ));
    }



}
