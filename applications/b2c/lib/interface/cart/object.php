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



interface b2c_interface_cart_object{
    public function add_object($aData);    // 增加包括追加
    public function update($sIdent,$quantity,&$msg); // 更新
    public function get($sIdent = null,$rich = false);
    public function getAll($rich = false);
    public function delete($sIdent = null);
    public function deleteAll();
    public function count(&$aData);
}
?>
