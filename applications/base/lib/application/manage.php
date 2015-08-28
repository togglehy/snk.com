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


class base_application_manage
{
    //应用程序资源探测器。
    //想添加自己的探测器? 注册服务: app_content_detector
    public static function content_detector($app_id = null)
    {
        $content_detectors = array(
            'list' => array(
                'base_application_dbtable',
                'base_application_service',
                'base_application_lang',
                'base_application_cache_expires',
                'base_application_crontab',
            ),
        );
        if ($app_id != 'base') {
            $content_detectors_addons = app::get('base')->model('app_content')->getlist('content_path,app_id', array(
                'content_type' => 'service',
                'content_name' => 'app_content_detector',
                'disabled' => 'false',
            ));
            foreach ($content_detectors_addons as $row) {
                $content_detectors['list'][$row['content_path']] = $row['content_path'];
            }
        }

        $service = new service($content_detectors);

        return $service;
    }
    public function uninstall_queue($apps)
    {
        if (is_string($apps)) {
            $apps = array(
                $apps,
            );
        }
        $rows = vmc::database()->select('select app_id,app_name from vmc_base_apps where status != "uninstalled"');
        $depends_apps_map = array();
        foreach ($rows as $row) {
            $namemap[$row['app_id']] = $row['app_name'];
            $depends_apps = app::get($row['app_id'])->define('depends/app');
            if ($depends_apps) {
                foreach ($depends_apps as $dep_app) {
                    $depends_apps_map[$dep_app['value']][] = $row;
                }
            }
        }
        foreach ($apps as $app_id) {
            $this->check_depends_uninstall($app_id, $depends_apps_map, $queue);
        }
        foreach ($apps as $app_id) {
            $queue[$app_id] = array(
                $namemap[$app_id],
                0,
            );
        }

        return $queue;
    }
    public function active_queue($apps)
    {
        if (is_string($apps)) {
            $apps = array(
                $apps,
            );
        }
        foreach ($apps as $app_id) {
            $this->check_active_install($app_id, $queue);
            $queue[$app_id] = app::get($app_id)->define();
        }

        return $queue;
    } //End Function
    private function check_active_install($app_id, &$queue)
    {
        $depends_app = app::get($app_id)->define('depends/app');
        foreach ((array) $depends_app as $depend_app_id) {
            $this->check_active_install($depend_app_id['value'], $queue);
        }
        if (app::get($app_id)->status() == 'uninstalled' || app::get($app_id)->status() == 'paused') {
            $queue[$app_id] = app::get($app_id)->define();
        }
    }
    public function pause_queue($apps)
    {
        if (is_string($apps)) {
            $apps = array(
                $apps,
            );
        }
        $rows = vmc::database()->select('select app_id,app_name from vmc_base_apps where status = "active"');
        $depends_apps_map = array();
        foreach ($rows as $row) {
            $namemap[$row['app_id']] = $row['app_name'];
            $depends_apps = app::get($row['app_id'])->define('depends/app');
            if ($depends_apps) {
                foreach ($depends_apps as $dep_app) {
                    $depends_apps_map[$dep_app['value']][] = $row;
                }
            }
        }
        foreach ($apps as $app_id) {
            $this->check_depends_uninstall($app_id, $depends_apps_map, $queue);
        }
        foreach ($apps as $app_id) {
            $queue[$app_id] = array(
                $namemap[$app_id],
                0,
            );
        }

        return $queue;
    } //End Function
    private function check_depends_uninstall($app_id, $depends_apps_map, &$queue)
    {
        if (isset($depends_apps_map[$app_id])) {
            foreach ($depends_apps_map[$app_id] as $to_delete) {
                $this->check_depends_uninstall($to_delete['app_id'], $depends_apps_map, $queue);
                $queue[$to_delete['app_id']] = array(
                    $to_delete['app_name'],
                    1,
                );
            }
        }
    }
    public function install_queue($apps, $force_install = false)
    {
        $queue = array();
        if (is_string($apps)) {
            $apps = array(
                $apps,
            );
        }

        foreach ($apps as $app_id) {
            $this->check_depends_install($app_id, $queue);
            if ($force_install) {
                $queue[$app_id] = app::get($app_id)->define();
            }
        }

        return $queue;
    }
    public function has_conflict_apps($apps, &$conflict_apps)
    {
        if (!vmc::is_online() || app::get('base')->status() == 'uninstalled') {
            return false;
        }
        if (is_string($apps)) {
            $apps = array(
                $apps,
            );
        }
        $queue = array();
        $installed_queue = array();
        $install_apps = array();
        $installed_apps = array();
        foreach ($apps as $app_id) {
            $install_apps[$app_id] = array();
            $this->check_conflicts_install($app_id, $queue);
        }
        $rows = vmc::database()->select('select app_id from vmc_base_apps where status != "uninstalled"');
        foreach ($rows as $row) {
            $installed_apps[$row['app_id']] = array();
            $this->check_conflicts_install($row['app_id'], $installed_queue);
        }
        $conflict_one = array_intersect_key($queue, $installed_apps);
        $conflict_two = array_intersect_key($installed_queue, $install_apps);
        $conflict_apps = array_merge($conflict_one, $conflict_two);

        return (count($conflict_apps)) ? true : false;
    } //End Function
    private function check_conflicts_install($app_id, &$queue)
    {
        $conflicts_app = app::get($app_id)->define('conflicts/app');
        foreach ((array) $conflicts_app as $conflict_app) {
            $conflict_app_id = $conflict_app['value'];
            $queue[$conflict_app_id] = app::get($app_id)->define();
        }
    } //End Function
    private function check_depends_install($app_id, &$queue)
    {
        $depends_app = app::get($app_id)->define('depends/app');
        foreach ((array) $depends_app as $depend_app_id) {
            $this->check_depends_install($depend_app_id['value'], $queue);
        }

        if (app::get($app_id)->status() == 'uninstalled') {
            //echo $app_id.'   uninstalled'."\n";
            $queue[$app_id] = app::get($app_id)->define();
        }
    }
    public function install($app_id, $options = null, $auto_enable = 1)
    {
        $app = app::get($app_id);
        if (!file_exists(APP_DIR.'/'.$app_id.'/app.xml')) {
            if (!$this->download($app_id)) {
                logger::info('Application package can not be download.');

                return false;
            }
        }
        if ($app_id != 'base' || app::get('base')->status() != 'uninstalled') {
            $rows = vmc::database()->select('select * from vmc_base_apps where app_id="'.$app_id.'"');
            if (isset($rows[0]) && $rows[0]['status'] != 'uninstalled') {
                logger::info('Application package Already installed');

                return false;
            }
            $app_self_detector = null;
            vmc::database()->exec('update vmc_base_apps set status="installing" where app_id="'.$app_id.'" and status="uninstalled" ');
        }
        $app->runtask('pre_install', $options);
        vmc::singleton('base_application_dbtable')->clear_by_app($app_id); //清除冗余表信息
        foreach ($this->content_detector($app_id) as $detector) {
            foreach ($detector->detect($app) as $name => $item) {
                $item->install();
            }
            vmc::set_online(true);
            base_kvstore::instance('system')->store('service_last_modified.'.get_class($detector).'.'.$app_id, $detector->last_modified($app_id));
        }
        //todo:clear service cache... 如果以后做了缓存的话...
        //用自己新安装的资源探测器，安装自己的资源
        foreach (vmc::servicelist('app_content_detector') as $k => $detector) {
            if ($detector->app->app_id == $app_id) {
                //遍历所有已经安装的app
                foreach ($detector->detect($app) as $name => $item) {
                    $item->install();
                }
                base_kvstore::instance('system')->store('service_last_modified.'.get_class($detector).'.'.$app_id, $detector->last_modified($app_id));
            }
        }
        app::get('base')->model('apps')->replace(array(
            'status' => 'installed',
            'app_id' => $app_id,
            'dbver' => $app->define('version'),
        ), array(
            'app_id' => $app_id,
        ));

        $app->runtask('post_install', $options);
        if ($auto_enable) {
            $this->enable($app_id);
        }

        logger::info('Application '.$app_id.' installed... ok.');
    }
    public function uninstall($app_id)
    {
        $this->disable($app_id);
        $app = app::get($app_id);
        $app->runtask('pre_uninstall');
        //对于BASE, 只要删除数据库即可  删无可删,无需再删
        if ($app_id == 'base') {
            vmc::singleton('base_application_dbtable')->clear_by_app('base');
        } else {
            foreach ($this->content_detector($app_id) as $detector) {
                $detector->clear_by_app($app_id);
            }
            app::get('base')->model('app_content')->delete(array(
                'app_id' => $app_id,
            ));
            $app->runtask('post_uninstall');
            app::get('base')->model('apps')->delete(array(
                'app_id' => $app_id,
            ));
        }
        logger::info('Application '.$app_id.' removed');
    }
    public function pause($app_id)
    {
        if ($app_id == 'base') {
            logger::info('Appication base can\'t be paused');
        } else {
            $row = vmc::database()->select('select app_id from vmc_base_apps where app_id = "'.$app_id.'" AND status = "active"');
            if (empty($row)) {
                logger::info('Application '.$app_id.' don\'t be pause');

                return;
            }
            $this->disable($app_id);
            $app = app::get($app_id);
            foreach ($this->content_detector($app_id) as $detector) {
                $detector->pause_by_app($app_id);
            }
            app::get('base')->model('app_content')->delete(array(
                'app_id' => $app_id,
            ));
            app::get('base')->model('apps')->update(array(
                'status' => 'paused',
            ), array(
                'app_id' => $app_id,
            ));
            logger::info('Application '.$app_id.' paused');
        }
    } //End Function
    public function active($app_id)
    {
        $row = vmc::database()->selectrow('select status from vmc_base_apps where app_id = "'.$app_id.'" AND status IN ("uninstalled", "paused") ');
        switch ($row['status']) {
            case 'paused':
                $this->enable($app_id);
                $app = app::get($app_id);
                foreach ($this->content_detector($app_id) as $detector) {
                    $detector->active_by_app($app_id);
                }
                //用自己新启用的资源探测器，启用自己的资源
                foreach (vmc::servicelist('app_content_detector') as $k => $detector) {
                    if ($detector->app->app_id == $app_id) {
                        //遍历所有已经安装的app
                        $detector->active_by_app($app_id);
                    }
                }
                app::get('base')->model('apps')->update(array(
                    'status' => 'active',
                ), array(
                    'app_id' => $app_id,
                ));
                logger::info('Application '.$app_id.' actived');

                return;
            case 'uninstalled':
                $this->install($app_id);

                return;
            default:
                logger::info('Application '.$app_id.' don\'t be active');

                return;
        }
    } //End Function
    public function enable($app_id)
    {
        $app = app::get($app_id);
        $app->runtask('pre_enable');
        app::get('base')->model('app_content')->update(array(
            'disabled' => 'false',
        ), array(
            'app_id' => $app_id,
        ));
        app::get('base')->model('apps')->update(array(
            'status' => 'active',
        ), array(
            'app_id' => $app_id,
        ));
        $app->runtask('post_enable');
    }
    public function disable($app_id)
    {
        $app = app::get($app_id);
        $app->runtask('pre_disable');
        app::get('base')->model('app_content')->update(array(
            'disabled' => 'true',
        ), array(
            'app_id' => $app_id,
        ));
        app::get('base')->model('apps')->update(array(
            'status' => 'installed',
        ), array(
            'app_id' => $app_id,
        ));
        $app->runtask('post_disable');
    }
    public function download($app_id, $force = false)
    {
        echo $app_id;
        exit;
    }
    public function update_app_content($app_id, $autofix = true)
    {
        foreach ($this->content_detector($app_id) as $k => $detector) {
            $last_modified = $detector->last_modified($app_id);
            if (base_kvstore::instance('system')->fetch('service_last_modified.'.get_class($detector).'.'.$app_id, $current_define_modified) == false || $last_modified != $current_define_modified) {
                logger::info('Updating '.$k.'@'.$app_id.'.');
                if ($autofix) {
                    $detector->update($app_id);
                    base_kvstore::instance('system')->store('service_last_modified.'.get_class($detector).'.'.$app_id, $last_modified);
                }
            }
        }
    }
    public function sync()
    {
        logger::info('Updating Application library..');
        $xmlfile = tempnam(TMP_DIR, 'appdb_');
        $appdb = vmc::singleton('base_xml')->xml2array(file_get_contents($xmlfile), 'base_app');

        foreach ((array) $appdb['app'] as $app) {
            $data = array(
                'app_id' => $app['id'],
                'app_name' => $app['name'],
                'remote_ver' => $app['version'],
                'description' => $app['description'],
                'author_name' => $app['author']['name'],
                'author_url' => $app['author']['url'],
                'author_email' => $app['author']['email'],
                'remote_config' => $app,
            );
            app::get('base')->model('apps')->replace($data, array(
                'app_id' => $app['id'],
            ));
        }
        $this->update_local();
        logger::info('Application libaray is updated, ok.');
    }
    private function update_local_app_info($app_id)
    {
        $app = app::get($app_id)->define();
        $data = array(
            'app_id' => $app_id,
            'app_name' => $app['name'],
            'local_ver' => $app['version'],
            'description' => $app['description'],
            'author_name' => $app['author']['name'],
            'author_url' => $app['author']['url'],
            'author_email' => $app['author']['email'],
        );
        app::get('base')->model('apps')->replace($data, array(
            'app_id' => $app_id,
        ));
    }
    public function update_local()
    {
        logger::info('Scanning local Applications... ');
        if ($handle = opendir(APP_DIR)) {
            while (false !== ($file = readdir($handle))) {
                if ($file{0} != '.' && is_dir(APP_DIR.'/'.$file) && file_exists(APP_DIR.'/'.$file.'/app.xml')) {
                    $this->update_local_app_info($file);
                }
            }
            closedir($handle);
        }
        logger::info('Scanning local Applications ok.');

        return $this->_list;
    }
}
