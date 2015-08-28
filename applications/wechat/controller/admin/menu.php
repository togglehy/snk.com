<?php
class wechat_ctl_admin_menu extends desktop_controller {

    function __construct($app) {
        parent::__construct($app);
    } //End Function
    //关注自动回复信息设置

    public function setting(){
        $wx_paccounts = app::get('wechat')->model('bind')->getList('id,avatar,name');
        $this->pagedata['wx_paccounts'] = $wx_paccounts;
        $this->page('admin/menu/setting.html');
    }

    public function edit($bind_id){
        $access_token = vmc::singleton('wechat_stage')->get_access_token($bind_id);
        $menu_info_action = "https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token=$access_token";
        $http = vmc::singleton('base_httpclient');
        $menus = $http->get($menu_info_action);
        $this->pagedata['menulist'] = json_decode($menus,1);
        $this->display('admin/menu/menu_list.html');
    }

    public function save($bind_id){

    }
}
