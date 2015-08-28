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


class desktop_ctl_tags extends desktop_controller
{

    public function remove($tag_id)
    {
        $mdl_tag = $this->app->model('tag');
        $mdl_tag_rel = $this->app->model('tag_rel');
        $this->begin();
        if(!$tag_id){
            $this->end(false);
        }
        $mdl_tag->delete(array(
            'tag_id'=>$tag_id
        ));
        $mdl_tag_rel->delete(array(
            'tag_id'=>$tag_id
        ));
        $this->end(true);
    }
}
