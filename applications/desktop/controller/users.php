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


class desktop_ctl_users extends desktop_controller {

    public function __construct($app) {
        parent::__construct($app);
    }
    function index() {
        $this->finder('desktop_mdl_users', array(
            'title' => '操作员管理' ,
            'use_buildin_recycle' => true,
            'actions' => array(
                array(
                    'icon'=>'fa-plus',
                    'label' => '添加操作员' ,
                    'href' => 'index.php?ctl=users&act=addnew',
                ) ,
            ) ,
        ));
    }
    function addnew() {
        $mdl_roles = $this->app->model('roles');
        $users = $this->app->model('users');
        if ($_POST) {
            if (!$_POST['super']) $_POST['super'] = 0;
            $this->begin('index.php?app=desktop&ctl=users&act=index');
            if ($users->validate($_POST, $msg)) {
                if ($_POST['super'] == 0 && (!$_POST['role'])) {
                    $this->end(false, '请至少选择一个角色');
                } elseif ($_POST['super'] == 0 && ($_POST['role'])) {
                    foreach ($_POST['role'] as $roles) $_POST['roles'][] = array(
                        'role_id' => $roles
                    );
                }
                $_POST['pam_account']['createtime'] = time();
                $use_pass_data['login_name'] = $_POST['pam_account']['login_name'];
                $use_pass_data['createtime'] = $_POST['pam_account']['createtime'];
                $_POST['pam_account']['login_password'] = pam_encrypt::get_encrypted_password($_POST['pam_account']['login_password'], pam_account::get_account_type($this->app->app_id) , $use_pass_data);
                $_POST['pam_account']['account_type'] = pam_account::get_account_type($this->app->app_id);
                if ($users->save($_POST)) {
                    foreach (vmc::servicelist('desktop_useradd') as $key => $service) {
                        if ($service instanceof desktop_interface_useradd) {
                            $service->useradd($_POST);
                        }
                    }
                    if ($_POST['super'] == 0) { //是超管就不保存
                        $this->save_ground($_POST);
                    }
                    $this->end(true, '保存成功');
                } else {
                    $this->end(false, '保存失败');
                }
            } else {
                $this->end(false, __($msg));
            }
        } else {
            $roles = $mdl_roles->getList('*');
            $this->pagedata['roles'] = $roles;
            $this->display('users/users_add.html');
        }
    }
    ####修改密码
    function chkpassword() {
        $this->begin('index.php?app=desktop&ctl=users&act=index');
        $users = $this->app->model('users');
        $sdf = $users->dump($_POST['user_id'], '*', array(
            ':account@pam' => array(
                '*'
            ) ,
            'roles' => array(
                '*'
            )
        ));
        $old_password = $sdf['account']['login_password'];
        $super_row = $users->getList('user_id', array(
            'super' => '1'
        ));
        $filter['account_id'] = $super_row[0]['user_id'];
        $filter['account_type'] = pam_account::get_account_type($this->app->app_id);
        $super_data = $users->dump($filter['account_id'], '*', array(
            ':account@pam' => array(
                '*'
            )
        ));
        $use_pass_data['login_name'] = $super_data['account']['login_name'];
        $use_pass_data['createtime'] = $super_data['account']['createtime'];
        $filter['login_password'] = pam_encrypt::get_encrypted_password(trim($_POST['old_login_password']) , pam_account::get_account_type($this->app->app_id) , $use_pass_data);
        $pass_row = app::get('pam')->model('account')->getList('account_id', $filter);
        if (!$pass_row) {
            $this->end(false, '超级管理员密码不正确');
        } elseif (!(strlen($_POST['new_login_password']) >= 6 && preg_match("/\d+/", $_POST['new_login_password']) && preg_match("/[a-zA-Z]+/", $_POST['new_login_password']))) {
            $this->end(false, '密码必须同时包含字母及数字且长度不能小于6!');
        } elseif ($sdf['account']['login_name'] == $_POST['new_login_password']) {
            $this->end(false, '用户名与密码不能相同');
        } elseif ($_POST['new_login_password'] !== $_POST['pam_account']['login_password']) { // //修改0000!=00000为true的问题@lujy
            $this->end(false, '两次密码不一致');
        } else {
            $_POST['pam_account']['account_id'] = $_POST['user_id'];
            $use_pass_data['login_name'] = $sdf['account']['login_name'];
            $use_pass_data['createtime'] = $sdf['account']['createtime'];
            $_POST['pam_account']['login_password'] = pam_encrypt::get_encrypted_password(trim($_POST['new_login_password']) , pam_account::get_account_type($this->app->app_id) , $use_pass_data);
            $users->save($_POST);
            $this->end(true, '密码修改成功');
        }
    }
    /**
     * This is method saveUser
     * 添加编辑
     * @return mixed This is the return value description
     *
     */
    function saveUser() {
        $this->begin('index.php?app=desktop&ctl=users&act=index');
        $users = $this->app->model('users');
        $roles = $this->app->model('roles');
        $workgroup = $roles->getList('*');
        $param_id = $_POST['account_id'];
        if (!$param_id) $this->end(false, '编辑失败,参数丢失！');
        $sdf_users = $users->dump($param_id);
        if (!$sdf_users) $this->end(false, '编辑失败,参数错误！');
        //if($sdf_users['super']==1) $this->end(false, '不能编辑超级管理员！');
        if (!$_POST['super']) $_POST['super'] = 0;
        $_POST['pam_account']['account_id'] = $param_id;
        if ($_POST['super'] != 0 || $_POST['role']) {
            if(!$_POST['role']){
                $_POST['role'] = array();
            }else{
                foreach ($_POST['role'] as $roles) {
                    $_POST['roles'][] = array(
                        'role_id' => $roles
                    );
                }
            }
            $a = $users->save($_POST);
            $users->save_per($_POST);
            #↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓记录管理员操作日志@lujy↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
            if ($obj_operatorlogs = vmc::service('operatorlog.system')) {
                if (method_exists($obj_operatorlogs, 'adminusers_log')) {
                    $newrole = $this->app->model('hasrole')->getList('*', array(
                        'user_id' => $param_id
                    ));
                    $newdata = $this->app->model('users')->dump($param_id);
                    $newdata['role'][$newrole[0]['role_id']] = $newrole[0]['role_id'];
                    $sdf_users['role'] = $_POST['role'];
                    $obj_operatorlogs->adminusers_log($newdata, $sdf_users);
                }
            }
            #↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑记录管理员操作日志@lujy↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
            $this->end(true, '编辑成功！');
        } else {
            $this->end(false, '请至少选择一个角色！');
        }
    }
    /**
     * This is method edit
     * 添加编辑
     * @return mixed This is the return value description
     *
     */
    function edit($param_id) {
        $users = $this->app->model('users');
        $roles = $this->app->model('roles');
        $workgroup = $roles->getList('*');
        $user = vmc::singleton('desktop_user');
        $sdf_users = $users->dump($param_id);
        if (empty($sdf_users)) echo 'ERROR';
        $hasrole = $this->app->model('hasrole');
        foreach ($workgroup as $key => $group) {
            $rolesData = $hasrole->getList('*', array(
                'user_id' => $param_id,
                'role_id' => $group['role_id']
            ));
            if ($rolesData) {
                $check_id[] = $group['role_id'];
                $workgroup[$key]['checked'] = "true";
            } else {
                $workgroup[$key]['checked'] = "false";
            }
        }
        $this->pagedata['roles'] = $workgroup;
        $this->pagedata['account_id'] = $param_id;
        $this->pagedata['user'] = $sdf_users;

        if($user->user_id === $param_id){
            $this->redirect('index.php?ctl=profile&act=index');
        }
        if (!$sdf_users['super']) {
            $this->pagedata['per'] = $users->detail_per($check_id, $param_id);
        }
        $this->page('users/users_detail.html');
    }
    //获取工作组细分
    function detail_ground() {
        $role_id = $_POST['name'];
        $roles = $this->app->model('roles');
        $menus = $this->app->model('menus');
        $check_id = json_decode($_POST['checkedName']);
        $aPermission = array();
        if (!$check_id) {
            echo '';
            exit;
        }
        foreach ($check_id as $val) {
            $result = $roles->dump($val);
            $data = unserialize($result['workground']);
            foreach ((array)$data as $row) {
                $aPermission[] = $row;
            }
        }
        $aPermission = array_unique($aPermission);
        if (!$aPermission) {
            echo '';
            exit;
        }
        $addonmethod = array();
        foreach ((array)$aPermission as $val) {
            $sdf = $menus->dump(array(
                'menu_type' => 'permission',
                'permission' => $val
            ));
            $addon = unserialize($sdf['addon']);
            if ($addon['show'] && $addon['save']) { //如果存在控制
                if (!in_array($addon['show'], $addonmethod)) {
                    $access = explode(':', $addon['show']);
                    $classname = $access[0];
                    $method = $access[1];
                    $obj = vmc::singleton($classname);
                    $html.= $obj->$method() . "<br />";
                }
                $addonmethod[] = $addon['show'];
            } else {
                echo '';
            }
        }
        echo $html;
    }
    //保存工作组细分
    function save_ground($aData) {
        $workgrounds = $aData['role'];
        $menus = $this->app->model('menus');
        $roles = $this->app->model('roles');
        foreach ($workgrounds as $val) {
            $result = $roles->dump($val);
            $data = unserialize($result['workground']);
            foreach ((array)$data as $row) {
                $aPermission[] = $row;
            }
        }
        $aPermission = array_unique($aPermission);
        if ($aPermission) {
            $addonmethod = array();
            foreach ((array)$aPermission as $key => $val) {
                $sdf = $menus->dump(array(
                    'menu_type' => 'permission',
                    'permission' => $val
                ));
                $addon = unserialize($sdf['addon']);
                if ($addon['show'] && $addon['save']) { //如果存在控制
                    if (!in_array($addon['save'], $addonmethod)) {
                        $access = explode(':', $addon['save']);
                        $classname = $access[0];
                        $method = $access[1];
                        $obj = vmc::singleton($classname);
                        $obj->$method($aData['user_id'], $aData);
                    }
                    $addonmethod[] = $addon['save'];
                }
            }
        }
    }
}
