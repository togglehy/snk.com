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



class b2c_finder_dlyplace{



    function __construct($app)
    {
        $this->app = $app;
    }

    public $column_editbutton = '编辑';
    public function column_editbutton($row)
    {
        return '<a class="btn btn-xs btn-default" href="index.php?app=b2c&ctl=admin_dlyplace&act=edit&p[0]='.$row['dp_id'].'"><i class="fa fa-edit"></i> '.('编辑').'</a>';
    }
}
