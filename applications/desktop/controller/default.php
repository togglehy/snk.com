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


class desktop_ctl_default extends desktop_controller
{
    public function index()
    {
        $desktop_user = vmc::singleton('desktop_user');

        $menus = $desktop_user->get_work_menu();
        $user_id = $this->user->get_id();
        foreach ((array) $menus['workground'] as $key => $value) {
            //if($i++>$workground_count) break;
            $fav_menus[] = $key;
        }
        
        $this->pagedata['title'] = $title;
        $this->pagedata['title_desc'] = $title_desc;
        $this->pagedata['session_id'] = vmc::singleton('base_session')->sess_id();
        $this->pagedata['uname'] = $this->user->get_login_name();
        $this->pagedata['avatar'] = $this->user->get_avatar();
        $this->pagedata['is_super'] = $this->user->is_super();
        $this->pagedata['param_id'] = $user_id;
        $this->pagedata['menus'] = $menus;
        $this->pagedata['fav_menus'] = (array) $fav_menus;
        $this->pagedata['shop_base'] = vmc::base_url(1);
        $this->pagedata['shopadmin_dir'] = ($_SERVER['REQUEST_URI']);

        // 桌面内容替换埋点
        foreach (vmc::servicelist('desktop_content') as $services) {
            if (is_object($services)) {
                if (method_exists($services, 'changeContent')) {
                    $services->changeContent(app::get('desktop'));
                    $services->changeContent($desktop_menu);
                }
            }
        }

        $this->display('index.html');
    }

    public function syntax_highlighter()
    {
        $this->pagedata['id'] = $_GET['id'];
        $this->pagedata['mode'] = $_GET['mode'];
        $this->pagedata['desktop_res_url'] = $this->app->res_url;
        $this->display('editor/codemirror.html');
    }
}
