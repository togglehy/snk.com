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
        $this->action = $this->_request->get_act_name();		
        $this->seller = $this->get_current_seller();
        $this->set_tmpl('seller');
		$this->_menus = $this->get_menu();
    }
	
	// 商家首页
	public function index()
	{
		//$menu = $this->app->getConf('avatar_max_size');
		//echo $this->app->app_dir;
	}
	
	// 审核进度
	public function process()
	{
		
	}
}
