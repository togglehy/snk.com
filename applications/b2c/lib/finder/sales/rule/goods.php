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


class b2c_finder_sales_rule_goods {
    var $column_control = '操作';
    // var $detail_basic = '查看';
    public function __construct($app) {
        $this->app = $app;
    }
    function column_control($arr) {
        $row = $arr;
        // if (!isset($row['apply_time']) || !isset($row['create_time']) || !isset($row['from_time']) || !isset($row['to_time'])) {
        //     $row = $this->app->model('sales_rule_goods')->dump($row['rule_id']);
        // }
        $str = '<a class="btn btn-default btn-xs" href="index.php?app=b2c&ctl=admin_sales_goods&act=edit&p[0]=' . $row['rule_id'] . '" ><i class="fa fa-edit"></i> ' . ('编辑') . '</a>';
        // if ((($row['apply_time'] < $row['create_time']) || empty($row['apply_time'])))
        // $str.= '<a class="btn btn-xs btn-default" href="index.php?app=b2c&ctl=admin_sales_goods&act=apply&p[0]=' . $row['rule_id'] . '" target="_command"><i class="fa fa-refresh"></i>&nbsp;' . ('重新应用到商品') . '</a>';

        $row = null;
        return $str;
    }
    // function detail_basic($id) {
    //     $arr = $this->app->model('sales_rule_goods')->dump($id);
    //     $render = $this->app->render();
    //     //会员等级
    //     if ($arr['member_lv_ids']) {
    //         $member_lv_id = explode(',', $arr['member_lv_ids']);
    //         $member = $this->app->model('member_lv')->getList('*', array(
    //             'member_lv_id' => $member_lv_id
    //         ));
    //         if (count($member_lv_id) > count($member)) {
    //             $member[] = array(
    //                 'name' => ('非会员')
    //             );
    //         }
    //         $render->pagedata['member'] = $member;
    //     }
    //     //过滤条件
    //     if ($arr['conditions']) {
    //         if ($arr['c_template']) {
    //             $render->pagedata['conditions'] = vmc::singleton($arr['c_template'])->tpl_name;
    //         }
    //     }
    //     //优惠方案
    //     if ($arr['action_solution']) {
    //         if ($arr['s_template']) {
    //             $o = vmc::singleton($arr['s_template']);
    //             $o->setString($arr['action_solution'][$arr['s_template']]);
    //             $render->pagedata['action_solution'] = $o->getString();
    //             #$render->pagedata['action_solution'] = vmc::singleton($arr['s_template'])->name;
    //
    //         }
    //     }
    //     $render->pagedata['rules'] = $arr;
    //     return $render->fetch('admin/sales/finder/goods.html');
    // }

}
