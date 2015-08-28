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

class b2c_ctl_admin_goods extends desktop_controller
{
    public $use_buildin_import = true;
    public function index()
    {
        $group[] = array(
            'label' => ('商品权重') ,
            'data-submit' => 'index.php?app=b2c&ctl=admin_goods&act=batch_edit&p[0]=dorder',
            'data-target' => '_ACTION_MODAL_',
        );
        if ($this->has_permission('catgoods')) {
            $group[] = array(
                'label' => ('商品分类') ,
                'data-submit' => 'index.php?app=b2c&ctl=admin_goods&act=batch_edit&p[0]=cat',
                'data-target' => '_ACTION_MODAL_',
            );
            // $group[] = array(
            //     'label' => '_SPLIT_'
            // );
        }
        foreach (vmc::servicelist('b2c.goods_finder_edit_group') as $object) {
            if (is_object($object) && method_exists($object, 'get_extends_group')) {
                $object->get_extends_group($group);
            }
        }
        if ($this->has_permission('addgoods')) {
            $custom_actions[] = array(
                'label' => ('添加商品') ,
                'icon' => 'fa-plus',
                'href' => 'index.php?app=b2c&ctl=admin_goods_editor&act=add',
            );
        }
        $custom_actions[] = array(
            'label' => ('批量操作') ,
            'group' => $group,
        );
        $actions_base['title'] = ('商品列表');
        $actions_base['actions'] = $custom_actions;
        $actions_base['use_buildin_set_tag'] = true;
        $actions_base['use_buildin_filter'] = true;
        if ($this->has_permission('importgoods')) {
            $actions_base['use_buildin_import'] = true;
        }
        if ($this->has_permission('exportgoods')) {
            $actions_base['use_buildin_export'] = true;
        }
        if ($this->has_permission('deletegoods')) {
            $actions_base['use_buildin_recycle'] = true;
        }
        $this->finder('b2c_mdl_goods', $actions_base);
    }

    public function import()
    {
        $this->pagedata['thisUrl'] = 'index.php?app=b2c&ctl=admin_goods&act=index';
        $oGtype = $this->app->model('goods_type');
        $this->pagedata['gtype'] = $oGtype->getList('type_id,name');
        $this->pagedata['check_policy'] = vmc::singleton('importexport_controller')->check_policy();
        //支持导出类型
        $this->pagedata['import_type'] = vmc::singleton('importexport_controller')->import_support_filetype();
        $this->display('admin/goods/goods_import.html');
    }
    /**
     * 批量编辑.
     */
    public function batch_edit($type = '')
    {
        $mdl_goods = $this->app->model('goods');
        $params = $_POST;

        if (count($_POST['goods_id']) < 1) {
            echo '请选择商品';
            exit;
        }
        $mdl_products = $this->app->model('products');
        $mdl_mlv = $this->app->model('member_lv');

        switch ($type) {

            case 'name':
                break;
            case 'cat':
                if (!$this->has_permission('catgoods')) {
                    echo('您无权批量操作商品分类');
                    exit;
                }
                $this->pagedata['cats'] = $this->app->model('goods_cat')->getMapTree(0, '');
                break;
            case 'brief':
                break;
            case 'dorder':
                break;
            case 'brand':
                if (!$this->has_permission('brandgoods')) {
                    echo('您无权批量操作商品品牌');
                    exit;
                }
                $mdl_brand = $this->app->model('brand');
                $brandMap = $mdl_brand->getAll();
                $brandList = array();
                foreach ($brandMap as $v) {
                    $brandList[$v['brand_id']] = $v['brand_name'];
                }
                $this->pagedata['brand'] = $brandList;
                break;
            }
        $this->pagedata['filter'] = htmlspecialchars(serialize($params));
        $this->display('admin/goods/batchedit/'.$type.'.html');
    }
    public function batch_save(){
        $this->begin();
        $params = $_POST;
        $type = $params['type'];
        $filter = unserialize(trim($params['filter']));
        $mdl_goods = $this->app->model('goods');
        $mdl_products = $this->app->model('products');
        switch ($type) {
            case 'cat':
            case 'dorder':
                if(!$mdl_goods->update($params['set'],$filter)){
                    $this->end(false,'保存失败');
                }
                $this->end(true,'保存成功');
                break;
        }

    }
}
