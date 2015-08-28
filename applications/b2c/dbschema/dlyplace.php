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


$db['dlyplace'] = array(
    'columns' => array(
        'dp_id' => array(
            'type' => 'int unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
            'comment' => ('发货地点ID') ,
        ) ,
        'dp_type' => array(
            'type' => array(
                'warehouse' => '仓库',
                'store' => '门店',
            ) ,
            'required' => true,
            'default' => 'warehouse',
            'label' => ('地点类型') ,
            'comment' => ('地点类型') ,
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'dp_title' => array(
            'type' => 'varchar(50)',
            'label' => ('发货地点名称') ,
            'comment' => ('发货地点名称') ,
            
            'searchtype' => 'tequal',
            'filtertype' => 'normal',
            
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'consignor_name' => array(
            'type' => 'varchar(50)',
            'sdfpath' => 'consignor/name',
            'label' => ('联系人') ,
            'comment' => ('联系人') ,
            
            'searchtype' => 'tequal',
            'filtertype' => 'normal',
            
            'in_list' => true,
            'default_in_list' => true,
        ) ,
        'consignor_area' => array(
            'type' => 'region',
            'sdfpath' => 'consignor/area',
            'label' => ('所在地区') ,
            'comment' => ('所在地区') ,
            
            'filtertype' => 'normal',
            
            'in_list' => true,
        ) ,
        'consignor_addr' => array(
            'type' => 'text',
            'sdfpath' => 'consignor/addr',
            'label' => ('详细地址') ,
            'comment' => ('详细地址') ,
            
            'filtertype' => 'normal',
            
            'in_list' => true,
        ) ,
        'consignor_zip' => array(
            'type' => 'varchar(20)',
            'sdfpath' => 'consignor/zip',
            'label' => ('邮编') ,
            'comment' => ('邮编') ,
            
            'filtertype' => 'normal',
            'in_list' => true,
        ) ,
        'consignor_tel' => array(
            'type' => 'varchar(50)',
            'sdfpath' => 'consignor/tel',
            'label' => ('联系电话') ,
            'comment' => ('联系电话') ,
            
            'filtertype' => 'normal',
            'in_list' => true,
        ) ,
        'consignor_mobile' => array(
            'type' => 'varchar(50)',
            'sdfpath' => 'consignor/mobile',
            'label' => ('联系人手机') ,
            'comment' => ('联系人手机') ,
            
            'filtertype' => 'normal',
            
            'in_list' => true,
        ) ,
        'consignor_email' => array(
            'type' => 'varchar(200)',
            'sdfpath' => 'consignor/email',
            'label' => ('联系人Email') ,
            'comment' => ('联系人Email') ,
            
            'filtertype' => 'normal',
            'in_list' => true,
        ) ,
        'memo' => array(
            'type' => 'longtext',
            'label' => ('备注') ,
            'comment' => ('备注') ,
            
            'filtertype' => 'normal',
            'in_list' => true,
        ) ,
        'disabled' => array(
            'type' => 'bool',
            'default' => 'false',
            'comment' => ('是否无效') ,
            
            'label' => ('是否无效')
        ) ,
    ) ,
    'index' => array(
        'ind_disabled' => array(
            'columns' => array(
                0 => 'disabled',
            ) ,
        )
    ) ,
    'comment' => ('发货地点') ,
);
