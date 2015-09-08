<?php
/*
 * 登录/注册/找回密码 手机号发送验证码,验证码存储，验证
 * */
class seller_user_vcode {

    var $ttl = 86400;
	var $_expire = 10;
	var $_valid_limit = 26;

    public function __construct(&$app) {
        $this->app = $app;
        vmc::singleton('base_session')->start();
    }
    //随机取6位字符数
    public function randomkeys($length) {
        $key = '';
        $pattern = '1234567890'; //字符池
        for ($i = 0;$i < $length;$i++) {
            $key.= $pattern{mt_rand(0, 9) }; //生成php随机数

        }
        return $key;
    }
    /*
     * 将验证码存储到缓存中(没开启缓存则存储到kv) 短信验证码或者邮箱验证码
     *
     * @params $account 手机号码 | 邮箱
     * @params $type  signup 注册 | activation登录激活 | forgot 忘记密码 | reset 会员中心修改手机或者邮箱发送验证码
     * */
    public function set_vcode($account, $type = 'signup', &$msg) {
        $vcodeData = $this->get_vcode($account, $type);		
        if ($vcodeData && !strpos($account, '@') && ENVIRONMENT!='DEVELOPMENT') {
            if ($vcodeData['createtime'] == date('Ymd') && $vcodeData['count'] == $this->_valid_limit) {
                $msg = "每天只能进行{$this->_valid_limit}次验证";
                return false;
            }
            $left_time = (time() - $vcodeData['lastmodify']);
            if ($left_time < $this->_expire) {
                $msg = '请'. ( $this->_expire - $left_time ) .'秒后重试';
                return false;
            }
            if ($vcodeData['createtime'] != date('Ymd')) {
                $vcodeData['count'] = 0;
            }
        }
        $vcode = $this->randomkeys(6);
        $vcodeData['account'] = $account;
        $vcodeData['vcode'] = $vcode;
        $vcodeData['count']+= 1;
        $vcodeData['createtime'] = date('Ymd');
        $vcodeData['lastmodify'] = time();	
        $key = $this->get_vcode_key($account, $type);		
        if (WITHOUT_CACHE === true) {
            base_kvstore::instance('vcode/seller')->store($key, $vcodeData, $this->ttl);
        } else {
            cacheobject::set($key, $vcodeData, $this->ttl + time());
        }
        return $vcode;
    }
    /*
     *
     * $vcode=>array(
     *   'account' => '13918087543',
     *   'vcode' => '123456',//验证码
     *   'count' => '4',
     *   'createtime'=> date('Ymd');
     *   'lastmodify'=> time(),
     * );
     *
     * */
    public function get_vcode($account, $type = 'signup') {
        $key = $this->get_vcode_key($account, $type);
        if (WITHOUT_CACHE === true) {
            base_kvstore::instance('vcode/seller')->fetch($key, $vcode);
        } else {
            cacheobject::get($key, $vcode);
        }
        return $vcode;
    }

    /*
     * 删除验证码（非物理删除，重新生成一个验证码）
     * */
    public function delete_vcode($account, $type, $vcodeData) {
        $vcode = $this->randomkeys(6);
        $vcodeData['vcode'] = $vcode;
        $key = $this->get_vcode_key($account, $type);
        if (WITHOUT_CACHE === true) {
            base_kvstore::instance('vcode/account')->store($key, $vcodeData, $this->ttl);
        } else {
            cacheobject::set($key, $vcodeData, $this->ttl + time());
        }
        return $vcodeData;
    }

    public function send_sms($type ='signup', $mobile, $tmpl_data)
	{
        if (!$action_name = $this->get_action_name($type)) return false;
        $sender_class = 'b2c_messenger_sms';
        $tmpl_name = 'messenger:'.$sender_class.'/' . $action_name;
		$stage = vmc::singleton('b2c_messenger_stage');		
        return $stage->action($tmpl_name, $tmpl_data, array('mobile'=>$mobile));
    }

    public function send_email($type = 'activation', $email, $tmpl_data) {
        if (!$action_name = $this->get_action_name($type)) return false;
        $sender_class = 'seller_messenger_email';
        $tmpl_name = 'messenger:'.$sender_class.'/' . $action_name;

        return vmc::singleton('seller_messenger_stage')->action($tmpl_name, $tmpl_data, array('email'=>$email));
    }

    public function get_action_name($sendtype) {
        $tmpl = false;
        switch ($sendtype) {
            case 'activation': //激活
            case 'reset': //重置时的手机号或者邮箱
                $tmpl = 'account-member';
            break;
            case 'signup': //手机注册
                $tmpl = 'account-signup';
            break;
        }
        return $tmpl;
    }
    public function get_vcode_key($account, $type = 'signup') {
        return md5('seller' . $account . $type); // 商家
    }
    //手机激活验证
    public function mobile_login_verify($vcode, $mobile, $type) {
        if ('mobile'!= vmc::singleton('seller_user_passport')->get_login_account_type($mobile)) {
            return true;
        }
        if ($this->verify($vcode, $mobile, $type)) {
            app::get('pam')->model('seller')->update(array(
                'disabled' => 'false'
            ) , array(
                'login_account' => $mobile
            ));
        } else {
            return false;
        }
        return true;
    }

    public function verify($vcode, $send, $type) {
        if (empty($vcode)) return false;		
        $vcodeData = $this->get_vcode((string)$send, $type);		
        if ($vcodeData && $vcodeData['vcode'] == $vcode) {
            $data = $this->delete_vcode($vcodeData['account'], $type, $vcodeData);
            return $data;
        } else {
            return false;
        }
    }
}
