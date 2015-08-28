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


class b2c_finder_stock
{

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function row_style($row)
    {
        $row = $row['@row'];

        // if($row['quantity'] - $row['freez_quantity'] <1){
        //     return ' has-error';
        // }
        // if($row['quantity'] - $row['freez_quantity'] <5){
        //     return ' has-warning';
        // }
    }
}
