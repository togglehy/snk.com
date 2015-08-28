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
set_error_handler('error_handler');
//-------------------------------------------------------------------------------------
require_once realpath(dirname(dirname(dirname(__FILE__)))).'/process/lib/runtime.php';

//-------------------------------------------------------------------------------------

base_crontab_schedule::trigger_all();
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
