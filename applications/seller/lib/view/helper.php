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

class seller_view_helper
{
    public function __construct($app)
    {
        $this->app = $app;
    }

	public function modifier_producttype($result)
    {
        switch ($result) {
        case '0':
            return ' 生鲜';
            break;
        case '1':
            return '冷冻';
            break;
		case '2':
            return '禽';
            break;
        }
    }
}