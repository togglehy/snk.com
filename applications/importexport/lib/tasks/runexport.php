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


class importexport_tasks_runexport extends base_task_abstract implements base_interface_task
{
    public function exec($params = null)
    {
        $mdl_task = app::get('importexport')->model('task');
        //执行队列，更新队列状态
        $mdl_task->update(array('status' => 1), array('key' => $params['key']));
        $FTP_policy = vmc::singleton('importexport_policy');
        $ret = $FTP_policy->connect();
        //连接FTP服务失败
        if ($ret !== true) {
            $mdl_task->update(array('status' => 3, 'complete_date' => time(), 'message' => $ret), array('key' => $params['key']));

            return;
        }
        $data_getter = vmc::singleton('importexport_data_object', $params['model']);
        //实例化导出文件类型类
        $file_type_obj = vmc::singleton('importexport_type_'.$params['filetype']);
        if (!$FTP_policy->local_io()) {
            $msg = '本地文件创建失败，请检查'.TMP_DIR.'文件夹权限';
            $mdl_task->update(array('status' => 3, 'complete_date' => time(), 'message' => $msg), array('key' => $params['key']));

            return;
        }
        $remote_file_name = $params['key'];
        //加入文件头部数据
        if ($file_header = $file_type_obj->fileHeader()) {
            $FTP_policy->local_io($file_header);
            if (!$FTP_policy->push($remote_file_name, $FTP_policy->local_file, 0, $msg)) {
                $FTP_policy->local_clean();
                trigger_error('FTP传输失败:'.$msg, E_USER_ERROR);
            }
        }
        if (!($sizeof_remote_file = $FTP_policy->size($remote_file_name))) {
            $FTP_policy->local_clean();
            trigger_error('FTP传输异常:sizeof_remote_file is '.$sizeof_remote_file, E_USER_ERROR);
        }
        //导出数据写到本地文件
        $offset = 0;
        while ($listFlag = $data_getter->fgetlist($data, $params['filter'], $offset)) {
            $offset++;
            $rs = $file_type_obj->arrToExportType($data);
            if (!$FTP_policy->local_io($rs)) {
                $FTP_policy->local_clean();
                trigger_error('写入文件内容时,本地文件IO异常', E_USER_ERROR);
                break;
            }
            //本地文件上传到远程
            if (!$FTP_policy->push($remote_file_name, $FTP_policy->local_file, $sizeof_remote_file, $msg)) {
                $mdl_task->update(array('status' => 3, 'complete_date' => time(), 'message' => 'FTP上传异常:'.$msg), array('key' => $params['key']));
                $FTP_policy->local_clean();
                break;
            }
            if (!$sizeof_remote_file = $FTP_policy->size($remote_file_name)) {
                $FTP_policy->local_clean();
                trigger_error('FTP传输异常:sizeof_remote_file is '.$sizeof_remote_file, E_USER_ERROR);
            }
        }

        //加入文件尾部数据
        if ($file_footer = $file_type_obj->fileFoot()) {
            if (!$FTP_policy->local_io($file_footer)) {
                $FTP_policy->local_clean();
                trigger_error('写入文件尾部信息时,本地文件IO异常', E_USER_ERROR);
            }
            //本地文件上传到远程
            if (!$FTP_policy->push($remote_file_name, $FTP_policy->local_file, $sizeof_remote_file, $msg)) {
                $mdl_task->update(array('status' => 3, 'complete_date' => time(), 'message' => 'FTP上传异常:'.$msg), array('key' => $params['key']));
                $FTP_policy->local_clean();
                return;
            }
        }

        if (!$sizeof_remote_file = $FTP_policy->size($remote_file_name)) {
            $FTP_policy->local_clean();
            trigger_error('FTP传输异常:sizeof_remote_file is '.$sizeof_remote_file, E_USER_ERROR);
        } else {
            $success_msg = '导出到FTP服务器成功！文件大小:'.number_format(($sizeof_remote_file/1024),3).'KB';
        }
        //导出结束
        $mdl_task->update(array('status' => 2, 'complete_date' => time(), 'message' => $success_msg), array('key' => $params['key']));
        //清除本地临时文件
        $FTP_policy->local_clean();

        return true;
    }
}
