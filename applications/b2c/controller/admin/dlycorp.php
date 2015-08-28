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

class b2c_ctl_admin_dlycorp extends desktop_controller
{
    public function __construct($app)
    {
        parent::__construct($app);
        $this->app = $app;
    }
    public function index()
    {
        $action = array(
            'label' => ('添加物流公司') ,
            'href' => 'index.php?app=b2c&ctl=admin_dlycorp&act=edit',
            'icon' => 'fa fa-plus',
        );
        $this->finder('b2c_mdl_dlycorp', array(
            'title' => ('物流公司'),
            'actions' => array(
                $action,
            ),
            'use_buildin_set_tag' => false,
            'use_buildin_recycle' => true,
            'use_buildin_export' => false,
        ));
    }

    public function edit($dlycorp_id)
    {
        if ($dlycorp_id) {
            $dly_corp = $this->app->model('dlycorp');
            $dlycorp = $dly_corp->getRow('*', array(
                'corp_id' => $dlycorp_id,
            ));
            $this->pagedata['dlycorp'] = $dlycorp;
        }
        $this->page('admin/delivery/dlycorp_edit.html');
    }

    public function save()
    {
        $this->begin('index?app=b2c&ctl=admin_dlycorp&act=index');
        $mdl_dlycorp = $this->app->model('dlycorp');
        $result = $mdl_dlycorp->save($_POST);
        if ($result) {
            $this->end(true, '保存成功!');
        } else {
            $this->end(false, '保存失败!');
        }
    }
}
