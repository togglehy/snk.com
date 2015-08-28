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


class pam_passport_oauth_qq implements pam_interface_passport{

      function __construct(){
            kernel::single('base_session')->start();
            $this->init();
      }

      /**
       * 获取配置信息.
       *
       * @return array 返回配置信息数组
       */
      public function init()
      {
          if ($ret = app::get('pam')->getConf('passport.'.__CLASS__)) {
              return $ret;
          } else {
              $ret = $this->get_setting();
              $ret['passport_id']['value'] = __CLASS__;
              $ret['passport_name']['value'] = $this->get_name();
              $ret['mobile_passport_status']['value'] = 'true';
              $ret['site_passport_status']['value'] = 'true';
              $ret['passport_version']['value'] = '1.0';
              app::get('pam')->setConf('passport.'.__CLASS__, $ret);
              return $ret;
          }
      }

      function get_name(){
        return "QQ信任登录";
      }
      function get_image_url(){
            $config = $this->config;
            if($config['site_passport_status']['value'] === 'true') {
                $_url = $this->gen_auth_url();
                return "<a class='oauth-btn ob-qq fw' href='$_url' ><span>QQ信任登录</span></a>";
            }else{
                return false;
            }
    }

    function gen_auth_url(){

            $config = $this->config;
            $appid = $config['app_id']['value'];
            $appsecret = $config['app_secret']['value'];
            $auth = pam_auth::instance(pam_account::get_account_type('b2c'));
            $redirect_uri = $this->redirect_uri;
            if(!strpos($_SERVER['HTTP_REFERER'],'passport') && !$_SESSION['signup_next'])
            {
                 $_SESSION['signup_next'] = $_SERVER['HTTP_REFERER'];
             }
            $back_url = $_SESSION['signup_next'];
            unset($_SESSION['signup_next']);
            $state = base64_encode($back_url);
            $_url="https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=$appid&redirect_uri=$redirect_uri&state=$state";
            if(base_mobiledetect::is_mobile()){
                $_url.='&display=mobile';
            }
            return $_url;
    }

     function get_login_form($auth, $appid, $view, $ext_pagedata=array()){
        return "";
        if(true || base_mobiledetect::is_mobile()){
            kernel::single('b2c_frontpage')->redirect($this->gen_auth_url());
        }
    }

    function login($auth,&$usrdata){
        $config = $this->config;
        $appid = $config['app_id']['value'];
        $appsecret = $config['app_secret']['value'];
        $code = $_REQUEST['code'];
        $state = $_REQUEST['state'];
        $token_url = "https://graph.qq.com/oauth2.0/token";
        $query = array(
            'grant_type'=>'authorization_code',
            'client_id'=>$appid,
            'client_secret'=>$appsecret,
            'code'=>$code,
            'redirect_uri'=>$this->redirect_uri
        );

        $objHttp = kernel::single('base_httpclient');
        $response = $objHttp->post($token_url,$query);
        parse_str($response,$response_arr);
        $access_token = $response_arr['access_token'];

        if(!$access_token){
                $msg = "授权失败";
                return false;
        }

        $graph_url = "https://graph.qq.com/oauth2.0/me?access_token=$access_token";
        $qq_open  = $objHttp->get($graph_url);
        if (strpos($qq_open, "callback") !== false){
            $lpos = strpos($qq_open, "(");
            $rpos = strrpos($qq_open, ")");
            $qq_open  = substr($qq_open, $lpos + 1, $rpos - $lpos -1);
        }
        $qq_open  = json_decode($qq_open,1);
        $qq_openid = $qq_open['openid'];
        if(!$qq_openid){
                $msg = "获取授权用户信息失败";
                return false;
        }
        $graph_url2 = "https://graph.qq.com/user/get_user_info?access_token=$access_token&oauth_consumer_key=$appid&openid=$qq_openid&format=json";
        $qq_user_info = $objHttp->get($graph_url2);
        $qq_user_info_arr = json_decode($qq_user_info,1);

        $qq_user_info_arr['nickname'] = urldecode($qq_user_info_arr['nickname']);

        $usrdata['login_name']  = 'QQ_'.substr($qq_openid,5,10);

        //老用户简化登陆
        $account = app::get('pam')->model('account');
        $_account = $account->getRow('*',array('account_type'=>pam_account::get_account_type('b2c'),'login_name'=> $usrdata['login_name'],'disabled'=>'false'));
        if($_account && $_account['account_id']){
            $_SESSION['account']['login_name'] = $usrdata['login_name'];
            $_SESSION['account'][pam_account::get_account_type('b2c')] = $_account['account_id'];
            kernel::single('b2c_frontpage')->bind_member_new($_account);
        }else{
            $account_id = $this->save_login_data($usrdata['login_name'],$qq_user_info);
            if($account_id){
                $_SESSION['account']['login_name'] = $usrdata['login_name'];
                $_SESSION['account'][pam_account::get_account_type('b2c')] = $account_id;
                $memberinfo = array(
                        'name' => $qq_user_info_arr['nickname'],
                        'trust_name'=>$qq_user_info_arr['nickname'].'-'.$qq_openid,
                        'email' =>($qq_openid.'@open-qq.com'),
                        'addr' =>''
                );
                if(!$this->update_member($memberinfo)){
                    $usrdata['log_data'] = "登录失败";
                    unset($usrdata['login_name']);
                    kernel::single('site_controller')->splash('error',false,"登录失败！请稍后重试。");
                }
            }else{
                $usrdata['log_data'] = "登录失败";
                unset($usrdata['login_name']);
                kernel::single('site_controller')->splash('error',false,"登录失败！请稍后重试。");
            }
        }

        if($state){
            $url = base64_decode($state);
        }else{
             if(base_mobiledetect::is_mobile()){
                $url = app::get('site')->router()->gen_url(array('app'=>'b2c','ctl'=>'wap_member','act'=>'index'));
             }else{
                $url = app::get('site')->router()->gen_url(array('app'=>'b2c','ctl'=>'site_member','act'=>'index'));
             }
        }
        if(false && !base_mobiledetect::is_mobile() && $memberinfo['state']<1){
            $url = app::get('site')->router()->gen_url(array('app'=>'b2c','ctl'=>'site_passport','act'=>'upstate_with_mobile','args01'=>$url));
            kernel::single('site_controller')->splash('success',$url,"登录成功,请设置手机登录");
        }
        if($_REQUEST['is_app']){
           echo json_encode(array('status'=>'success','member_id'=>$account_id));
           exit;
        }
        kernel::single('b2c_frontpage')->redirect($url);
    }

    function login_app($auth,&$usrdata){
        if($_SESSION['account'][pam_account::get_account_type('b2c')]){
            //echo json_encode(array('status'=>'success','member_id'=>$_SESSION['account'][pam_account::get_account_type('b2c')],'msg'=>'您已经是登录状态，不需要重新登录'));
            kernel::single('site_controller')->splash('error',false,"您已经是登录状态，不需要重新登录");
            exit;
        }
		if(!$_REQUEST['id'] || !$_REQUEST['nickname'] || !$_REQUEST['code']){
			//echo json_encode(array('status'=>'faile','msg'=>'参数错误'));
            kernel::single('site_controller')->splash('error',false,"参数错误");
			exit;
		}
        $id = urldecode($_REQUEST['id']);
        $user['nickname'] = urldecode($_REQUEST['nickname']);
        $usrdata['login_name']  = 'QQ_'.substr($id,5,10);

        //老用户简化登陆
        $account = app::get('pam')->model('account');
        $_account = $account->getRow('*',array('account_type'=>pam_account::get_account_type('b2c'),'login_name'=> $usrdata['login_name'],'disabled'=>'false'));
        if($_account && $_account['account_id']){
            $_SESSION['account']['login_name'] = $usrdata['login_name'];
            $_SESSION['account'][pam_account::get_account_type('b2c')] = $_account['account_id'];
            kernel::single('b2c_frontpage')->bind_member_new($_account);
            $return = json_encode(array('status'=>'success','member_id'=>$_account['account_id']));
            echo $return;

            //手机接口通信日志
            $arr['api']       = $_SERVER['REQUEST_URI'];
            $arr['ip']        = $_SERVER['REMOTE_ADDR'];
            $arr['member_id'] = $_SESSION['account']['member'];
            $arr['agent']     = $_SERVER['HTTP_USER_AGENT'];
            $arr['add_time']  = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);
            $arr['param']     = empty($_POST)?'':json_encode($_POST);
            $arr['server']    = $_SERVER['SERVER_ADDR'];
            $arr['return']    = $return;

            $api_url = 'http://115.29.209.163:8080/api/write_api_error_log.php';

            $http = kernel::single('base_httpclient');
            $http->set_timeout(1);
            $result = $http->post($api_url,$arr);
            exit;
        }

        $account_id = $this->save_login_data($usrdata['login_name'],$user);
        if($account_id){
		   $_SESSION['account'][pam_account::get_account_type('b2c')] = $account_id;
           $_SESSION['account']['login_name'] = $usrdata['login_name'];
		   $memberinfo = array(
					'name' => $user['nickname'],
					'trust_name'=>$user['nickname'].'-'.$id,
					'email' =>($id.'@open-qq.com'),
					'addr' =>''
			);
		   if(!$this->update_member($memberinfo)){
			   //echo json_encode(array('status'=>'error member'));
               kernel::single('site_controller')->splash('error',false,"error member");
			   exit;
		   }
	   }else{
		   //echo json_encode(array('status'=>'error account'));
           kernel::single('site_controller')->splash('error',false,"error account");
		   exit;
	   }

		if($_REQUEST['is_app']){
			echo json_encode(array('status'=>'success','member_id'=>$account_id));
			exit;
		}
    }

    function save_login_data($login_name,$authdata){
        $account = app::get('pam')->model('account');
        $auth_model = app::get('pam')->model('auth');

        $new_account_data = array(
            'login_name' => $login_name,
            'login_password' => md5(time()),
            'account_type'=>'member',
            'createtime'=>time(),
        );
        $new_account_id = $account->insert($new_account_data);
        $auth_data = array(
            'account_id'=>$new_account_id,
            'module_uid'=>$login_name,
            'module'=>'pam_passport_qq',
            'data' =>$authdata
        );
        $auth_model->insert($auth_data);

        //新会员注册优惠
        foreach(kernel::servicelist('b2c_register_after') as $object) {
            $object->registerActive($new_account_id);
        }

        return $new_account_id;
    }

    private function update_member(&$member_info){
        $member_id = $_SESSION['account'][pam_account::get_account_type('b2c')];
        if($member_id){
            $obj_mem = app::get('b2c')->model('members');
            $data = array(
                        'member_id' => $member_id,
                        'member_lv_id' => 1,
                        'name'=> $member_info['name'],
                        'regtime'=>time()
            );
            if($obj_mem->insert($data)){
                 kernel::single('b2c_frontpage')->bind_member($member_id);
                 return true;
            }else{
                return false;
            }
        }else{
                return false;
        }
    }

   function loginout($auth,$backurl="index.php"){
        unset($_SESSION['account'][$this->type]);
        unset($_SESSION['account']['login_name']);
        unset($_SESSION['last_error']);
        unset($_SESSION['signup_next']);
        setcookie('_HN','',time()-3600,'/','.hitao.com');
    }

    function get_data(){
    }

    function get_id(){
    }

    function get_expired(){
    }



    function get_config(){
        $ret = app::get('pam')->getConf('passport.'.__CLASS__);
        if($ret && isset($ret['shopadmin_passport_status']['value']) && isset($ret['site_passport_status']['value'])){
            return $ret;
        }else{
            $ret = $this->get_setting();
            $ret['passport_id']['value'] = __CLASS__;
            $ret['passport_name']['value'] = $this->get_name();
            $ret['shopadmin_passport_status']['value'] = 'false';
            $ret['site_passport_status']['value'] = 'false';
            $ret['passport_version']['value'] = '2.0';
            app::get('pam')->setConf('passport.'.__CLASS__,$ret);
            return $ret;
        }
    }

    function set_config(&$config){
        $save = app::get('pam')->getConf('passport.'.__CLASS__);
        if(count($config))
            foreach($config as $key=>$value){
                if(!in_array($key,array_keys($save))) continue;
                $save[$key]['value'] = $value;
            }
        return app::get('pam')->setConf('passport.'.__CLASS__,$save);
    }

    function get_setting(){
        return array(
            'passport_id'=>array('label'=>app::get('pam')->_('APPID'),'type'=>'text','editable'=>false),
            'app_id'=>array('label'=>app::get('pam')->_('APPID'),'type'=>'text'),
            'app_secret'=>array('label'=>app::get('pam')->_('APPSECRET'),'type'=>'text'),
            'passport_name'=>array('label'=>app::get('pam')->_('qq信任登录'),'type'=>'text','editable'=>false),
            'shopadmin_passport_status'=>array('label'=>app::get('pam')->_('后台开启'),'type'=>'bool','value'=>false,'editable'=>false),
            'site_passport_status'=>array('label'=>app::get('pam')->_('前台开启'),'type'=>'bool',),
            'passport_version'=>array('label'=>app::get('pam')->_('版本'),'type'=>'text','editable'=>false),
        );
    }

}
