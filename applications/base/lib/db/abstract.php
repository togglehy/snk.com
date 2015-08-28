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



class base_db_abstract

{
    public $prefix = 'vmc_';
    public static $mysql_query_executions = 0;

    function __construct(){
        $this->prefix = DB_PREFIX;
    }

    public function query($sql , $skipModifiedMark = false,$db_lnk=null){
        $rs = $this->exec($sql,$skipModifiedMark,$db_lnk);
        return $rs;
    }
}//End Class
