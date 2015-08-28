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


class desktop_ctl_profile extends desktop_controller {
    function index() {
        $account_id = $this->user->get_id();
        $users = $this->app->model('users');
        $sdf = $users->dump($account_id, '*', array(
            ':account@pam' => array(
                '*'
            ) ,
            'roles' => array(
                '*'
            )
        ));
        $this->pagedata['profile'] = $sdf;
        $this->page('users/profile.html');
    }
    ##非超级管理员修改密码
    function save_profile() {
        $this->begin();
        $account_id = $this->user->get_id();
        $users = $this->app->model('users');
        $sdf = $users->dump($account_id, '*', array(
            ':account@pam' => array(
                '*'
            ) ,
            'roles' => array(
                '*'
            )
        ));
        $save_data['user_id'] = $save_data['pam_account']['account_id'] = $account_id;
        if ($_POST['reset_password'] == '1') {
            $old_password = $sdf['account']['login_password'];
            $filter['account_id'] = $account_id;
            $filter['account_type'] = pam_account::get_account_type($this->app->app_id);
            $use_pass_data['login_name'] = $sdf['account']['login_name'];
            $use_pass_data['createtime'] = $sdf['account']['createtime'];
            $filter['login_password'] = pam_encrypt::get_encrypted_password(trim($_POST['rp']['old_login_password']) , pam_account::get_account_type($this->app->app_id) , $use_pass_data);
            $pass_row = app::get('pam')->model('account')->getList('account_id', $filter);
            if (!$pass_row) {
                $this->end(false, '原始密码不正确');
            } elseif (!(strlen($_POST['rp']['new_login_password']) >= 6 && preg_match("/\d+/", $_POST['rp']['new_login_password']) && preg_match("/[a-zA-Z]+/", $_POST['rp']['new_login_password']))) {
                $this->end(false, '密码必须同时包含字母及数字且长度不能小于6!');
            } elseif ($_POST['rp']['new_login_password'] != $_POST['rp'][':account@pam']['login_password']) {
                $this->end(false, '两次密码不一致');
            } elseif ($sdf['account']['login_name'] == $_POST['rp']['new_login_password']) {
                $this->end(false, '用户名与密码不能相同');
            } else {
                $save_data['pam_account']['login_password'] = pam_encrypt::get_encrypted_password(trim($_POST['rp']['new_login_password']) , pam_account::get_account_type($this->app->app_id) , $use_pass_data);

            }
        }
        $save_data['op_no'] = $_POST['op_no'];
        $save_data['name'] = $_POST['name'];
        $save_data['avatar'] = $_POST['avatar'];
        $flag = $users->save($save_data);

        $this->end($flag, $flag ? '保存成功' : '保存失败');
    }
}
