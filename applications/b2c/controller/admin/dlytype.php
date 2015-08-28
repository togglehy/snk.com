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

class b2c_ctl_admin_dlytype extends desktop_controller {
    function index() {
        $this->finder('b2c_mdl_dlytype', array(
            'title' => ('配送方式') ,
            'use_buildin_recycle' => true,
            'actions' => array(
                array(
                    'label' => ('添加配送方式') ,
                    'href' => 'index.php?app=b2c&ctl=admin_dlytype&act=add_dlytype',
                    'icon' => 'fa fa-plus'
                ) ,
            )
        ));
    }
    public function add_dlytype() {
        $oDlyCorp = $this->app->model('dlycorp');
        $dlycorp = $oDlyCorp->getList('*', '', 0, -1);
        $mdl_region = app::get('ectools')->model('regions');
        $areas = $mdl_region->getList('region_id,local_name', array(
            'region_grade' => 1
        ));
        $this->pagedata['areas'] = $areas;
        $this->pagedata['clist'] = $dlycorp;
        $this->page('admin/delivery/dtype_edit.html');
    }

    function showEdit($dt_id) {
        $oDlyType = $this->app->model('dlytype');
        $oDlyCorp = $this->app->model('dlycorp');
        $dlycorp = $oDlyCorp->getList('*', '', 0, -1);
        $dt_info = $oDlyType->dump($dt_id);
        
        $dt_info['area_fee_conf'] = unserialize($dt_info['area_fee_conf']);
        $dt_info['protect_rate'] = $dt_info['protect_rate'] * 100;

        $this->pagedata['dt_info'] = $dt_info;
        $this->pagedata['clist'] = $dlycorp;
        $mdl_region = app::get('ectools')->model('regions');
        $areas = $mdl_region->getList('region_id,local_name', array(
            'region_grade' => 1
        ));
        $this->pagedata['areas'] = $areas;
        $this->pagedata['weightunit'] = $this->_weightunit();
        $this->pagedata['is_delivery_discount_close'] = $this->app->getConf('is_delivery_discount_close');
        $this->pagedata['arr_is_threshold_value'] = array(
            '0' => ('不启用') ,
            '1' => ('启用') ,
        );

        $this->page('admin/delivery/dtype_edit.html');
    }
    public function save() {
        $this->begin('index.php?app=b2c&ctl=admin_dlytype&act=index');
        $mdl_dlytype = $this->app->model('dlytype');
        $is_saved = $mdl_dlytype->save($_POST);
        if (!$is_saved) {
            $this->end(false, $this->app->_('配送方式保存失败！'));
        } else {
            $this->end(true, $this->app->_('保存成功'));
        }
    }
    function _weightunit() {
        return array(
            "500" => ("500克") ,
            "1000" => ("1公斤") ,
            "1200" => ("1.2公斤") ,
            "2000" => ("2公斤") ,
            "5000" => ("5公斤") ,
            "10000" => ("10公斤") ,
            "20000" => ("20公斤") ,
            "50000" => ("50公斤")
        );
    }
}
