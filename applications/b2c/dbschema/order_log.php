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


$db['order_log'] = array(
    'columns' => array(
        'log_id' => array(
            'type' => 'number',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'comment' => ('订单日志ID') ,
        ) ,
        'order_id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'default' => 0,
            'comment' => ('订单ID') ,
        ) ,
        'op_model' => array(
            'type' => array(
                'members' => '会员\顾客',
                'shopadmin' => '操作员',
                'unknown' => '未知'
            ) ,
            'label' => ('操作员类型') ,
            'filtertype' => 'normal',
            'sdfpath'=>'operator/model',
            'default'=>'unknown',
            'in_list' => true,
            'comment' => ('操作员类型') ,
        ) ,
        'op_id' => array(
            'type' => 'number', //'table:users@desktop',
            'label' => ('操作员ID') ,
            'filtertype' => 'normal',
            'sdfpath'=>'operator/ident',
            'in_list' => true,
            'comment' => ('操作员ID') ,
        ) ,
        'op_name' => array(
            'type' => 'varchar(100)',
            'label' => ('操作人名称') ,
            'filtertype' => 'normal',
            'sdfpath'=>'operator/name',
            
            'in_list' => true,
        ) ,
        'behavior' => array(
            'type' => array(
                'create' => ('创建') ,
                'update' => ('更新') ,
                'payment' => ('支付') ,
                'refund' => ('退款') ,
                'shipment' => ('发货') ,
                'reship' => ('退货') ,
                'finish' => ('完成') ,
                'cancel' => ('取消') ,
                'discount' => ('减价') ,
            ) ,
            'default' => 'update',
            'required' => true,
            'label' => ('操作行为') ,
            'filtertype' => 'yes',
            
            'in_list' => true,
            'comment' => ('日志记录操作的行为') ,
        ) ,
        'result' => array(
            'type' => array(
                'success' => ('成功') ,
                'fail' => ('失败') ,
            ) ,
            'required' => true,
            'label' => ('操作结果') ,
            'filtertype' => 'yes',
            
            'in_list' => true,
            'comment' => ('日志结果') ,
        ) ,
        'log' => array(
            'type' => 'text',
            'in_list' => true,
            'default_in_list' => false,
            'comment' => ('日志内容') ,
        ) ,
        'addons' => array(
            'type' => 'longtext',
            'comment' => ('序列化数据') ,
        ) ,
        'log_time' => array(
            'type' => 'time',
            'label' => ('操作时间') ,
            'filtertype' => 'time',
            
            'in_list' => true,
            'comment' => ('操作时间') ,
        ) ,
    ) ,
    'engine' => 'innodb',
    'comment' => ('订单日志表') ,
);
