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


class importexport_tasks_runimport extends base_task_abstract implements base_interface_task
{
    public function exec($params = null)
    {
        $mdl_task = app::get('importexport')->model('task');
        //执行队列，更新队列状态
        $mdl_task->update(array('status' => 4), array('key' => $params['key']));

        //连接导入文件上传的服务器
        $FTP_policy = vmc::singleton('importexport_policy');
        $ret = $FTP_policy->connect();
        if ($ret !== true) {
            $mdl_task->update(array('status' => 6, 'complete_date' => time(), 'message' => $ret), array('key' => $params['key']));

            return;
        }
        $remote_file_name = $params['key'];
        //创建本地临时文件
        if (!$FTP_policy->local_io()) {
            $msg = '本地文件创建失败，请检查'.TMP_DIR.'文件夹权限';
            $mdl_task->update(array('status' => 7, 'message' => $msg), array('key' => $params['key']));

            return false;
        }
        //同步远程文件到本地临时文件
        if (!$FTP_policy->pull($FTP_policy->local_file, $remote_file_name, 0, $msg)) {
            $mdl_task->update(array('status' => 7, 'message' => $msg), array('key' => $params['key']));
            trigger_error('从FTP传输文件到本地失败:'.$msg, E_USER_ERROR);
        }
        //实例化数据类
        $data_getter = vmc::singleton('importexport_data_object', $params['model']);
        //实例化导入文件类型类
        $file_type_obj = vmc::singleton('importexport_type_'.$params['filetype']);

        $local_io = fopen($FTP_policy->local_file, 'rb');
        $ready_mdl = vmc::singleton($params['model']);

        $offset = 0;
        $has_error = false;
        while (!feof($local_io)) {
            if ($row = fgets($local_io)) {
                $row = unserialize($row);
                if ($row_sdf = $data_getter->dataToSdf($row, $msg)) {
                    if (!$ready_mdl->save($row_sdf)) {
                        $mdl_task->update(array('status' => ($offset > 0 ? 8 : 6), 'message' => 'ROW:'.($offset + 1).',数据保存异常'), array('key' => $params['key']));
                        break;
                    } else {
                        $offset++;
                    }
                } else {
                    $has_error = true;
                    $mdl_task->update(array('status' => ($offset > 0 ? 8 : 6), 'message' => $msg.',ROW:'.$offset), array('key' => $params['key']));
                    break;
                }
            }
        }
        $FTP_policy->local_clean();
        //完美
        if ($offset > 0 && !$has_error) {
            $taskModel->update(array('status' => 5, 'complete_date' => time(), 'message' => '成功导入'.$offset.'条数据'), array('key' => $params['key']));
        }
    }
}
