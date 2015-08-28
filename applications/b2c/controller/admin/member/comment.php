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

class b2c_ctl_admin_member_comment extends desktop_controller
{
    public function index()
    {
        $this->finder('b2c_mdl_member_comment', array(
            'title' => ('评价列表'),
            'use_buildin_recycle' => true,
            'actions' => array(
                // array(
                //     'icon' => 'fa-cog',
                //     'label' => ('设置'),
                //     'href' => 'javascript:;',
                // ),
                // array(
                //     'icon' => 'fa fa-plus',
                //     'label' => ('录入评价'),
                //     'href' => 'javascript:;',
                // ),
            ),
            'base_filter' => array('comment_type' => 'comment', 'for_comment_id' => 0),
        ));
    }

    public function edit()
    {
    }

    public function save()
    {
    }
    public function show($comment_id){
        $this->begin();
        $this->end(app::get('b2c')->model('member_comment')->update(array('display'=>'true'),array('comment_id'=>$comment_id)));
    }
    public function hide($comment_id){
        $this->begin();
        $this->end(app::get('b2c')->model('member_comment')->update(array('display'=>'false'),array('comment_id'=>$comment_id)));
    }

    public function reply()
    {
        $reply_content = $_POST['reply_content'];
        $forcommentid = $_POST['forcommentid'];
        $mdl_mcomment = app::get('b2c')->model('member_comment');
        $this->begin();
        if(!$forcommentid || !$reply_content){
            $this->end(false,'缺少参数！');
        }
        $new_reply = $mdl_mcomment->getRow('*',array('comment_id'=>$forcommentid));
        unset($new_reply['comment_id']);
        $new_reply['for_comment_id'] = $forcommentid;
        $new_reply['content'] = htmlspecialchars($reply_content);
        $new_reply['createtime'] = time();
        /**
         * 在member_comment 中 当member_id<0 时，即为管理员回复
         */
        $new_reply['member_id'] = -1;
        $new_reply['author_name'] = $this->user->user_data['name'];
        $new_reply['display'] = 'true';
        $this->end($mdl_mcomment->update(array('lastreply'=>$new_reply['createtime']),array('comment_id'=>$forcommentid)) && $mdl_mcomment->save($new_reply));
    }
}
