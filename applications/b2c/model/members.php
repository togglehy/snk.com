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


class b2c_mdl_members extends dbeav_model
{
    public $has_tag = true;
    public $defaultOrder = array('regtime','DESC');
    public $has_many = array(
        'pam_account' => 'members@pam:contrast:member_id^member_id',
    );

    public $subSdf = array(
        'default' => array(
            'pam_account' => array('*'),
         ),
    );

    private static $member_info;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->use_meta();  //member中的扩展属性将通过meta系统进行存储
    }

    public function save(&$sdf, $mustUpdate = null, $mustInsert = false)
    {
        if (isset($sdf['profile']['gender'])) {
            if ($sdf['profile']['gender'] === 'male') {
                $sdf['profile']['gender'] = 1;
            } elseif ($sdf['profile']['gender'] === 'female') {
                $sdf['profile']['gender'] = 0;
            } else {
                unset($sdf['profile']['gender']);
            }
        }
        if (isset($sdf['profile']['birthday']) && $sdf['profile']['birthday']) {
            $data = explode('-', $sdf['profile']['birthday']);
            $sdf['b_year'] = intval($data[0]);
            $sdf['b_month'] = intval($data[1]);
            $sdf['b_day'] = intval($data[2]);
            unset($sdf['profile']['birthday']);
        }
        $sdf['contact']['addr'] = htmlspecialchars($sdf['contact']['addr']);
        if(empty($sdf['member_lv']) || empty($sdf['member_lv']['member_group_id'])){
            $sdf['member_lv']['member_group_id'] = $this->app->model('member_lv')->get_default_lv();
        }
        $info_object = vmc::service('sensitive_information');
        if (is_object($info_object)) {
            $info_object->opinfo($sdf, 'b2c_mdl_members', __FUNCTION__);
        }
        $flag = parent::save($sdf, $mustUpdate = null, $mustInsert = false);

        return $flag;
    }

    public function dump($filter, $field = '*', $subSdf = null)
    {
        if ($ret = parent::dump($filter, $field, $subSdf)) {
            $ret['profile']['birthday'] = $ret['b_year'].'-'.$ret['b_month'].'-'.$ret['b_day'];
            if ($ret['profile']['gender'] == 1) {
                $ret['profile']['gender'] = 'male';
            } elseif ($ret['profile']['gender'] == 0) {
                $ret['profile']['gender'] = 'female';
            } else {
                $ret['profile']['gender'] = 'no';
            }
        }
        if (intval($filter) == 0 || (is_array($filter) && intval($filter['member_id']) == 0)) {
            $ret['contact']['name'] = '匿名购买';
        }

        return $ret;
    }

     //密码修改
    public function save_security($nMemberId, $aData, &$msg)
    {
        $aMem = $this->dump($nMemberId, '*', array(':account@pam' => array('*')));
        if (!$aMem) {
            $msg = ('无效的用户Id');

            return false;
        }
        $member_sdf['member_id'] = $nMemberId;
        //如果密码是空的则进入安全问题修改过程
        if (empty($aData['passwd'])) {
            if (!$aData['pw_answer'] || !$aData['pw_question']) {
                $msg = ('安全问题修改失败！');

                return false;
            }
            $member_sdf = $this->dump($nMemberId, '*');
            $member_sdf['account']['pw_question'] = $aData['pw_question'];
            $member_sdf['account']['pw_answer'] = $aData['pw_answer'];
            $msg = ('安全问题修改成功');

            return $this->save($member_sdf);
        } else {
            $use_pass_data['login_name'] = $aMem['pam_account']['login_name'];
            $use_pass_data['createtime'] = $aMem['pam_account']['createtime'];
            if (pam_encrypt::get_encrypted_password(trim($aData['old_passwd']), pam_account::get_account_type($this->app->app_id), $use_pass_data) != $aMem['pam_account']['login_password']) {
                $msg = ('输入的旧密码与原密码不符！');

                return false;
            }

            if ($aData['passwd'] != $aData['passwd_re']) {
                $msg = ('两次输入的密码不一致！');

                return false;
            }

            if (strlen($aData['passwd']) <  6) {
                $msg = ('密码长度不能小于6');

                return false;
            }

            if (strlen($aData['passwd']) > 20) {
                $msg = ('密码长度不能大于20');

                return false;
            }
            $aMem['pam_account']['login_password'] = pam_encrypt::get_encrypted_password(trim($aData['passwd']), pam_account::get_account_type($this->app->app_id), $use_pass_data);
            $aMem['pam_account']['account_id'] = $nMemberId;
            if ($this->save($aMem)) {
                $aData = array_merge($aMem, $aData);
                $data['email'] = $aMem['contact']['email'];
                $data['uname'] = $aMem['pam_account']['login_name'];
                $data['passwd'] = $aData['passwd_re'];
                $obj_account = $this->app->model('member_account');
                $msg = ('密码修改成功');

                return true;
            } else {
                $msg = ('密码修改失败！');

                return false;
            }
        }
    }

    public function create($data)
    {
        $arrDefCurrency = app::get('ectools')->model('currency')->getDefault();
        $data['currency'] = $arrDefCurrency['cur_code'];
        $data['pam_account']['account_type'] = pam_account::get_account_type($this->app->app_id);
        $data['pam_account']['createtime'] = time();
        $data['reg_ip'] = base_request::get_remote_addr();
        $data['regtime'] = time();
        $data['pam_account']['login_name'] = strtolower($data['pam_account']['login_name']);
        $use_pass_data['login_name'] = $data['pam_account']['login_name'];
        $use_pass_data['createtime'] = $data['pam_account']['createtime'];
        $data['pam_account']['login_password'] = pam_encrypt::get_encrypted_password(trim($data['pam_account']['login_password']), pam_account::get_account_type($this->app->app_id), $use_pass_data);
        $this->save($data);

        return $data['member_id'];
    }

    /**
     * 会员等级更新
     */
    public function touch_lv($member_id){
        $mdl_member_lv = $this->app->model('member_lv');
        $member = $this->dump($member_id);
        $mdl_member_lv->defaultOrder = array('experience',' desc');
        $member['experience'] = $member['experience']?$member['experience']:0;
        $m_lv = $mdl_member_lv->getList('member_lv_id',array('experience|sthan'=>$member['experience']));
        if( $m_lv[0]){
            $member['member_lv']['member_group_id'] = $m_lv[0]['member_lv_id'];
        }else{
            $member['member_lv']['member_group_id'] = $mdl_member_lv->get_default_lv();
        }
        return $this->save($member);
    }

    /**
     * 经验值更新
     */
     public function exp_renew($member_id)
     {
        if(!$member_id)return true;
        $db = $this->db;
        $tb = $this->table_name(1);
        $order_tb = $this->app->model('orders')->table_name(1);
        $order_where = " status IN('active','finish') AND  pay_status='1'";
        $sql = "UPDATE $tb SET experience=(SELECT SUM(order_total) FROM $order_tb WHERE member_id = $member_id AND $order_where) WHERE member_id = $member_id";
        return $db->exec($sql,true);
     }

    /**
     * 会员回收站操作暂时禁用
     */
     public function pre_recycle($data)
     {
         $falg = false;
        //TODO
        return $falg;
     }

    public function pre_restore(&$data, $restore_type = 'add')
    {
        foreach ((array) $data['pam_account'] as $key => $row) {
            $data[$this->schema['idColumn']] = $row['member_id'];
            if (!$this->is_exists($row['login_account'])) {
                $data['need_delete'] = true;
            } else {
                return false;
            }

            return true;
        }

        if ($restore_type == 'add') {
            foreach ((array) $data['pam_account'] as $key => $row) {
                $new_name = $row['login_account'].'_1';
                while ($this->is_exists($new_name)) {
                    $new_name = $new_name.'_1';
                }
                $data['pam_account'][$key]['login_account'] = $new_name;
                $data['need_delete'] = true;
            }

            return true;
        }
        if ($restore_type == 'none') {
            $data['need_delete'] = false;

            return true;
        }
    }

    public function modifier_avatar($col){
        if(!$col)return '';
        $img_url = base_storager::image_path($col,'xs');
        return '<img class="img-thumbnail img-circle" width="30" src="'.$img_url.'">';
    }
    public function _filter($filter, $tableAlias = null, $baseWhere = null)
    {
        foreach (vmc::servicelist('b2c_mdl_members.filter') as $k => $obj_filter) {
            if (method_exists($obj_filter, 'extend_filter')) {
                $obj_filter->extend_filter($filter);
            }
        }

        if ($filter['login_account']) {
            $aData = app::get('pam')->model('members')->getList('member_id', array('login_account|head' => $filter['login_account']));
            unset($filter['login_account']);

            if ($aData) {
                foreach ($aData as $key => $val) {
                    $member[$key] = $val['member_id'];
                }
                $filter['member_id'] = $member;
            } else {
                return 0;
            }
        }

        $info_object = vmc::service('sensitive_information');
        if (is_object($info_object)) {
            $info_object->opinfo($filter, 'b2c_mdl_members', __FUNCTION__);
        }
        $filter = parent::_filter($filter);

        return $filter;
    }

    /**
     * 重写搜索的下拉选项方法.
     *
     * @param null
     */
    public function searchOptions()
    {
        $columns = array();
        foreach ($this->_columns() as $k => $v) {
            if (isset($v['searchtype']) && $v['searchtype']) {
                if ($k == 'member_id') {
                    $columns['member_key'] = $v['label'];
                } else {
                    $columns[$k] = $v['label'];
                }
            }
        }
        $columns = array_merge($columns, array(
            'login_account' => ('登录账号'),
        ));

        return $columns;
    }

    /**
     * @根据会员ID获取会员等级信息
     *
     * @param $cols 查询字段
     * @param $sLv 会员等级id
     */
    public function get_lv_info($member_id)
    {
        if (empty($member_id) || $member_id < 0) {
            return;
        }
        $row = $this->db->selectrow('SELECT mlv.* FROM vmc_b2c_members AS m
                                                        LEFT JOIN vmc_b2c_member_lv  AS mlv ON m.member_lv_id=mlv.member_lv_id
                                                        WHERE mlv.disabled = \'false\' AND m.member_id = '.intval($member_id));

        return $row;
    }



    public function title_recycle($sdf)
    {
        if (!$sdf) {
            return;
        }

        if ($sdf['pam_account']['mobile']) {
            $login_name = $sdf['pam_account']['mobile']['login_account'];
        }
        if ($sdf['pam_account']['email']) {
            $login_name = $sdf['pam_account']['email']['login_account'];
        }
        if ($sdf['pam_account']['local']) {
            $login_name = $sdf['pam_account']['local']['login_account'];
        }

        return $login_name;
    }

    public function is_exists($uname)
    {
        return vmc::singleton('b2c_user_passport')->is_exists_login_name($uname);
    }
}
