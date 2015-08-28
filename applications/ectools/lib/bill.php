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


class ectools_bill {
    /**
     * 共有构造方法
     * @params app object
     * @return null
     */
    public function __construct($app) {
        $this->app = $app;
        $this->mdl_bills = app::get('ectools')->model('bills');
    }
    /**
     * 创建账单
     * @params array - 账单数据
     * @params string - 支付单生成的记录
     * @return boolean - 创建成功与否
     */
    public function generate(&$sdf, &$msg = '') {
        if (!$sdf['bill_id']) {
            try {
                $sdf['bill_id'] = $this->mdl_bills->apply_id($sdf);
            }
            catch(Exception $e) {
                $msg = $e->getMessage();
                return false;
            }
        }
        if ($sdf['pay_object'] == 'order' && empty($sdf['order_id'])) {
            $msg = '未知订单号';
            return false;
        }
        if (empty($sdf['money']) || $sdf['money'] < 0) {
            $msg = '金额错误';
            return false;
        }
        $sdf['ip'] = base_request::get_remote_addr();
        $sdf['pay_mode'] = $sdf['pay_mode']?$sdf['pay_mode']:(in_array($order_sdf['pay_app_id'], array(
            '-1',
            'cod',
            'offline',
        )) ? 'offline' : 'online');
        switch ($sdf['pay_mode']) {
            case 'online':
                if ($sdf['bill_type'] == 'payment' && empty($sdf['pay_app_id'])) {
                    $msg = "未知在线付款应用程序";
                    return false;
                }
            break;
            case 'offline':
                $sdf['status'] = 'succ';
                //case 'deposit':
            break;
            default:
                $msg = "暂不支持".$sdf['pay_mode'];
                return false;
        }
        if (!$this->mdl_bills->save($sdf)) {
            $msg = '单据保存失败';
            return fasle;
        } else {
            switch ($sdf['status']) {
                case 'succ':
                case 'progress':
                    $service_key = implode('.', array(
                        "ectools.bill",
                        $sdf['bill_type'],
                        $sdf['pay_object'],
                        $sdf['status']
                    ));/*
                    *订单付款成功  ectools.bill.payment.order.succ
                    *订单付款到担保方成功  ectools.bill.payment.order.progress
                    *订单退款成功  ectools.bill.refund.order.succ
                    *订单退款到担保方成功  ectools.bill.refund.order.progress
                    */
                    logger::debug('支付单据保存成功,支付成功！service_key:'.$service_key);
                    foreach (vmc::servicelist($service_key) as $service) {
                        if (!$service->exec($sdf,$msg)) {
                            logger::error('支付成功回调service出错:'.$msg.'|bill_id:'.$sdf['bill_id']);
                            break;
                        }
                    }
                    break;
                default:
                    logger::debug('支付单据保存成功！'.var_export($sdf,1));
                    break;
                }
            }
            return true;
        }
    }
