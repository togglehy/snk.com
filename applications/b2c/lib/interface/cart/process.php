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



/**
 * 购物车处理接口
 * $ 2010-04-28 20:30 $
 */
interface b2c_interface_cart_process{
    public function process($aData,&$aResult=array(),$aConfig=array());
}
?>
