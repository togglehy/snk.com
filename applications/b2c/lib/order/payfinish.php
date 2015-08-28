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


class b2c_order_payfinish
{
    /**
     * 公开构造方法.
     *
     * @params app object
     */
    public function __construct($app)
    {
        $this->app = $app;
    }
    /**
     * 订单支付后的处理.
     *
     * @params array 支付完的信息
     * @params 支付时候成功的信息
     */
    public function exec(&$bill, &$msg = '')
    {
        logger::debug($bill['order_id'].'payfinish exec');
        if ($bill['status'] != 'succ' && $bill['status'] != 'progress') {
            $msg = '支付其实没有完成!';

            return false;
        }
        $order_id = $bill['order_id'];
        if (!$order_id) {
            $msg = '未知订单ID';

            return false;
        }
        $omath = vmc::singleton('ectools_math');
        $mdl_orders = $this->app->model('orders');
        $order_sub_sdf = array(
            'items' => array(
                '*',
            ),
        );
        $order_sdf = $mdl_orders->dump($order_id, '*', $order_sub_sdf);
        //$order_sdf = $mdl_orders->dump($order_id);
        if (!$order_sdf) {
            $msg = '未知订单';

            return false;
        }
        if ($order_sdf['pay_status'] == '1') {
            $msg = '重复在支付订单'.date('Y-m-d H:i:s');

            return fasle;
        }
        if ($order_sdf['pay_status'] == '2' && $bill['status'] == 'progress') {
            $msg = '重复在支付订单'.date('Y-m-d H:i:s');

            return fasle;
        }
        $payed = $omath->number_plus(array(
            $bill['money'],
            $order_sdf['payed'],
        ));
        switch ($bill['status']) {
            case 'succ':
                $update['pay_status'] = '1'; //支付完成
                if ($payed < $order_sdf['order_total']) {
                    $update['pay_status'] = '3'; //部分支付
                }
            break;
            case 'progress':
                $update['pay_status'] = '2'; //付款到了担保方

            break;
            default:
                return false;
        }
        $update['payed'] = $payed;
        if (!$mdl_orders->update($update, array('order_id' => $order_id))) {
            $msg = '订单主单据信息更新失败!';

            return false;
        }
        //订单日志记录
        vmc::singleton('b2c_order_log')->set_operator(array(
            'ident' => $order_sdf['member_id'],
            'model' => 'members',
            'name' => '会员',
        ))->set_order_id($order_sdf['order_id'])->success('payment', '订单支付成功', $bill);

        //积分兑现
        $integral_change_flag = vmc::singleton('b2c_member_integral')->change(array(
            'member_id'=>$order_sdf['member_id'],
            'order_id'=>$order_sdf['order_id'],
            'change'=>$order_sdf['score_g'],//订单交易可得分值
            'change_time'=>time(),
            //'change_expire'=>null, //TODO 积分过期时间
            'change_reason'=>'order',//原因
            'op_model'=>$bill['op_id']?'shopadmin':'member',
            'op_id'=>$bill['op_id']
        ),$msg);
        if(!$integral_change_flag){
            logger::error('积分兑现失败!ORDER_ID:'.$order_sdf['order_id'].','.$msg);
        }
        //经验值  经验值变更会自动check 会员等级
        if(!vmc::singleton('b2c_member_exp')->renew($order_sdf['member_id'])){
            logger::error('经验值兑现失败!ORDER_ID:'.$order_sdf['order_id'].','.$msg);
        }

        //消息通知
         $pam_data = vmc::singleton('b2c_user_object')->get_pam_data('*', $order_sdf['member_id']);
         $pay_app = app::get('ectools')->model('payment_applications')->dump($bill['pay_app_id']);
         $env_list = array(
             'order_id'=>$bill['order_id'],
             'bill_id'=>$bill['bill_id'],
             'pay_app_name'=>$pay_app['display_name'],
             'out_trade_no'=>$bill['out_trade_no'],
             'money'=>ectools_cur::format($bill['money']),
             'timestr'=>date('Y-m-d H:i:s',$bill['last_modify']),
         );

        vmc::singleton('b2c_messenger_stage')->trigger('orders-payed', $env_list, array(
            'email' => $pam_data['email'] ? $pam_data['email']['login_account'] : $order_sdf['consignee']['email'],
            'member_id' => $order_sdf['member_id'],
            'mobile' => $pam_data['mobile'] ? $pam_data['mobile']['login_account'] : $order_sdf['consignee']['mobile'],
        ));

        if ($order_sdf['is_cod'] != 'Y') {
            $freeze_data = array();
            foreach ($order_sdf['items'] as $key => $item) {
                //购买计数
                vmc::singleton('b2c_openapi_goods', false)->counter(array(
                    'goods_id' => $item['goods_id'],
                    'buy_count' => $item['nums'],
                    'buy_count_sign' => md5($item['goods_id'].'buy_count'.($item['nums'] * 1024)),//计数签名
                ));
                //组织冻结库存数组
                $freeze_data[] = array(
                    'sku' => $item['bn'],
                    'quantity' => $item['nums'],
                );
            }
            //库存冻结
            if (!vmc::singleton('b2c_goods_stock')->freeze($freeze_data, $msg)) {
                logger::error('库存冻结异常!ORDER_ID:'.$order_sdf['order_id'].','.$msg);
            }
        }

        return true;
    }
}
