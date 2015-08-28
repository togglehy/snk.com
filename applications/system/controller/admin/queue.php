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




class system_ctl_admin_queue extends desktop_controller {


    function index() {
        $params = array (
            'title' => '队列管理',
            'use_buildin_recycle'=>true,
        );
        $queue_controller_name = system_queue::get_controller_name();
        $support_queue_controller_name = 'system_queue_adapter_mysql';

        if ($queue_controller_name == $support_queue_controller_name) {
            $this->finder('system_mdl_queue_mysql', $params);
        }else{
            $this->pagedata['queue_controller_name'] = $queue_controller_name;
            $this->pagedata['support_queue_controller_name'] = $support_queue_controller_name;

            $this->page('admin/queue.html');
        }
    }

    function retry($task_id){
        $this->begin();
        $queue_controller_name = system_queue::get_controller_name();
        $support_queue_controller_name = 'system_queue_adapter_mysql';
        if ($queue_controller_name == $support_queue_controller_name) {
            $mdl_queue_mysql = $this->app->model('queue_mysql');
            $task = $mdl_queue_mysql->dump($task_id);
            try{
                $params = $task['params'];
                if(!is_array($params)){
                    $params = unserialize($params);
                }
                vmc::singleton($task['worker'])->exec($params);
            }catch(Exception $e){
                $exception_msg = $e->getTrace().$e->getMessage();
                $mdl_queue_mysql->update(array('exception_msg'=>$exception_msg),array('id'=>$tatask_id));
                $this->end(false,'出现异常！');
            }
            $mdl_queue_mysql->delete(array('id'=>$task_id));
            $this->end(true,'重试成功!');
        }else{
            $this->end(fase,'暂不支持');
        }
    }
}
