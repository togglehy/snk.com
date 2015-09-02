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


ini_set('display_error', 1);
class seller_router implements base_interface_router
{
	/*
     * 系统模块对应数组, 通过路径标识取得对应的模块信息
     *
     * array('<路径标识>' => array('app'=>'<app_name>', 'ctl'=>'<controler_name>', title'='<title>', 'extension'=>'<url_extension_name>', 'use_ssl'=>'<ssl>')
     * @var array $_sitemap
     * @accessvar private
    */
    private $_sitemap = array();
    /*
     * sitemap对应表
     *
     * array('<app_name>:<ctl_name>' => <路径标识>')
     *
     * @var array $_urlmap
     * @access private
    */
    private $_urlmap = array();
    /*
     * @var array $_query_info
     * @access private
    */
    private $_query_info = null;
    /*
     * @var object $_request
     * @access private
    */
    private $_request = null;
    /*
     * @var object $_response
     * @access private
    */
    private $_response = null;
    /*
     * 保存当前进程的gen_url, 避免重复生成
     *
     * @var object $_response
     * @access private
    */
    private $__gen_url_array = array();
    /*
     * uri扩展名
     *
     * @var object $_response
     * @access private
    */
    private $__uri_expended_name = null;
    /*
     * 构造
     * @var object $app
     * @access public
     * @return void
    */

    function __construct($app)
	{ 
		$this->app = $app;
        $this->_sitemap = app::get('site')->getConf('sitemaps');
        if (!is_array($this->_sitemap)) {
            $sitemap_config = vmc::singleton('site_module_base')->assemble_config();
            if (is_array($sitemap_config)) {
                $this->_sitemap = $sitemap_config; //todo：兼容kvstroe出错的情况下
                if (!vmc::singleton('site_module_base')->write_config($sitemap_config)) {
                    logger::info('Error: sitemap can\'t save to kvstore'); //todo：如果写入失败，记录于系统日志中，前台不报错，保证网站运行正常
                }
            } else {
                trigger_error('sitemap is lost!', E_USER_ERROR); //todo：无sitemap时报错
            }
        }
        foreach ($this->_sitemap as $part => $controller) {
            $urlmap[$controller['app'].':'.$controller['ctl']] = $part;
            if ($controller['extension']) {
                $extmap[$part] = '.'.$controller['extension'];
            }
        }
        if (isset($urlmap)) {
            $this->_urlmap = $urlmap;
        }
        if (isset($extmap)) {
            $this->_extmap = $extmap;
        }
        $this->_request = vmc::singleton('base_component_request');
        $this->_response = vmc::singleton('base_component_response');
    }

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

    public function dispatch($query)
	{		
		$query_args = explode('/',$query);		
        $controller = array_shift($query_args);
        $action = array_shift($query_args);		
        if($controller == 'index.php'){
            $controller = '';
        }
        foreach($query_args as $i=>$v){
            if($i%2){
                $params[$k] = $v;
            }else{
                $k = $v;
            }
        }
        $controller = $controller?$controller:'default';		
        vmc::request()->set_params($params);
        $action = $action?$action:'index';
        $controller = $this->app->controller($controller);
		var_dump( $action);
        $controller->$action();
    }

}
