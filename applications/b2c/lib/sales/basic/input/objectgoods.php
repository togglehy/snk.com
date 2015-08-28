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


class b2c_sales_basic_input_objectgoods
{
    public $type = 'objectgoods';
    /**
     * 构建促销规则商品选择 输入
     * $data array 参数
     * $object string  对象
     */
    public function create($data, $object = '')
    {
        $render = app::get('b2c')->render();
        $render->pagedata['data'] = $data;
        $render->pagedata['object'] = $object;
        return $render->fetch('admin/sales/input/object_goods.html');
    }
}
