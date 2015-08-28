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


class b2c_order_check_reship {
    /**
     * 构造方法
     * @param object app
     */
    public function __construct($app) {
        $this->app = $app;
        $this->obj_math = vmc::singleton('ectools_math');
    }

    public function exec(&$delivery_sdf, &$msg = '') {
        return true;
    }


}
