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


class b2c_desktop_widgets_stats implements desktop_interface_widget {
    function __construct($app) {
        $this->app = $app;
        $this->render = new base_render(app::get('b2c'));
    }
    function get_layout() {
        return 'top';
    }
    function get_order() {
        return 1;
    }
    function get_title() {
        return;
    }
    function get_html($from, $to) {
        $render = $this->render;
        $mdl_order = app::get('b2c')->model('orders');

        $where_ft = $mdl_order->_filter(array(
            'createtime|between'=>array($from,$to)
        ));
        $SQL_amount = "SELECT sum(`order_total`) as amount_sum FROM vmc_b2c_orders WHERE (pay_status='1' OR is_cod='Y') AND " .$where_ft;
        $SQL_avg_amount = "SELECT avg(`order_total`) as amount_avg FROM vmc_b2c_orders WHERE (pay_status='1' OR is_cod='Y') AND " .$where_ft;
        $SQL_ordercount = "SELECT count(`order_id`) as order_count FROM vmc_b2c_orders WHERE (pay_status='1' OR is_cod='Y') AND " .$where_ft;

        $render->pagedata['amount_sum'] = $mdl_order->db->select($SQL_amount);
        $render->pagedata['amount_avg'] = $mdl_order->db->select($SQL_avg_amount);
        $render->pagedata['order_count'] = $mdl_order->db->select($SQL_ordercount);
        $render->pagedata['gview_count'] = vmc::database()->select("SELECT sum(view_count) as gview_count FROM vmc_b2c_goods"); //TODO

        return $render->fetch('desktop/widgets/stats.html');
    }
}
?>
