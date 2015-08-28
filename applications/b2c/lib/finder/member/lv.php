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



class b2c_finder_member_lv{
    var $column_edit = '编辑';
    function column_edit($row){
        $return = '<a class="btn btn-default btn-xs" href="index.php?app=b2c&ctl=admin_member_lv&act=addnew&p[0]='.$row['member_lv_id'].'" ><i class="fa fa-edit"></i>'.('编辑').'</a>';
        if(!$row['default_lv']){
            $return .= '<a class="btn btn-xs btn-default" target="_command" href="index.php?app=b2c&ctl=admin_member_lv&act=setdefault&p[0]='.$row['member_lv_id'].'">'.('设为默认等级').'</a>';
        }else{
            $return .= '<span class="label label-default"><i></i> 默认等级</span>';
        }
        return $return;

    }

    public function row_style($row){
        if($row['default_lv']){
            return 'active';
        }
    }
}
