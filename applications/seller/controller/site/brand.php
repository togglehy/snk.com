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

class seller_ctl_site_brand extends seller_frontpage
{
    public $title = '商家品牌';

    public function __construct(&$app)
    {
        parent::__construct($app);
    }
	
	// 商家品牌
	public function index()
	{		
	
		$brands = $this->app->model('brand')->getRow('*', array(
			'seller_id' => $this->seller['seller_id']
		));		
		$this->pagedata['brands'] = $seller;
		
		$this->output();
	}

	// 品牌申请 添加
	public function add($id=0)
	{
		if($_POST) $this->_post($_POST);
		if($id)
		{
			$mdl_brand = app::get('b2c')->model('brand');
			$brand = $mdl_brand->getRow('*', array(
				'brand_id' => $id
			));
			$mdl_seller_brand = $this->app->model('brand');
			$seller_brand = $mdl_seller_brand->getRow('*', array(
				'brand_id' => $id,
				'seller_id'=> $this->seller['seller_id']
			));
			$this->pagedata['brands'] = $brand;
		}
		$this->page('site/brand/form.html', true, 'seller');
		
	}

	// 申请提交
	private function _post($post)
	{
		extract($post);		
		$this->begin($this->gen_url(array(
			'app' => 'seller',
			'ctl' => 'site_brand',
			'act' => 'index'
		)));
		try{
			// 更新brand
			$mdl_brand = app::get('b2c')->model('brand');
			$last_modify = time();
			$update_brand_data = compact(
				'brand_name', 'brand_initial', 'brand_url', 'brand_desc', 
				'brand_logo', 'ordernum', 'last_modify'
			);
			if($mdl_brand->save($update_brand_data, array('seller_id' => $seller_id)))
			{
				throw new Exception('保存失败');
			}
			// 更新seller
			$update_seller_data = compact('area', 'addr', 'contact');
			if($mdl_seller->save($update_seller_data, array('seller_id' => $this->seller['seller_id'])))
			{
				throw new Exception('申请失败');
			}
			$this->db->commit();
		}catch(Exception $e){
			$this->db->rollback();
			$this->end(false, $e->getMessage());
		}
		$this->end(true, '成功');
	}
}