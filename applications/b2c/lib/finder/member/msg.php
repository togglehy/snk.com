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



class b2c_finder_member_msg{

    public function detail_msg($msg_id)
    {
        $render = app::get('b2c')->render();
        $render->pagedata['msg'] = app::get('b2c')->model('member_msg')->dump($msg_id);
        return $render->fetch('admin/member/msg_detail.html');
    }


    public function row_style($row){
        //TODO
    }
}
