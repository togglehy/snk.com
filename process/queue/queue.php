#!/usr/bin/env php
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


error_reporting(E_ALL ^ E_NOTICE);
set_time_limit(0);
//-------------------------------------------------------------------------------------
require_once realpath(dirname(dirname(dirname(__FILE__)))).'/process/lib/runtime.php';
set_error_handler('error_handler');
//-------------------------------------------------------------------------------------
if (!isset($argv[1])) {
    echo "Hey boy or girl, Please give me the queue name!\n";
    exit;
}

$queue_name = $argv[1];

$queues = system_queue::instance()->get_config('queues');
if ($num = (int) $queues[$queue_name]['thread']) {
    system_queue_consumer::instance('proc')->exec($queue_name, $num);
}

//-------------------------------------------------------------------------------------

function error_handler($code, $msg, $file, $line)
{
    if ($code == ($code & (E_ERROR ^ E_USER_ERROR ^ E_USER_WARNING))) {
        logger::error(sprintf('ERROR:%d @ %s @ file:%s @ line:%d', $code, $msg, $file, $line));
        if ($code == ($code & (E_ERROR ^ E_USER_ERROR))) {
            echo sprintf('ERROR:%d @ %s @ file:%s @ line:%d', $code, $msg, $file, $line);
            echo "\n";
            exit;
        }
    }

    return true;
}
