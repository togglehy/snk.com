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

class b2c_ctl_admin_dlyplace extends desktop_controller {
    public function __construct($app) {
        parent::__construct($app);
        $this->app = $app;
    }
    public function index() {
        $action = array(
            'label' => ('添加发货地点') ,
            'href' => 'index.php?app=b2c&ctl=admin_dlyplace&act=edit',
            'icon' => 'fa fa-plus',
        );
        $this->finder('b2c_mdl_dlyplace', array(
            'title' => ('发货地点') ,
            'actions' => array(
                $action
            ) ,
            'use_buildin_set_tag' => false,
            'use_buildin_recycle' => true,
            'use_buildin_export' => false
        ));
    }
    public function edit($dlyplace_id) {
        if ($dlyplace_id) {
            $mdl_dlyplace = $this->app->model('dlyplace');
            $dlyplace = $mdl_dlyplace->dump($dlyplace_id);
            $this->pagedata['dlyplace'] = $dlyplace;
        }
        $this->page('admin/delivery/dlyplace.html');
    }
    public function save() {
        $this->begin('index?app=b2c&ctl=admin_dlyplace&act=index');
        $mdl_dlyplace = $this->app->model('dlyplace');
        $result = $mdl_dlyplace->save($_POST);
        if ($result) $this->end(true, '保存成功!');
        else $this->end(false, '保存失败!');
    }
}
