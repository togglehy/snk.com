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

class seller_ctl_site_store extends seller_frontpage
{
    public $title = '商家订单';

    public function __construct(&$app)
    {
        parent::__construct($app);		
    }	
	
	// 已开店 店铺状态
	public function index()
	{
		echo 'seller_store_index';
	}

	// 未开店到申请页面
	public function apply()
	{
		echo 'seller_store_apply';
	}
	
	// 申请进度
	public function process()
	{
		echo 'seller_store_apply';
	}
}