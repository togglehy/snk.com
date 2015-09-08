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


 
class base_router implements base_interface_router{

    function __construct($app){
        $this->app = $app;
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
		echo "#" . $this->app->app_id . "#\n";
        return $this->app->base_url($full).implode('/',$url_array);
    }

    function dispatch($query){
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

        $controller->$action();
    }

}
