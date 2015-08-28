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



class b2c_finder_gtype{


    var $column_control = '操作';
    function column_control($row){
        return '<a class="btn btn-default btn-xs" href=\'index.php?app=b2c&ctl=admin_goods_type&act=set&p[0]='.$row['type_id'].'\'"><i class="fa fa-edit"></i> '.('编辑').'</a>';
    }


}
