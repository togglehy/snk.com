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

/**
 * VMCSHOP 站点入口文件.
 *
 * @version $Revision$
 */
header("Content-type: text/html; charset=UTF-8");
ini_set('display_error', 1);

define('ROOT_DIR', realpath(dirname(__FILE__)));
require ROOT_DIR.'/driver/vmcshop.php';
vmc::start();
