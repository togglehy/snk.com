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


$db['stock'] = array(
  'columns' => array(
    'stock_id' => array(
      'type' => 'number',
      'required' => true,
      'pkey' => true,
      'extra' => 'auto_increment',
      'comment' => ('库存记录ID'),
    ),
    'title' => array(
        'type' => 'varchar(255)',
        'label' => ('名称') ,
        'searchtype' => 'has',
        'in_list' => true,
        'default_in_list' => true,
        'orderby' => true,
    ) ,
    'sku_bn' => array(
      'type' => 'varchar(50)',
      'label' => ('SKU货号'),
      'required' => true,
      'comment' => ('SKU货号'),
      'searchtype' => 'has',
      'in_list' => true,
      'default_in_list' => true,
      'orderby' => true,
    ),
    'barcode' => array(
      'type' => 'varchar(255)',
      'label' => ('条码'),
      'comment' => ('条码'),
      'filtertype' => 'yes',
      'searchtype' => 'has',
      'in_list' => true,
      //'default_in_list' => true,
      'orderby' => true,
    ),
    'quantity' => array(
      'type' => 'number',
      'comment' => ('库存量'),
      'label' => ('库存量'),
      'default' => 0,
      'filtertype' => 'number',
      'in_list' => true,
      'default_in_list' => true,
      'orderby' => true,
    ),
    'freez_quantity' => array(
      'type' => 'number',
      'comment' => ('冻结库存'),

      'default' => 0,
      'label' => ('冻结库存'),
      'filtertype' => 'yes',
      'in_list' => true,
      'default_in_list' => true,
      'orderby' => true,
    ),
    'warehouse' => array(
          'type' => 'varchar(50)',
          'comment' => ('仓库\门店编号'),
          'default' => '0',
        //   'label' => ('仓库\门店编号'),
        //   'filtertype' => 'yes',
        //   'in_list' => true,
        //   'default_in_list' => true,
        //   'orderby' => true,
     ),
    'last_modify' => array(
        'type' => 'last_modify',
        'label' => ('最后更新时间') ,
        'in_list' => true,
        'filtertype' => 'yes',
        'in_list' => true,
        'default_in_list' => true,
        'orderby' => true,
    ) ,

  ),
  'index' => array(
    'ind_sku_bn' => array(
      'columns' => array(
        0 => 'sku_bn',
      ),
    ),
    'ind_barcode' => array(
      'columns' => array(
        0 => 'barcode',
      ),
    ),
  ),

  'comment' => ('库存表'),
);
