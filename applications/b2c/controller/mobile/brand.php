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


class b2c_ctl_mobile_brand extends b2c_mfrontpage
{
    public $title = '品牌专区';
    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function index()
    {
        $mdl_brand = $this->app->model('brand');
        $brand_list = $mdl_brand->getList();
        $this->pagedata['brand_list'] = utils::array_change_key($brand_list, 'brand_initial', true);
        $this->set_tmpl('brand');
        $this->page('mobile/brand/index.html');
    }
}
