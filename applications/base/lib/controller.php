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


class base_controller extends base_render
{
    public $pagedata = array();
    public $force_compile = 0;
    public $_tag_stack = array();
    public $_end_message = null;

    public function begin($url_params = null)
    {
        set_error_handler(array(&$this, '_errorHandler'), E_USER_ERROR | E_ERROR);
        if ($this->transaction_start) {
            trigger_error('The transaction has been started', E_USER_ERROR);
        }
        $db = vmc::database();
        $this->transaction_status = $db->beginTransaction();
        $this->transaction_start = true;
        if (is_array($url_params)) {
            $this->_action_url = $this->app->router()->gen_url($url_params);
        } else {
            $this->_action_url = $url_params;
        }
    }

    public function endonly($result = true)
    {
        if (!$this->transaction_start) {
            trigger_error('The transaction has not started yet', E_USER_ERROR);
        }
        $this->transaction_start = false;
        $db = vmc::database();
        restore_error_handler();
        if ($result) {
            $db->commit($this->transaction_status);
        } else {
            $db->rollback();
        }
    }

    public function end($result = true, $message = null, $url_params = null, $params = array())
    {
        if (!$this->transaction_start) {
            trigger_error('The transaction has not started yet', E_USER_ERROR);
        }
        $this->transaction_start = false;
        $db = vmc::database();
        restore_error_handler();
        if (is_null($url_params)) {
            $url = $this->_action_url;
        } elseif (is_array($url_params)) {
            $url = $this->app->router()->gen_url($url_params);
        } else {
            $url = $url_params;
        }
        if ($result) {
            $db->commit($this->transaction_status);
            $status = 'success';
            $message = ($message == '' ? '操作成功！' : $message);
        } else {
            $db->rollback();
            $status = 'error';
            $message = $message ? $message : '操作失败！';
        }
        $this->_end_message = $message;
        $this->_end_status = $status;
        $this->splash($status, $url, $message, 'redirect', $params);
    }

    public function splash($status = 'success', $url = null, $msg = null, $method = 'redirect', $params = array())
    {
        header('Cache-Control:no-store, no-cache, must-revalidate'); // HTTP/1.1
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');// 强制查询etag
        header('Progma: no-cache');
        header('Location: '.$url);
    }

    public function page($detail)
    {
        header('Content-type: text/html; charset=utf-8');
        $object = vmc::service('theme');
        if ($object) {
            $object->display($detail);
        } else {
            $this->display($detail);
        }
    }

    public function _errorHandler($errno, $errstr, $errfile, $errline)
    {
        if ($errno == E_ERROR) {
            $errstr = basename($errfile).':'.$errline.'&nbsp;'.$errstr;
        } elseif ($errno == E_USER_ERROR) {
            $errstr = $errstr;
        } else {
            return;
        }

        $this->splash('error', $this->_action_url, $errstr);
        header('Location: '.$this->_action_url);

        return true;
    }
}
