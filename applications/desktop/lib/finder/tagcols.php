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



class desktop_finder_tagcols{
    var $column_tag = '标签';
    var $column_tag_order = COLUMN_IN_TAIL;
    function column_tag($row){
        $rel_id = $row[$row['idColumn']];
        $oTagRel = app::get('desktop')->model('tag_rel');
        $oTag = app::get('desktop')->model('tag');
        $filter = array('rel_id'=>$rel_id,'tag_type'=>$row['tag_type'],'app_id'=>$row['app_id']);
        $tag_ids = $oTagRel->getList('tag_id',$filter);
        foreach($tag_ids as $id){
            $ids[] = $id['tag_id'];
        }
        if($ids){
            $rows = $oTag->getList('*',array('tag_id'=>$ids));
            //$class_name_arr = array('label-primary','label-success','label-danger','label-warning','label-info');
            foreach($rows as $k=>$row){
                $return .= '<span class="btn default grey-cascade-stripe btn-xs">'.$row['tag_name'].'</span>';
            }
            return $return;
        }
    }

}
