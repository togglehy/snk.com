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


class b2c_tasks_messenger extends base_task_abstract implements base_interface_task
{
    public function exec($params = null)
    {
        vmc::singleton('b2c_messenger_stage')->action($params['tmpl_name'], $params['tmpl_data'], $params['target']);
    }
}
