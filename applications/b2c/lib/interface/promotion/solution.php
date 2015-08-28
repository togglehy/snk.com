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



interface b2c_interface_promotion_solution
{
    public function config($config=array());
    public function apply(&$cart_object,$config,&$cart_result);
    public function apply_order(&$cart_object, $config, &$cart_result);
    public function getString();
    public function setString($aData);
    public function get_status();
}
?>
