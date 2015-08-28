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

class b2c_ctl_admin_member_consult extends desktop_controller {
    function index() {
        $this->finder('b2c_mdl_member_comments', array(
            'title' => ('咨询列表') ,
            'use_buildin_recycle' => true,
            'actions' => array(
                array(
                    'icon'=>'fa-cog',
                    'label' => ('设置') ,
                    'href' => 'index.php?app=b2c&ctl=admin_dlytype&act=add_dlytype',
                ) ,
            ),
            'base_filter'=>array('comment_type'=>'consult')
        ));
    }

    public function edit() {

    }

    public function save() {

    }

    public function reply() {

    }


}
