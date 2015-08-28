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

/**
 *  getConf 默认值.
 */
$setting = array(
    'coupon_code_encrypt_len' => array(
        'default' => 5,
    ) ,
    'coupon_code_count_len' => array(
        'default' => 5,
    ) ,
    'shop_logo' => array(
        'type' => 'image',
        'desc' => '商店LOGO' ,
    ) ,
    'order_invoice' => array(
        'type' => 'select',
        'options' => array(
            'false' => '不启用',
            'true' => '启用',
        ),
        'default' => 'true',
        'desc' => ('是否启用发票？'),
    ) ,
    'order_invoice_tax' => array(
        'type' => 'text',
        'default' => '0',
        'desc' => ('发票税率'),
    ) ,
    'score_convert' => array(
        'type' => 'text',
        'default' => '1',
        'desc' => '积分换算比例',
        'helpinfo'=>'消费金额 x 积分换算比例=可得积分数'
    ),
    'member_avatar_max_size' => array(
        'type' => 'number',
        'default' => get_cfg_var('upload_max_filesize') ? intval(get_cfg_var('upload_max_filesize')) : '5',
        'desc' => '会员头像上传大小限制(单位：MB)',
        'helpinfo'=>(get_cfg_var('upload_max_filesize') ? '<span class="text-danger">服务器当前限制'.get_cfg_var('upload_max_filesize').'</span>' : '')
    ),
    'member_signup_show_attr' => array(
        'type' => 'select',
        'default' => 'false',
        'options' => array(
                'true' => '展示',
                'false' => '隐藏',
        ),
        'desc' => '是否在会员注册表单展示完整注册项？',
    ),
    'order_autocancel_time'=>array(
        'type' => 'number',
        'default' => 86400,
        'desc' => '订单自动关闭时间（单位：秒）',
        'helpinfo'=>'非货到付款订单,未及时进行支付操作,系统将自动定时关闭'
    ),
    'order_autofinish_day'=>array(
        'type' => 'number',
        'default' => 9,
        'desc' => '订单自动完成交易时间（单位：天）',
        'helpinfo'=>'已发货订单,系统将在一定时间后自动完成交易'
    ),
    'comment_image_size' => array(
        'type' => 'number',
        'desc' => '评价时晒单附件上传限制(单位:MB)' ,
        'default'=>get_cfg_var('upload_max_filesize')?intval(get_cfg_var('upload_max_filesize')):2,
        'helpinfo'=>(get_cfg_var('upload_max_filesize') ? '<span class="text-danger">服务器当前限制'.get_cfg_var('upload_max_filesize').'</span>' : '')
    ) ,

);
