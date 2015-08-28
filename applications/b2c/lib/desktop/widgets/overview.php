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


class b2c_desktop_widgets_overview implements desktop_interface_widget
{
    public function __construct($app)
    {
        $this->app = $app;
        $this->render = new base_render(app::get('b2c'));
    }
    public function get_order()
    {
        return 1;
    }
    public function get_layout()
    {
        return 'left';
    }
    public function get_title()
    {
        return false;
    }
    public function get_html($from, $to)
    {
        $render = $this->render;
        $mdl_order = app::get('b2c')->model('orders');
        $mdl_order_items = app::get('b2c')->model('order_items');
        $mdl_members = app::get('b2c')->model('members');
        $mdl_member_account = app::get('pam')->model('members');
        $order_ft_filter = array(
            'createtime|between' => array(
                $from,
                $to,
            ),
        );
        $order_where_ft = $mdl_order->_filter($order_ft_filter);
        /*热销商品*/
        $SQL_orders = "SELECT order_id,member_id FROM vmc_b2c_orders WHERE (pay_status='1' OR is_cod='Y') AND ".$order_where_ft;
        $orders = $mdl_order->db->select($SQL_orders);
        //vmc::dump($SQL_orders);
        $order_ids = array_keys(utils::array_change_key($orders, 'order_id'));
        $SQL_top_sell = 'SELECT CONCAT(oi.name,IFNULL(oi.spec_info,"")) as pname,oi.bn,oi.goods_id,oi.product_id,p.price,sum(oi.nums) as sell_count FROM vmc_b2c_order_items as oi LEFT JOIN vmc_b2c_products as p ON oi.product_id=p.product_id  WHERE '.$mdl_order_items->_filter(array(
            'order_id' => $order_ids,
        ), 'oi').' GROUP BY pname ORDER BY sell_count DESC LIMIT 0,15';
        //vmc::dump($SQL_top_sell);
        $render->pagedata['top_sell'] = $mdl_order_items->db->select($SQL_top_sell);
        /*热销商品*/
        /*下单最多*/
        $member_ids = array_keys(utils::array_change_key($orders, 'member_id'));
        $members = $this->_get_member_list(array('member_id' => $member_ids), array(
            0,
            15,
        ), 'order_num DESC');
        $render->pagedata['member_top_ordercount'] = $members;
        /*下单最多*/
        /*消费最多*/
        $SQL_member_order_amount = "SELECT sum(order_total) as amount,member_id FROM vmc_b2c_orders  WHERE (pay_status='1' OR is_cod='Y') AND member_id>0 AND ".$order_where_ft.' GROUP BY member_id ORDER BY amount DESC LIMIT 0,15';
        $moa_arr = $mdl_order->db->select($SQL_member_order_amount);
        $moa_arr = utils::array_change_key($moa_arr, 'member_id');
        $member_ids2 = array_keys($moa_arr);
        $members2 = $this->_get_member_list(array('member_id' => $member_ids2), array(
            0,
            15,
        ));
        $members2 = utils::array_change_key($members2, 'member_id');
        foreach ($moa_arr as $key => $value) {
            $moa_arr[$key] = array_merge($moa_arr[$key], $members2[$value['member_id']]);
        }
        $render->pagedata['member_top_amount'] = $moa_arr;
        /*消费最多*/
        /*最新注册*/
        $members3 = $this->_get_member_list(array('regtime|between' => array($from, $to)), array(
            0,
            15,
        ), 'regtime DESC');
        $render->pagedata['member_top_new'] = $members2;
        /*最新注册*/
        return $render->fetch('desktop/widgets/overview.html');
    }
    /*获得会员数据*/
    private function _get_member_list($filter = false, $limit = array(
        0, -1,
    ), $orderby = null)
    {
        $mdl_members = app::get('b2c')->model('members');
        $mdl_member_account = app::get('pam')->model('members');
        $members = $mdl_members->getList('member_id,regtime,member_lv_id,name,order_num,mobile,tel,email', $filter, $limit[0], $limit[1], $orderby);
        $member_ids = array_keys(utils::array_change_key($members, 'member_id'));
        $accounts = $mdl_member_account->getList('login_account,member_id', array(
            'member_id' => $member_ids,
        ));
        $accounts = utils::array_change_key($accounts, 'member_id');
        foreach ($members as $key => $value) {
            $members[$key]['login_account'] = $accounts[$value['member_id']]['login_account'];
        }

        return $members;
    }
}
