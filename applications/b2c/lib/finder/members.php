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



class b2c_finder_members{
	var $column_editbutton;
    public function __construct($app)
    {
        $this->app = $app;
		$this->column_editbutton = ('操作');
		$this->column_uname = ('登录账号');
        $this->userObject = vmc::singleton('b2c_user_object');
    }

	var $column_editbutton_order = 0;

	public function column_editbutton($row){

		$btn =  '<a href="index.php?app=b2c&ctl=admin_member&act=detail&p[0]='.$row['member_id'].'" class="btn btn-default btn-xs"><i class="fa  fa-edit"></i> 查看/编辑</a>';
		//$btn .=  '<a href="index.php?app=b2c&ctl=admin_member&act=edit&p[0]='.$row['member_id'].'" class="btn btn-default btn-xs"><i class="fa fa-edit"></i> 编辑</a>';
		return $btn;
	}


    var $column_uname_order = 11;
    public function column_uname($row){
        $pam_member_info = $this->userObject->get_members_data(array('account'=>'login_account'),$row['member_id']);
        //$this->pam_member_info[$row['member_id']] = $pam_member_info;
        return $pam_member_info['account']['login_account'];
    }

}
