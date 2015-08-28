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



class ectools_ctl_admin_payment extends desktop_controller{


    public function __construct($app)
    {
        parent::__construct($app);

    }

    function index(){
        $this->finder('ectools_mdl_bills',array(
            'title'=>'应收款',
            'use_buildin_recycle'=>false,
            'base_filter'=>array('bill_type'=>'payment')
        ));
    }


    /** 新建支付订单
     * @params array - 订单详细内容
     * @return boolean - 订单成功与否
     */
    public function addnew($arrPayments=array())
    {
        echo __FILE__.':'.__LINE__;
    }

}
