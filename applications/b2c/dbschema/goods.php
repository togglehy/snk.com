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


$db['goods'] = array(
    'columns' => array(
        'goods_id' => array(
            'type' => 'bigint unsigned',
            'required' => true,
            'pkey' => true,
            'extra' => 'auto_increment',
        ) ,
        'gid' => array(
            'type' => 'varchar(200)',
            'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'yes',
            'searchtype' => 'has',
            'label' => ('商品编号') ,
        ) ,
        'name' => array(
            'type' => 'varchar(200)',
            'required' => true,
            'default' => '',
            'label' => ('商品名称') ,
            'is_title' => true,
            'searchtype' => 'has',
            'filtertype' => 'custom',

            'in_list' => true,
            'default_in_list' => true,
            'order' => '1',
        ) ,
        'type_id' => array(
            'type' => 'table:goods_type',
            'sdfpath' => 'type/type_id',
            'label' => ('类型') ,
        ) ,
        'cat_id' => array(
            'type' => 'table:goods_cat',
            'required' => true,
            'sdfpath' => 'category/cat_id',
            'default' => 0,
            'label' => ('分类') ,
            'filtertype' => 'yes',

            'in_list' => true,
            'default_in_list' => true,
            'orderby' => true,
        ) ,
        'extended_cat'=>array(
            'type'=>'serialize',
            'label'=>'扩展分类'
        ),
        'brand_id' => array(
            'type' => 'table:brand',
            'sdfpath' => 'brand/brand_id',
            'label' => ('品牌') ,
            'filtertype' => 'yes',
            'in_list' => true,
        ) ,
        'marketable' => array(
            'type' => 'bool',
            'default' => 'true',
            'required' => true,
            'label' => ('上架') ,
            'filtertype' => 'yes',

            'in_list' => true,
        ) ,
        'uptime' => array(
            'type' => 'time',
            'depend_col' => 'marketable:true:now',
            'label' => ('上架时间') ,
            'in_list' => true,
            'orderby' => true,
        ) ,
        'downtime' => array(
            'type' => 'time',
            'depend_col' => 'marketable:false:now',
            'label' => ('下架时间') ,
            'in_list' => true,
            'orderby' => true,
        ) ,
        'last_modify' => array(
            'type' => 'last_modify',
            'label' => ('更新时间') ,
            'filtertype' => 'yes',
            'in_list' => true,
            'orderby' => true,
        ) ,
        'w_order' => array(
            'type' => 'number',
            'default' => 0,
            'required' => true,
            'label' => ('权重') ,
            'filtertype' => 'normal',
            'in_list' => true,
            'orderby' => true,
            'default_in_list' => true,
        ) ,
        'score' => array(
            'type' => 'number',
            'sdfpath' => 'gain_score',
            'label' => ('积分') ,
        ) ,
        'brief' => array(
            'type' => 'varchar(255)',
            'label' => ('商品简介') ,
            'filtertype' => 'normal',
            'in_list' => true,
        ) ,
        'goods_type' => array(
            'type' => array(
                'normal' => ('普通商品') ,
                'bind' => ('捆绑商品') ,
                'gift' => ('赠品') ,
            ) ,
            'default' => 'normal',
            'required' => true,
            'label' => ('销售类型') ,
        ) ,
        'image_default_id' => array(
            'type' => 'varchar(32)',
            'label' => ('默认图片') ,
        ) ,
        'intro' => array(
            'type' => 'longtext',
            'sdfpath' => 'description',
            'label' => ('详细介绍') ,
        ) ,
        'min_buy' => array(
            'type' => 'number',
            'label' => ('起定量') ,
        ) ,
        'nostore_sell' => array(
            'type' => 'intbool',
            'default' => '0',
            'filtertype' => 'normal',
            'label' => ('无库存也可销售') ,
        ) ,
        'goods_setting' => array(
            'type' => 'serialize',
            'label' => ('商品设置') ,
            'deny_export' => true,
        ) ,
        'spec_desc' => array(
            'type' => 'serialize',
            'comment' => ('货品规格序列化') ,
        ) ,

        'disabled' => array(
            'type' => 'bool',
            'default' => 'false',
            'required' => true,
            'deny_export' => true,
        ) ,
        'comment_count' => array(
            'type' => 'int unsigned',
            'default' => 0,
            'required' => true,
            'filtertype' => 'normal',
            'label' => ('评价次数') ,
        ) ,
        'view_w_count' => array(
            'type' => 'int unsigned',
            'default' => 0,
            'required' => true,
            'filtertype' => 'normal',
            'label' => ('周浏览次数') ,
        ) ,
        'view_count' => array(
            'type' => 'int unsigned',
            'default' => 0,
            'required' => true,
            'filtertype' => 'normal',
            'label' => ('浏览次数') ,
        ) ,
        'buy_count' => array(
            'type' => 'int unsigned',
            'default' => 0,
            'required' => true,
            'filtertype' => 'normal',
            'label' => ('购买次数') ,
        ) ,
        'buy_w_count' => array(
            'type' => 'int unsigned',
            'default' => 0,
            'required' => true,
            'filtertype' => 'normal',
            'label' => ('周购买次数') ,
        ) ,
        'params' =>
            array (
              'type' => 'serialize',
              'label' =>'商品参数表序列化',
            ),
        'p_1' => array(
            'type' => 'number',
            'sdfpath' => 'props/p_1/value',
        ) ,
        'p_2' => array(
            'type' => 'number',
            'sdfpath' => 'props/p_2/value',
        ) ,
        'p_3' => array(
            'type' => 'number',
            'sdfpath' => 'props/p_3/value',
        ) ,
        'p_4' => array(
            'type' => 'number',
            'sdfpath' => 'props/p_4/value',
        ) ,
        'p_5' => array(
            'type' => 'number',
            'sdfpath' => 'props/p_5/value',
        ) ,
        'p_6' => array(
            'type' => 'number',
            'sdfpath' => 'props/p_6/value',
        ) ,
        'p_7' => array(
            'type' => 'number',
            'sdfpath' => 'props/p_7/value',
        ) ,
        'p_8' => array(
            'type' => 'number',
            'sdfpath' => 'props/p_8/value',
        ) ,
        'p_9' => array(
            'type' => 'number',
            'sdfpath' => 'props/p_9/value',
        ) ,
        'p_10' => array(
            'type' => 'number',
            'sdfpath' => 'props/p_10/value',
        ) ,
        'p_11' => array(
            'type' => 'number',
            'sdfpath' => 'props/p_11/value',
        ) ,
        'p_12' => array(
            'type' => 'number',
            'sdfpath' => 'props/p_12/value',
        ) ,
        'p_13' => array(
            'type' => 'number',
            'sdfpath' => 'props/p_13/value',
        ) ,
        'p_14' => array(
            'type' => 'number',
            'sdfpath' => 'props/p_14/value',
        ) ,
        'p_15' => array(
            'type' => 'number',
            'sdfpath' => 'props/p_15/value',
        ) ,
        'p_16' => array(
            'type' => 'number',
            'sdfpath' => 'props/p_16/value',
        ) ,
        'p_17' => array(
            'type' => 'number',
            'sdfpath' => 'props/p_17/value',
        ) ,
        'p_18' => array(
            'type' => 'number',
            'sdfpath' => 'props/p_18/value',
        ) ,
        'p_19' => array(
            'type' => 'number',
            'sdfpath' => 'props/p_19/value',
        ) ,
        'p_20' => array(
            'type' => 'number',
            'sdfpath' => 'props/p_20/value',
        ) ,
        'p_21' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_21/value',
        ) ,
        'p_22' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_22/value',
        ) ,
        'p_23' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_23/value',
        ) ,
        'p_24' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_24/value',
        ) ,
        'p_25' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_25/value',
        ) ,
        'p_26' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_26/value',
        ) ,
        'p_27' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_27/value',
        ) ,
        'p_28' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_28/value',
        ) ,
        'p_29' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_29/value',
        ) ,
        'p_30' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_30/value',
        ) ,
        'p_31' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_31/value',
        ) ,
        'p_32' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_32/value',
        ) ,
        'p_33' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_33/value',
        ) ,
        'p_34' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_34/value',
        ) ,
        'p_35' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_35/value',
        ) ,
        'p_36' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_36/value',
        ) ,
        'p_37' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_37/value',
        ) ,
        'p_38' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_38/value',
        ) ,
        'p_39' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_39/value',
        ) ,
        'p_40' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_40/value',
        ) ,
        'p_41' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_41/value',
        ) ,
        'p_42' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_42/value',
        ) ,
        'p_43' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_43/value',
        ) ,
        'p_44' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_44/value',
        ) ,
        'p_45' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_45/value',
        ) ,
        'p_46' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_46/value',
        ) ,
        'p_47' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_47/value',
        ) ,
        'p_48' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_48/value',
        ) ,
        'p_49' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_49/value',
        ) ,
        'p_50' => array(
            'type' => 'varchar(255)',
            'sdfpath' => 'props/p_50/value',
        ) ,
    ) ,
    'comment' => ('商品表') ,
    'index' => array(
        'uni_gid' => array(
            'columns' => array(
                0 => 'gid',
            ) ,
            'prefix' => 'UNIQUE',
        ) ,
        'ind_p_1' => array(
            'columns' => array(
                0 => 'p_1',
            ) ,
        ) ,
        'ind_p_2' => array(
            'columns' => array(
                0 => 'p_2',
            ) ,
        ) ,
        'ind_p_3' => array(
            'columns' => array(
                0 => 'p_3',
            ) ,
        ) ,
        'ind_p_4' => array(
            'columns' => array(
                0 => 'p_4',
            ) ,
        ) ,
        'ind_p_5' => array(
            'columns' => array(
                0 => 'p_5',
            ) ,
        ) ,
        'ind_p_6' => array(
            'columns' => array(
                0 => 'p_6',
            ) ,
        ) ,
        'ind_p_7' => array(
            'columns' => array(
                0 => 'p_7',
            ) ,
        ) ,
        'ind_p_8' => array(
            'columns' => array(
                0 => 'p_8',
            ) ,
        ) ,
        'ind_p_9' => array(
            'columns' => array(
                0 => 'p_9',
            ) ,
        ) ,
        'ind_p_10' => array(
            'columns' => array(
                0 => 'p_10',
            ) ,
        ) ,
        'ind_p_11' => array(
            'columns' => array(
                0 => 'p_11',
            ) ,
        ) ,
        'ind_p_12' => array(
            'columns' => array(
                0 => 'p_12',
            ) ,
        ) ,
        'ind_p_13' => array(
            'columns' => array(
                0 => 'p_13',
            ) ,
        ) ,
        'ind_p_14' => array(
            'columns' => array(
                0 => 'p_14',
            ) ,
        ) ,
        'ind_p_15' => array(
            'columns' => array(
                0 => 'p_15',
            ) ,
        ) ,
        'ind_p_16' => array(
            'columns' => array(
                0 => 'p_16',
            ) ,
        ) ,
        'ind_p_17' => array(
            'columns' => array(
                0 => 'p_17',
            ) ,
        ) ,
        'ind_p_18' => array(
            'columns' => array(
                0 => 'p_18',
            ) ,
        ) ,
        'ind_p_19' => array(
            'columns' => array(
                0 => 'p_19',
            ) ,
        ) ,
        'ind_p_20' => array(
            'columns' => array(
                0 => 'p_20',
            ) ,
        ) ,
        'ind_p_5' => array(
            'columns' => array(
                0 => 'p_5',
            ) ,
        ) ,
        'ind_p_6' => array(
            'columns' => array(
                0 => 'p_6',
            ) ,
        ) ,
        'ind_p_7' => array(
            'columns' => array(
                0 => 'p_7',
            ) ,
        ) ,
        'ind_p_8' => array(
            'columns' => array(
                0 => 'p_8',
            ) ,
        ) ,
        'ind_p_23' => array(
            'columns' => array(
                0 => 'p_23',
            ) ,
        ) ,
        'ind_p_22' => array(
            'columns' => array(
                0 => 'p_22',
            ) ,
        ) ,
        'ind_p_21' => array(
            'columns' => array(
                0 => 'p_21',
            ) ,
        ) ,
        'ind_p_24' => array(
            'columns' => array(
                0 => 'p_24',
            ) ,
        ) ,
        'ind_p_25' => array(
            'columns' => array(
                0 => 'p_25',
            ) ,
        ) ,
        'ind_p_26' => array(
            'columns' => array(
                0 => 'p_26',
            ) ,
        ) ,
        'ind_p_27' => array(
            'columns' => array(
                0 => 'p_27',
            ) ,
        ) ,
        'ind_p_28' => array(
            'columns' => array(
                0 => 'p_28',
            ) ,
        ) ,
        'ind_p_29' => array(
            'columns' => array(
                0 => 'p_29',
            ) ,
        ) ,
        'ind_p_30' => array(
            'columns' => array(
                0 => 'p_30',
            ) ,
        ) ,
        'ind_p_31' => array(
            'columns' => array(
                0 => 'p_31',
            ) ,
        ) ,
        'ind_p_32' => array(
            'columns' => array(
                0 => 'p_32',
            ) ,
        ) ,
        'ind_p_33' => array(
            'columns' => array(
                0 => 'p_33',
            ) ,
        ) ,
        'ind_p_34' => array(
            'columns' => array(
                0 => 'p_34',
            ) ,
        ) ,
        'ind_p_35' => array(
            'columns' => array(
                0 => 'p_35',
            ) ,
        ) ,
        'ind_p_36' => array(
            'columns' => array(
                0 => 'p_36',
            ) ,
        ) ,
        'ind_p_37' => array(
            'columns' => array(
                0 => 'p_37',
            ) ,
        ) ,
        'ind_p_38' => array(
            'columns' => array(
                0 => 'p_38',
            ) ,
        ) ,
        'ind_p_39' => array(
            'columns' => array(
                0 => 'p_39',
            ) ,
        ) ,
        'ind_p_40' => array(
            'columns' => array(
                0 => 'p_40',
            ) ,
        ) ,
        'ind_p_41' => array(
            'columns' => array(
                0 => 'p_41',
            ) ,
        ) ,
        'ind_p_42' => array(
            'columns' => array(
                0 => 'p_42',
            ) ,
        ) ,
        'ind_p_43' => array(
            'columns' => array(
                0 => 'p_43',
            ) ,
        ) ,
        'ind_p_44' => array(
            'columns' => array(
                0 => 'p_44',
            ) ,
        ) ,
        'ind_p_45' => array(
            'columns' => array(
                0 => 'p_45',
            ) ,
        ) ,
        'ind_p_46' => array(
            'columns' => array(
                0 => 'p_46',
            ) ,
        ) ,
        'ind_p_47' => array(
            'columns' => array(
                0 => 'p_47',
            ) ,
        ) ,
        'ind_p_48' => array(
            'columns' => array(
                0 => 'p_48',
            ) ,
        ) ,
        'ind_p_49' => array(
            'columns' => array(
                0 => 'p_49',
            ) ,
        ) ,
        'ind_p_50' => array(
            'columns' => array(
                0 => 'p_50',
            ) ,
        ) ,
        'ind_frontend' => array(
            'columns' => array(
                0 => 'disabled',
                1 => 'goods_type',
                2 => 'marketable',
            ) ,
        ) ,
        'idx_goods_type' => array(
            'columns' => array(
                0 => 'goods_type',
            ) ,
        ) ,
        'idx_w_order' => array(
            'columns' => array(
                0 => 'w_order',
            ) ,
        ) ,
        'idx_marketable' => array(
            'columns' => array(
                0 => 'marketable',
            ) ,
        ) ,
    ) ,
    'engine' => 'innodb',
    'comment' => ('商品主表') ,
);
