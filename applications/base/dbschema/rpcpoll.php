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


$db['rpcpoll'] = array(
  'columns' => array(
        'id' => array(
            'label' => '序号',
            'type' => 'varchar(32)',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'process_id' => array(
            'label' => '进程序号',
            'type' => 'varchar(32)',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'type' => array(
            'type' => array(
                    'request' => '发出请求',
                    'response' => '接收的请求',
                ),
            'label' => '类型',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'calltime' => array(
            'type' => 'time',
            'label' => '请求或被请求时间',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'network' => array(
            'type' => 'table:network',
            'label' => '连接节点名称',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'method' => array(
            'type' => 'varchar(100)',
            'label' => '同步的接口名称',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'params' => array('type' => 'serialize', 'comment' => '请求和响应的参数(序列化)'),
        'callback' => array(
            'type' => 'varchar(200)',
            'label' => '回调地址',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'callback_params' => array('type' => 'text'),
        'result' => array(
            'type' => 'text',
            'label' => '请求响应的结果',
            'in_list' => true,
            'default_in_list' => true,
         ),
        'fail_times' => array(
            'type' => 'int(10)',
            'default' => 1,
            'required' => true,
            'label' => '失败的次数',
            'filtertype' => 'number',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'status' => array(
            'type' => array(
                'succ' => '成功',
                'failed' => '失败',
              ),
              'default' => 'failed',
              'required' => true,
              'label' => '交互状态',

              'in_list' => true,
        ),
    ),
  'index' => array(
    'ind_rpc_task_id' => array(
        'columns' => array(
          0 => 'id',
          1 => 'type',
          2 => 'process_id',
        ),
        'prefix' => 'unique',
    ),
    'ind_rpc_response_id' => array(
        'columns' => array(
            0 => 'process_id',
        ),
        'type' => 'hash',
    ),
  ),
  'engine' => 'innodb',
  'version' => '$Rev: 40912 $',
  'ignore_cache' => true,
  'comment' => 'ec-rpc连接池表',
);
