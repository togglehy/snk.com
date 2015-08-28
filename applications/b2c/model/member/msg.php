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


class b2c_mdl_member_msg extends dbeav_model
{
    public $defaultOrder = array('createtime', ' DESC');

    public function modifier_msg_type($col)
    {
        switch ($col) {
            case 'normal':
                return "<i class='glyphicon glyphicon-comment'></i>";
                break;
            case 'sms':
                return "<i class='glyphicon glyphicon-phone'></i>";
                break;
            case 'email':
                return "<i class='glyphicon glyphicon-envelope'></i>";
                break;
        }
    }
}
