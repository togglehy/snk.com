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


class b2c_ctl_site_product extends b2c_frontpage
{
    public $title = '商品详情';
    public function __construct($app)
    {
        parent::__construct($app);

        $this->goods_stage =
        vmc::singleton('b2c_goods_stage');
        if ($this->app->member_id = vmc::singleton('b2c_user_object')->get_member_id()) {
            $this->goods_stage->set_member($this->app->member_id);
        }
    }

    public function index()
    {
        //获取参数 货品ID
        $params = $this->_request->get_params();
        $data_detail = $this->goods_stage->detail($params[0], $msg); //引用传递
        if (!$data_detail) {
            vmc::singleton('site_router')->http_status(404);
            //$this->splash('error', null, $msg);
        }
        $this->pagedata['data_detail'] = $data_detail;

        //设置模板
        if ($data_detail['goods_setting']['site_template']) {
            //设置模板页
            $this->set_tmpl_file($data_detail['goods_setting']['site_template']);
        }
        $this->pagedata['goods_path'] = $this->app->model('goods')->getPath($data_detail['goods_id']);
        $this->_set_seo($data_detail);
        $this->page('site/product/index.html');
    }

    /*设置详情页SEO --start*/
    public function _set_seo($goods_detail)
    {
        $seo_info = $goods_detail['seo_info'];
        if(!is_array($seo_info)){
            $seo_info = unserialize($seo_info);
        }
        if (!empty($seo_info['seo_title']) || !empty($seo_info['seo_keywords']) || !empty($seo_info['seo_description'])) {
                $this->title = $seo_info['seo_title'];
                $this->keywords = $seo_info['seo_keywords'];
                $this->description = $seo_info['seo_description'];
        } else {
            $this->setSeo('site_product', 'index', $this->generate_seo_data($goods_detail));
        }
    }
    /**
     * 组织SEO数据，更换动态占位符 ENV_key.
     */
    public function generate_seo_data($goods_detail)
    {
        $keywords = '';
        if (isset($goods_detail['keywords'])) {
            foreach ($goods_detail['keywords'] as $key => $value) {
                $keywords .= $value['keyword'].',';
            }
        }
        $tags = array();
        if (isset($goods_detail['tag'])) {
            foreach ($goods_detail['tag'] as $key => $value) {
                $tags[] = $key;
            }
            $tags = app::get('desktop')->model('tag')->getList('tag_name', array('tag_id' => $tags));
            $tags = utils::array_change_key($tags, 'tag_name');
            $tags = array_keys($tags);
        }

        return array(
            'goods_name' => $goods_detail['name'].'_'.$goods_detail['product']['spec_info'],
            'goods_brand' => $goods_detail['brand']['brand_name'],
            'goods_bn' => $goods_detail['product']['bn'],
            'goods_cat' => $goods_detail['category']['cat_name'],
            'goods_intro' => $goods_detail['brief'],
            'goods_barcode' => $goods_detail['product']['barcode'],
            'goods_keywords' => $keywords,
            'goods_tags' => implode(',', $tags),
        );
    }

    
}
