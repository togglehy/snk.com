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
// | Description: 商家商品
// +----------------------------------------------------------------------

class seller_ctl_site_goods extends seller_frontpage
{
    public $title = '商家商品';

    public function __construct(&$app)
    {
        parent::__construct($app);
        if(in_array($this->action, array('index', 'apply'))	$this->verify_store(); // 店铺状态
    }	
	
	// 在售商品 审核|待审
	public function index($status)
	{
		// 入商品库
		$mdl_b2c_goods = app::get('b2c')->model('goods');
		$mdl_seller_goods = $this->app->model('goods');
		$this->output();
	}
	// 商品添加
	public function add()
	{
		
	}	
	// 商品编辑
	public function edit($id)
	{
	
	}
	
	// 商品保存
	public function save()
	{
	
	}
	// 库存
	public function stock()
	{
		
	}
	// 仓库
	public function storage()
	{
	
	}
	// 价格修改
	public function price()
	{
	
	}	
}