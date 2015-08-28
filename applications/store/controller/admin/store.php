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


class store_ctl_admin_store extends desktop_controller {
    function index() {
        $this->finder('store_mdl_store', array(
            'title' => ('门店列表') ,
            'actions' => array(
                array(
                    'label' => ('添加门店') ,
                    'icon' => 'fa-plus',
                    'href' => 'index.php?app=store&ctl=admin_store&act=edit',
                ) ,
            )
        ));
    }

    function save() {
        $this->begin('index.php?app=store&ctl=admin_store&act=index');
        $data = $_POST;
        $mdl_store = $this->app->model('store');

        if ($mdl_store->save($data)) {
            $this->end(true, '保存成功');
        } else {
            $this->end(false, '保存失败');
        }
    }
    function edit($store_id) {
        if($store_id){
            $mdl_store = $this->app->model('store');
            $this->pagedata['store'] = $mdl_store->dump($store_id);
        }
        $this->page('admin/edit.html');
    }

}
