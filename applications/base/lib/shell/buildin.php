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


class base_shell_buildin extends base_shell_prototype
{
    public $vars;

    public $command_alias = array(
            'ls' => 'list',
            'q' => 'exit',
            'quit' => 'exit',
        );

    public function php_call()
    {

        if ($this->vars) {
            extract($this->vars);
        }
        $this->output(eval(func_get_arg(0)));
        $this->vars = get_defined_vars();
    }

    public function command_reset()
    {

        $this->cmdlibs = null;
        $this->vars = null;
    }

    public $command_help_options = array(
            'verbose' => array('title' => '显示详细信息','short' => 'v'),
        );

    public function command_help($help_item = null, $shell_command = null)
    {
        if ($help_item) {
            list($app_id, $package) = explode(':', $help_item);
            $this->app_help($app_id, $package, $shell_command);
        } else {
            $this->help();
            printf("\033[40;33m");//设置颜色
            if ($handle = opendir(APP_DIR)) {
                while (false !== ($app_id = readdir($handle))) {
                    if ($app_id{0} != '.' && is_dir(APP_DIR.'/'.$app_id) && is_dir(APP_DIR.'/'.$app_id.'/lib/command')) {
                        $this->app_help($app_id);
                    }
                }
                closedir($handle);
            }
            printf("\033[40;32m");//设置颜色
        }
    }

    public function app_help($app_id, $package = null, $command = false)
    {
        if ($package) {
            $commander = $this->shell->get_commander($app_id, $package);
            $commander->help($command);
        } else {
            if ($handle = opendir(APP_DIR.'/'.$app_id.'/lib/command')) {
                while (false !== ($file = readdir($handle))) {
                    if (substr($file, -4, 4) == '.php' && is_file(APP_DIR.'/'.$app_id.'/lib/command/'.$file)) {
                        $commander = $this->shell->get_commander($app_id, substr($file, 0, -4));
                        if ($commander) {
                            $commander->help();
                        }
                    }
                }
                closedir($handle);
            }
        }
    }

    public function name_prefix()
    {
        return '';
    }

    public $command_exit = '退出';
    public function command_exit()
    {
        echo 'exit';
        exit;
    }

    public $command_ls = '列出所有应用';
    public function command_ls()
    {
        if(app::get('base')->status()=='uninstalled'){
            logger::info('系统未安装!请先运行install');
            return;
        }
        vmc::singleton('base_application_manage')->sync();
        $rows = app::get('base')->model('apps')->getlist('*');
        foreach ($rows as $k => $v) {
            $rows[$k] = array(
                    'app_id' => $v['app_id'],
                    'name' => $v['name'],
                    'version' => $v['version'],
                    'status' => $v['status'] ? $v['status'] : 'uninstalled',
                );
        }
        $this->output_line();
        echo "应用ID\t\t当前状态(uninstalled:未安装、active:正在运行、paused:暂停运行)";
        $this->output_line();
        echo "\n";
        $this->output_table($rows);
        $this->output_line();
    }

    public $command_install = '安装应用 e.g. install base';
    public $command_install_options = array(
               'reset' => array('title' => '重新安装','short' => 'r'),
                'options' => array('title' => '参数','short' => 'o','need_value' => 1),
            );
    public function command_install()
    {
        $args = func_get_args();
        $options = $this->get_options();
        if(count($args)<1){
            $args[0] = 'vmcshop';
        }
        $install_queue = vmc::singleton('base_application_manage')->install_queue($args, $options['reset']);
        $this->install_app_by_install_queue($install_queue, $options);
    }

    /**
     * 安装应用过程.
     *
     * @param array intall queue
     * @param array setup options
     */
    private function install_app_by_install_queue($install_queue, $options = array())
    {
        if (!$install_queue) {
            return;
        }

        if (vmc::singleton('base_application_manage')->has_conflict_apps(array_keys($install_queue), $conflict_info)) {
            foreach ($conflict_info as $conflict_app_id => $conflict_detail) {
                $conflict_app_info = app::get($conflict_app_id)->define();
                $conflict_message .= $conflict_app_info['name'].' '.'与'.' '.$conflict_detail['name'].' '.'存在冲突'."\n";
            }
            logger::info($conflict_message.'请手工卸载冲突应用'."\n");

            return false;
        }

        if ($options['options']) {
            parse_str($options['options'], $this->shell->input);
        }
        //---start---增加如果是通过web访问，则进行相关处理-------@lujunyi------
        if ($_SERVER['HTTP_USER_AGENT'] && !$this->shell->input) {
            $a = 'install '.$args[0].' -o "';
            foreach ((array) $install_queue as $app_id_ => $app_info_) {
                if (!$app_info_) {
                    logger::info('无法找到应用'.$app_id_."\n");
                    return false;
                }
                $install_options_ = app::get($app_id_)->runtask('install_options');
                if ($install_options_) {
                    $c = '';
                    foreach ($install_options_ as $key => $item) {
                        $b .= $app_id_.'['.$key.']='.$item['default'].'&';
                    }
                }
                $c .= $b;
            }
            $d = rtrim($c, '&');
            if (!empty($d)) {
                echo "请按以下格式输入数据：\n";
                $e = $a.$d.'"';
                echo $e;
                exit;
            };
        }
        //---end---
        foreach ((array) $install_queue as $app_id => $app_info) {
            if (!$app_info) {
                logger::info('无法找到应用'.$app_id."\n");
                return false;
            }
            $install_options = app::get($app_id)->runtask('install_options');

            if (is_array($install_options) && count($install_options) > 0 && !$this->shell->input[$app_id]) {
                do {
                    $this->shell->input_option($install_options, $app_id);
                } while (app::get($app_id)->runtask('checkenv', $this->shell->input[$app_id]) === false);
            }
            vmc::singleton('base_application_manage')->install($app_id, $this->shell->input[$app_id]);
        }
    }



    public $command_uninstall = '卸载应用 e.g. uninstall weixin';
    public $command_uninstall_options = array(
            'recursive' => array('title' => '递归删除依赖之app','short' => 'r'),
        );
    public function command_uninstall()
    {
        $args = func_get_args();
        $uninstall_queue = vmc::singleton('base_application_manage')->uninstall_queue($args);
        $options = $this->get_options();

        if (!$options['recursive']) {
            foreach ($uninstall_queue as $app_id => $type) {
                $to_delete[$type[1]][] = $app_id;
            }
            if ($to_delete[1]) {
                echo 'error in remove app '.implode(' ', $args)."\n";
                echo '以下应用依赖欲删除的app: '.implode(' ', $to_delete[1])."\n";
                echo '使用 -r 参数按依赖关系全部删除';

                return true;
            }
        }
        foreach ($uninstall_queue as $app_id => $type) {
            vmc::singleton('base_application_manage')->uninstall($app_id);
        }
    }

    public $command_pause = '暂停应用 e.g. pause mobile';
    public $command_pause_options = array(
            'recursive' => array('title' => '递归删除依赖之app','short' => 'r'),
        );
    public function command_pause()
    {
        $args = func_get_args();
        $pause_queue = vmc::singleton('base_application_manage')->pause_queue($args);
        $options = $this->get_options();

        if (!$options['recursive']) {
            foreach ($pause_queue as $app_id => $type) {
                $to_pause[$type[1]][] = $app_id;
            }
            if ($to_pause[1]) {
                echo 'error in pause app '.implode(' ', $args)."\n";
                echo '以下应用依赖欲暂停的app: '.implode(' ', $to_pause[1])."\n";
                echo '使用 -r 参数按依赖关系全部暂停';

                return true;
            }
        }
        foreach ($pause_queue as $app_id => $type) {
            vmc::singleton('base_application_manage')->pause($app_id);
        }
    }//End Function

    public $command_active = '开启应用 e.g. active mobile';
    public $command_active_options = array(
            'recursive' => array('title' => '递归删除依赖之app','short' => 'r'),
        );
    public function command_active()
    {
        $args = func_get_args();
        $active_queue = vmc::singleton('base_application_manage')->active_queue($args);
        $options = $this->get_options();
        if (!$active_queue) {
            return;
        }

        if (vmc::singleton('base_application_manage')->has_conflict_apps(array_keys($active_queue), $conflict_info)) {
            foreach ($conflict_info as $conflict_app_id => $conflict_detail) {
                $conflict_app_info = app::get($conflict_app_id)->define();
                $conflict_message .= $conflict_app_info['name'].' '.'与'.' '.$conflict_detail['name'].' '.'存在冲突'."\n";
            }
            logger::info($conflict_message.'请手工卸载冲突应用'."\n");

            return false;
        }//todo：安装时判断app冲突，检测包括所有依赖的app和现有安装app之间的冲突

        foreach ((array) $active_queue as $app_id => $app_info) {
            if (!$app_info) {
                logger::info('无法找到应用'.$app_id."\n");

                return false;
            }
            vmc::singleton('base_application_manage')->active($app_id);
        }
    }//End Function

    public $command_update = '更新应用 e.g. update mobile';
    public function command_update()
    {
        $args = func_get_args();
        if(app::get('base')->status()=='uninstalled'){
            logger::info('系统未安装!请先运行install');
            return;
        }

        if (!$args) {
            $rows = app::get('base')->model('apps')->getList('app_id', array('installed' => 1));
            foreach ($rows as $r) {
                if ($r['app_id'] == 'base') {
                    continue;
                }
                $args[] = $r['app_id'];
            }
        }
        array_unshift($args, 'base');
        $args = array_unique($args);
        foreach ($args as $app_id) {
            $appinfo = app::get('base')->model('apps')->getList('*', array('app_id' => $app_id));

            if (version_compare($appinfo[0]['local_ver'], $appinfo[0]['dbver'], '>') || $options['force-update-app']) {
                app::get($app_id)->runtask('pre_update', array('dbver' => $appinfo[0]['dbver']));
                vmc::singleton('base_application_manage')->update_app_content($app_id);
                app::get($app_id)->runtask('post_update', array('dbver' => $appinfo[0]['dbver']));
                app::get('base')->model('apps')->update(array('dbver' => $appinfo[0]['local_ver']), array('app_id' => $app_id));
            } else {
                vmc::singleton('base_application_manage')->update_app_content($app_id);
            }
        }
        logger::info('Applications update, complete!');
    }


    public $command_kvrecovery = '从数据库恢复KV';
    public function command_kvstorerecovery($instance = null)
    {
        return $this->command_kvrecovery($instance);
    }

    public function command_kvrecovery($instance = null)
    {
        if(app::get('base')->status()=='uninstalled'){
            logger::info('系统未安装!请先运行install');
            return;
        }
        base_kvstore::config_persistent(false);//临时禁用KV持久化 TODO 比较危险
        $testObj = base_kvstore::instance('test');
        if (get_class($testObj->get_controller()) === 'base_kvstore_mysql') {
            logger::info('The \'base_kvstore_mysql\' is default persistent, Not necessary recovery');
            exit;
        }
        logger::info('KVrecovery BEGIN...');
        $db = vmc::database();
        $count = $db->count('SELECT count(*) AS count FROM vmc_base_kvstore', true);
        if (empty($count)) {
            logger::info('No data recovery');
            exit;
        }
        $pagesize = 100;
        $page = ceil($count / 100);
        for ($i = 0; $i < $page; $i++) {
            $rows = $db->selectlimit('SELECT * FROM vmc_base_kvstore', $pagesize, $i * $pagesize);
            foreach ($rows as $row) {
                $arr_value = unserialize($row['value']);
                if (!$arr_value || !is_array($arr_value)) {
                    logger::error($row['prefix'].'=>'.$row['key'].' ... KVrecovery ERROR');
                }
                $row['value'] = $arr_value;
                if (base_kvstore::instance($row['prefix'])->recovery($row)) {
                    logger::info($row['prefix'].'=>'.$row['key'].' ... KVrecovery Success');
                } else {
                    logger::warning($row['prefix'].'=>'.$row['key'].' ... KVrcovery Failure');
                }
            }
        }
        logger::info('KVrecovery END...');
    }//End Function

    public $command_kvdelexpires = 'KV数据持久层过期数据清理';
    public function command_kvdelexpires()
    {
        logger::info('KVstore Delete Expires Data...');
        base_kvstore::delete_expire_data();
    }//End Function

    public $command_cacheclean = '清除缓存';
    public function command_cacheclean()
    {
        if(app::get('base')->status()=='uninstalled'){
            logger::info('系统未安装!请先运行install');
            return;
        }
        logger::info('Cache Clear...');
        cachemgr::init(true);
        if (cachemgr::clean($msg)) {
            logger::info($msg ? $msg : '...Clear Success');
        } else {
            logger::info($msg ? $msg : '...Clear Failure');
        }
        cachemgr::init(false);
    }//End Function

    public $command_init_demo = 'DEMO演示数据初始化';
    public function command_init_demo()
    {
        if(app::get('vmcshop')->status()=='uninstalled'){
            logger::info('VMCSHOP未安装!请先运行install');
            return;
        }
        return vmc::singleton('base_demo')->init();
    }


}
