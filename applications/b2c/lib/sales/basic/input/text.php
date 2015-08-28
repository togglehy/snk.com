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



class b2c_sales_basic_input_text
{
    public $type = 'text';
    public function create($aData, $table_info=array()) {
        return '<input type="text" class="input-sm" name="'.$aData['name'].'" required=true value="'.$aData['default'].'"  />'.$aData['desc'];
    }
}
?>
