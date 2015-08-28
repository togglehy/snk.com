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


 
class desktop_mdl_tag_rel extends dbeav_model{

    function save( &$item,$mustUpdate = null,$mustInsert = false){
        $list = parent::getList('*',array('tag_id'=>$item['tag']['tag_id'],'rel_id'=>$item['rel_id']));
        if($list && count($list)>0){
            $item = $list[0];
        }else{
            parent::save($item);
        }
    }
}
