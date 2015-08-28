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


 
class desktop_finder_magicvars{
    var $column_control = '编辑';
    function column_control($row){
        return '<a href="index.php?app=desktop&ctl=magicvars&act=edit&p[0]='.$row['var_name'].'&_finder[finder_id]='.$_GET['_finder']['finder_id'].'" target="_blank">'.'编辑'.'</a>';
    }

}
