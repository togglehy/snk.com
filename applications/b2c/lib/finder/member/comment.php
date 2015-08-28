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



class b2c_finder_member_comment{
    

    public function detail_comment($comment_id)
    {
        $comment = app::get('b2c')->model('member_comment')->groupList('*',array('comment_id'=>$comment_id),0,-1,null,'comment_id');
        $reply_list = app::get('b2c')->model('member_comment')->groupList('*',array('for_comment_id'=>$comment_id),0,-1,null,'comment_id');
        $render = app::get('b2c')->render();
        $render->pagedata['comment'] = $comment[$comment_id][$comment_id];
        $render->pagedata['reply_list'] = $reply_list;
        return $render->fetch('admin/member/comment.html');
    }


    public function row_style($row){
        //TODO
    }
}
