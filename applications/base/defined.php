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


$constants = array(
    'SET_T_STR' => 0,
    'SET_T_INT' => 1,
    'SET_T_ENUM' => 2,
    'SET_T_BOOL' => 3,
    'SET_T_TXT' => 4,
    'SET_T_FILE' => 5,
    'SET_T_DIGITS' => 6,
    'LC_MESSAGES' => 6,
    'PHP_SELF' => (isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']) ,
    'DATA_DIR' => ROOT_DIR . '/data',
    'TMP_DIR' => sys_get_temp_dir() ,
    'LOG_TYPE' => 3, //文件系统日志
    'DEFAULT_TIMEZONE' => 8,
    'URL_REWRITE' => false,
    'DATABASE_OBJECT' => 'base_db_pdo_mysql',
    'DB_PREFIX'=>'vmc_',
    'KV_ADAPTER' => 'base_kvstore_filesystem',
    'KV_PREFIX' => 'default',
    'CACHE_ADAPTER' => 'base_cache_filesystem',
);
foreach ($constants as $k => $v) {
    if (!defined($k)) define($k, $v);
}
