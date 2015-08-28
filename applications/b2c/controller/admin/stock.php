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

class b2c_ctl_admin_stock extends desktop_controller
{
    public function index($goods_id,$product_id)
    {
        #更新货品表到库存维护表
        //vmc::singleton('b2c_goods_stock')->refresh($msg);

        if(!empty($goods_id)){
            $sku = app::get('b2c')->model('products')->getList('bn',array('goods_id'=>$goods_id));
            $goods = app::get('b2c')->model('goods')->getRow('name,image_default_id',array('goods_id'=>$goods_id));
        }elseif($product_id){
            $sku = app::get('b2c')->model('products')->getList('*',array('product_id'=>$product_id));
            $goods = $sku[0];
        }
        if($sku){
            $base_filter = array('sku_bn'=>array_keys(utils::array_change_key($sku,'bn')));
        }
        $finder_options = array(
            'title' => ('商品库存管理'),
            'use_buildin_import' => true,
            'use_buildin_export' => true,
            'actions' => array(
                array(
                    'label' => ('批量编辑'),
                    'icon' => 'fa-edit',
                    'data-submit' => 'index.php?app=b2c&ctl=admin_stock&act=batch_edit',
                    'data-target' => '_ACTION_MODAL_',
                ),
            ),
            'finder_extra_view' => array(array('app' => 'b2c','view' => '/admin/goods/stock/finder_extra.html','extra_pagedata'=>$goods), ),
        );

        if($base_filter){
            $finder_options['base_filter'] = $base_filter;
        }
        $this->finder('b2c_mdl_stock', $finder_options);
    }
    public function refresh()
    {
        $this->begin('index.php?app=b2c&ctl=admin_stock&act=index');
        $this->end(vmc::singleton('b2c_goods_stock')->refresh($msg), $msg);
    }
    public function batch_edit()
    {
        $this->pagedata['goods_id'] = $goods_id;
        $this->pagedata['product_id'] = $product_id;
        $this->display('admin/goods/stock/batch_edit.html');
    }
    public function batch_save()
    {
        $this->begin();
        $params = $_POST;
        $filter = array(
            'stock_id' => explode(',', $params['stock_id']),
        );
        $flag = vmc::singleton('b2c_goods_stock')->update_stock($filter, $params['col'], $params['nums'], $params['operation'], $msg);
        $this->end($flag, $msg);
    }
    public function quick_update()
    {
        $this->begin('index.php?app=b2c&ctl=admin_stock&act=index');
        $params = $_POST;
        if (!$params['stock_id']) {
            $this->end(false);
        }
        $stock_id = $params['stock_id'];
        unset($params['stock_id']);
        foreach ($params as $key => $value) {
            if (!in_array($key, array('stock_id', 'quantity', 'freez_quantity'))) {
                unset($params[$key]);
            }
        }
        $mdl_stock = app::get('b2c')->model('stock');
        $update_sql  = base_db_tools::getupdatesql($mdl_stock->table_name(1),$params," stock_id=$stock_id");
        $this->end($mdl_stock->db->exec($update_sql,true));
    }
}
