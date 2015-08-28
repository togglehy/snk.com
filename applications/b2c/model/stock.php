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



class b2c_mdl_stock extends dbeav_model{
    var $defaultOrder = array('last_modify',' DESC');

    public function modifier_quantity($col,$row)
    {
        $pkey = $row['stock_id'];

        $_return = <<<HTML
            <input class='form-control edit-col input-sm input-xsmall' name="quantity" type='text' data-pkey='$pkey' value='$col'>
HTML;
        return $_return;
    }

    public function modifier_freez_quantity($col,$row)
    {
        $pkey = $row['stock_id'];

        $_return = <<<HTML
            <input class='form-control edit-col input-sm input-xsmall' name="freez_quantity" type='text' data-pkey='$pkey' value='$col'>
HTML;
        return $_return;
    }
}
