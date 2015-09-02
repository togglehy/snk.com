<?php

class seller_user_object{

    public function __construct(&$app){
        $this->app = $app;
        if($_COOKIE['AUTO_LOGIN']){
            $minutes = 30*24*60;//30天
            vmc::singleton('base_session')->set_sess_expires($minutes);
            vmc::singleton('base_session')->set_cookie_expires($minutes);
            $this->cookie_expires = $minutes;
        }//如果有自动登录，设置session过期时间，单位：分
        vmc::singleton('base_session')->start();
    }

    /**
     * 判断当前用户是否登录
     */
    public function is_login(){
        $seller_id = $this->get_seller_session();
        return $seller_id ? true : false;
    }

    /**
     * 获取当前用户ID
     */
    public function get_seller_id(){
        return $seller_id = $this->get_seller_session();
    }

    //根据用户名得到会员ID
    public function get_seller_id_by_username($login_account){
        $pam_sellers_model = app::get('pam')->model('sellers');
        $data = $pam_sellers_model->getList('seller_id',array('login_account'=>$login_account));
        return $data[0]['seller_id'];
    }

    /**
     * 设置会员登录session seller_id
     */
    public function set_seller_session($seller_id){
        unset($_SESSION['error_count']['b2c']);
        $_SESSION['account']['seller'] = $seller_id;
    }

    /**
     * 获取会员登录session seller_id
     */
    public function get_seller_session(){
        if($this->seller_id)return $this->seller_id;
        if(isset($_SESSION['account']['seller']) &&  $_SESSION['account']['seller']){
            return $_SESSION['account']['seller'];
        }else{
            return false;
        }
    }

    /**
	 * 得到当前登陆用户的信息
     *
     * @param null
	 * @return array 用户信息
	 */
	public function get_current_seller(){
        if($this->seller_info){
          return $this->seller_info;
        }
        return $this->get_seller_info( );
    }

    /**
     *当前商家信息
     */
    public function get_seller_info( $seller_id ) {
        if(!$seller_id){
            $seller_id = $this->seller_id = $this->get_seller_id();
        }
        $sellerFilter = array(
            'account' => 'seller_id,login_account,login_type',
            'sellers'=> 'seller_id,avatar,email,mobile,name,phone',
			'company'=> 'company_id,name',
        );
        $sellerData = $this->get_sellers_data($sellerFilter,$seller_id);
        $seller_sdf = $sellerData['sellers'];

        if( !empty($seller_sdf) ) {
            $login_name = $this->get_seller_name();
            $this->seller_info['seller_id'] = $seller_sdf['seller_id'];           
            $this->seller_info['local_uname'] =  $sellerData['account']['local'];
            $this->seller_info['login_account'] =  $sellerData['account']['login_account'];
            $this->seller_info['name'] = $seller_sdf['name'];
            $this->seller_info['avatar'] = $seller_sdf['avatar'];            
            $this->seller_info['email'] =  $seller_sdf['email'];
            $this->seller_info['mobile'] =  $seller_sdf['mobile'];
        }
        return $this->seller_info;
    }

    /**
     * 获取当前会员信息(标准格式，按照表结构获取)
     * $columns = array(
     *      'account' => 'seller_id',
     *      'sellers' => 'seller_id',
     * );
     */
    public function get_sellers_data($columns,$seller_id=null){
        if(!$seller_id){
            $this->seller_id = $this->get_seller_id();
        }

        if( $columns['account'] ){
            $data['account'] = $this->_get_pam_sellers_data($columns['account'],$seller_id);
        }

        if($columns['sellers']){
            $data['sellers'] = $this->_get_seller_sellers_data($columns['sellers'],$seller_id);
        }

        return $data;
    }

    /**
     * 获取当前会员用户基本信息(sellers)
     */
    private function _get_seller_sellers_data($columns='*',$seller_id=null){
        if(!$seller_id){
            $seller_id = $this->seller_id;
        }
        $b2c_sellers_model = app::get('seller')->model('sellers');
        if(is_array($columns) ){
            $columns = implode(',',$columns);
        }
        $sellersData = $b2c_sellers_model->getList($columns,array('seller_id'=>$seller_id));
        return $sellersData[0];
    }

    /**
     * 获取当前登录账号(pam_sellers)表信息
     */
    private function _get_pam_sellers_data($columns='*',$seller_id){
        if(!$seller_id){
            $seller_id = $this->seller_id;
        }
        $pam_sellers_model = app::get('pam')->model('sellers');
        if(is_array($columns)){
            $columns = implode(',',$columns);
        }
        if( $columns != '*' && !strpos($columns,'login_type') ){
            $columns .= ',login_type';
        }
        $accountData = $pam_sellers_model->getList($columns,array('seller_id'=>$seller_id));
        foreach((array)$accountData as $row){
            foreach((array)$row as $key=>$val){
                if($key == 'login_type'){
                    $arr_colunms[$val] = $row['login_account'];
                }else{
                    $arr_colunms[$key] = $val;
                }
            }
        }
        return $arr_colunms;
    }

    public function get_pam_data($columns="*",$seller_id){
        if(is_array($columns)){
            $columns = implode(',',$columns);
        }
        if( $columns != '*' && !strpos($columns,'login_type') ){
            $columns .= ',login_type';
        }
        $pam_sellers_model = app::get('pam')->model('sellers');
        $accountData = $pam_sellers_model->getList($columns,array('seller_id'=>$seller_id));
        foreach((array)$accountData as $row){
          $arr_colunms[$row['login_type']] = $row;
        }
        return $arr_colunms;
    }

    /**
     * 获取当前会员用户名/或指定用户的用户名
     */
    public function get_seller_name($login_name=null,$seller_id=null){
        if(!$login_name){
            $seller_id = $seller_id ? $seller_id : $this->get_seller_id();
            $pam_sellers_model = app::get('pam')->model('sellers');
            $data = $pam_sellers_model->getList('*',array('seller_id'=>$seller_id));
            foreach((array)$data as $row){
                $arr_name[$row['login_type']] = $row['login_account'];
            }

            if( isset($arr_name['local']) ){
                $login_name = $arr_name['local'];
            }elseif(isset($arr_name['email'])){
                $login_name = $arr_name['email'];
            }elseif(isset($arr_name['mobile'])){
                $login_name = $arr_name['mobile'];
            }else{
                $login_name = current($arr_name);
            }
        }

        //信任登录用户名显示
        $service = vmc::service('pam_account_login_name');
        if(is_object($service)){
            if(method_exists($service,'get_login_name')){
                $login_name = $service->get_login_name($login_name);
            }
        }
        return $login_name;
    }
}
