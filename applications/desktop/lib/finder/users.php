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



class desktop_finder_users{
    var $column_control = '操作';
    function __construct($app){
        $this->app=$app;
    }

     function column_control($row){
        return '<a class="btn btn-xs btn-default" href="index.php?app=desktop&ctl=users&act=edit&p[0]='.$row['user_id'].'" ><i class="fa fa-edit"></i>'.'编辑'.'</a>';

      }

      function row_style($row){
          if($row['@row']['super']=='1'){
              return 'text-success';
          }
      }


}

?>
