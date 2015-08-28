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

class b2c_ctl_admin_member extends desktop_controller
{
    public $pagelimit = 10;
    public $member_model;
    public function __construct($app)
    {
        parent::__construct($app);
        $this->member_model = $this->app->model('members');
    }
    public function index()
    {
        if ($this->has_permission('addmember')) {
            $custom_actions[] = array(
                'label' => ('添加会员') ,
                'icon'=>'fa-plus',
                'href' => 'index.php?app=b2c&ctl=admin_member&act=add_page',
            );
        }
        $actions_base['title'] = ('会员列表');
        $actions_base['actions'] = $custom_actions;
        $actions_base['use_buildin_set_tag'] = true;
        $actions_base['use_buildin_export'] = true;
        $actions_base['use_buildin_filter'] = true;
        $this->finder('b2c_mdl_members', $actions_base);
    }
    public function edit($member_id)
    {
        $app = app::get('b2c');
        $member_model = $app->model('members');
        $userPassport = vmc::singleton('b2c_user_passport');
        $userObject = vmc::singleton('b2c_user_object');
        if ($_POST) {
            $this->begin('index.php?app=b2c&ctl=admin_member&act=detail&p[0]='.$_POST['member_id']);
            $saveData['b2c_members'] = $_POST;
            $member_id = $_POST['member_id'];
            unset($saveData['b2c_members']['pam_members']);
            //密码修改
            if (($newpassword = $_POST['pam_account_repassword']) && trim($newpassword) != '') {
                $userPassport = vmc::singleton('b2c_user_passport');
                if (!$userPassport->reset_passport($member_id, $newpassword)) {
                    $this->end(false, '密码修改失败！');
                }
            }
            //操作日志
            if ($obj_operatorlogs = vmc::service('operatorlog.members')) {
                $olddata = app::get('b2c')->model('members')->dump($member_id);
            }

            if ($member_model->save($saveData['b2c_members'])) {
                //操作日志
                if ($obj_operatorlogs = vmc::service('operatorlog.members')) {
                    if (method_exists($obj_operatorlogs, 'detail_edit_log')) {
                        $newdata = app::get('b2c')->model('members')->dump($member_id);
                        $obj_operatorlogs->detail_edit_log($newdata['contact'], $olddata['contact']);
                    }
                }
                $this->end(true, '编辑成功');
            } else {
                $this->end(false, '编辑失败');
            }
        }
        $membersData = $userObject->get_members_data(array(
            'account' => '*',
            'members' => '*',
        ), $member_id, false);
        $member_lv = $app->model('member_lv');
        foreach ($member_lv->getMLevel() as $row) {
            $options[$row['member_lv_id']] = $row['name'];
        }
        $membersData['lv']['options'] = is_array($options) ? $options : array(
            ('请添加会员等级'),
        );
        $membersData['lv']['value'] = $membersData['members']['member_lv_id'];
        $this->pagedata['mem'] = $membersData;
        $this->pagedata['attr'] = $userPassport->get_signup_attr($member_id);
        $this->pagedata['member_id'] = $member_id;
        $this->display('admin/member/edit.html');
    }
    public function detail($member_id)
    {

        $userObject = vmc::singleton('b2c_user_object');
        $member_model = $this->app->model('members');
        //$member_model->touch_lv($member_id);
        $a_mem = $member_model->dump($member_id);
        $accountData = $userObject->get_members_data(array(
            'account' => 'login_account',
        ), $member_id);
        if (!$a_mem['contact']['name']) {
            $a_mem['contact']['name'] = $accountData['account']['local'];
        }
        if (!$a_mem['contact']['email']) {
            $a_mem['contact']['email'] = $accountData['account']['email'];
        }
        if (!$a_mem['contact']['phone']['mobile']) {
            $a_mem['contact']['phone']['mobile'] = $accountData['account']['mobile'];
        }
        $a_mem['integral'] = vmc::singleton('b2c_member_integral')->amount($member_id);
        $userPassport = vmc::singleton('b2c_user_passport');
        $this->pagedata['attr'] = $userPassport->get_signup_attr($member_id);
        $this->pagedata['mem'] = $a_mem;

        $this->pagedata['account'] = $accountData;
        $mdl_memlv = app::get('b2c')->model('member_lv');
        $this->pagedata['member_lv_info'] = $mdl_memlv->dump($a_mem['member_lv']['member_group_id']);
        $this->page('admin/member/detail.html');
    }
    /**
     * 会员相关订单
     */
    public function order($member_id, $page = 1, $pagelimit = 10)
    {
        $mdl_member = $this->app->model('members');
        $mdl_order = $this->app->model('orders');
        $orders = $mdl_order->getList('*', array(
            'member_id' => $member_id,
        ), $pagelimit * ($page - 1), $pagelimit);
        $count = $mdl_order->count(array(
            'member_id' => $member_id,
        ));
        foreach ($orders as $key => $row) {
            $orders[$key]['order_status_label'] = vmc::singleton('b2c_finder_orders')->column_orderstatus($row);
        }
        $this->pagedata['orders'] = $orders;
        $this->pagedata['pager'] = array(
            'current' => $page,
            'total' => ceil($count / $pagelimit) ,
            'link' => 'index.php?app=b2c&ctl=admin_member&act=order&p[0]='.$member_id.'&p[1]='.time() ,
            'token' => time(),
        );
        $this->display('admin/member/order.html');
    }

    /**
     * 会员相关站内信
     */

     public function member_msg($member_id,$page = 1,$pagelimit = 5)
     {

         $mdl_member_msg = $this->app->model('member_msg');
         $msgs = $mdl_member_msg->getList('*', array(
             'member_id' => $member_id,
             'msg_type'=>'normal',
         ), $pagelimit * ($page - 1), $pagelimit);
         $count = $mdl_member_msg->count(array(
             'member_id' => $member_id,
             'msg_type'=>'normal'
         ));

         $this->pagedata['msgs'] = $msgs;
         $this->pagedata['pager'] = array(
             'current' => $page,
             'total' => ceil($count / $pagelimit) ,
             'link' => 'index.php?app=b2c&ctl=admin_member&act=member_msg&p[0]='.$member_id.'&p[1]='.time() ,
             'token' => time(),
         );
         $this->display('admin/member/msg.html');
     }

    

    public function add_page()
    {
        $member_lv = $this->app->model('member_lv');
        foreach ($member_lv->getMLevel() as $row) {
            $options[$row['member_lv_id']] = $row['name'];
        }
        $a_mem['lv']['options'] = is_array($options) ? $options : array(
            ('请添加会员等级'),
        );
        $attr = vmc::singleton('b2c_user_passport')->get_signup_attr();
        $this->pagedata['attr'] = $attr;
        $this->pagedata['mem'] = $a_mem;
        if ($_GET['mini']) {
            return $this->display('admin/member/mini_new.html');
        }
        $this->page('admin/member/new.html');
    }

    public function namecheck()
    {
        $userPassport = vmc::singleton('b2c_user_passport');
        if (!$userPassport->check_signup_account($_POST['pam_account']['login_name'], $message)) {
            echo "<span class='text-danger'>$message</span>";
            exit;
        }
        echo "<span class='text-success'>可以使用</span>";
        exit;
    }
    //后台添加新会员
    public function add()
    {
        $params = $_POST;
        $this->begin('index.php?app=b2c&ctl=admin_member&act=index');
        $obj_u_passport = vmc::singleton('b2c_user_passport');
        if (!$obj_u_passport->check_signup($params, $msg)) {
            $this->end(false, $msg);
        }
        $member_sdf_data = $obj_u_passport->pre_signup_process($params);
        if ($member_id = $obj_u_passport->save_members($member_sdf_data,$msg)) {
            /*本站会员注册完成后做某些操作!*/
            foreach (vmc::servicelist('member.create_after') as $object) {
                $object->create_after($member_id);
            }
            $this->end(true, '添加成功');
        } else {
            $this->end(false, '添加失败,数据保存异常'.$msg);
        }
    }


}
