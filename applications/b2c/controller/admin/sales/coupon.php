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


class b2c_ctl_admin_sales_coupon extends desktop_controller
{
    public $cup = array(0 => 'A',1 => 'B');

    public function index()
    {
        $this->finder('b2c_mdl_coupons', array(
                'title' => ('优惠券'),
                'use_buildin_recycle' => true,
                'use_buildin_filter' => true,
                'actions' => array(
                                array(
                                'icon'=>'fa-plus',
                                'label' => ('添加优惠券'),
                                'href' => 'index.php?app=b2c&ctl=admin_sales_coupon&act=add'
                                ),
                            ),
                'finder_extra_view'=>array(array('app'=>'b2c','view'=>'/admin/sales/coupon/issue.html'))
                ));
    }

    public function log()
    {
        $this->finder('b2c_mdl_member_couponlog', array(
                'title' => ('优惠券使用记录'),
                'use_buildin_export' => true,
                'use_buildin_filter' => true,
                ));
    }

    public function issue_list($cpns_id)
    {
        if (!$cpns_id) {
            die('<div class="alert alert-danger">错误的参数</div>');
        }
        $mdl_coupon = $this->app->model('coupons');
        $coupon = $mdl_coupon->dump($cpns_id);
        $this->finder('b2c_mdl_coupons_issue', array(
                'title' => ('已发行优惠券'),
                'use_buildin_export' => true,
                'use_buildin_recycle' => false,
                //'use_buildin_filter' => true,
                //'use_buildin_selectrow' => false,
                'base_filter' => array('cpns_id' => $cpns_id),
                'finder_extra_view' => array(array(
                    'extra_pagedata'=> array('coupon' => $coupon),
                    'app' => 'b2c',
                    'view' => '/admin/sales/coupon/coupon_info.html'
                    )),
                ));
    }

    /**
     * 添加coupon.
     */
    public function add()
    {
        $this->pagedata['rule']['sort_order'] = 50;
        $this->_editor();
    }

    /**
     * 修改coupon.
     */
    public function edit($coupon_id)
    {
        //////////////////////////// 优惠劵信息 //////////////////////////////

        $mCoupon = $this->app->model('coupons');
        $aCoupon = $mCoupon->dump($coupon_id);
        if (empty($aCoupon)) {
            $this->splash('fail', 'index.php?app=b2c&ctl=admin_sales_coupon', ('数据错误'));
        }
        $aCoupon['cpns_prefix'] = substr($aCoupon['cpns_prefix'], 1);

        $this->pagedata['coupon'] = $aCoupon;

        ////////////////////////// 订单促销规则信息 ///////////////////////////
        $mSRO = $this->app->model('sales_rule_order');
        $aRule = $mSRO->dump($aCoupon['rule']['rule_id']);
        $aRule['member_lv_ids'] = empty($aRule['member_lv_ids']) ? null : explode(',', $aRule['member_lv_ids']);
        $aRule['conditions'] = empty($aRule['conditions']) ? null : $aRule['conditions'];
        $aRule['conditions'] = is_null($aRule['conditions']) ? null : $aRule['conditions']['conditions'][1];
        $aRule['action_conditions'] = empty($aRule['conditions']) ? null : ($aRule['action_conditions']);
        $aRule['action_solutions'] = empty($aRule['action_solutions']) ? null : ($aRule['action_solutions']);
        $this->pagedata['rule'] = $aRule;

        ///////////////////////////// 过滤条件 ///////////////////////////////
        $oSOP = vmc::singleton('b2c_sales_order_process');
        $aHtml = $oSOP->getTemplate($aRule['c_template'], $aRule);
        if ((empty($aHtml)) || (is_array($aHtml) && (empty($aHtml['conditions']) || empty($aHtml['action_conditions'])))) {
            $this->pagedata['multi_conditions'] = false;
            $this->pagedata['conditions'] = '<b align="center">'.('模板生成失败').'</b>';
        }
        if (is_array($aHtml)) {
            $this->pagedata['conditions'] = $aHtml['conditions'];
            $this->pagedata['action_conditions'] = $aHtml['action_conditions'];
            $this->pagedata['multi_conditions'] = true;
        } else {
            $this->pagedata['multi_conditions'] = false;
            $this->pagedata['conditions'] = $aHtml;
        }

        ///////////////////////////// 优惠方案 ///////////////////////////////
        $aRule['action_solution'] = empty($aRule['action_solution']) ? null : ($aRule['action_solution']);
        $oSSP = vmc::singleton('b2c_sales_solution_process');
        $this->pagedata['solution_type'] = $oSSP->getType($aRule['action_solution'], $aRule['s_template']);
        $this->pagedata['action_solution_name'] = $aRule['s_template'];

        $html = $oSSP->getTemplate($aRule['s_template'], $aRule['action_solution'], $this->pagedata['solution_type']);
        $this->pagedata['action_solution'] = $html;
        $this->_editor($aCoupon['rule']['rule_id']);
    }

    /**
     * 添加修改coupon共用部分.
     */
    public function _editor($rule_id)
    {
        $time = time();
        $filter = array(
            'from_time|sthan' => $time,
            'to_time|bthan' => $time,
            'status' => 'true',
            'rule_type' => 'C',
        );
        if ($rule_id) {
            $filter['rule_id|noequal'] = $rule_id;
        }
        $arr = $this->app->model('sales_rule_order')->getList('name,sort_order,stop_rules_processing', $filter, 0, -1, 'sort_order ASC');
        $this->pagedata['sales_list'] = $arr;
        $arr = null;
        $this->pagedata['rule']['sort_order'] = $this->pagedata['rule']['sort_order'] ? $this->pagedata['rule']['sort_order'] : ($arr[0]['sort_order'] ? $arr[0]['sort_order'] - 1 : 50);

        //////////////////////////// 会员等级 //////////////////////////////
        $mMemberLevel = $this->app->model('member_lv');
        $this->pagedata['member_level'] = $mMemberLevel->getList('member_lv_id,name', array(), 0, -1, 'member_lv_id ASC');

        //////////////////////////// 过滤条件模板 //////////////////////////////
        $this->pagedata['promotion_type'] = 'order'; // 促销规则过滤条件模板类型
        $oSOP = vmc::singleton('b2c_sales_order_process');
        $this->pagedata['pt_list'] = $oSOP->getTemplateList();

        //////////////////////////// 优惠方案模板 //////////////////////////////
        $oSSP = vmc::singleton('b2c_sales_solution_process');
        $this->pagedata['stpl_list'] = $oSSP->getTemplateList('is_order');

        $this->page('admin/sales/coupon/edit.html');
    }

    /**
     * 添加&修改(post).
     */
    public function toAdd()
    {
        $this->begin('index.php?app=b2c&ctl=admin_sales_coupon');
        $aData = $this->_prepareData($_POST);

        /////////////////////////////  保存促销规则  ///////////////////////////////
        $aRule = $aData['rule'];
        $mSRO = $this->app->model('sales_rule_order');
        $mSRO->save($aRule);
        //////////////////////////////  保存优惠劵 ////////////////////////////////
        $aCoupon = $aData['coupon'];
        $aCoupon['rule']['rule_id'] = $aRule['rule_id'];
        $oCoupon = $this->app->model('coupons');

        $this->end($oCoupon->save($aCoupon), ('操作成功'));
    }

    public function _prepareData($aData)
    {
        $this->_checkData($aData);
        $aResult = array();
        ///////////////////////////////// coupon ///////////////////////////////////
        $aResult['coupon'] = $aData['coupon'];
        if (empty($aResult['coupon']['cpns_point'])) {
            $aResult['coupon']['cpns_point'] = null;
        }
        if (isset($aResult['coupon']['cpns_prefix'])) { // 修改的时候这个是没有的 编辑的话只显示不提交到这里
            $aResult['coupon']['cpns_prefix'] = $this->cup[$aData['coupon']['cpns_type']].$aData['coupon']['cpns_prefix'];
        } else {
            $arr_coupon_info = $this->app->model('coupons')->dump($aResult['coupon']['cpns_id']);
            $aResult['coupon']['cpns_prefix'] = $arr_coupon_info['cpns_prefix'];
        }

        if (!$aResult['coupon']['cpns_key']) {
            $aResult['coupon']['cpns_key'] = substr(base64_encode(serialize($aData)), rand(0, 10), 10);
        }

        ///////////////////////////////// order rule ///////////////////////////////////
        $aResult['rule'] = $aData['rule'];
        $aResult['rule']['rule_id'] = $aData['coupon']['rule_id'];

        // 启用状态
        $aResult['rule']['status'] = empty($aData['coupon']['cpns_status']) ? 'false' : 'true'; // 和优惠劵的状态一致
        $aResult['rule']['rule_type'] = 'C';            // 规则类型


        $aResult['rule']['name'] = ('优惠劵规则').'-'.$aData['coupon']['cpns_name']; // 名称
        if (!$aResult['rule']['name']) {
            $this->end(false, '优惠劵规则名称不能为空！');
        }

        $aResult['rule']['from_time'] = strtotime($aData['from_time']);
        $aResult['rule']['to_time'] = strtotime($aData['to_time']);
        if ($aResult['rule']['to_time'] <= $aResult['rule']['from_time']) {
            $this->end(false, '结束时间不能小于开始时间！');
        }

        // 会员等级
        $aResult['rule']['member_lv_ids'] = empty($aData['rule']['member_lv_ids']) ? null : implode(',', $aData['rule']['member_lv_ids']);

        // 创建时间 (修改时不处理)
        if (empty($aResult['rule']['rule_id'])) {
            $aResult['rule']['create_time'] = time();
        }

        ////////////////////////////// 过滤规则 //////////////////////////////////
        $aResult['rule']['conditions'] = empty($aData['conditions']) ? array('type' => 'b2c_sales_order_aggregator_combine','conditions' => array()) : $aData['conditions'];
        $aResult['rule']['conditions'] = array(
                                            'type' => 'b2c_sales_order_aggregator_combine',
                                            'aggregator' => 'all',
                                            'value' => 1,
                                            'conditions' => array(
                                                               array(// 0
                                                                     'type' => 'b2c_sales_order_item_coupon',
                                                                     'attribute' => 'coupon',
                                                                     'operator' => '=',
                                                                     'value' => $aResult['coupon']['cpns_prefix'],
                                                               ),
                                                               $aResult['rule']['conditions'], // 1 将订单的'conditions'放到这里
                                             ),
                                         );

        $aResult['rule']['action_conditions'] = empty($aData['action_conditions']) ? array('type' => 'b2c_sales_order_aggregator_item','conditions' => array()) : $aData['action_conditions'];

        ////////////////////////////// 优惠方案 //////////////////////////////////
        $s_template = $aData['rule']['s_template'];
        if (empty($aData['action_solution'][$s_template])) {
            $this->end(false, '优惠方案数据异常,保存失败！');
        }
        $aResult['rule']['action_solution'] = empty($aData['action_solution']) ? array() : ($aData['action_solution']);
        if ($aData['rule']['sort_order']) {
            $aResult['rule']['sort_order'] = (int) $aData['rule']['sort_order'];
        }

        return $aResult;
    }

    /**
     * 检测数据.
     */
    public function _checkData($aData)
    {
        $c_template = $aData['rule']['c_template'];
        $obj_c_template = vmc::singleton($c_template);
        if (method_exists($obj_c_template, 'verify_form')) {
            $flag = $obj_c_template->verify_form($aData, $msg);
            if (!$flag) {
                $this->end(false, $msg);
            }
        }
        // POST数据为空
        if (empty($aData)) {
            $this->end(false, ('数据错误'));
        }

        // 添加的时候检测是否已存在相同的coupon 这个可以放在第一步的ajax验证中处理...
        $oCoupon = $this->app->model('coupons');
        if (empty($aData['coupon']['cpns_id'])) {
            if ($oCoupon->checkPrefix($this->cup[$aData['coupon']['cpns_type']].$aData['coupon']['cpns_prefix'])) {
                $this->end(false, ('优惠劵号码已经存在'));
            }
        }
    }

    public function coupon_issue()
    {
        $this->begin('index.php?app=b2c&ctl=admin_sales_coupon&act=index');
        $cpns_id = $_POST['cpns_id'];
        $nums = $_POST['nums'];
        $remark = $_POST['remark'];
        $mdl_coupon = $this->app->model('coupons');
        if (!$nums || $nums < 1) {
            $this->end(false, '数量错误');
        }
        $op_name = $this->user->get_login_name();
        if ($list = $mdl_coupon->downloadCoupon($cpns_id, $nums, '1', $remark, $op_name)) {
            $this->end(true, '成功发行'.count($list).'张优惠券');
        }
        $this->end(false, '发行失败');
    }

    /*
     * 下载优惠券
    function download($cpnsId,$nums){
        $exporter = vmc::singleton("b2c_sales_csv");
        $mCoupon = $this->app->model('coupons');
        if( !$nums ) {
            header("Content-type: text/html; charset=UTF-8");
            echo '<script>alert("'.("数量错误！".'");try{opener=null;close();}catch(e){}</script>');exit;
        }
        if ($list = $mCoupon->downloadCoupon($cpnsId,$nums)) {
            $exporter->download(('优惠券代码'),'coupon',$nums, $list);
        }else{
            header("Content-type: text/html; charset=UTF-8");
            echo '<script>alert("'.("当前优惠券未发布/时间未到,暂时不能下载".'");try{opener=null;close();}catch(e){}</script>');
        }

    }
    */
}
