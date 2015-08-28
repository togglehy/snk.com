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

class b2c_ctl_admin_goods_editor extends desktop_controller
{
    public function add()
    {
        $this->pagedata['cat']['type_id'] = $this->pagedata['goods']['type']['type_id'] = 1;
        $this->_editor(1);
        //$this->pagedata['spec_history'] = app::get('b2c')->model('goods')->get_spec_history();
        $this->pagedata['IMAGE_MAX_SIZE'] = IMAGE_MAX_SIZE;
        $this->page('admin/goods/detail/frame.html');
    }

    /*编辑商品*/
    public function edit($goods_id)
    {
        $this->goods_id = $goods_id;
        $mdl_goods = $this->app->model('goods');
        $goods = $mdl_goods->dump($goods_id, '*', 'default');
        //ksort($goods['images']);
        $this->_editor($goods['type']['type_id']);
        $this->pagedata['goods'] = $goods;
        $this->pagedata['goods']['glink'] = $mdl_goods->getLinkList($goods_id);
        $this->pagedata['goods']['goods_setting'] = $goods['goods_setting'];
        $this->page('admin/goods/detail/frame.html');
    }

    public function save()
    {
        $this->begin($_POST['redirect']);
        $mdl_goods = $this->app->model('goods');
        $mdl_products = $this->app->model('products');

        $goods = $this->_prepare_goods_data($_POST);

        if (!empty($goods['gid']) && $mdl_goods->count(array(
            'gid' => $goods['gid'],
            'goods_id|notin' => $goods['goods_id'],
        )) > 0) {
            $this->end(false, ('重复的商品ID'.$goods['gid']));
        }

        if (empty($goods['product']) || count($goods['product']) == 0) {
            $this->end(false, '货品信息未填写完整');
        }
        if (!$goods['name']) {
            $this->end(false, ('商品名称不能为空'));
        }

        //验证
        foreach ($goods['product'] as $k => $p) {
            if (empty($p['bn'])) {
                $this->end(false, ('货号未填写'));
            }
            if (!empty($p['price']) && !is_numeric($p['price'])) {
                $this->end(false, ($p['bn'].'销售价填写有误'));
            }
            if (!empty($p['mktprice']) && !is_numeric($p['mktprice'])) {
                $this->end(false, ($p['bn'].'市场价填写有误'));
            }
            if ($mdl_products->count(array(
                'bn' => $p['bn'],
                'goods_id|notin' => $goods['goods_id'],
            )) > 0) {
                $this->end(false, ('重复的货号：'.$p['bn']));
            }
            if ($p['barcode'] && $mdl_products->count(array(
                'barcode' => $p['barcode'],
                'goods_id|notin' => $goods['goods_id'],
            )) > 0) {
                $this->end(false, ('重复的条码：'.$p['barcode']));
            }
        }
        $mdl_goods->has_many['product'] = 'products:contrast';
        if (!$mdl_goods->save($goods)) {
            $this->end(false, ('保存失败!'));
        }
        $_POST['goods'] = $goods;
        //更新库存表
        vmc::singleton('b2c_goods_stock')->refresh($msg);
        $this->end(true, ('操作成功'), null, array(
            'goods_id' => $goods['goods_id'],
        ));
    }
    //相关商品
    public function ajax_goods_rel()
    {
        $gids = $_POST['goods_id'];
        $this->pagedata['items'] = $this->app->model('goods')->getList('goods_id,name', array(
            'goods_id' => $gids,
        ));
        $this->display('admin/goods/detail/ajax_rel_items.html');
    }

    /*扩展信息*/
    public function prototype($type_id = 0, $render = false)
    {
        $mdl_gtype = $this->app->model('goods_type');
        $this->pagedata['gtype'] = $mdl_gtype->getList('*');
        $mdl_brand = $this->app->model('brand');
        $this->pagedata['brandList'] = $mdl_brand->getList('brand_id,brand_name');
        $this->pagedata['goods_prototype'] = $mdl_gtype->dump($type_id, '*');
        if ($render) {
            $this->display('admin/goods/detail/params.html');
        }
    }
    private function _prepare_goods_data(&$data)
    {
        $mdl_goods = $this->app->model('goods');
        $mdl_products = $this->app->model('products');
        $mdl_gtype = $this->app->model('goods_type');
        $last_goods = $mdl_goods->getRow('goods_id', null, 0, 1, 'goods_id desc');
        $last_goods_id = $last_goods['goods_id'];
        $goods = $data['goods'];

        //相册默认图
        if (is_array($goods['images'])) {
            $goods['image_default_id'] = $data['image_default'];
        } else {
            $goods['image_default_id'] = null;
        }
        if (isset($goods['spec_desc']) && isset($goods['spec_desc']['v']) && is_array($goods['spec_desc']['v'])) {
            foreach ($goods['spec_desc']['v'] as $key => $value) {
                if (is_array($goods['spec_desc']['vplus']) && $goods['spec_desc']['vplus'][$key]) {
                    $goods['spec_desc']['v'][$key] .= ','.$goods['spec_desc']['vplus'][$key];
                    unset($goods['spec_desc']['vplus'][$key]);
                }
            }

            //去除空规格值
            if(isset($goods['spec_desc']['t'])){
                foreach ($goods['spec_desc']['t'] as $key => $value) {
                    if(trim($value) == ''){
                        unset($goods['spec_desc']['t'][$key]);
                        unset($goods['spec_desc']['v'][$key]);
                    }
                }
            }
            if(isset($goods['spec_desc']['v'])){
                foreach ($goods['spec_desc']['v'] as $key => $value) {
                    if(trim($value) == ''){
                        unset($goods['spec_desc']['t'][$key]);
                        unset($goods['spec_desc']['v'][$key]);
                    }
                }
            }

        }
        if(!$goods['spec_desc'] || empty($goods['spec_desc']) || empty($goods['spec_desc']['v']) ||empty($goods['spec_desc']['t']) || count($goods['product'])<2){
            $goods['spec_desc'] = null;
        }

        //关键词
        if ($data['keywords']) {
            foreach (explode(',', $data['keywords']) as $keyword) {
                $goods['keywords'][] = array(
                    'keyword' => $keyword,
                    'res_type' => 'goods',
                );
            }
        }else{
            $goods['keywords'] = array();
        }

        if ($goods['params']) {
            $g_params = array();
            foreach ($goods['params'] as $gpk => $gpv) {
                $g_params[$data['goodsParams']['group'][$gpk]][$data['goodsParams']['item'][$gpk]] = $gpv;
            }
            $goods['params'] = $g_params;
        }
        if (!$goods['min_buy']) {
            unset($goods['min_buy']);
        }
        if (!$goods['brand']['brand_id']) {
            $goods['brand']['brand_id'] = null;
        }
        $images = array();
        foreach ((array) $goods['images'] as $imageId) {
            $images[] = array(
                'target_type' => 'goods',
                'image_id' => $imageId,
            );
        }
        $goods['images'] = $images;
        unset($images);
        if($goods['default_product']){
            foreach ($goods['product'] as $key=>$product) {
                if($key == $goods['default_product']){
                    $goods['product'][$key]['is_default'] = 'true';
                }else{
                    $goods['product'][$key]['is_default'] = 'false';
                }
            }
        }else{
            $goods['product'][key((array) $goods['product']) ]['is_default'] = 'true';
        }

        foreach ($goods['product'] as $prok => $pro) {
            if ($goods['unit']) {
                $goods['product'][$prok]['unit'] = $goods['unit'];
            }
            if (!$pro['product_id'] || substr($pro['product_id'], 0, 4) == 'new') {
                unset($goods['product'][$prok]['product_id']);
            }
            if (!$pro['marketable']) {
                $goods['product'][$prok]['marketable'] = 'false';
            }
            $goods['product'][$prok]['price'] = trim($goods['product'][$prok]['price']);
            $goods['product'][$prok]['mktprice'] = trim($goods['product'][$prok]['mktprice']);
        }


        if (is_array($data['linkid'])) {
            foreach ($data['linkid'] as $k => $id) {
                if (!empty($goods['goods_id'])) {
                    $last_goods_id = $goods['goods_id'];
                } else {
                    $last_goods_id = intval($last_goods_id) + 1;
                }
                $link_arr[] = array(
                    'goods_1' => $last_goods_id,
                    'goods_2' => $id,
                    'manual' => $data['linktype'][$id],
                    'rate' => 100,
                );
            }
            $goods['rate'] = $link_arr;
        }
        if (!$goods['category']['cat_id']) {
            $goods['category']['cat_id'] = 0;
        }
        if (!$goods['tag']) {
            $goods['tag'] = array();
        }

        if (!$goods['rate']) {
            $goods['rate'] = array();
        }
        if ($goods['gain_score'] === '') {
            $goods['gain_score'] = null;
        }
        if ($goods['props']) {
            foreach ($goods['props'] as $pk => $pv) {
                if (substr($pk, 2) <= 20 && $pv['value'] === '') {
                    $goods['props'][$pk]['value'] = null;
                }
            }
        }
        foreach ($goods['product'] as $k => $v) {
            if (!$k && $k !== 0) {
                unset($goods['product'][$k]);
            }
            if (empty($goods['product'][$k]['weight'])) {
                $goods['product'][$k]['weight'] = 0;
            }
            if (empty($goods['product'][$k]['mktprice'])) {
                $goods['product'][$k]['mktprice'] = 0;
            }
        }
        if (!$goods['marketable']) {
            $goods['marketable'] = 'false';
        }

        return $goods;
    }

    private function _editor($type_id)
    {
        $mdl_cat = $this->app->model('goods_cat');
        $this->pagedata['cats'] = $mdl_cat->getMapTree(0, '');
        $this->prototype($type_id);
        $this->pagedata['sections'] = array();
        $sections = array(
            'basic' => array(
                'label' => ('基本信息') ,
                'file' => 'admin/goods/detail/basic.html',
            ) ,
            'content' => array(
                'label' => ('图文介绍') ,
                'file' => 'admin/goods/detail/content.html',
            ) ,
            'params' => array(
                'label' => ('属性参数') ,
                'file' => 'admin/goods/detail/params.html',
            ) ,
            // 'adj'=>array(
            //     'label'=>('配件'),
            //     'options'=>'',
            //     'file'=>'admin/goods/detail/adj.html',
            // ),
            'template' => array(
                'label' => ('展示模板') ,
                'file' => 'admin/goods/detail/template.html',
            ) ,
            'rel' => array(
                'label' => ('相关商品') ,
                'file' => 'admin/goods/detail/rel.html',
            ) ,
            'seo' => array(
                'label' => ('SEO相关') ,
                'file' => 'admin/goods/detail/seo.html',
            ) ,
        );
        foreach ($sections as $key => $section) {
            if (!isset($prototype['setting']['use_'.$key]) || $prototype['setting']['use_'.$key]) {
                if (method_exists($this, ($func = '_editor_'.$key))) {
                    $this->$func();
                }
            }
            $this->pagedata['sections'][$key] = $section;
        }
    }

     /**
      * 规格历史.
      */
     public function get_spec_history()
     {
         $this->display('admin/goods/detail/ajax_get_spec_history.html');
     }
}
