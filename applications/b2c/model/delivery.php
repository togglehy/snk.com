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


class b2c_mdl_delivery extends dbeav_model {
    var $has_many = array(
        'delivery_items' => 'delivery_items',
    );
    var $defaultOrder = array(
        ' createtime',
        'DESC'
    );
    function apply_id($delivery_sdf) {
        $sign = (($delivery_sdf['delivery_type'] == 'send') ? '1' : '2');
        $tb = $this->table_name(1);
        do{
            $microtime = utils::microtime();
            mt_srand($microtime);
            $i = substr(mt_rand() , -3);
            $delivery_id =  $sign . date('ymdHi') . $i;
            $row = $this->db->selectrow('SELECT delivery_id from '.$tb.' where delivery_id ='.$delivery_id);
        }while($row);

        return $delivery_id;
    }

    public function modifier_member_id($row) {
        if (is_null($row) || empty($row)) {
            return '未知';
        }
        $login_name = vmc::singleton('b2c_user_object')->get_member_name(null, $row);
        if ($login_name) {
            return $login_name;
        } else {
            return '未知';
        }
    }
    /**
     * filter字段显示修改
     * @params string 字段的值
     * @return string 修改后的字段的值
     */
    public function modifier_op_id($row) {
        if (is_null($row) || empty($row)) {
            return '-';
        }
        $obj_pam_account = app::get('pam')->model('account');
        $arr_pam_account = $obj_pam_account->getList('login_name', array(
            'account_id' => $row
        ));
        return $arr_pam_account[0]['login_name'] ? $arr_pam_account[0]['login_name'] : '未知操作员';
    }
}
