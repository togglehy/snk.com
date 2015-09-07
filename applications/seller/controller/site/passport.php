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

class seller_ctl_site_passport extends seller_frontpage
{
    public $title = '账户';

    public function __construct(&$app)
    {
        parent::__construct($app);
    }

    
    public function index()
    {		
        $this->check_login();
        $this->login();
    }

	public function login($forward)
    {
        $this->title = '商家登录';        
        $this->check_login();
        $this->set_forward($forward);
        $mdl_toauth_pam = app::get('toauth')->model('pam')->getList('*',array('status'=>'true'));
        $this->pagedata['toauth'] = $mdl_toauth_pam;
        $this->set_tmpl('passport');		
        $this->page('site/passport/login.html');
    }	
    public function post_login()
    {
        $login_url = $this->gen_url(array(
            'app' => 'seller',
            'ctl' => 'site_passport',
            'act' => 'login',
        ));
        //_POST过滤
        $params = utils::_filter_input($_POST);
        unset($_POST);
        $account_data = array(
            'login_account' => $params['uname'],
            'login_password' => $params['password'],
        );
        if (empty($params['vcode'])) {
            $this->splash('error', $login_url, '请输入验证码');
        }

        //尝试登陆
        $seller_id = vmc::singleton('pam_passport_site_basic')->login($account_data, $params['vcode'], $msg);
        if (!$seller_id) {
            $this->splash('error', $login_url,  $msg);
        }
		$mdl_sellers = $this->app->model('sellers');		
        $seller_data['order_num'] = $this->app->model('orders')->count(array(
            'seller_id' => $seller_id,
        ));

        //更新会员数据
        $mdl_sellers->update($seller_data, array(
            'seller_id' => $seller_id,
        ));
        //设置session
        $this->user_obj->set_seller_session($seller_id);
        //设置客户端cookie
        $this->bind_seller($seller_id);
        $forward = $params['forward'];
        if (!$forward) {
            $forward = $this->gen_url(array(
                'app' => 'seller',
                'ctl' => 'site_seller',
                'act' => 'index',
            ));
        }
        $this->splash('success', $forward, '登录成功');
    } //end function

	
	// 用户状态检测
	public function check_login()
    {
        if ($this->user_obj->is_login()) {
            $redirect = $this->gen_url(array(
                'app' => 'seller',
                'ctl' => 'site_seller',
                'act' => 'index',
            ));
            $this->splash('success', $redirect, '已经是登陆状态！');
        }
        return false;
    }  

	//注册页面
    public function apply($forward, $step=0)
    {	
		$query_str = utils::_filter_input($_GET);		
        $this->title = '注册成为会员';        
        $this->check_login(); //检查是否登录，如果已登录则直接跳转到会员中心
        $this->set_forward($forward); //设置登录成功后跳转		
        $this->set_tmpl('passport');
		if(empty($query_str['step']))
		{
			$tpl = 'account.html';	// 账号注册
		}else if($query_str['step'] == 1){
			$tpl = 'agreement.html'; // 协议	
		}else if($query_str['step'] == 2){
			$tpl = 'company.html'; // 
		}else if($query_str['step'] == 3){
			$tpl = 'company.html';
		}

        $this->page('site/passport/apply.account.html');
    }
    
    // 注册页面
    public function joinin($forward)
    {
        $this->title = '注册成为商家用户';        
        $this->check_login();
        $this->set_forward($forward); //设置登录成功后跳转        
        $this->set_tmpl('passport');
        $this->page('site/passport/signup.html');
    }

    //注册的时，检查用户名
    public function check_login_name()
    {
        if ($this->passport_obj->check_signup_account(trim($_POST['pam_account']['login_name']), $msg)) {
            if ($msg == 'mobile') { //用户名为手机号码
                $this->splash('success', null, array(
                    'is_mobile' => 'true',
                ));
            }
            $this->splash('success', null, '该账号可以使用');
        } else {
            $this->splash('error', null, $msg, true);
        }
    }
  
    public function create()
    {
        $params = $_POST;
        $forward = $params['forward'];
        if (!$forward) {
            $forward = $this->gen_url(array(
                'app' => 'seller',
                'ctl' => 'seller',
            )); 
			// 商家中心首页
        }
        unset($_POST['forward']);
        $signup_url = $this->gen_url(array(
            'app' => 'seller',
            'ctl' => 'site_passport',
            'act' => 'joinin',
            'args' => array(
                $forward,
            ),
        ));
        $login_type = $this->passport_obj->get_login_account_type($params['pam_account']['login_name']);
        if ($login_type == 'mobile' && !vmc::singleton('seller_user_vcode')->verify($params['vcode'], $params['pam_account']['login_name'], 'signup')) {
            $this->splash('error', $signup_url, '手机短信验证码不正确');
        } elseif ($login_type != 'mobile' && !base_vcode::verify('passport', $params['vcode'])) {
            $this->splash('error', $signup_url, '验证码不正确');
        }
        if (!$this->passport_obj->check_signup($params, $msg)) {
            $this->splash('error', $signup_url, $msg);
        }
        $seller_sdf_data = $this->passport_obj->pre_signup_process($params);

        if ($seller_id = $this->passport_obj->save_sellers($seller_sdf_data, $msg)) {
            $this->user_obj->set_seller_session($seller_id);
            $this->bind_seller($seller_id);
            /*本站会员注册完成后做某些操作!*/
            foreach (vmc::servicelist('seller.create_after') as $object) {
                $object->create_after($seller_id);
            }
            $this->splash('success', $forward, '注册成功');
        } else {
            $this->splash('error', $signup_url, '注册失败,会员数据保存异常');
        }
    }

    /**
     * 重置密码操作
     */
    public function reset_password($action){
        $this->title= '重置密码';
        if($action == 'doreset' ){
            $redirect_here = array('app' => 'seller','ctl' => 'site_passport','act' => 'reset_password');
            $params = $_POST;
            $forward = $params['forward'];
            if (!$forward) {
                $forward = $this->gen_url(array(
                    'app' => 'site',
                    'ctl' => 'index',
                ));
            }
            // if(!vmc::singleton('seller_user_passport')-is_exists_login_name($params['account'])){
            //     $this->splash('error', null, '未知账号!');
            // }
            if(empty($params['new_password'])){
                $this->splash('error',$redirect_here,'请输入新密码!');
            }
            if($params['new_password1'] != $params['new_password']){
                $this->splash('error',$redirect_here,'两次输入的密码不一致!');
            }

            if(!vmc::singleton('seller_user_vcode')->verify($params['vcode'], $params['account'], 'reset')){
                $this->splash('error',$redirect_here,'验证码错误！');
            }
            $p_m = app::get('pam')->model('sellers')->getRow('seller_id',array('login_account'=>$params['account']));
            if(empty($p_m['seller_id'])){
                $this->splash('error',$redirect_here,'账号异常!');
            }
            $seller_id = $p_m['seller_id'];
            if(!$this->passport_obj->reset_password($seller_id,$params['new_password'])){
                $this->splash('error',$redirect_here,'密码重置失败!');
            }
            $this->unset_seller();
            //设置session
            $this->user_obj->set_seller_session($seller_id);
            //设置客户端cookie
            $this->bind_seller($seller_id);

            $this->splash('success', $forward, '密码重置成功');


        }else{
            $this->set_tmpl('passport');
            $this->page('site/passport/reset_password.html');
        }
    }

    //发送身份识别验证码
    public function vcode(){
        $account = $_POST['account'];
        $login_type = $this->passport_obj->get_login_account_type($account);
        if($login_type != 'mobile' && $login_type!='email'){
            $this->splash('error', null, '请输入正确的手机或邮箱!');
        }
        if(!$this->passport_obj->is_exists_login_name($account)){
            $this->splash('error', null, '未知账号!');
        }
        if(!$vcode = vmc::singleton('seller_user_vcode')->set_vcode($account,'reset',$msg)){
            $this->splash('error', null, $msg);
        }
        //$data[$login_type] = $account;
        $data['vcode'] = $vcode;
        switch ($login_type) {
            case 'email':
                $send_flag = vmc::singleton('seller_user_vcode')->send_email('reset',(string)$account,$data);
                break;
            case 'mobile':
                $send_flag = vmc::singleton('seller_user_vcode')->send_sms('reset',(string)$account,$data);
                break;
        }
        if(!$send_flag){
            $this->splash('error', null, '发送失败');
        }
        $this->splash('success', null, '发送成功');

    }

    //发送邮件验证码
    public function send_vcode_email($type="activation")
    {
        $email = $_POST['email'];

        if (!$this->passport_obj->check_signup_account(trim($email), $msg)) {
            $this->splash('error', null, $msg);
        }
        if($msg != 'email'){
            $this->splash('error', null, '邮箱格式错误');
        }
        $uvcode_obj = vmc::singleton('seller_user_vcode');
        $vcode = $uvcode_obj->set_vcode($email, $type, $msg);
        if ($vcode) {
            //发送邮箱验证码
            $data['vcode'] = $vcode;
            if (!$uvcode_obj->send_email($type, (string) $email, $data)) {
                $this->splash('error', null, '邮件发送失败');
            }
        } else {
            $this->splash('failed', null, $msg);
        }
        $this->splash('success', null, '邮件已发送');
    }

    //短信发送验证码
    public function send_vcode_sms($type = 'signup')
    {

        $mobile = trim($_POST['mobile']);

        if (!$this->passport_obj->check_signup_account($mobile, $msg)) {
            $this->splash('error', null, $msg);
        }
        if($msg != 'mobile'){
            $this->splash('error', null, '错误的手机格式');
        }
        $uvcode_obj = vmc::singleton('seller_user_vcode');
        $vcode = $uvcode_obj->set_vcode($mobile, $type , $msg);
        if ($vcode) {
            //发送验证码 发送短信
            $data['vcode'] = $vcode;
            if (!$uvcode_obj->send_sms($type, (string) $mobile, $data)) {
                $this->splash('error', null, '短信发送失败');
            }
        } else {
            $this->splash('failed', null, $msg);
        }
        $this->splash('success', null, '短信已发送');
    }

    public function logout($forward)
    {
        $this->unset_seller();
        if (!$forward) {
            $forward = $this->gen_url(array(
                'app' => 'site',
                'ctl' => 'index',
                'full' => 1,
            ));
        }
        $this->splash('success', $forward, '退出登录成功');
    }

    private function unset_seller()
    {
        $auth = pam_auth::instance(pam_account::get_account_type($this->app->app_id));
        foreach (vmc::servicelist('passport') as $k => $passport) {
            $passport->loginout($auth);
        }
        $this->app->seller_id = 0;
        vmc::singleton('base_session')->set_cookie_expires(0);
        $this->cookie_path = vmc::base_url().'/';
        $this->set_cookie('UNAME', '', time() - 3600); //用户名
        $this->set_cookie('SELLER_IDENT', 0, time() - 3600);//会员ID
        foreach (vmc::servicelist('seller.logout_after') as $service) {
            $service->logout();
        }
    }
}
