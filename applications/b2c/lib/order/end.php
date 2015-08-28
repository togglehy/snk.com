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


class b2c_order_end
{
    /**
     * 订单完成\归档.
     *
     * @param $sdf array 订单ID\操作者ID\操作者名称
     *
     * @return bool - 成功与否
     */
    public function generate($sdf, &$msg = '')
    {
        $mdl_order = app::get('b2c')->model('orders');
        $order_sdf = $mdl_order->dump($sdf['order_id']);

        //订单作废前验证
        foreach (vmc::servicelist('b2c.order.end.finish') as $service) {
            if (!$service->exec($order_sdf, $msg)) {
                return false;
            }
        }

        $order_sdf['status'] = 'finish';
        // 更新退款日志结果
        if ($mdl_order->save($order_sdf)) {
            //订单日志记录
            vmc::singleton('b2c_order_log')->set_operator(array(
                'ident' => $sdf['op_id'] ? $sdf['op_id'] : $order_sdf['member_id'],
                'model' => $sdf['op_id'] ? 'shopadmin' : 'members',
                'name' => $sdf['op_name'] ? $sdf['op_name'] : '会员',
            ))->set_order_id($order_sdf['order_id'])->success('finish', '订单已完成归档!', $order_sdf);
        } else {
            $msg = '完成\归档失败!';

            return false;
        }

        //订单作废时同步扩展服务
        foreach (vmc::servicelist('b2c.order.end.finish') as $service) {
            if (!$service->exec($order_sdf, $msg)) {
                //记录日志，不中断
                logger::error($sdf['order_id'].'完成归档时出错！'.$msg);
            }
        }

        return true;
    }
}
