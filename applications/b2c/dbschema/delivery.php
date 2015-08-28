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


$db['delivery'] = array(
    'columns' => array(
        'delivery_id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'pkey' => true,
            'label' => ('货单号') ,
            'comment' => ('货单流水号') ,
            'searchtype' => 'has',
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'delivery_type' => array(
            'type' => array(
                'send' => '商家发货',
                'reship' => '客户退货'
            ) ,
            'required' => true,
            'label' => ('业务类型') ,
            'comment' => ('业务类型') ,
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'order_id' => array(
            'type' => 'bigint unsigned',
            'label' => ('相关订单') ,
            'comment' => ('相关订单') ,

            'searchtype' => 'has',
            'filtertype' => 'normal',
            
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'member_id' => array(
            'type' => 'table:members',
            'label' => ('相关会员') ,
            'comment' => ('相关会员') ,
            'filtertype' => 'yes',
            
            'in_list' => true,
            'default_in_list' => false,
        ) ,
        'op_id' => array(
            'type' => 'number',
            'label' => ('相关操作员') ,
            'comment' => ('相关操作员') ,
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'dlycorp_id' => array(
            'type' => 'table:dlycorp',
            'comment' => ('物流公司') ,
            'label' => ('物流公司') ,
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'logistics_no' => array(
            'type' => 'varchar(50)',
            'label' => ('物流单号') ,
            'comment' => ('物流单号') ,

            
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'cost_freight' => array(
            'type' => 'money',
            'required' => true,
            'default' => 0,
            'label' => ('物流费用') ,
            'comment' => ('物流费用') ,

            'filtertype' => 'number',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'cost_protect' => array(
            'type' => 'money',
            'default' => 0,
            'required' => true,
            'label' => ('保价费用') ,
            'comment' => ('保价费用') ,
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => false,
        ) ,
        'consignor_name' => array(
            'type' => 'varchar(50)',
            'sdfpath' => 'consignor/name',
            'label' => ('发货人') ,
            'comment' => ('发货人') ,
        ) ,
        'consignor_area' => array(
            'type' => 'region',
            'sdfpath' => 'consignor/area',
            'label' => ('发货人地区') ,
            'comment' => ('发货人地区') ,
        ) ,
        'consignor_addr' => array(
            'type' => 'text',
            'sdfpath' => 'consignor/addr',
            'label' => ('发货人地址') ,
            'comment' => ('发货人地址') ,
        ) ,
        'consignor_zip' => array(
            'type' => 'varchar(20)',
            'sdfpath' => 'consignor/zip',
            'label' => ('发货地邮编') ,
            'comment' => ('发货地邮编') ,
        ) ,
        'consignor_tel' => array(
            'type' => 'varchar(50)',
            'sdfpath' => 'consignor/tel',
            'label' => ('发货人电话') ,
            'comment' => ('发货人电话') ,
        ) ,
        'consignor_mobile' => array(
            'type' => 'varchar(50)',
            'sdfpath' => 'consignor/mobile',
            'label' => ('发货人手机') ,
            'comment' => ('发货人手机') ,
        ) ,
        'consignor_email' => array(
            'type' => 'varchar(200)',
            'sdfpath' => 'consignor/email',
            'label' => ('发货人Email') ,
            'comment' => ('发货人Email') ,
        ) ,
        'consignee_name' => array(
            'type' => 'varchar(50)',
            'sdfpath' => 'consignee/name',
            'label' => ('收货人') ,
            'comment' => ('收货人姓名') ,
        ) ,
        'consignee_area' => array(
            'type' => 'region',
            'sdfpath' => 'consignee/area',
            'label' => ('收货地区') ,
            'comment' => ('收货人地区') ,
        ) ,
        'consignee_addr' => array(
            'type' => 'text',
            'sdfpath' => 'consignee/addr',
            'label' => ('收货地址') ,
            'comment' => ('收货人地址') ,
        ) ,
        'consignee_zip' => array(
            'type' => 'varchar(20)',
            'sdfpath' => 'consignee/zip',
            'label' => ('收货邮编') ,
            'comment' => ('收货人邮编') ,
        ) ,
        'consignee_tel' => array(
            'type' => 'varchar(50)',
            'sdfpath' => 'consignee/tel',
            'label' => ('收货人电话') ,
            'comment' => ('收货人电话') ,
        ) ,
        'consignee_mobile' => array(
            'type' => 'varchar(50)',
            'sdfpath' => 'consignee/mobile',
            'label' => ('收货人手机') ,
            'comment' => ('收货人手机') ,
        ) ,
        'consignee_email' => array(
            'type' => 'varchar(200)',
            'sdfpath' => 'consignee/email',
            'label' => ('收货人Email') ,
            'comment' => ('收货人Email') ,
        ) ,
        'createtime' => array(
            'type' => 'time',
            'comment' => ('单据创建时间') ,
            'label' => ('单据创建时间') ,
            'in_list' => true,
        ) ,
        'last_modify' => array(
            'type' => 'last_modify',
            'comment' => ('最后更新时间') ,
            'label' => ('最后更新时间') ,
            'in_list' => true,
        ) ,
        'status' => array(
            'type' => array(
                'ready' => ('单据成功创建') ,
                'succ' => ('已被确认') ,
                'cancel' => ('已取消') ,
            ) ,
            'default' => 'ready',
            'required' => true,
        ) ,
        'memo' => array(
            'type' => 'longtext',
            'label' => ('备注') ,
            'comment' => ('备注') ,
        ) ,
        'disabled' => array(
            'type' => 'bool',
            'default' => 'false',
            'comment' => ('是否无效') ,
        ) ,
    ) ,
    'index' => array(
        'ind_disabled' => array(
            'columns' => array(
                0 => 'disabled',
            ) ,
        ) ,
        'ind_logi_no' => array(
            'columns' => array(
                0 => 'logistics_no',
            ) ,
        ) ,
    ) ,
    'comment' => ('运货单据表') ,
);
