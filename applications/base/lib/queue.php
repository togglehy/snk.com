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


class base_queue
{
    private $queue;
    public function __construct()
    {
        if(!defined('MESSAGE_QUEUE') || !constant('MESSAGE_QUEUE')) {
            define('MESSAGE_QUEUE', 'base_queue_mysql');
        }
        return $this->set_queue(vmc::singleton(MESSAGE_QUEUE));
    }

    public function publish($message)   
    {
       return $this->queue->publish($message); 
    }

    public function consume()
    {
        return $this->queue->consume();
    }

    private function set_queue(base_interface_queue $queue)
    {
        $this->queue = $queue;
        return $this->queue;
    }

}

