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


class desktop_ctl_finder extends desktop_controller
{
    public function __construct($app)
    {
        parent::__construct($app);
        $this->_request = vmc::singleton('base_component_request');
    }
    public function object_select()
    {
        $finder_mdl = $this->_request->get_get('finder_mdl');
        $filter = $this->_request->get_get('base_filter');
        $multiple = $this->_request->get_get('multiple');

        $this->finder($finder_mdl, array(
            'object_select_model' => true,
            'selectrow_type' => ($multiple == 'true' ? 'checkbox' : 'radio'),
        ));
    }
    public function object_row()
    {
        $params = $this->_request->get_params(1);
        $res = app::get($prams['app']?$prams['app']:'b2c')->model($params['model'])->getList($params['cols'],$params['filter']);
        $this->pagedata['name'] = $params['name'];
        $this->pagedata['rows'] = $res;
        $this->pagedata['pkey'] = $params['pkey'];
        $this->display('finder/object_row.html');

    }
}
