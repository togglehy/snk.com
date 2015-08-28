<?php

class importexport_finder_task
{
    public $column_control = '操作';

    public function column_control($row)
    {
        $value = app::get('importexport')->model('task')->getList('*', array('task_id' => $row['task_id']));
        if ($value[0]['status'] == '2') {
            $href = 'index.php?app=importexport&ctl=admin_export&act=queue_download&task_id='.$row['task_id'];
        } elseif ($value[0]['status'] == '6' || $value[0]['status'] == '8') {
            $href = 'index.php?app=importexport&ctl=admin_import&act=queue_download&task_id='.$row['task_id'];
        }
        if ($href) {
            $returnValue = "<a href='$href' target='_blank' class='btn btn-xs btn-default'><i class='fa fa-cloud-download'></i>下载文件</a>";
        }

        return $returnValue;
    }
}
