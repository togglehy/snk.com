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
 * 购物车项预处理接口
 * $ 2010-04-29 11:55 $
 */
interface b2c_interface_cart_postfilter{
    /**
     *
     *
     * @param array $aData    // $_POST&$_GET
     * @param array $aResult  // cart_objects
     * @param array $aConfig  // 订单修改时的入参
     */
    public function filter(&$aData,&$aResult,$aConfig = array());
}
?>
