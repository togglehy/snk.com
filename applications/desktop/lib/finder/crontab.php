<?php
class desktop_finder_crontab {

    var $column_control = '操作';

    function __construct($app) {
	$this->app = $app;
    }

    function column_control($row) {
	return '<a href="index.php?app=desktop&ctl=crontab&act=edit&_finder[finder_id]=' . $_GET['_finder']['finder_id'] . '&p[0]=' . $row['task'] . '" target="dialog::{title:\'' . '编辑计划任务' . '\', width:680, height:250}">' . '编辑' . '</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="index.php?app=desktop&ctl=crontab&act=exec&_finder[finder_id]=' . $_GET['_finder']['finder_id'] . '&p[0]=' . $row['task'] . '" >' . '执行' . '</a>';
    }
}