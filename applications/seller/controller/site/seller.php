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

class seller_ctl_site_seller extends seller_frontpage
{
    public $title = '商家中心';

    public function __construct(&$app)
    {
        parent::__construct($app);
        $this->set_tmpl('seller');		
    }
	
	// 商家首页
	public function index()
	{
		echo $this->title;
	}
	
	// 审核进度
	public function process()
	{
		echo $this->title . "process";
	}

	// 入驻
	public function register()
	{
		echo $this->title . "register";
	}

	// login
	public function login()
	{
		echo $this->title . "login";
	}
}
