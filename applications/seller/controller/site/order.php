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

class seller_ctl_site_order extends seller_frontpage
{
    public $title = '商家订单';

    public function __construct(&$app)
    {
        parent::__construct($app);
    }
	
	// 订单
	public function index()
	{
		echo 'seller_order_index';
	}
}