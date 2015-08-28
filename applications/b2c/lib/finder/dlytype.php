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



class b2c_finder_dlytype{

    var $column_control = '编辑';
    function column_control($row){
        return '<a class="btn btn-xs btn-default" href="index.php?app=b2c&ctl=admin_dlytype&act=showEdit&p[0]='.$row['dt_id'].'" ><i class="fa fa-edit"></i>'.('编辑').'</a>';
    }

}
