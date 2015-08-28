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


class b2c_mdl_member_integral extends dbeav_model
{
    public $defaultOrder = array('change_time', ' DESC');

    public function amount($member_id){
        $db  = $this->db;
        $tb = $this->table_name(1);
        $cur_time = time();
        $sql = "SELECT sum(`change`) as amount FROM $tb WHERE member_id = $member_id AND (change_expire = 0 OR change_expire < $cur_time)";
        
        $result = $db->selectRow($sql);
        return $result['amount'];
    }
}
