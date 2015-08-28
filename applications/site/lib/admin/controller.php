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




class site_admin_controller extends desktop_controller
{

    /*
     * @param object $app
     */
    function __construct($app)
    {
        parent::__construct($app);
        $this->_request = vmc::singleton('base_component_request');
        $this->_response = vmc::singleton('base_component_response');
    }//End Function

    /*
     * 错误
     * @param string $msg
     */
    public function _error($msg='非法操作')
    {
        header("Content-type: text/html; charset=utf-8");
        echo $msg;exit;
    }//End Function

    protected function check($theme,&$msg='')
    {
        if(vmc::singleton('site_theme_file')->check($theme,$msg)){
            return true;
        }else{
            return false;
        }
    }//End Function

    /*
     * 跳转
     * @param string $url
     */
    public function _redirect($url)
    {
        $this->_response->set_redirect($url)->send_headers();
    }//End Function


}//End Class
