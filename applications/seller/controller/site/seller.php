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
    }

	// 商家首页
	public function index()
	{
		$this->output();
	}

	// 审核进度
	public function process()
	{
		// echo $this->title . "process";
	}

	public function company()
	{
		$this->title .= "-公司信息";
		if($_POST) $this->_company_post($_POST);
		$seller = $this->app->model('sellers')->getRow('*', array(
			'seller_id' => $this->seller['seller_id']
		));
		$this->pagedata['seller'] = $seller;
		$company = $this->app->model('company')->getRow('*', array(
			'company_id' => $$seller['seller_id']
		));
		$this->pagedata['company'] = $company;
		$this->output();
	}
	// 公司信息提交
	private function _company_post($post)
	{
		extract($post);
		$this->begin($this->gen_url(array(
			'app' => 'seller',
			'ctl' => 'site_seller',
			'act' => 'company'
		)));
		try{
			// 更新seller_company
			$mdl_company = $this->app->model('company');
			$update_company_data = compact('name', 'area', 'addr', 'nature');
			if($mdl_company->update($update_company_data, array('company_id' => $company_id)))
			{
				throw new Exception('公司信息更新失败');
			}
			// 更新seller
			$update_seller_data = compact('area', 'addr', 'contact');
			if($mdl_seller->update($update_seller_data, array('seller_id' => $this->seller['seller_id'])))
			{
				throw new Exception('商家信息更新失败');
			}
			$this->db->commit();
		}catch(Exception $e){
			$this->db->rollback();
			$this->end(false, $e->getMessage());
		}
		$this->end(true, '成功');
	}

	public function account()
	{
		$this->title .= "-账户信息";
		if($_POST) $this->_company_post($_POST);
		
		$this->output();
	}
	private function _account_post($post)
	{
		extract($post);
		$this->begin($this->gen_url(array(
			'app' => 'seller',
			'ctl' => 'site_seller',
			'act' => 'account'
		)));
		$update_data = compact('area', 'addr', 'contact');
		if($mdl_seller->update($update_data, array('seller_id' => $this->seller['seller_id'])))
		{
			$this->end(false, '账户信息更新失败');
		}
		$this->end(true, '更新成功');
	}

	// 资质
	public function quality()
	{
		$this->title .= "-资质信息";
		if($_POST) $this->_company_post($_POST);
	}
	private function _quality_post()
	{

	}
}
