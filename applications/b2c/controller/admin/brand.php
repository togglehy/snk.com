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

class b2c_ctl_admin_brand extends desktop_controller {
    function index() {
        $this->finder('b2c_mdl_brand', array(
            'title' => ('商品品牌') ,
            'use_buildin_recycle'=>true,
            'actions' => array(
                array(
                    'label' => ('添加品牌') ,
                    'icon' => 'fa-plus',
                    'href' => 'index.php?app=b2c&ctl=admin_brand&act=create',
                ) ,
            )
        ));
    }
    function create() {
        $this->page('admin/goods/brand/detail.html');
    }
    function save() {
        $this->begin('index.php?app=b2c&ctl=admin_brand&act=index');
        $objBrand = $this->app->model('brand');
        $brandname = $objBrand->dump(array(
            'brand_name' => $_POST['brand_name'],
            'brand_id'
        ));
        if (empty($_POST['brand_id']) && is_array($brandname)) {
            $this->end(false, ('品牌名重复'));
        }
        $_POST['ordernum'] = intval($_POST['ordernum']);
        $data = $this->_preparegtype($_POST);
        #↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓记录管理员操作日志@lujy↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        if ($obj_operatorlogs = vmc::service('operatorlog.goods')) {
            $olddata = app::get('b2c')->model('brand')->dump($_POST['brand_id']);
        }
        #↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑记录管理员操作日志@lujy↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
        if ($objBrand->save($data)) {
            #↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓记录管理员操作日志@lujy↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
            if ($obj_operatorlogs = vmc::service('operatorlog.goods')) {
                if (method_exists($obj_operatorlogs, 'brand_log')) {
                    $obj_operatorlogs->brand_log($_POST, $olddata);
                }
            }
            #↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑记录管理员操作日志@lujy↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
            $this->end(true, ('品牌保存成功'));
        } else {
            $this->end(false, ('品牌保存失败'));
        }
    }
    function edit($brand_id) {
        $this->path[] = array(
            'text' => ('商品品牌编辑')
        );
        $objBrand = $this->app->model('brand');
        $this->pagedata['brandInfo'] = $objBrand->dump($brand_id);
        $this->page('admin/goods/brand/detail.html');
    }
    function _preparegtype($data) {
        $data['seo_info']['seo_title'] = $data['seo_title'];
        $data['seo_info']['seo_keywords'] = $data['seo_keywords'];
        $data['seo_info']['seo_description'] = $data['seo_description'];
        $data['seo_info'] = serialize($data['seo_info']);
        unset($data['seo_title']);
        unset($data['seo_keywords']);
        unset($data['seo_description']);
        return $data;
    }

}
