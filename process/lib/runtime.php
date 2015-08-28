<?php

ob_implicit_flush(1);
require realpath(dirname(dirname(dirname(__FILE__)))).'/driver/vmcshop.php';
vmc::register_autoload();
cachemgr::init(false);
if (!defined('BASE_URL')) {
    if ($shell_base_url = app::get('base')->getConf('shell_base_url')) {
        define('BASE_URL', $shell_base_url);
    } else {
        echo 'Please login vmcshop shopadmin first';
    }
}
