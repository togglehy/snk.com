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



class desktop_tasks_runexport extends base_task_abstract implements base_interface_task{
    public function exec($params=null){
        $o = vmc::singleton('desktop_finder_builder_to_run_export');
        $tmp = 0;
        $o->run($tmp, $params);
    }
}
