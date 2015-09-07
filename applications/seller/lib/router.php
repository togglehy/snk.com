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

class seller_router extends site_router
{  
	 public function gen_url($params = array())
    {
        $app = $params['app'];
        if (empty($app)) {
            return '/';
        }
		if($app == 'site')
		{
			return parent::gen_url($params);
		}
        if (!is_null($this->get_urlmap($params['app'].':'.$params['ctl']))) {
            if (is_array($params['args'])) {
                ksort($params['args']);
            }
            ksort($params);
            $gen_key = md5(serialize($params)); //todo：缓存相同的url
            if (!isset($this->__gen_url_array[$gen_key])) {
                foreach ($params as $k => $v) {
                    if ($k != 'args' && substr($k, 0, 3) == 'arg') {
                        if (empty($v)) {
                            unset($params['args'][substr($k, 3) ]);
                        } else {
                            $params['args'][substr($k, 3) ] = $v;
                        }
                    }
                } //fix smarty function
                $params['args'] = (is_array($params['args'])) ? $this->encode_args($params['args']) : array();
                if (!isset($this->__site_router_service[$app])) {
                    $app_router_service = vmc::service('site_router.'.$app);
                    if (is_object($app_router_service) && $app_router_service->enable()) {
                        $this->__site_router_service[$app] = $app_router_service;
                    } else {
                        $this->__site_router_service[$app] = false;
                    }
                }
                if ($this->__site_router_service[$app]) {
                    $this->__gen_url_array[$gen_key] = $this->__site_router_service[$app]->gen_url($params);
                } else {
                    $this->__gen_url_array[$gen_key] = $this->default_gen_url($params);
                }
                $this->__gen_url_array[$gen_key] = utils::_filter_crlf($this->__gen_url_array[$gen_key]);
            }

            return $this->__gen_url_array[$gen_key];
        } else {
            return '/';
        }
    } 
	
	// 商家2015/9/6
	protected function check_expanded_name()
    {		
        if (!array_key_exists($this->app->app_id . $this->get_query_info('module'), $this->get_sitemap())) {
            $this->http_status(404); //404页面
        }
        if (app::get('site')->getConf('base_check_uri_expanded_name') == 'true' && $this->get_uri_expended_name($this->get_query_info('module')) != $this->get_query_info('extension')) {
            $this->http_status(404); //404页面
        }
    }
	protected function get_current_sitemap($key = null)
    {		
		// 2015/9/6
		$module = $this->get_query_info('module');
		$curModule = $this->app->app_id;
		$module == 'index' || $curModule .= $module;		
		// 		
        if ($key === null) {			
            $result = $this->_sitemap[$curModule];
        }		
		$result = $this->_sitemap[$curModule][$key];
		return $result;
    }
}
