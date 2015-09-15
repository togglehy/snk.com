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

    public function __construct(&$app)
    {
        parent::__construct($app);
		//if(in_array($this->action, array('index', 'add'))) $this->verify_store(); // 店铺状态
        $this->mCat = app::get('store')->model('goods_cat');
    }

	// 分类列表
	public function index()
	{
		$this->pagedata['cats'] = $this->mCat->getlist('*', array('store_id' => $this->store['store_id']));
		$this->output();
	}

	// add
	public function add($id=0)
	{
        $this->pagedata['parents'] = $this->mCat->getlist('cat_id,cat_name,p_order', array('parent_id' => 0));
        array_unshift($this->pagedata['parents'], array(
            'cate_id' => 0,
            'p_order' => 0,
            'cat_name' => '---无---'
        ));
        $this->page('site/cat/form.html', true, 'seller');
	}
	public function edit($id)
	{
        $this->pagedata['parents'] = $this->mCat->getlist('cat_id,cat_name,p_order', array('parent_id' => 0));
        array_unshift($this->pagedata['parents'], array(
            'cate_id' => 0,
            'p_order' => 0,
            'cat_name' => '---无---'
        ));
        $this->pagedata['cat'] = $this->mCat->dump($id);
        print_r($this->pagedata['cat']);
		$this->page('site/cat/form.html', true, 'seller');
	}
	public function save($id)
	{
        if(empty($_POST['cat']))  $this->end(false, '操作受限');
		extract($_POST['cat']);
		$this->begin($this->redirect_url());
		empty($parent_id) && $parent_id = 0;
		isset($cat_id) || $cat_id = 0;
        // $is_menu = $is_menu == 1 ? 'true' : 'false';
		$last_modify = time();
        $store_id = $this->store['store_id'];
		$update_data = compact('cat_id', 'parent_id', 'cat_name', 'last_modify', 'p_order', 'ismenu', 'store_id');
		if(!$this->mCat->save($update_data))
		{
		 	$this->end(false, ('分类添加失败'));
		}
		$this->end(true,('保存成功'),null);
	}

	// 更新是否设为导航
	public function update($id)
	{
		$this->begin($this->redirect_url());
        extract($_POST);
        $dbschema = $this->mCat->get_schema();
        if(empty($id) || !isset($dbschema['columns'][$column])) $this->end(false, ('操作失败'));
        $res = $this->mCat->update(
            array(
                $column => $value
            ), array(
                'cat_id'=> $id,
                'store_id' => $this->store['store_id']
        ));
        if(!$res) $this->end(false, ('操作失败'));
        $this->end(true,('操作成功'));
	}
    // 更新排序
    public function order()
    {
        $this->begin($this->redirect_url());
        foreach( $_POST['p_order'] as $k => $v ){
            $this->mCat->update(array('p_order'=>($v===''?null:$v)),array('cat_id'=>$k) );
        }
        $this->mCat->cat2json();
        $this->end(true,('操作成功'));
    }
    // 删除
    public function remove($id)
    {
        $this->begin($this->redirect_url());
        $cat_sdf = $this->mCat->dump($id);
        if($this->mCat->toRemove($id, $msg)){
            $this->end(true, $cat_sdf['cat_name'].('已删除'));
        }
        $this->end(false, $msg);
    }

}
