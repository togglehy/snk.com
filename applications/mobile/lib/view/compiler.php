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


class mobile_view_compiler {

    function compile_MAIN_M($attrs, &$compiler) {

            return " echo  \$this->_fetch_compile_include('" . ($compiler->controller->get_tmpl_main_app_id() ? $compiler->controller->get_tmpl_main_app_id() : $compiler->controller->app->app_id) . "', '" . $compiler->controller->_vars['_MAIN_M_'] . "', array()); ";

    }
    function compile_require_m($attrs, &$compiler) {
        $is_preview = 'false';
        return " echo \$this->_fetch_tmpl_compile_require({$attrs['file']},{$is_preview});";
    }
}
