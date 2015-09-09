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

class seller_ctl_site_cat extends seller_frontpage
{
    public $title = '店铺分类';
    
    private $store = Array();

    public function __construct(&$app)
    {
        parent::__construct($app);
        if(in_array($this->action, array('index', 'apply'))	$this->verify_store(); // 店铺状态
        
    }
	// 分类列表
	public function index()
	{	
		$mdl_cat = $this->app->model('cat');
		$this->pagedata['cats'] = $mdl_cat->getlist('*', array('store_id' => $this->store['store_id']);
		$this->output();
	}

	// add
	public function add($id=0)
	{
		if($_POST) $this->_setting_post($_POST);
		$this->output();
	}
	public function edit($id)
	{
		if($_POST) $this->_save($_POST);
		$this->_info($id, 'edit');
	}
	// 表单
	public function _form($id = 0, $type = 'add')
	{
		// 父级分类
		$this->display('site/cat/form.html');
	}
	private function _save()
	{
		extract($post);
		$this->begin($this->gen_url(array(
			'app' => 'seller',
			'ctl' => 'site_store',
			'act' => 'setting'
		)));
		isset($parent_id) || $parent_id = 0;
		isset($cat_id) || $cat_id = 0;
		$last_modify = time();
		$update_data = compact('cat_id', 'parent_id', 'cat_name', 'last_modify', 'disabled');
		$mdl_goods_cat = $this->model('goods_cat');
		if($mdl_goods_cat->save($update_data)
		{
			$this->end(false, '分类添加失败');
		}
		$this->end(true, '成功');
	}
	
	public function remove($id)
	{
		$this->_info($nCatId,'edit');
	}
		
	// 排序
	public function update($id)
	{
		$this->begin($this->);
        $mdl_goods_cat = $this->app->model('goods_cat');
        foreach( $_POST['p_order'] as $k => $v ){
            $mdl_goods_cat->update(array('p_order'=>($v===''?null:$v)),array('cat_id'=>$k) );
        }
        $mdl_goods_cat->cat2json();
        $this->end(true,('操作成功'));
	}
	
}