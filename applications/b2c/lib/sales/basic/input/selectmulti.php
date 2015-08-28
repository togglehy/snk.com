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


 
class b2c_sales_basic_input_selectmulti
{
    public $type = 'selectmulti';
    public function create($aData, $table_info=array()) {
        $aData['multi'] = true;
        return vmc::singleton('b2c_sales_basic_input_selectmulti')->create($aData);
    }
}
?>
