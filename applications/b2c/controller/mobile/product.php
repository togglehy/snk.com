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


class b2c_ctl_mobile_product extends b2c_mfrontpage
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
            vmc::singleton('mobile_router')->http_status(404);
            //$this->splash('error', null, $msg);
        }
        $this->pagedata['data_detail'] = $data_detail;

        //设置模板
        if ($data_detail['goods_setting']['site_template']) {
            //设置模板页
            $this->set_tmpl_file($data_detail['goods_setting']['site_template']);
        }
        $this->pagedata['goods_path'] = $this->app->model('goods')->getPath($data_detail['goods_id']);
        $this->page('mobile/product/index.html');
    }



}
