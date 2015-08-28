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



class b2c_finder_brand{

    var $column_edit = '编辑';
    function column_edit($row){
        return '<a class="btn btn-default btn-xs" href="index.php?app=b2c&ctl=admin_brand&act=edit&p[0]='.$row['brand_id'].'" ><i class="fa fa-edit"></i> '.('编辑').'</a>';
    }

}
