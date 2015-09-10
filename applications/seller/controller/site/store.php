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
    public $title = '店铺管理';
    
    public function __construct(&$app)
    {
        parent::__construct($app);
        
        if(in_array($this->action, array('index', 'apply'))) $this->verify_store(); // 店铺状态
        
    }	
    
    // 是否开店
    private function verify_store()
    {
    	$redirect = $this->gen_url(array(
			'app' => 'seller', 
			'ctl' => 'store', 
			'act' => 'index'
		));
	 	$this->store = app::get('store')->model('store')->getRow('*', array(
			'seller_id' => $this->seller['seller_id']
		));
		if(!$this->store) $this->splash('error', $redirect, '店铺尚未开启！');
		if($this->store['disable'] == false) $this->splash('Error', $redirect, '店铺正在审核');
		$this->pagedata['store'] = $this->store;
		return true;
    }
	
	// 已开店 店铺状态
	public function index()
	{	
		$this->output();
	}

	// 填写资料
	public function apply()
	{
		$this->output();
	}
	
	// 店铺设置
	public function setting()
	{
		$this->title .= "-店铺设置";
		if($_POST) $this->_setting_post($_POST);
 		$this->output();
	}
	private function _setting_post($post)
	{
		extract($post);
		$this->begin($this->gen_url(array(
			'app' => 'seller',
			'ctl' => 'site_store',
			'act' => 'setting'
		)));
		$update_data = compact('logo', 'name', 'template');
		$mdl_store = app::get('store')->model('store');
		if($mdl_store->update($update_data, array('seller_id' => $this->seller['seller_id'])))
		{
			$this->end(false, '账户信息更新失败');
		}
		$this->end(true, '更新成功');

	}
	
	public function template()
	{
		$this->pagedata['template'] = $this->store['template_setting'];
		$this->output();	
	}
	//模板设置
	private function _template_post()
	{
		extract($post);
		$this->begin($this->gen_url(array(
			'app' => 'seller',
			'ctl' => 'site_store',
			'act' => 'setting'
		)));
		// 幻灯广告
		// 热门产品
		// banner
		// 新品推荐
		$setting = compact('slider', 'hot', 'banner', 'new');
		$mdl_store = app::get('store')->model('store');
		if($mdl_store->update(array('template_setting' => $setting), array('seller_id' => $this->seller['seller_id'])))
		{
			$this->end(false, '账户信息更新失败');
		}
		$this->end(true, '更新成功');
	}
	
	// 查看
	public function view()
	{
		$this->redirect(app::get('site')->router()->gen_url(array(
			'app' => 'site', 
			'ctl' => 'store', 
			'args' => array(
				$this->store['store_id']
			)
		)));
	}
}