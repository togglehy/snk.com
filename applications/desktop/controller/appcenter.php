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


class desktop_ctl_appcenter extends desktop_controller
{
    public $require_super_op = true;
    public function __construct(&$app)
    {
        parent::__construct($app);
        if (!$this->user->is_super()) {
            header('Content-Type:text/html; charset=utf-8');
            echo '您无权操作';
            exit;
        }
    } //End Function
    public function index()
    {
        $mdl_appscenter = app::get('base')->model('apps');
        $this->pagedata['apps'] = $mdl_appscenter->getList('*');
        $this->pagedata['core_apps'] = $mdl_appscenter->getList('*', array('app_id' => $locked_app_ids));
        $this->page('appcenter/index.html');
    }
    public function prepare()
    {
        if (method_exists($this, 'prepare_'.$_POST['action'])) {
            $prepare_result = $this->{'prepare_'.$_POST['action']}($_POST['app_id']);
            foreach ($prepare_result['queue'] as $k => $queue) {
                $prepare_result['queue'][$k]['data'] = serialize($queue['data']);
            }
            echo json_encode($prepare_result);
        }
    }
    public function command()
    {
        if (method_exists($this, 'command_'.$_GET['command_id'])) {
            $this->{'command_'.$_GET['command_id']}(unserialize($_GET['data']));
            echo "\nok.";
        }
    }
    public function maintenance()
    {
        vmc::singleton('base_shell_webproxy')->exec_command('update');
    }
    public function fetchindex()
    {
        vmc::singleton('base_shell_webproxy')->exec_command('update --sync-only');
    }
    private function prepare_install($app_id)
    {
        $depends_install = '以下应用将被安装, 是否继续?'."\n";
        $install_queue = vmc::singleton('base_application_manage')->install_queue(array(
            $app_id,
        ));
        if (vmc::singleton('base_application_manage')->has_conflict_apps(array_keys($install_queue), $conflict_info)) {
            foreach ($conflict_info as $conflict_app_id => $conflict_detail) {
                $conflict_app_info = app::get($conflict_app_id)->define();
                $conflict_message .= (($conflict_app_id == $app_id || app::get($conflict_app_id)->status() != 'uninstalled') ? $conflict_app_info['name'] : $conflict_app_info['name'].'(被依赖)').' '.'与'.' '.$conflict_detail['name'].' '.'存在冲突'."\n";
            }

            return array(
                'status' => 'alert',
                'message' => $conflict_message.'请手工卸载冲突应用' ,
                'queue' => array() ,
            );
        } //todo：安装时判断app冲突，检测包括所有依赖的app和现有安装app之间的冲突
        $queue = array();
        $download_queue = array();
        foreach ($install_queue as $queue_app_id => $appinfo) {
            $depends_install .= "\t".($queue_app_id == $app_id ? $appinfo['name'] : str_pad($appinfo['name'], 20)."\t".'(被依赖)')."\n";
            if (!file_exists(APP_DIR.'/'.$queue_app_id.'/app.xml') && !file_exists(EXTENDS_DIR.'/'.$queue_app_id.'/app.xml')) {
                $download_queue[] = array(
                    'type' => 'command',
                    'command_id' => 'download',
                    'data' => $queue_app_id,
                );
            }
            $queue[] = array(
                'type' => 'command',
                'name' => $appinfo['name'],
                'command_id' => 'install',
                'data' => $queue_app_id,
            );
        }
        if ($queue) {
            array_unshift($queue, array(
                'type' => 'dialog',
                'action' => 'install_options',
                'data' => array_keys($install_queue),
            ));
        }
        if ($download_queue) {
            $queue = array_merge($download_queue, $queue);
        }
        $return = array(
            'status' => 'confirm',
            'message' => $depends_install,
            'queue' => $queue,
        );

        return $return;
    }
    public function install_options()
    {
        $apps = unserialize($_GET['data']);
        if (!$apps) {
            return;
        }
        $rows = app::get('base')->model('apps')->getList('app_id,app_name', array(
            'app_id' => $apps,
        ));
        foreach ($rows as $r) {
            $apps_name[$r['app_id']] = $r['app_name'];
        }
        foreach ($apps as $app_id) {
            $option = app::get($app_id)->runtask('install_options');
            if (is_array($option) && count($option) > 0) {
                $install_options[$app_id] = $option;
            }
        }
        $this->pagedata['install_options'] = &$install_options;
        $this->pagedata['apps_name'] = &$apps_name;
        $this->display('appmgr/install.html');
    }
    public function app_console()
    {
        $this->pagedata['base_url'] = vmc::base_url();
        $this->display('appmgr/console.html');
    }
    private function command_install($app_id)
    {
        $shell = vmc::singleton('base_shell_webproxy');
        $shell->input = $_POST['options'];
        $shell->exec_command('install '.$app_id);
    }
    private function prepare_uninstall($app_id)
    {
        $depends_uninstall = '以下应用将被删除, 是否继续?'."\n";
        $uninstall_queue = vmc::singleton('base_application_manage')->uninstall_queue(array(
            $app_id,
        ));
        $queue = array();
        foreach ($uninstall_queue as $queue_app_id => $appinfo) {
            $depends_uninstall .= "\t".$appinfo[0].' '.($appinfo[1] ? "\t".'(依赖)' : '')."\n";
            $queue[] = array(
                'type' => 'command',
                'name' => $appinfo[0],
                'command_id' => 'uninstall',
                'data' => $queue_app_id,
            );
        }
        if ($queue) { //追加备份提示
            array_unshift($queue, array(
                'type' => 'dialog',
                'action' => 'uninstall_bakup',
                'data' => '',
            ));
        }
        $return = array(
            'status' => 'confirm',
            'message' => $depends_uninstall,
            'queue' => $queue,
        );

        return $return;
    }
    private function prepare_pause($app_id)
    {
        $depends_pause = '以下应用将被停用, 是否继续?'."\n";
        $pause_queue = vmc::singleton('base_application_manage')->pause_queue(array(
            $app_id,
        ));
        $queue = array();
        foreach ($pause_queue as $queue_app_id => $appinfo) {
            $depends_pause .= "\t".$appinfo[0].' '.($appinfo[1] ? "\t".'(依赖)' : '')."\n";
            $queue[] = array(
                'type' => 'command',
                'name' => $appinfo[0],
                'command_id' => 'pause',
                'data' => $queue_app_id,
            );
        }
        $return = array(
            'status' => 'confirm',
            'message' => $depends_pause,
            'queue' => $queue,
        );

        return $return;
    } //End Function
    private function prepare_active($app_id)
    {
        $depends_active = '以下应用将被启用或安装, 是否继续?'."\n";
        $active_queue = vmc::singleton('base_application_manage')->active_queue(array(
            $app_id,
        ));
        if (vmc::singleton('base_application_manage')->has_conflict_apps(array_keys($active_queue), $conflict_info)) {
            foreach ($conflict_info as $conflict_app_id => $conflict_detail) {
                $conflict_app_info = app::get($conflict_app_id)->define();
                $conflict_message .= (($conflict_app_id == $app_id || app::get($conflict_app_id)->status() != 'uninstalled') ? $conflict_app_info['name'] : $conflict_app_info['name'].'(被依赖)').' '.'与'.' '.$conflict_detail['name'].' '.'存在冲突'."\n";
            }

            return array(
                'status' => 'alert',
                'message' => $conflict_message.'请手工卸载冲突应用' ,
                'queue' => array() ,
            );
        } //todo：安装时判断app冲突，检测包括所有依赖的app和现有安装app之间的冲突
        $queue = array();
        $download_queue = array();
        foreach ($active_queue as $queue_app_id => $appinfo) {
            $depends_active .= "\t".($queue_app_id == $app_id ? $appinfo['name'] : str_pad($appinfo['name'], 20)."\t".'(被依赖)')."\n";
            if (!file_exists(APP_DIR.'/'.$queue_app_id.'/app.xml') && !file_exists(EXTENDS_DIR.'/'.$queue_app_id.'/app.xml')) {
                $download_queue[] = array(
                    'type' => 'command',
                    'command_id' => 'download',
                    'data' => $queue_app_id,
                );
                $queue[] = array(
                    'type' => 'command',
                    'name' => $appinfo['name'],
                    'command_id' => 'install',
                    'data' => $queue_app_id,
                );
            } elseif (app::get($queue_app_id)->status() == 'paused') {
                $queue[] = array(
                    'type' => 'command',
                    'name' => $appinfo['name'],
                    'command_id' => 'active',
                    'data' => $queue_app_id,
                );
            } else {
                $queue[] = array(
                    'type' => 'command',
                    'name' => $appinfo['name'],
                    'command_id' => 'install',
                    'data' => $queue_app_id,
                );
            }
        }
        if ($queue) {
            array_unshift($queue, array(
                'type' => 'dialog',
                'action' => 'install_options',
                'data' => array_keys($install_queue),
            ));
        }
        if ($download_queue) {
            $queue = array_merge($download_queue, $queue);
        }
        $return = array(
            'status' => 'confirm',
            'message' => $depends_active,
            'queue' => $queue,
        );

        return $return;
    } //End Function
    public function uninstall_bakup()
    {
        $this->display('system/backup/check.html');
    }
    private function command_pause($app_id)
    {
        vmc::singleton('base_shell_webproxy')->exec_command('pause '.$app_id);
    } //End Function
    private function command_active($app_id)
    {
        vmc::singleton('base_shell_webproxy')->exec_command('active '.$app_id);
    } //End Function
    private function command_uninstall($app_id)
    {
        
        vmc::singleton('base_shell_webproxy')->exec_command('uninstall '.$app_id);
    }
    private function command_download($app_id)
    {
        vmc::singleton('base_shell_webproxy')->exec_command('update --force-download --download-only '.$app_id);
    }
    private function command_update($app_id)
    {
        vmc::singleton('base_shell_webproxy')->exec_command('update '.$app_id);
    }
    private function prepare_download($app_id)
    {
        $return = array(
            'queue' => array(
                array(
                    'type' => 'command',
                    'name' => $app_id,
                    'command_id' => 'download',
                    'data' => $app_id,
                ) ,
            ),
        );

        return $return;
    }
    private function prepare_update($app_id)
    {
        $return = array(
            'queue' => array(
                array(
                    'type' => 'command',
                    'name' => $app_id,
                    'command_id' => 'update',
                    'data' => $app_id,
                ) ,
            ),
        );

        return $return;
    }
}
