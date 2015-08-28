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


class desktop_ctl_cachestage extends desktop_controller
{
    public function index()
    {
        $this->pagedata['enable'] =
         (get_class(cachemgr::instance()) == 'base_cache_nocache') ? 'false' : 'true';
        if (cachemgr::status($status)) {
            // foreach ($status as $key => $value) {
            //     $status_obj[] = array(
            //         'label'=>$key,
            //         'data'=>$value
            //     );
            // }
            $this->pagedata['status'] = $status;
        }
        $this->pagedata['cache_name'] = cachemgr::instance()->name;
        $this->pagedata['static_cache'] = array();

        $this->pagedata['syscache'] = array(
            'setting_cache'=>syscache::instance('setting')->get_status(),
            'service_cache'=>syscache::instance('service')->get_status(),
        );

        $this->pagedata['kvstore'] = array(
            'name'=>vmc::singleton('base_kvstore')->get_controller()->name,
            'kvprefix'=>base_kvstore::kvprefix(),
        );

        foreach (vmc::servicelist('site.router.cache') as $value) {
            if (!method_exists($value, 'get_cache_methods')) {
                continue;
            }
            $methods = $value->get_cache_methods();
            foreach ((array) $methods as $method) {
                if (isset($method['app']) && isset($method['ctl']) && isset($method['act'])) {
                    if ($expires = app::get('site')->getConf($method['app'].'_'.$method['ctl'].'_'.$method['act'].'.cache_expires')) {
                        $method['expires'] = $expires;
                    }
                    $this->pagedata['static_cache'][] = $method;
                }
            }
        }

        foreach (vmc::servicelist('mobile.router.cache') as $value) {
            if (!method_exists($value, 'get_cache_methods')) {
                continue;
            }
            $methods = $value->get_cache_methods();
            foreach ((array) $methods as $method) {
                if (isset($method['app']) && isset($method['ctl']) && isset($method['act'])) {
                    if ($expires = app::get('mobile')->getConf($method['app'].'_'.$method['ctl'].'_'.$method['act'].'.cache_expires')) {
                        $method['expires'] = $expires;
                    }
                    $this->pagedata['mstatic_cache'][] = $method;
                }
            }
        }

        $this->page('cachestage/index.html');
    }//End Function

    public function status()
    {
        $this->index();
    }

    public function cachemgr_clean()
    {
        $this->begin('index.php?app=desktop&ctl=cachestage');
        cachemgr::optimize($msg);
        $this->end(cachemgr::clean($msg), $msg);
    }//End Function
    public function kvstore_clean(){
        $this->begin();
        base_kvstore::delete_expire_data();
        $this->end(true);

    }
    public function router_cache_save()
    {
        $this->begin('');
        $expirse = $_POST;
        foreach ($expirse as $key => $value) {
            app::get('site')->setConf($key.'.cache_expires', (int) $value);
        }

        $this->end(true);
    }
    public function mrouter_cache_save()
    {
        $this->begin('');
        $expirse = $_POST;
        $expirse = $_POST;
        foreach ($expirse as $key => $value) {
            app::get('mobile')->setConf($key.'.cache_expires', (int) $value);
        }
        $this->end(true);
    }

    public function clean_syscache($k){
        $this->begin('index.php?app=desktop&ctl=cachestage');
        switch ($k) {
            case 'setting_cache':
                $this->end(syscache::instance('setting')->set_last_modify());
                break;
            case 'service_cache':
                $this->end(syscache::instance('service')->set_last_modify());
                break;
        }
    }

}//End Class
