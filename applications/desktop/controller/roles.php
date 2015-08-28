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


class desktop_ctl_roles extends desktop_controller {

    public function __construct($app) {
        parent::__construct($app);
        $this->obj_roles = vmc::singleton('desktop_roles');
    }
    function index() {
        $this->finder('desktop_mdl_roles', array(
            'title' => '角色及权限管理' ,
            'use_buildin_recycle' => true,
            'actions' => array(
                array(
                    'label' => '新建角色' ,
                    'icon' => 'fa fa-plus',
                    'href' => 'index.php?ctl=roles&act=addnew',
                ) ,
            )
        ));
    }
    function addnew() {
        $workgrounds = app::get('desktop')->model('menus')->getList('*', array(
            'menu_type' => 'workground',
            'disabled' => 'false',
            'display' => 'true'
        ));
        $widgets = app::get('desktop')->model('menus')->getList('*', array(
            'menu_type' => 'widgets'
        ));
        $this->pagedata['widgets'] = $widgets;
        foreach ($workgrounds as $k => $v) {
            $workgrounds[$k]['permissions'] = $this->obj_roles->get_permission_per($v['menu_id'], array());
        }
        $this->pagedata['workgrounds'] = $workgrounds;
        $this->pagedata['adminpanels'] = $this->obj_roles->get_adminpanel(null, array());
        $this->page('users/edit_roles.html');
    }
    function save() {
        $this->begin('index.php?ctl=roles&act=index');
        $roles = $this->app->model('roles');
        if ($roles->validate($_POST, $msg)) {
            if ($roles->save($_POST)) $this->end(true, '保存成功');
            else $this->end(false, '保存失败');
        } else {
            $this->end(false, $msg);
        }
    }
    function edit($roles_id) {
        $param_id = $roles_id;
        $this->begin();
        $opctl = $this->app->model('roles');
        $menus = $this->app->model('menus');
        $sdf_roles = $opctl->dump($param_id);
        $this->pagedata['roles'] = $sdf_roles;
        $permissions = (array)unserialize($sdf_roles['permissions']);
        foreach ($permissions as $v) {
            #$sdf = $menus->dump($v);
            $menuname = $menus->getList('*', array(
                'menu_type' => 'menu',
                'permission' => $v
            ));
            foreach ($menuname as $val) {
                $menu_workground[] = $val['workground'];
            }
        }
        $menu_workground = array_unique((array)$menu_workground);
        $workgrounds = app::get('desktop')->model('menus')->getList('*', array(
            'menu_type' => 'workground',
            'disabled' => 'false',
            'display' => 'true'
        ));
        foreach ($workgrounds as $k => $v) {
            $workgrounds[$k]['permissions'] = $this->obj_roles->get_permission_per($v['menu_id'], $permissions);
            if (in_array($v['workground'], (array)$menu_workground)) {
                $workgrounds[$k]['checked'] = 1;
            }
        }
        $widgets = app::get('desktop')->model('menus')->getList('*', array(
            'menu_type' => 'widgets'
        ));
        foreach ($widgets as $key => $widget) {
            if (in_array($widget['addon'], $permissions)) $widgets[$key]['checked'] = true;
        }
        $this->pagedata['widgets'] = $widgets;
        $this->pagedata['workgrounds'] = $workgrounds;
        $this->pagedata['adminpanels'] = $this->obj_roles->get_adminpanel($param_id, $permissions);
        $this->page('users/edit_roles.html');
    }
}
