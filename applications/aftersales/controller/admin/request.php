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




class aftersales_ctl_admin_request extends desktop_controller{

    public function __construct($app)
    {
        parent::__construct($app);

    }

    public function index()
    {

        $this->finder('aftersales_mdl_request',array(
            'title'=>'售后服务申请管理',
            'actions'=>array(
                        ),
            'use_buildin_set_tag'=>true,
            'use_buildin_recycle'=>false,
            'use_buildin_filter'=>true,
            ));
    }

    public function save()
    {
        //TODO
        $this->begin('index.php?app=aftersales&ctl=admin_request&act=index&page='.$_GET['finder_page']);
        $data = $_POST;
        $this->end($this->app->model('request')->save($data));
    }

}
