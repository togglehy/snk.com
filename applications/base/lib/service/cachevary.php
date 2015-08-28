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




class base_service_cachevary
{
    public function get_varys()
    {
        $varys['HOST'] = vmc::base_url(true);    //host信息
        $varys['REWRITE'] = (defined('URL_REWRITE')) ? URL_REWRITE : '';  //是否有rewirte支持
        $varys['LANG'] = vmc::get_lang(); //语言环境
        return $varys;
    }//End Function

}//End Class
