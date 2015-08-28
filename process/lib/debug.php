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


declare (ticks = 1);

function tick_handler()
{
    global $backtrace;
    $backtrace = debug_backtrace();
}
register_tick_function('tick_handler');

function shutdown()
{
    global $backtrace;
    // do check if $backtrace contains a fatal error...
    var_dump($backtrace);
}
register_shutdown_function('shutdown');
