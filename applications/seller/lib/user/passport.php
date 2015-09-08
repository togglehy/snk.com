<?php

/**
 * 登录注册流程/逻辑处理类.
 */
class seller_user_passport
{
    public function __construct(&$app)
    {
        $this->app = $app;
        $this->user_obj = vmc::singleton('seller_user_object');
        vmc::singleton('base_session')->start();
    }

    /*
     * 获取前台登录用户类型(用户名,邮箱，手机号码)
     *
     * @params $login_account 登录账号
     * @return $account_type
     * */
    public function get_login_account_type($login_account)
    {
        $login_type = 'local';
        if ($login_account && strpos($login_account, '@')) {
            $login_type = 'email';

            return $login_type;
        }
        if (preg_match('/^1[34578]{1}[0-9]{9}$/', $login_account)) {
            $login_type = 'mobile';

            return $login_type;
        }

        return $login_type;
    } //end function
    /*
     * 检查注册账号合法性
     * */
    public function check_signup_account($login_name, &$msg)
    {
        if (empty($login_name)) {
            $msg = ('请输入用户名');
            return false;
        }

        //获取到注册时账号类型
        // $login_type = $this->get_login_account_type($login_name);
		$login_type = 'mobile'; // 只允许手机登录、注册

        switch ($login_type) {
            case 'local':
                if (strlen(trim($login_name)) < 4) {
                    $msg = $this->app->_('登录账号最少4个字符');

                    return false;
                } elseif (strlen($login_name) > 100) {
                    $msg = $this->app->_('登录账号过长，请换一个重试');
                }
                if (is_numeric($login_name)) {
                    $msg = $this->app->_('登录账号不能全为数字');

                    return false;
                }
                if (!preg_match('/^[^\x00-\x2d^\x2f^\x3a-\x3f]+$/i', trim($login_name))) {
                    $msg = $this->app->_('该登录账号包含非法字符');

                    return false;
                }
                $exists_msg = $this->app->_('用户名'.$login_name.'已经被占用，请换一个重试');
            break;
            case 'email':
                if (!preg_match('/^(?:[a-z\d]+[_\-\+\.]?)*[a-z\d]+@(?:([a-z\d]+\-?)*[a-z\d]+\.)+([a-z]{2,})+$/i', trim($login_name))) {
                    $msg = $this->app->_('邮件格式不正确');

                    return false;
                }
                $exists_msg = $this->app->_('该邮箱已被注册，请更换一个');
            break;
            case 'mobile':
                $exists_msg = $this->app->_('该手机号已被注册，请更换一个');
            break;
        }
        //判断账号是否存在
        if ($this->is_exists_login_name($login_name)) {
            $msg = $exists_msg;

            return false;
        }
        $msg = $login_type;

        return true;
    } //end function
    /*
     * 判断前台用户名是否存在
     * */
    public function is_exists_login_name($login_account)
    {
        if (empty($login_account)) {
            return false;
        }
        $pam_sellers_model = app::get('pam')->model('sellers');
        $flag = $pam_sellers_model->getList('seller_id', array(
            'login_account' => trim($login_account),
        ));

        return $flag ? true : false;
    }
    /**
     *组织注册需要的数据.
     */
    public function pre_signup_process($data)
    {
		
        if ($data['pam_account']) {
            $accountData = $this->pre_account_signup_process($data['pam_account']);
        }
		      
        //$data['currency'] = $arrDefCurrency['cur_code'];
        $data['reg_ip'] = base_request::get_remote_addr();
        $data['regtime'] = time();
        //--防止恶意修改
        foreach ($data as $key => $val) {
            if (strpos($key, 'box:') !== false) {
                $aTmp = explode('box:', $key);
                $data[$aTmp[1]] = serialize($val);
            }
        }
        $arr_colunm = array(
            'regtime',
            'reg_ip',
            'currency',
            'contact',
            'profile',
            // 'seller_lv',
        );
		
        if ($accountData['login_type'] == 'mobile') {
            $data['contact']['phone']['mobile'] = $accountData['login_account'];
        }
        if ($accountData['login_type'] == 'email') {
            $data['contact']['email'] = $accountData['login_account'];
        }
        //---end
        $return = array(
            'pam_account' => $accountData,
            'seller_sellers' => $data,
        );
        $return = vmc::singleton('seller_site_filter')->check_input($return);		
        return $return;
		
    }

    /**
     * 检查会员注册数据合法性.
     */
    public function check_signup($data, &$msg)
    {
        //检查注册账号合法性
        if (!$this->check_signup_account(trim($data['pam_account']['login_name']), $msg)) {
            return false;
        }
        $password = $data['pam_account']['login_password'];
        $password_cfm = $data['pam_account']['psw_confirm'];
        //检查密码合法性
        if (strlen($password) < 6) {
            $msg = $this->app->_('密码长度不能小于6位');

            return false;
        }
        if (strlen($password) > 20) {
            $msg = $this->app->_('密码长度不能大于20位');

            return false;
        }
        if ($password != $password_cfm) {
            $msg = $this->app->_('两次输入的密码不一致');

            return false;
        }

        return true;
    }//end function


    /**
     * 注册pam_sellers 表数据结构.
     */
    public function pre_account_signup_process($accountData, $password_account = null)
    {
        $login_account = strtolower($accountData['login_name']);
        $password_account = $password_account ? $password_account : $login_account;
        $use_pass_data['login_name'] = $password_account;
        $use_pass_data['createtime'] = time();
		
        $login_password = pam_encrypt::get_encrypted_password(trim($accountData['login_password']), 'seller', $use_pass_data);
        $login_type = $this->get_login_account_type($login_account);
        $account = array(
            'login_type' => $login_type,
            'login_account' => $login_account,
            'login_password' => $login_password,
            'password_account' => $password_account, //登录密码加密账号
            'disabled' => $login_type == 'email' ? 'true' : 'false', //邮箱需要到会员中心进行验证
            'createtime' => $use_pass_data['createtime'],
        );

        return $account;
    }

    /*
     * 修改密码
     *
     * @params $seller_id int
     * @params $data array
     * */
    public function save_security($seller_id, $data, &$msg)
    {
        $pamsellersModel = app::get('pam')->model('sellers');
        $pamData = $pamsellersModel->getList('login_password,password_account,createtime', array(
            'seller_id' => $seller_id,
        ));
        $use_pass_data['login_name'] = $pamData[0]['password_account'];
        $use_pass_data['createtime'] = $pamData[0]['createtime'];
        $login_password = pam_encrypt::get_encrypted_password(trim($data['old_passwd']), 'seller', $use_pass_data);
        if ($login_password !== $pamData[0]['login_password']) {
            $msg = ('输入的旧密码与原密码不符！');

            return false;
        }
        if ($this->reset_passport($seller_id, trim($data['passwd']))) {
            $msg = ('密码修改成功');
        } else {
            $msg = ('密码修改失败！');

            return false;
        }
        $arr_colunms = $this->user_obj->get_pam_data('*', $seller_id);
        $aData['email'] = $arr_colunms['email'] ? $arr_colunms['email']['login_account'] : '';
        $aData['uname'] = $arr_colunms['local'] ? $arr_colunms['local']['login_account'] : $arr_colunms['mobile'] ? $arr_colunms['mobile']['login_account'] : '';
        $aData['uname'] = $aData['uname'] ? $aData['uname'] : $arr_colunms['email'] ? $arr_colunms['email']['login_account'] : '';
        $aData['passwd'] = $data['passwd'];
        //发送邮件或者短信
        //$obj_account = $this->app->model('seller_account');
        //TODO $obj_account->fireEvent('chgpass', $aData, $seller_id);
        return true;
    }
    /*
     * 根据会员ID 修改用户密码
     **/
    public function reset_password($seller_id,$password){
        return $this->reset_passport($seller_id,$password);
    }
    public function reset_passport($seller_id, $password)
    {
        $pamsellersModel = app::get('pam')->model('sellers');
        $pamData = $pamsellersModel->getList('login_account,password_account,createtime', array(
            'seller_id' => $seller_id,
        ));
        $db = vmc::database();
        $db->beginTransaction();
        foreach ($pamData as $row) {
            $use_pass_data['login_name'] = $row['password_account'];
            $use_pass_data['createtime'] = $row['createtime'];
            $login_password = pam_encrypt::get_encrypted_password(trim($password), 'seller', $use_pass_data);
            if (!$pamsellersModel->update(array(
                'login_password' => $login_password,
            ), array(
                'login_account' => $row['login_account'],
            ))) {
                $db->rollBack();

                return false;
            }
        }
        $db->commit();

        return true;
    }

    //设置当前用户名
    public function set_local_uname($local_uname, &$msg)
    {
        $local_uname = strtolower($local_uname);
        $seller_id = $this->user_obj->get_seller_id();
        if (!$seller_id) {
            $msg = ('页面已过期，请重新登录，到会员中心设置');

            return false;
        }
        $sellersData = $this->user_obj->get_pam_data('*', $seller_id);
        if ($sellersData['local']) {
            $msg = ('用户名已设置，不可更改');

            return false;
        }
        if (!$this->check_signup_account($local_uname, $msg)) {
            return false;
        }
        if ($msg != 'local') {
            $type = ($msg == 'mobile') ? ('手机号') : ('邮箱');
            $msg = ('用户名不能为').$type;

            return false;
        }
        $pamsellersModel = app::get('pam')->model('sellers');
        $row = $pamsellersModel->getList('login_account,login_password,password_account,createtime', array(
            'seller_id' => $seller_id,
        ));
        $row = $row[0];
        $data['seller_id'] = $seller_id;
        $data['login_account'] = strtolower($local_uname);
        $data['login_type'] = 'local';
        $data['login_password'] = $row['login_password'];
        $data['password_account'] = $row['password_account'];
        $data['createtime'] = $row['createtime'];
        if ($pamsellersModel->insert($data)) {
            $msg = ('用户名设置成功');

            return true;
        } else {
            $msg = ('用户名设置失败');

            return false;
        }
    }

    //设置绑定手机号
    public function set_mobile($mobile, &$msg)
    {
        $seller_id = $this->user_obj->get_seller_id();
        if (!$seller_id) {
            $msg = ('页面已过期，请重新登录，到会员中心设置');

            return false;
        }
        $sellersData = $this->user_obj->get_pam_data('*', $seller_id);
        if ($sellersData['mobile']) {
            $msg = ('手机号已设置，不可更改');

            return false;
        }
        if (!$this->check_signup_account($mobile, $msg)) {
            return false;
        }
        if ($msg != 'mobile') {
            $msg = '错误的手机号!';

            return false;
        }
        $pamsellersModel = app::get('pam')->model('sellers');
        $row = $pamsellersModel->getList('login_account,login_password,password_account,createtime', array(
            'seller_id' => $seller_id,
        ));
        $row = $row[0];
        $data['seller_id'] = $seller_id;
        $data['login_account'] = $mobile;
        $data['login_type'] = 'mobile';
        $data['login_password'] = $row['login_password'];
        $data['password_account'] = $row['password_account'];
        $data['createtime'] = $row['createtime'];
        if ($pamsellersModel->insert($data)) {
            $msg = ('手机号设置成功');

            return true;
        } else {
            $msg = ('手机号设置失败');

            return false;
        }
    }

    //设置绑定邮箱
    public function set_email($email, &$msg)
    {
        $seller_id = $this->user_obj->get_seller_id();
        if (!$seller_id) {
            $msg = ('页面已过期，请重新登录，到会员中心设置');

            return false;
        }
        $sellersData = $this->user_obj->get_pam_data('*', $seller_id);
        if ($sellersData['email']) {
            $msg = ('邮箱已设置，不可更改');

            return false;
        }
        if (!$this->check_signup_account($email, $msg)) {
            return false;
        }
        if ($msg != 'email') {
            $msg = '错误的邮箱地址!';

            return false;
        }
        $pamsellersModel = app::get('pam')->model('sellers');
        $row = $pamsellersModel->getList('login_account,login_password,password_account,createtime', array(
            'seller_id' => $seller_id,
        ));
        $row = $row[0];
        $data['seller_id'] = $seller_id;
        $data['login_account'] = $email;
        $data['login_type'] = 'email';
        $data['login_password'] = $row['login_password'];
        $data['password_account'] = $row['password_account'];
        $data['createtime'] = $row['createtime'];
        if ($pamsellersModel->insert($data)) {
            $msg = ('邮箱设置成功');

            return true;
        } else {
            $msg = ('邮箱设置失败');

            return false;
        }
    }

    //获取会员注册项加载
    public function get_signup_attr($seller_id = null)
    {
        if ($seller_id) {
            $seller_model = $this->app->model('sellers');
            $mem = $seller_model->dump($seller_id);
        }
        $seller_model = $this->app->model('sellers');
        $mem_schema = $seller_model->_columns();
        $attr = array();
        foreach ($this->app->model('seller_attr')->getList() as $item) {
            if ($item['attr_show'] == 'true') {
                $attr[] = $item;
            } //筛选显示项
        }
        foreach ((array) $attr as $key => $item) {
            $sdfpath = $mem_schema[$item['attr_column']]['sdfpath'];
            if ($sdfpath) {
                $a_temp = explode('/', $sdfpath);
                if (count($a_temp) > 1) {
                    $name = array_shift($a_temp);
                    if (count($a_temp)) {
                        foreach ($a_temp as $value) {
                            $name .= '['.$value.']';
                        }
                    }
                }
            } else {
                $name = $item['attr_column'];
            }
            if ($mem && $item['attr_group'] == 'defalut') {
                switch ($attr[$key]['attr_column']) {
                    case 'area':
                        $attr[$key]['attr_value'] = $mem['contact']['area'];
                    break;
                    case 'birthday':
                        $attr[$key]['attr_value'] = $mem['profile']['birthday'];
                    break;
                    case 'name':
                        $attr[$key]['attr_value'] = $mem['contact']['name'];
                    break;
                    case 'mobile':
                        $attr[$key]['attr_value'] = $mem['contact']['phone']['mobile'];
                    break;
                    case 'tel':
                        $attr[$key]['attr_value'] = $mem['contact']['phone']['telephone'];
                    break;
                    case 'zip':
                        $attr[$key]['attr_value'] = $mem['contact']['zipcode'];
                    break;
                    case 'addr':
                        $attr[$key]['attr_value'] = $mem['contact']['addr'];
                    break;
                    case 'sex':
                        $attr[$key]['attr_value'] = $mem['profile']['gender'];
                    break;
                    case 'pw_answer':
                        $attr[$key]['attr_value'] = $mem['account']['pw_answer'];
                    break;
                    case 'pw_question':
                        $attr[$key]['attr_value'] = $mem['account']['pw_question'];
                    break;
                }
            }
            if ($item['attr_group'] == 'contact' || $item['attr_group'] == 'input' || $item['attr_group'] == 'select') {
                $attr[$key]['attr_value'] = $mem['contact'][$attr[$key]['attr_column']];
                if ($item['attr_sdfpath'] == '') {
                    $attr[$key]['attr_value'] = $mem[$attr[$key]['attr_column']];
                    if ($attr[$key]['attr_type'] == 'checkbox') {
                        $attr[$key]['attr_value'] = unserialize($mem[$attr[$key]['attr_column']]);
                    }
                }
            }
            if ($attr[$key]['attr_type'] == 'select' || $attr[$key]['attr_type'] == 'checkbox') {
                $attr[$key]['attr_option'] = unserialize($attr[$key]['attr_option']);
            }
            $attr[$key]['attr_column'] = $name;
            if ($attr[$key]['attr_column'] == 'birthday') {
                $attr[$key]['attr_column'] = 'profile[birthday]';
            }
        }

        return $attr;
    } //end function

    /*
     * 保存会员信息sellers表和注册扩展项数据
     *
     **/
    public function save_sellers($saveData, &$msg)
    {
        $saveData = vmc::singleton('seller_site_filter')->check_input($saveData);
        $seller_model = $this->app->model('sellers');		
        $db = vmc::database();
        $db->beginTransaction();
        if ($seller_model->save($saveData['seller_sellers'])) {
            $seller_id = $saveData['seller_sellers']['seller_id'];
            $saveData['pam_account']['seller_id'] = $seller_id;
            if (!app::get('pam')->model('sellers')->save($saveData['pam_account'])) {
                $db->rollBack();
                $msg = '账户数据保存异常!';
                return false;
            }
            $db->commit();
        } else {
            $msg = '保存失败!';
            return false;
        }
        return $seller_id;
    }


	public function unset_seller()
    {
        $auth = pam_auth::instance(pam_account::get_account_type('seller'));
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
