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


class desktop_controller extends base_controller
{
    public $defaultwg;
    public $certcheck = true;
    public function __construct($app)
    {

        header('Cache-Control:no-store, no-cache, must-revalidate'); // HTTP/1.1
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // 强制查询etag
        header('Progma: no-cache');
        if (app::get('base')->getConf('shell_base_url') != vmc::base_url(1)) {
            app::get('base')->setConf('shell_base_url', vmc::base_url(1));
        }
        $this->fix_location();
        $this->defaultwg = $this->defaultWorkground;
        parent::__construct($app);
        vmc::singleton('base_session')->start();
        if ($_COOKIE['autologin'] > 0) {
            vmc::singleton('base_session')->set_sess_expires($_COOKIE['autologin']);
        } //如果有自动登录，设置session过期时间，单位：分
        $auth = pam_auth::instance(pam_account::get_account_type('desktop'));
        $account = $auth->account();
        if (get_class($this) != 'desktop_ctl_passport' && !$account->is_valid()) {
            if (get_class($this) != 'desktop_ctl_default') {
                $url = app::get('desktop')->router()->gen_url($_GET, 1);
            } else {
                $url = app::get('desktop')->router()->gen_url(array(), 1);
            }
            $url = base64_encode($url);
            $_SESSION['passport_redirect_url'] = $url;
            echo "<script>top.location = '?ctl=passport'</script>";
            exit;
        }
        $this->user = vmc::singleton('desktop_user');

        if ($_GET['ctl'] != 'passport' && $_GET['ctl'] != '') {
            $this->status = $this->user->get_status();
            if (!$this->status && $this->status == 0) {
                unset($_SESSION['account']);
                $url = app::get('desktop')->router()->gen_url(array(), 1);
                $url = base64_encode($url);
                $_SESSION['passport_redirect_url'] = $url;
                header('Content-Type:text/html; charset=utf-8');
                $this->pagedata['link_url'] = '?ctl=passport';
                echo $this->fetch('auth_error.html');
                exit;
            }
        }

        ###如果不是超级管理员就查询操作权限
        if (!$this->user->is_super()) {
            if (!$this->user->chkground()) {
                echo $this->fetch('auth_error.html');
                exit;
            }
        }
        
        $this->_finish_modifier = array();
        foreach (vmc::servicelist(sprintf('desktop_controller_content.%s.%s.%s', $_GET['app'], $_GET['ctl'], $_GET['act'])) as $class_name => $service) {
            if ($service instanceof desktop_interface_controller_content) {
                if (method_exists($service, 'modify')) {
                    $this->_finish_modifier[$class_name] = $service;
                }
                if (method_exists($service, 'boot')) {
                    $service->boot($this);
                }
            }
        }
        //修改tab detail 里的内容
        foreach (vmc::servicelist(sprintf('desktop_controller_content_finderdetail.%s.%s.%s.%s', $_GET['app'], $_GET['ctl'], $_GET['act'], (string) (isset($_GET['finderview']) ? $_GET['finderview'] : '0'))) as $class_name => $service) {
            if ($service instanceof desktop_interface_controller_content) {
                if (method_exists($service, 'modify')) {
                    $this->_finish_modifier[$class_name] = $service;
                }
                if (method_exists($service, 'boot')) {
                    $service->boot($this);
                }
            }
        }
        if ($this->_finish_modifier) {
            ob_start();
            register_shutdown_function(array(&$this,
                'finish_modifier',
            ));
        }
        $this->url = 'index.php?app='.$this->app->app_id.'&ctl='.$_GET['ctl'];
        foreach (vmc::servicelist('desktop_controller_destruct') as $service) {
            if (is_object($service) && method_exists($service, 'construct')) {
                $service->construct();
            }
        }
    }
    public function __destruct()
    {
        foreach (vmc::servicelist('desktop_controller_destruct') as $service) {
            if (is_object($service) && method_exists($service, 'destruct')) {
                $service->destruct($this);
            }
        } //todo: 析构
    }
    /*
     * 有modifier的处理程序
    */
    public function finish_modifier()
    {
        $content = ob_get_contents();
        ob_end_clean();
        foreach ($this->_finish_modifier as $modifier) {
            $modifier->modify($content, $this);
        }
        echo $content;
    }
    public function redirect($url)
    {
        $arr_url = parse_url($url);
        if ($arr_url['scheme'] && $arr_url['host']) {
            header('Location: '.$url);
        } else {
            header('Location: '.app::get('desktop')->base_url(1).$url);
        }
    }
    private function fix_location()
    {
        $current_request = vmc::singleton('base_component_request');
        if ($current_request->is_ajax() != true && $current_request->is_get()) {
            if (in_array(get_class($this), array(
                'desktop_ctl_default',
                'desktop_ctl_passport',
                'importexport_ctl_admin_export',
                'importexport_ctl_admin_import'
            ))) {
                return;
            }
            parse_str($_SERVER['QUERY_STRING'], $params);
            if ($params['singlepage'] || $params['act'] == 'printing') {
                return;
            }

            foreach ($params as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k1 => $v1) {
                        $arr[] = $k.'['.$k1.']'.':'.$v1;
                    }
                    continue;
                }
                $arr[] = $k.':'.$v;
            }
            if (!strpos($_SERVER['HTTP_REFERER'], 'ctl=passport')) {
                $arr[] = 'close_sidebar:1';
            }
            header('Location: .#'.implode('-', $arr));
        }
    }
    public function finder($object_name, $params = array())
    {
        header('cache-control: no-store, no-cache, must-revalidate');
        $_GET['action'] = $_GET['action'] ? $_GET['action'] : 'view';
        $finder = vmc::singleton('desktop_finder_builder_'.$_GET['action'], $this);
        foreach ($params as $k => $v) {
            $finder->$k = $v;
        }
        $app_id = substr($object_name, 0, strpos($object_name, '_'));
        $app = app::get($app_id);
        $finder->app = $app;
        $finder->work($object_name);
    }
    public function singlepage($view, $app_id = '')
    {
        return $this->display($view, $app_id);
    }
    public function page($view = '', $app_id = '')
    {
        $_SESSION['message'] = '';
        $service = vmc::service(sprintf('desktop_controller_display.%s.%s.%s', $_GET['app'], $_GET['ctl'], $_GET['act']));
        if ($service) {
            if (method_exists($service, 'get_file')) {
                $view = $service->get_file();
            }
            if (method_exists($service, 'get_app_id')) {
                $app_id = $service->get_app_id();
            }
        }
        if (!$view) {
            $view = 'common/default.html';
            $app_id = 'desktop';
        }
        ob_start();
        parent::display($view, $app_id);
        $output = ob_get_contents();
        ob_end_clean();
        $output = $this->sidebar_active().$output;
        $this->output($output);
    }
    public function sidebar_active()
    {
        $menuObj = app::get('desktop')->model('menus');
        $bcdata = $menuObj->get_allid($_GET);
        $output = '';
        if (!$this->workground) {
            $this->workground = get_class($this);
        }
        $output .= "<script>activeMenu('".($bcdata['workground_id'] ? $bcdata['workground_id'] : 0).':'.($bcdata['menu_id'] ? $bcdata['menu_id'] : 0)."');</script>";

        return $output;
    }
    public function output(&$output)
    {
        echo $output;
    } //End Function
    public function splash($status = 'success', $url = null, $msg = null, $method = 'redirect', $params = array())
    {
        header('Cache-Control:no-store, no-cache, must-revalidate'); // HTTP/1.1
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // 强制查询etag
        header('Progma: no-cache');
        $default = array(
            $status => $msg ? $msg : '操作成功' ,
            $method => $url,
        );
        $arr = array_merge($default, $params, array(
            'splash' => true,
        ));
        $json = json_encode($arr);
        if ($_FILES) {
            header('Content-Type: text/html; charset=utf-8');
        } else {
            header('Content-Type:application/json; charset=utf-8');
        }
        echo $json;
        exit;
    }
    /**
     * jump_to.
     *
     * @param string $act
     * @param string $ctl
     * @param array  $args
     */
    public function jumpTo($act = 'index', $ctl = null, $args = null)
    {
        $_GET['act'] = $act;
        if ($ctl) {
            $_GET['ctl'] = $ctl;
        }
        if ($args) {
            $_GET['p'] = $args;
        }
        if (!is_null($ctl)) {
            if ($pos = strpos($_GET['ctl'], '/')) {
                $domain = substr($_GET['ctl'], 0, $pos);
            } else {
                $domain = $_GET['ctl'];
            }
            $ctl = $this->app->single(str_replace('/', '-', $ctl));
            $ctl->message = $this->message;
            $ctl->pagedata = $this->pagedata;
            $ctl->ajaxdata = $this->ajaxdata;
            call_user_func(array(
                str_replace('/', '_', $ctl),
                $act,
            ), $args);
        } else {
            call_user_func(array(
                get_class($this),
                $act,
            ), $args);
        }
    }
    public function has_permission($perm_id)
    {
        $user = vmc::singleton('desktop_user');

        return $user->has_permission($perm_id);
    }
    public function pre_display(&$content)
    {
        parent::pre_display($content);
        if ($this->_ignore_pre_display === false) {
            foreach (vmc::serviceList('desktop_render_pre_display') as $service) {
                if (method_exists($service, 'pre_display')) {
                    $service->pre_display($content);
                }
            }
        }
    }
    public function get_view_filter($controller, $model)
    {
        $controller = vmc::singleton($controller);
        $object_name = $model;
        if (!isset($_POST['view'])) {
            return array();
        }
        list($app_id, $model) = explode('_mdl_', $object_name);
        if ($app_id != $controller->app->app_id) {
            return array();
        }
        if (method_exists($controller, '_views')) {
            $views = $controller->_views();
        }
        if (isset($views[$_POST['view']])) {
            return $views[$_POST['view']]['filter'];
        }
        //自定义筛选器
        $filter = app::get('desktop')->model('filter');
        $_filter = array(
            'model' => $object_name,
            'app' => $_POST['app'],
            'ctl' => $_POST['ctl'],
            'act' => $_POST['act'],
            'user_id' => $this->user->user_id,
        );
        $rows = $filter->getList('*', $_filter, 0, -1, 'create_time asc');
        if ($views) {
            end($views);
            $view_id = $_POST['view'] - key($views) - 1;
        } else {
            $view_id = $_POST['view'] - 1;
        }
        if ($rows[$view_id]) {
            parse_str($rows[$view_id]['filter_query'], $filter_query);
        }

        return $filter_query;
    }
}
