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


class b2c_order_delivery {

    function __construct($app) {
        $this->app = $app;
        $this->mdl_delivery = app::get('b2c')->model('delivery');
        $this->omath = vmc::singleton('ectools_math');
    }
    /**
     * 发货\退货单据数组组织
     * @delivery_sdf array - 发\退货单据SDF基础数据
     * @send_arr 发货情况数组   $send_arr = array($order_item_id=>$send_num);
     * @msg string 错误消息
     * @return boolean - 创建成功与否
     */
    public function generate(&$delivery_sdf, $send_arr, &$msg = '') {
        if (!$delivery_sdf['delivery_type']) {
            $msg = '未知货单类型,发货？退货？';
            return false;
        }
        $dtype = $delivery_sdf['delivery_type'];

        if (empty($delivery_sdf['delivery_id'])) {
            try {
                $delivery_sdf['delivery_id'] = $delivery_id = $this->mdl_delivery->apply_id($delivery_sdf);
            }
            catch(Exception $e) {
                $msg = $e->getMessage();
                return false;
            }
            $delivery_sdf['createtime'] = time();
        }
        $order_items = app::get('b2c')->model('order_items')->getList('*', array(
            'order_id' => $delivery_sdf['order_id']
        ));
        if (!$order_items || !count($order_items)) {
            $msg = '订单' . $delivery_sdf['order_id'] . '明细异常';
            return false;
        }
        //$partial = false;
        foreach ($order_items as $item) {
            $max_send = ($item['nums'] - $item['sendnum']); //最多应发
            if($dtype == 'reship'){
                $max_send = $item['sendnum']; //当单据类型为退货单据时，计算最多能退
            }
            $real_send = $send_arr[$item['item_id']]; //实际
            if ($max_send - $real_send < 0) {
                $msg = $item['name'].$item['bn'].($dtype == 'reship'?'退货':'发货').'数量异常';
                return false;
            }

            $delivery_sdf['delivery_items'][] = array(
                'delivery_id' => $delivery_id,
                'order_item_id' => $item['item_id'],
                'item_type' => $item['item_type'],
                'product_id' => $item['product_id'],
                'goods_id' => $item['goods_id'],
                'bn' => $item['bn'],
                'spec_info' => $item['spec_info'],
                'image_id' => $item['image_id'],
                'weight' => $item['weight'],
                'name' => $item['name'],
                'sendnum' => $real_send
            );
        }
        $service_key = 'b2c.order.delivery.' . $delivery_sdf['delivery_type'] . '.before';
        foreach (vmc::servicelist($service_key) as $service) {
            if (method_exists($service, 'exec')) {
                if (!$service->exec($delivery_sdf, $send_arr, $msg = '')) {
                    return false;
                }
            }
        }

        return true;
    }
    //保存单据数据
    public function save(&$delivery_sdf, &$msg = '') {

        if (!$this->mdl_delivery->save($delivery_sdf)) {
            $msg = '单据保存失败';
            return false;
        } else {
            //时同步扩展服务
            foreach (vmc::servicelist('b2c.order.delivery.' . $delivery_sdf['delivery_type'] . '.finish') as $service) {
                if (!$service->exec($delivery_sdf, $msg)) {
                    logger::error($delivery_sdf['delivery_id'] . $delivery_sdf['delivery_type'] . '单据保存出错！' . $msg);
                    return false; //直接中断

                }
            }
        }
        return true;
    }
}
