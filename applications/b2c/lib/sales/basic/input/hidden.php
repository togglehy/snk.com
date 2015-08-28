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


 
class b2c_sales_basic_input_hidden
{
    public $type = 'hidden';
    public function create($aData, $table_info=array()) {
        return '<input type="hidden" name="'.$aData['name'].'" value="'.$aData['default'].'" />'.$aData['desc'];
    }
}
?>
