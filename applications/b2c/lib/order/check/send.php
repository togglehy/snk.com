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


class b2c_order_check_send {
    /**
     * 构造方法
     * @param object app
     */
    public function __construct($app) {
        $this->app = $app;
        $this->obj_math = vmc::singleton('ectools_math');
    }

    public function exec(&$delivery_sdf, $send_arr,&$msg = '') {
        $order = app::get('b2c')->model('orders')->dump($delivery_sdf['order_id']);
        if($order['ship_status']!='0'){
            //二次发货
            $stock_data = array();
            foreach ($delivery_sdf['delivery_items'] as $key => $value) {
                $stock_data[] = array(
                    'sku'=>$value['bn'],
                    'quantity'=>$value['sendnum']
                );
            }
            //触发冻结库存
            if(!vmc::singleton('b2c_goods_stock')->freeze($stock_data,$msg)){
                return false;
            }
        }
        return true;
    }


}
