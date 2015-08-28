<?php

class b2c_user_object{

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
        $member_id = $this->get_member_session();
        return $member_id ? true : false;
    }

    /**
     * 获取当前用户ID
     */
    public function get_member_id(){
        return $member_id = $this->get_member_session();
    }

    //根据用户名得到会员ID
    public function get_member_id_by_username($login_account){
        $pam_members_model = app::get('pam')->model('members');
        $data = $pam_members_model->getList('member_id',array('login_account'=>$login_account));
        return $data[0]['member_id'];
    }

    /**
     * 设置会员登录session member_id
     */
    public function set_member_session($member_id){
        unset($_SESSION['error_count']['b2c']);
        $_SESSION['account']['member'] = $member_id;
    }

    /**
     * 获取会员登录session member_id
     */
    public function get_member_session(){
        if($this->member_id)return $this->member_id;
        if(isset($_SESSION['account']['member']) &&  $_SESSION['account']['member']){
            return $_SESSION['account']['member'];
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
	public function get_current_member(){
        if($this->member_info){
          return $this->member_info;
        }
        return $this->get_member_info( );
    }

    /**
     *当前会员用户信息
     */
    public function get_member_info( $member_id ) {
        if(!$member_id){
            $member_id = $this->member_id = $this->get_member_id();
        }
        $memberFilter = array(
            'account' => 'member_id,login_account,login_type',
            'members'=>'member_id,member_lv_id,avatar,email,mobile,name,sex,experience,cur,advance',
        );
        $memberData = $this->get_members_data($memberFilter,$member_id);
        $member_sdf = $memberData['members'];

        if( !empty($member_sdf) ) {
            $login_name = $this->get_member_name();
            $this->member_info['member_id'] = $member_sdf['member_id'];
            $this->member_info['uname'] =  $login_name;
            $this->member_info['local_uname'] =  $memberData['account']['local'];
            $this->member_info['login_account'] =  $memberData['account']['login_account'];
            $this->member_info['name'] = $member_sdf['name'];
            $this->member_info['avatar'] = $member_sdf['avatar'];
            $this->member_info['sex'] =  $member_sdf['sex'] == 1 ?'男':'女';
            $this->member_info['email'] =  $member_sdf['email'];
            $this->member_info['mobile'] =  $member_sdf['mobile'];
            $this->member_info['integral'] = app::get('b2c')->model('member_integral')->amount($member_id);
            $this->member_info['experience'] = $member_sdf['experience'];
            $this->member_info['email'] = $memberData['account']['email'];
            $this->member_info['member_lv'] = $member_sdf['member_lv_id'];
            $this->member_info['member_cur'] = $member_sdf['cur'];
            //$this->member_info['advance'] = $member_sdf['advance'];
            #获取会员等级信息，及下级信息
            $obj_mem_lv = $this->app->model('member_lv');
            $levels = $obj_mem_lv->getList("name,member_lv_id,disabled,dis_count,experience",array('disabled'=>'false'),0,-1,null,'experience DESC');
            foreach ($levels as $key => $level) {
                if($this->member_info['member_lv'] == $level['member_lv_id']){
                    $this->member_info['levelname'] = $level['name'];
                    $this->member_info['lv_discount'] = $level['dis_count'];
                    $this->member_info['next_levelname'] = $levels[$key+1]['name'];
                    $this->member_info['next_lv_discount'] = $levels[$key+1]['dis_count'];
                    $this->member_info['next_experience'] = $levels[$key+1]['experience'];
                    break;
                }
            }

        }
        return $this->member_info;
    }

    /**
     * 获取当前会员信息(标准格式，按照表结构获取)
     * $columns = array(
     *      'account' => 'member_id',
     *      'members' => 'member_id',
     * );
     */
    public function get_members_data($columns,$member_id=null){
        if(!$member_id){
            $this->member_id = $this->get_member_id();
        }

        if( $columns['account'] ){
            $data['account'] = $this->_get_pam_members_data($columns['account'],$member_id);
        }

        if($columns['members']){
            $data['members'] = $this->_get_b2c_members_data($columns['members'],$member_id);
        }

        return $data;
    }

    /**
     * 获取当前会员用户基本信息(b2c_members)
     */
    private function _get_b2c_members_data($columns='*',$member_id=null){
        if(!$member_id){
            $member_id = $this->member_id;
        }
        $b2c_members_model = app::get('b2c')->model('members');
        if(is_array($columns) ){
            $columns = implode(',',$columns);
        }
        $membersData = $b2c_members_model->getList($columns,array('member_id'=>$member_id));
        return $membersData[0];
    }

    /**
     * 获取当前登录账号(pam_members)表信息
     */
    private function _get_pam_members_data($columns='*',$member_id){
        if(!$member_id){
            $member_id = $this->member_id;
        }
        $pam_members_model = app::get('pam')->model('members');
        if(is_array($columns)){
            $columns = implode(',',$columns);
        }
        if( $columns != '*' && !strpos($columns,'login_type') ){
            $columns .= ',login_type';
        }
        $accountData = $pam_members_model->getList($columns,array('member_id'=>$member_id));
        foreach((array)$accountData as $row){
            foreach((array)$row as $key=>$val){
                if($key == 'login_type'){
                    $arr_colunms[$val] = $row['login_account'];
                }else{
                    $arr_colunms[$key] = $val;
                }
            }
        }//end foreach

        //$arr_colunms['login_account'] = $arr_colunms['local'];
        return $arr_colunms;
    }

    public function get_pam_data($columns="*",$member_id){
        if(is_array($columns)){
            $columns = implode(',',$columns);
        }
        if( $columns != '*' && !strpos($columns,'login_type') ){
            $columns .= ',login_type';
        }
        $pam_members_model = app::get('pam')->model('members');
        $accountData = $pam_members_model->getList($columns,array('member_id'=>$member_id));
        foreach((array)$accountData as $row){
          $arr_colunms[$row['login_type']] = $row;
        }
        return $arr_colunms;
    }

    /**
     * 获取当前会员用户名/或指定用户的用户名
     */
    public function get_member_name($login_name=null,$member_id=null){
        if(!$login_name){
            $member_id = $member_id ? $member_id : $this->get_member_id();
            $pam_members_model = app::get('pam')->model('members');
            $data = $pam_members_model->getList('*',array('member_id'=>$member_id));

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
