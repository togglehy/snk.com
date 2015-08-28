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

class b2c_ctl_admin_order extends desktop_controller
{
    /**
     * 构造方法.
     *
     * @params object app object
     */
    public function __construct($app)
    {
        parent::__construct($app);
        $this->objMath = vmc::singleton('ectools_math');
    }
    public function index()
    {

        $actions_define = array(
            array(
                'label' => ('打印') ,
                'group' => array(
                    array(
                        'label' => ('打印样式配置') ,
                        'target' => '_blank',
                        'href' => 'index.php?app=b2c&ctl=admin_order&act=showPrintStyle',
                    ) ,
                    array(
                        'label' => '_SPLIT_',
                    ) ,
                    array(
                        'label' => ('打印订单详情') ,
                        'data-submit' => 'index.php?app=b2c&ctl=admin_order&act=toprint',
                        'data-target' => '_blank',
                    ) ,
                    array(
                        'label' => ('打印购物小票') ,
                        'data-submit' => 'index.php?app=b2c&ctl=admin_order&act=printing&p[0]=1',
                        'data-target' => '_blank',
                    ) ,
                    array(
                        'label' => ('打印配货清单') ,
                        'data-submit' => 'index.php?app=b2c&ctl=admin_order&act=printing&p[0]=2',
                        'data-target' => '_blank',
                    ) ,
                    array(
                        'label' => ('打印快递单') ,
                        'data-submit' => 'index.php?app=b2c&ctl=admin_order&act=printing&p[0]=8',
                        'data-target' => '_blank',
                    ),
                ),
            ),
        );
        $app_express = app::get('express');
        if (isset($app_express) && $app_express && is_object($app_express) && $app_express->is_actived()) {
        } else {
            unset($actions_define['group'][5]);
        }
        $this->finder('b2c_mdl_orders', array(
            'title' => ('订单列表'),
            'allow_detail_popup' => true,
            'base_filter' => array(
                'order_refer' => 'local',
                'disabled' => 'false',
            ),
            'use_buildin_export' => true,
            'use_buildin_set_tag' => true,
            'use_buildin_recycle' => false,
            'use_buildin_filter' => true,
            //    'actions' => $actions_define

        ));
    }
    /**
     * 查看\处理订单.
     */
    public function detail($order_id = false)
    {
        if (!$order_id) {
            trigger_error('未知订单ID', E_USER_ERROR);
        }

        $mdl_orders = $this->app->model('orders');
        $mdl_bills = app::get('ectools')->model('bills');
        $mdl_delivery = $this->app->model('delivery');
        $mdl_order_log = $this->app->model('order_log');
        $subsdf = array(
            'items' => array(
                '*',
            ) ,
            'promotions' => array(
                '*',
            ) ,
            ':dlytype' => array(
                '*',
            ),
        );
        $order = $mdl_orders->dump($order_id, '*', $subsdf);

        //邻居
        $border = $mdl_orders->get_border($order_id);
        $this->pagedata['border'] = $border;

        //订单状态标签
        $this->pagedata['order_status_label'] = vmc::singleton('b2c_finder_orders')->column_orderstatus($order);
        //订单操作按钮
        $this->pagedata['order_actions'] = $this->get_actions($order);
        $this->pagedata['order'] = $order;
        $this->pagedata['order_logs'] = $mdl_order_log->getList('*', array(
            'order_id' => $order_id,
        ));
        $this->pagedata['payapp'] = app::get('ectools')->model('payment_applications')->dump($order['pay_app']);
        $this->pagedata['member_info'] = vmc::singleton('b2c_user_object')->get_member_info($order['member_id']);

        /*
         * 相关单据
         */
        //付款
        $this->pagedata['order_payments'] = $mdl_bills->getList('*', array('bill_type' => 'payment', 'pay_object' => 'order', 'order_id' => $order_id));
        //发货
        $this->pagedata['order_delivery'] = $mdl_delivery->getlist('*', array('order_id' => $order_id, 'delivery_type' => 'send'));
        if($this->pagedata['order_delivery']){
            foreach ($this->pagedata['order_delivery'] as $key => &$item) {
                $item['items'] = app::get('b2c')->model('delivery_items')->getList('*',array('delivery_id'=>$item['delivery_id']));
            }
        }
        //退货
        $this->pagedata['order_reship'] = $mdl_delivery->getlist('*', array('order_id' => $order_id, 'delivery_type' => 'reship'));
        if($this->pagedata['order_reship']){
            foreach ($this->pagedata['order_reship'] as $key => &$item) {
                $item['items'] = app::get('b2c')->model('delivery_items')->getList('*',array('delivery_id'=>$item['delivery_id']));
            }
        }
        //退款
        $this->pagedata['order_refunds'] = $mdl_bills->getList('*', array('bill_type' => 'refund', 'pay_object' => 'order', 'order_id' => $order_id));

        $this->page('admin/order/detail.html');
    }
     /**
      * 编辑订单.
      */
     public function edit($order_id)
     {
         if (!$order_id) {
             trigger_error('未知订单号', E_USER_ERROR);
         }
         $mdl_orders = $this->app->model('orders');
         $subsdf = array(
             ':dlytype' => array(
                 '*',
             ),
         );
         $order = $mdl_orders->dump($order_id, '*', $subsdf);
         $this->pagedata['order'] = $order;
         $this->pagedata['payapp'] = app::get('ectools')->model('payment_applications')->dump($order['pay_app']);


         $this->display('admin/order/edit.html');
     }
     /**
      * 保存订单
      */
      public function save(){
          $params = $_POST;
          $order_id = $params['order_id'];
          if(!$order_id){
              trigger_error('未知订单号', E_USER_ERROR);
          }
          $this->begin('index.php?app=b2c&ctl=admin_order&act=detail&p[0]='.$order_id);
          $mdl_orders = $this->app->model('orders');
          $is_save = $mdl_orders->save($params);
          //记录日志
          $is_save && vmc::singleton('b2c_order_log')->set_operator(array(
              'ident' => $this->user->user_id,
              'model' => 'shopadmin',
              'name' => $this->user->user_data['account']['login_name'],
          ))->set_order_id($order_id)->success('update', '订单更新',$params);
          $this->end($is_save);
      }

      /**
       * 订单处理人员备注.
       */
      public function remarks($order_id)
      {
          $this->begin();
          $mdl_orders = $this->app->model('orders');
          $last_marks = $mdl_orders->getRow('remarks', array('order_id' => $order_id));
          if ($mdl_orders->update(array('remarks' => $_POST['remarks']), array('order_id' => $order_id))) {
              //记录日志
              vmc::singleton('b2c_order_log')->set_operator(array(
                  'ident' => $this->user->user_id,
                  'model' => 'shopadmin',
                  'name' => $this->user->user_data['account']['login_name'],
              ))->set_order_id($order_id)->success('update', '订单备注更新', array('prev' => $last_marks['remarks'], 'current' => $_POST['remarks']));
              $this->end(true);
          }
          $this->end(false);
      }

       /**
        * 手工订单减价促销优惠.
        */
       public function discount($order_id)
       {
           $mdl_orders = $this->app->model('orders');
           $order = $mdl_orders->dump($order_id);
           $this->pagedata['order'] = $order;
           $this->display('admin/order/discount.html');
       }
    public function dodiscount($order_id)
    {
        $this->begin("index.php?app=b2c&ctl=admin_order&act=detail&p[0]=$order_id");
        $mdl_orders = $this->app->model('orders');
        $order = $mdl_orders->dump($order_id);
        if (!isset($_POST['discount']) || $_POST['discount'] <= 0) {
            $this->end(false, '请输入正确优惠金额！');
        }
        if ($order['status'] != 'active' || $order['pay_status'] != '0') {
            $this->end(false, '无法操作！');
        }

        $new_order_total = $this->objMath->number_minus(array(
               $order['order_total'],
               $_POST['discount'],
           ));
        $new_pmt_order = $this->objMath->number_plus(array(
               $order['pmt_order'],
               $_POST['discount'],
           ));

        if ($new_order_total <= 0) {
            $this->end(false, '优惠金额不能超过订单应付金额！');
        }
        $pmt_save = $_POST['discount'];
        $new_order_pmt = array(
               'rule_id' => -1,
               'order_id' => $order['order_id'],
               'pmt_type' => 'order',
               'pmt_tag' => '减价',
               'pmt_description' => '订单操作人员后台减价优惠操作',
               'pmt_solution' => '订单优惠'.vmc::singleton('ectools_mdl_currency')->changer($pmt_save),
               'pmt_save' => $pmt_save,
           );

        if (
           app::get('b2c')->model('order_pmt')->save($new_order_pmt) &&
           $mdl_orders->update(array('order_total' => $new_order_total, 'pmt_order' => $new_pmt_order), array('order_id' => $order_id))
           ) {
            //记录日志
               vmc::singleton('b2c_order_log')->set_operator(array(
                   'ident' => $this->user->user_id,
                   'model' => 'shopadmin',
                   'name' => $this->user->user_data['account']['login_name'],
               ))->set_order_id($order_id)->success('discount', '订单被操作员减价，理由：'.$_POST['discount_mark'], array(
                   'last' => $order['order_total'],
                   'current' => $order['order_total'] - $_POST['discount'],
               ));
            $this->end(true);
        }
        $this->end(false);
    }

    /**
     * 打印选定订单.
     *
     * @param null
     */
    public function toprint()
    {
    }
    /**
     * 打印订单.
     *
     * @param string 打印类型
     * @param string order id
     */
    public function printing($type, $order_id)
    {
    }
    /**
     * 产生支付页面.
     *
     * @params string order id
     *
     * @return string html
     */
    public function gopay($order_id)
    {
        $mdl_order = $this->app->model('orders');
        $order = $mdl_order->dump($order_id);
        $payapps = app::get('ectools')->model('payment_applications')->getList();
        foreach ($payapps as $papp) {
            $this->pagedata['payapps'][$papp['app_id']] = $papp['name'];
        }
        $order['require_pay'] = $this->objMath->number_minus(array(
            $order['order_total'],
            $order['payed'],
        ));
        $this->pagedata['order'] = $order;
        $this->display('admin/order/gopay.html');
    }
    /**
     * 订单开始支付.
     *
     * @params null
     */
    public function dopay()
    {
        $params = array_filter($_POST);
        $this->begin('index.php?app=b2c&ctl=admin_order&act=detail&p[0]='.$params['order_id']);
        $mdl_orders = $this->app->model('orders');
        $order = $mdl_orders->dump($params['order_id']);
        //账单生产类
        $obj_bill = vmc::singleton('ectools_bill');
        $bill_sdf = array(
            'bill_type' => 'payment',
            'pay_object' => 'order',
            'member_id' => $order['member_id'],
            'op_id' => $this->user->user_id,
            'status' => 'succ',
        );
        if (in_array($params['pay_app_id'], array(
            'cod',
            'offline',
        ))) {
            $bill_sdf['pay_mode'] = 'offline';
        }
        $bill_sdf = array_merge($bill_sdf, $params);
        $mdl_bills = app::get('ectools')->model('bills');
        //未交互过的账单复用
        $exist_bill = $mdl_bills->getRow('*', array(
            'member_id' => $order['member_id'],
            'order_id' => $order['order_id'],
            'status' => 'ready',
        ));
        if ($exist_bill) {
            $bill_sdf = array_merge($exist_bill, $bill_sdf);
        }
        if (!$obj_bill->generate($bill_sdf, $msg)) {
            $this->end(false, $msg);
        }
        $this->end(true, '订单支付成功！');
    }
    /**
     * 生成退款单页面.
     *
     * @params string order id
     *
     * @return string html
     */
    public function gorefund($order_id)
    {
        $mdl_order = $this->app->model('orders');
        $sdf_order = $mdl_order->dump($order_id);
        $this->pagedata['mode_list'] = array(
            'online' => ('在线') ,
            'offline' => ('线下') ,
            //'deposit' => ("退款到会员预存款账户") ,
        );
        $this->pagedata['order'] = $sdf_order;

        // 退还订单消费积分
        $this->pagedata['bills'] = app::get('ectools')->model('bills')->getList('*', array('bill_type' => 'payment', 'pay_status' => 'succ', 'order_id' => $order_id));

        $this->display('admin/order/gorefund.html');
    }
    /**
     * 退款处理.
     *
     * @params null
     */
    public function dorefund()
    {
        $params = $_POST;
        $return_score = $params['return_score'];
        unset($params['return_score']);
        $this->begin('index.php?app=b2c&ctl=admin_order&act=detail&p[0]='.$params['order_id']);
        $obj_order = $this->app->model('orders');
        $sdf_order = $obj_order->dump($params['order_id']);
        if (!$params['money']) {
            //退款金额不是从弹出的退款单里输入而来
            $params['money'] = $sdf_order['payed'];
            $params['return_score'] = $sdf_order['score_g'];
        }

        //账单生产类
        $obj_bill = vmc::singleton('ectools_bill');
        $bill_sdf = array(
            'bill_type' => 'refund',
            'pay_object' => 'order',
            'member_id' => $sdf_order['member_id'],
            'op_id' => $this->user->user_id,
            'status' => $params['status'] ? $params['status'] : 'succ',
        );

        $bill_sdf = array_merge($bill_sdf, $params);

        if (!$obj_bill->generate($bill_sdf, $msg)) {
            $this->end(false, $msg);
        } else {
            //退积分，即新建负积分记录对冲
            vmc::singleton('b2c_member_integral')->change(array(
                'member_id' => $sdf_order['member_id'],
                'order_id' => $sdf_order['order_id'],
                'change' => $return_score * -1,
                'change_reason' => 'refund',
                'op_model' => 'shopadmin',
                'op_id' => $this->user->user_id,
            ), $msg);
        }
        $this->end('true', '退款操作成功!', null, array('bill_id' => $bill_sdf['bill_id']));
    }
    /**
     * 产生订单发货页面.
     *
     * @params string order id
     *
     * @return string html
     */
    public function godelivery($order_id)
    {
        $mdl_orders = $this->app->model('orders');
        $subsdf = array(
            'items' => array(
                '*',
            ) ,
            ':dlytype' => array(
                '*',
            ),
        );
        $this->pagedata['dlycorp_list'] = $this->app->model('dlycorp')->getList('*', array(
            'disabled' => 'false',
        ));
        $this->pagedata['dlyplace_list'] = $this->app->model('dlyplace')->getList();
        $order = $mdl_orders->dump($order_id, '*', $subsdf);
        $this->pagedata['order'] = $order;
        $this->display('admin/order/godelivery.html');
    }
    /**
     * 订单退货页面.
     *
     * @params stirng orderid
     *
     * @return string html
     */
    public function goreship($order_id, $member_addr_id = false)
    {
        $mdl_orders = $this->app->model('orders');
        $subsdf = array(
            'items' => array(
                '*',
            ) ,
            ':dlytype' => array(
                '*',
            ) ,
        );
        $dlycorp_list = $this->app->model('dlycorp')->getList('*', array(
            'disabled' => 'false',
        ));
        $this->pagedata['dlycorp_list'] = $dlycorp_list;
        $this->pagedata['dlyplace_list'] = $this->app->model('dlyplace')->getList();
        $order = $mdl_orders->dump($order_id, '*', $subsdf);
        if ($member_addr_id) {
            $member_addr = $this->app->model('member_addrs')->dump($member_addr_id);
            $order['consignee'] = array_merge($order['consignee'], $member_addr);
        }
        $dlivery_send = $this->app->model('delivery')->getRow('*', array(
            'order_id' => $order['order_id'],
        ));
        $this->pagedata['last_dlycorp'] = $this->app->model('dlycorp')->dump($dlivery_send['dlycorp_id']);
        $this->pagedata['order'] = $order;
        $this->display('admin/order/goreship.html');
    }
    /**
     * 发货单据处理.
     *
     * @params null
     */
    public function dodelivery()
    {
        $order_id = $_POST['order_id']; //发货订单
        $this->begin('index.php?app=b2c&ctl=admin_order&act=detail&p[0]='.$order_id);
        $mdl_orders = $this->app->model('orders');
        $dlycorp_id = $_POST['dlycorp_id']; //物流公司
        $dlyplace_id = $_POST['dp_id']; //发货地
        $logistics_no = $_POST['logistics_no']; //物流单号
        $order = $mdl_orders->dump($order_id);
        if ($order['is_cod'] != 'Y' && $order['pay_status'] < 1) {
            $this->end(false, '订单未付款!'); //非货到付款，但未付款，不能操作发货！
        }
        $dlyplace = app::get('b2c')->model('dlyplace')->dump($dlyplace_id);
        $delivery_sdf = array(
            'order_id' => $order_id,
            'delivery_type' => 'send', //发货
            'member_id' => $order['member_id'],
            'op_id' => $this->user->user_id,
            'dlycorp_id' => $dlycorp_id, //实际选择的物流公司
            'logistics_no' => $logistics_no,
            'cost_freight' => $order['cost_freight'],
            'consignor' => $dlyplace['consignor'], //sdf array
            'consignee' => $order['consignee'], //sdf array
            'status' => 'ready',
            'memo' => $_POST['memo'],
        );
        $send_arr = $_POST['send']; // array('$order_item_id'=>$sendnum);
        $obj_delivery = vmc::singleton('b2c_order_delivery');
        $db = vmc::database();
        $transaction_status = $db->beginTransaction();
        if (!$obj_delivery->generate($delivery_sdf, $send_arr, $msg) || !$obj_delivery->save($delivery_sdf, $msg)) {
            //var_dump($delivery_sdf);
            $db->rollback(); //事务回滚
            $this->end(false, $msg);
        }
        $db->commit($this->transaction_status); //事务提交
        $this->end(true, '发货成功！');
    }
    /**
     * 退货单据处理.
     *
     * @params null
     */
    public function doreship()
    {
        $order_id = $_POST['order_id']; //发货订单
        $this->begin('index.php?app=b2c&ctl=admin_order&act=detail&p[0]='.$order_id);
        $mdl_orders = $this->app->model('orders');
        $dlycorp_id = $_POST['dlycorp_id']; //物流公司
        $dlyplace_id = $_POST['dp_id']; //收货地
        $logistics_no = $_POST['logistics_no']; //物流单号
        $order = $mdl_orders->dump($order_id);
        $dlyplace = app::get('b2c')->model('dlyplace')->dump($dlyplace_id);
        $delivery_sdf = array(
            'order_id' => $order_id,
            'delivery_type' => 'reship', //退货
            'member_id' => $order['member_id'],
            'op_id' => $this->user->user_id,
            'dlycorp_id' => $dlycorp_id, //实际选择的物流公司
            'logistics_no' => $logistics_no,
            'cost_freight' => $_POST['cost_freight'],
            'consignor' => $_POST['consignor'], //sdf array
            'consignee' => $dlyplace['consignor'], //退回发货地
            'status' => 'ready',
            'memo' => $_POST['reason'].$_POST['memo'],
        );
        $send_arr = $_POST['reship']; // array('$order_item_id'=>$sendnum);
        $obj_delivery = vmc::singleton('b2c_order_delivery');
        $db = vmc::database();
        $transaction_status = $db->beginTransaction();
        if (!$obj_delivery->generate($delivery_sdf, $send_arr, $msg) || !$obj_delivery->save($delivery_sdf, $msg)) {
            //var_dump($delivery_sdf);
            $db->rollback(); //事务回滚
            $this->end(false, $msg);
        }
        $db->commit($this->transaction_status); //事务提交
        $this->end(true, '退货单据创建成功！', null, array('delivery_id' => $delivery_sdf['delivery_id']));
    }

    /**
     * 订单取消.
     *
     * @params string order id
     */
    public function docancel($order_id)
    {
        $this->begin('index.php?app=b2c&ctl=admin_order&act=detail&p[0]='.$order_id);

        $sdf['order_id'] = $order_id;
        $sdf['op_id'] = $this->user->user_id;
        $sdf['op_name'] = $this->user->user_data['account']['login_name'];

        if (vmc::singleton('b2c_order_cancel')->generate($sdf, $msg)) {
            $this->end(true, ('订单取消成功！'));
        } else {
            $this->end(false, ('订单取失败！'.$msg));
        }
    }
    /**
     * 订单删除.
     *
     * @params string order id
     */
    public function dodelete($order_id)
    {
        $this->begin('index.php?app=b2c&ctl=admin_order&act=index');
        $obj_recycle = vmc::singleton('desktop_system_recycle');
        $filter = array(
            'order_id' => $order_id,
        );
        if (!$obj_recycle->dorecycle('b2c_mdl_orders', $filter)) {
            $this->end(false, ('订单删除失败！'));
        } else {
            $this->end(true, ('订单删除成功！'));
        }
    }
    /**
     * 订单完成.
     *
     * @params string oder id
     *
     * @return bool 成功与否
     */
    public function dofinish($order_id)
    {
        $this->begin('index.php?app=b2c&ctl=admin_order&act=detail&p[0]='.$order_id);

        $sdf['order_id'] = $order_id;
        $sdf['op_id'] = $this->user->user_id;
        $sdf['op_name'] = $this->user->user_data['account']['login_name'];

        if (vmc::singleton('b2c_order_end')->generate($sdf, $msg)) {
            $this->end(true, ('订单完成归档成功！'));
        } else {
            $this->end(false, ('订单完成归档失败！'.$msg));
        }
    }
    /**
     * 设置订单样式.
     *
     * @param null
     */
    public function showPrintStyle()
    {
        $dbTmpl = $this->app->model('member_systmpl');
        $filetxt = $dbTmpl->get('/admin/order/orderprint');
        $cartfiletxt = $dbTmpl->get('/admin/order/print_cart');
        $sheetfiletxt = $dbTmpl->get('/admin/order/print_sheet');
        $this->pagedata['styleContent'] = $filetxt;
        $this->pagedata['styleContentCart'] = $cartfiletxt;
        $this->pagedata['styleContentSheet'] = $sheetfiletxt;
        $this->page('admin/order/printstyle.html');
    }
    /**
     * 保存订单打印样式.
     *
     * @param null
     */
    public function savePrintStyle()
    {
        $this->begin();
        $dbTmpl = $this->app->model('member_systmpl');
        $dbTmpl->set('/admin/order/print_sheet', $_POST['txtcontentsheet']);
        $dbTmpl->set('/admin/order/print_cart', $_POST['txtcontentcart']);
        $dbTmpl->set('/admin/order/orderprint', $_POST['txtcontent']);
        $this->end(true, '保存成功');
    }
    /**
     * rebackPrintStyle.
     */
    public function rebackPrintStyle()
    {
        $this->begin('');
        $dbTmpl = $this->app->model('member_systmpl');
        $dbTmpl->clear('/admin/order/print_sheet', $msg);
        $dbTmpl->clear('/admin/order/print_cart', $msg);
        $is_clear = $dbTmpl->clear('/admin/order/orderprint', $msg);
        if ($is_clear) {
            $this->end(true, ('恢复默认值成功'));
        } else {
            $this->end(false, $msg);
        }
    }
    private function get_actions($sdf_order = array(), $is_all_disabled = false)
    {
        $arr_order = array();
        $disabled_payed = 'false';
        $disabled_delivery = 'false';
        $disabled_finish = 'false';
        $disabled_refund = 'false';
        $disabled_reship = 'false';
        $disabled_cancel = 'false';
        $disabled_delete = 'false';
        if ($is_all_disabled) {
            $disabled_payed = 'true';
            $disabled_delivery = 'true';
            $disabled_finish = 'true';
            $disabled_refund = 'true';
            $disabled_reship = 'true';
            $disabled_cancel = 'true';
            $disabled_delete = 'true';
        }
        if ($sdf_order) {
            if ($sdf_order['status'] != 'active' || $sdf_order['pay_status'] == 1 || $sdf_order['pay_status'] == 2 || $sdf_order['pay_status'] == 4 || $sdf_order['pay_status'] == 5) {
                $disabled_payed = 'true';
            }
            if ($sdf_order['status'] != 'active' || $sdf_order['is_all_ship'] == 1 || $sdf_order['ship_status'] == 1) {
                $disabled_delivery = 'true';
            }
            if ($sdf_order['status'] != 'active' || $sdf_order['pay_status'] == 0 || $sdf_order['pay_status'] == 5) {
                $disabled_refund = 'true';
            }
            if ($sdf_order['status'] != 'active' || $sdf_order['ship_status'] == 4 || $sdf_order['ship_status'] == 0) {
                $disabled_reship = 'true';
            }
            if ($sdf_order['status'] != 'active' || $sdf_order['ship_status'] > 0 || $sdf_order['pay_status'] > 0) {
                $disabled_cancel = 'true';
            }
            if ($sdf_order['status'] != 'active') {
                $disabled_finish = 'true';
            }
            if ($sdf_order['status'] != 'dead') {
                $disabled_delete = 'true';
            }
        }
        $actions = array(
            'pay' => array(
                'icon' => 'fa  fa-chevron-circle-right',
                'label' => ('付款') ,
                'disabled' => $disabled_payed,
                'app' => 'b2c',
                'act' => 'gopay',
                'target' => 'modal',
            ) ,
            'delivery' => array(
                'icon' => 'fa  fa-chevron-circle-right',
                'label' => ('发货') ,
                'disabled' => $disabled_delivery,
                'app' => 'b2c',
                'act' => 'godelivery',
                'target' => 'modal',
            ) ,
            'reship' => array(
                'icon' => 'fa  fa-chevron-circle-left',
                'label' => ('退货') ,
                'disabled' => $disabled_reship,
                'app' => 'b2c',
                'act' => 'goreship',
                'target' => 'modal',
            ) ,
            'refund' => array(
                'icon' => 'fa  fa-chevron-circle-left',
                'label' => ('退款') ,
                'disabled' => $disabled_refund,
                'app' => 'b2c',
                'act' => 'gorefund',
                'target' => 'modal',
            ) ,
            'cancel' => array(
                'icon' => 'fa  fa-times-circle',
                'label' => ('作废\关闭') ,
                'disabled' => $disabled_cancel,
                'app' => 'b2c',
                'act' => 'docancel',
                'target' => '_command',
                'confirm' => ('作废后该订单将不能做任何操作，只能删除，确认要执行吗?') ,
            ) ,
            'finish' => array(
                'icon' => 'fa  fa-check-circle',
                'label' => ('完成\归档') ,
                'disabled' => $disabled_finish,
                'app' => 'b2c',
                'act' => 'dofinish',
                'target' => '_command',
                'confirm' => ('该操作会将该订单归档并且不允许再做任何操作，确认要执行吗?') ,
            ) ,
            'delete' => array(
                'icon' => 'fa  fa-trash',
                'label' => ('删除') ,
                'disabled' => $disabled_delete,
                'app' => 'b2c',
                'act' => 'dodelete',
                'target' => '_command',
                'confirm' => ('确认要执行吗?') ,
            ) ,
        );

        return $actions;
    }
}
