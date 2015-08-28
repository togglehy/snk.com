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



class b2c_finder_orders{


    public function __construct($app)
    {
        $this->app = $app;
        $this->app_ectools = app::get('ectools');
        $this->odr_action_buttons = array('pay','delivery','finish','refund','reship','cancel','delete');
        $mdl_orders = app::get('b2c')->model('orders');
        $orders_schema = $mdl_orders->get_schema();
        $this->columns = $orders_schema['columns'];
    }




    var $column_control = '操作';
    var $column_control_order = HEAD;
    public function column_control($row)
    {
        $order_id = $row['order_id'];
        if($row['@row']['status']!='active'){
            $opt_btn =  "<a href='index.php?app=b2c&ctl=admin_order&act=detail&p[0]=".$row['order_id']."' class='btn btn-default btn-xs'><i class='fa fa-edit'></i> 查看订单</a>";
        }else{
            $opt_btn =  "<a href='index.php?app=b2c&ctl=admin_order&act=detail&p[0]=".$row['order_id']."' class='btn btn-default btn-xs'><i class='fa fa-edit'></i> 处理订单</a>";
        }



        $print_btn = <<<HTML
        <div class="btn-group">
        <button type="button" class="btn btn-xs btn-default" data-toggle="dropdown"   data-close-others="true" aria-expanded="false"><i class="fa fa-print"></i> 打印</button>
        <ul class="dropdown-menu" role="menu">
				<li>
                    <a target="_blank" href="index.php?app=b2c&ctl=admin_order&act=printing&p[0]=1&p[1]=$order_id">打印购物小票</a>
				</li>
				<li>
                    <a target="_blank" href="index.php?app=b2c&ctl=admin_order&act=printing&p[0]=2&p[1]=$order_id">打印配货单</a>
				</li>
                <!--<li>
                    <a target="_blank" href="index.php?app=b2c&ctl=admin_order&act=printing&p[0]=3&p[1]=$order_id">打印快递单</a>
                </li>-->
				<li class="divider">
				</li>
                <li>
                    <a href="#">打印订单详情</a>
                </li>
			</ul>
        </div>
HTML;

        return $opt_btn.$print_btn;

    }

    var $column_orderstatus = '状态';
    var $column_orderstatus_order = HEAD;
    public function column_orderstatus($row)
    {
        if(isset($row['@row']))
        $row = $row['@row'];

        $orders_columns = $this->columns;
        $_return = '';
        if($row['is_cod'] == 'Y'){
            $_return .= $this->wrap_label('货到付款','warning');
        }
        //已完成或已作废 归档
        if($row['status']!='active'){
            $_return .= $this->wrap_label($orders_columns['status']['type'][$row['status']],'default');
            return $_return;
        }
        //待付款 非COD货到付款订单
        if($row['pay_status'] == '0' && $row['is_cod']!='Y'){
            $_return .= $this->wrap_label('待付款','danger');
            return $_return;
        }
        //待发货
        if($row['ship_status'] == '0'){
            $_return .= $this->wrap_label('待发货','primary');
            return $_return;
        }
        //已发货
        if($row['ship_status'] == '1'){
            $_return .= $this->wrap_label('已发货','success');
            return $_return;
        }
        //其他状态
        $_return .= $this->wrap_label($orders_columns['pay_status']['type'][$row['pay_status']],'info');
        $_return .= $this->wrap_label($orders_columns['ship_status']['type'][$row['ship_status']],'info');
        return $_return;

    }


    private function wrap_label($c,$t){
        return '<span class="label label-'.$t.'">'.$c.'</span>';
    }



    public function row_style($row)
    {
        $row = $row['@row'];
        if($row['status'] == 'finish' || $row['status'] == 'dead'){
            return 'text-muted';
        }

        if ( $row['pay_status'] > 0 && $row['ship_status'] > 0){
            //return 'text-success';
        }


        //return 'text-info';

    }

}
