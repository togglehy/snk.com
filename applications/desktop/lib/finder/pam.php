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



class desktop_finder_pam{
    var $column_control = '配置';
     function __construct($app){
        $this->app= $app;
    }
    function column_control($row){
        $passport_id = $row['passport_id'];
        return "<a class='btn btn-xs btn-default' href='index.php?app=desktop&ctl=pam&act=setting&p[0]=$passport_id' ><i class='fa fa-cog'></i> 配置</a>
";
    }

}
