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



class desktop_view_helper{



    function block_help($params, $content, &$template_object){
        return "";
    }

    function block_permission($params, $content, &$tpl){
        //没有权限则增加属性diabled='true',以使不能编辑-@lujy
        if($params['perm_id'] && !$tpl->has_permission($params['perm_id'])){
            if($params['noshow']){return null;}
            $content = preg_replace('/readonly\s*=?\s*((["\']?)[\w\s\r\n-]*\2)?/i', '', $content);
            $content = preg_replace('/(<input|<select|<textarea|<button)/i', '$1 readonly', $content);
            return $content;
        }
        return $content;
    }

    function function_filter($params,&$smarty){
        $o = new desktop_finder_builder_filter_render();
        $o->name_prefix = $params['name'];
        if($params['app']){
            $app = app::get($params['app']);
        }else{
            $app = $smarty->app;
        }
        $html = $o->main($params['object'],$app,$filter,$smarty);
        echo $html;
    }

    function function_uploader($params, &$smarty){
        echo $smarty->_fetch_compile_include('desktop','system/tools/uploader.html',$params);
    }

    public function function_desktop_header($params, &$smarty)
    {
        $headers = $smarty->pagedata['headers'];
        if(is_array($headers)){
            foreach($headers AS $header){
                $html .= $header;
            }
        }//
        $services = vmc::servicelist("desktop_view_helper");
        foreach($services AS $service){
            if(method_exists($service, 'function_desktop_header'))
                $html .= $service->function_desktop_header($params, $smarty);
        }
        return $html;
    }//End Function

    public function function_desktop_footer($params, &$smarty)
    {
        $footers = $smarty->pagedata['footers'];
        if(is_array($footers)){
            foreach($footers AS $footer){
                $html .= $footer;
            }
        }//
        $services = vmc::servicelist("desktop_view_helper");
        foreach($services AS $service){
            $html .= $service->function_desktop_footer($params, $smarty);
        }
        return $html;
    }//End Function

    function modifier_userdate($timestamp){
        return utils::mydate(app::get('desktop')->getConf('format_date'),$timestamp);
    }

    function modifier_usertime($timestamp){
        return utils::mydate(app::get('desktop')->getConf('format_time'),$timestamp);
    }

}
