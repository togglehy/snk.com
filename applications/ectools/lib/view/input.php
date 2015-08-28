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


class ectools_view_input
{
    public function input_region($params)
    {
        $package = vmc::service('ectools_regions.ectools_mdl_regions');
        $params['package'] = $package->key;

        if (!$params['callback']) {
            unset($params['callback']);
        }

        $render = app::get('ectools')->render();

        $render->pagedata['params'] = $params;
        $area_depth = app::get('ectools')->getConf('system_area_depth');
        $aDepth = array();
        for ($i = 0;$i < $area_depth;$i++) {
            $aDepth[] = $i;
        }
        $render->pagedata['area_depth'] = $aDepth;

        $views = 'common/region.html';

        return $render->fetch($views);
    }
}
