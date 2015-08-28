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


class base_queue_mysql implements base_interface_queue
{
    private $queue;
    public function __construct()
    {
        $this->queue = app::get('base')->model('queue');
    }

    public function publish($message)
    {
        $data = $message;
        return $this->queue->insert($data);
    }
    
    public function consume()
    {
        $this->queue->flush();
    }
}
