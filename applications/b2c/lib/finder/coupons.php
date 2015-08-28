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



class b2c_finder_coupons{
    var $column_control = '操作';
    var $detail_basic = '查看';

    public function __construct($app) {
        $this->app = $app;
    }

    function column_control($row){
        $row = $row['@row'];
        if ($row['cpns_type']=='1') {
            $download_html = "<a onclick=\"$('#coupon_issue_modal').find('input[name=cpns_id]').val(".$row['cpns_id'].")\" class='btn btn-xs btn-default' href='#coupon_issue_modal' class='btn btn-xs btn-defaut' data-toggle='modal'><i class='fa fa-share-square'></i> 发行优惠券</a>";
            $issue_list = "<a class='btn btn-xs btn-default' href='index.php?app=b2c&ctl=admin_sales_coupon&act=issue_list&p[0]=".$row['cpns_id']."' class='btn btn-xs btn-defaut' ><i class='fa fa-list'></i> 查看已发行优惠券</a>";
        }elseif($row['cpns_type']=='0'){
            $download_html = "<span class='btn-group'><span  class='btn green-meadow btn-xs'>优惠暗号:</span><span class='btn btn-xs default' onclick= prompt('暗号A型优惠券\\n\\n&raquo;\\t有效期内,可多次使用。\\n&raquo;\\t但每个订单不能重复使用。\\n\\n（这个对话框是专门方便您复制用的）','".$row['cpns_prefix']."')>".$row['cpns_prefix']."</span>";
        }
        return '<a class="btn btn-xs btn-default" href="index.php?app=b2c&ctl=admin_sales_coupon&act=edit&p[0]='.$row['cpns_id'].'" ><i class="fa fa-edit"></i> '.('编辑').'</a>'.$download_html.$issue_list;
    }

    function row_style($row){
        $row = $row['@row'];

    }
}
