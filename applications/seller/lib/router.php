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

    function gen_url($params=array(),$full=false){

        $url_array = array(
            $params['ctl']?$params['ctl']:'default',
            $params['act']?$params['act']:'index',
        );

        unset($params['ctl'],$params['act']);

        foreach($params as $k=>$v){
            $url_array[] = $k;
            $url_array[] = $v;
        }

        return $this->app->base_url($full).implode('/',$url_array);
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
