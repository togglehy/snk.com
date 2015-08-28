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




class b2c_site_view_helper
{

    function function_SYSTEM_HEADER($params, &$smarty)
    {
        // 首页

        if ($smarty->app->app_id == 'site') return '';
        return $smarty->fetch('site/common/header.html', app::get('b2c')->app_id);
    }



    function function_SYSTEM_FOOTER($params, &$smarty)
    {

        
        if ($smarty->app->app_id == 'site') return '';
        $html= $smarty->fetch('site/common/footer.html',app::get('b2c')->app_id);

        return $html;
    }

}//结束
