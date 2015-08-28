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
 * 消息通知中枢类
 */
class b2c_messenger_stage
{

    public function __construct(&$app)
    {
        $this->app = $app;
        $this->mdl_mem_systmpl = $this->app->model('member_systmpl');
    }



    /**
     * 消息通知地图
     * 所有自动消息发送列表，只要触发匹配格式的事件就会发送.
     */
    public function get_actions()
    {
        //level < 10  进队列执行
        $actions = array(
            'account-member' => array(
                'label' => ('身份验证') ,
                'level' => 11,
                'lock' => true,
                'env_list'=>array(
                    'vcode'=>'*随机验证码',
                ),
                'exclude'=>array(
                    'b2c_messenger_msgbox',
                )
            ) ,
            'account-signup' => array(
                'label' => ('手机号验证') ,
                'level' => 11,
                'lock' => true,
                'env_list'=>array(
                    'vcode'=>'*随机验证码',
                ),
                'exclude'=>array(
                    'b2c_messenger_msgbox',
                    'b2c_messenger_email'
                )
            ) ,
            'orders-shipping' => array(
                'label' => ('订单发货时') ,
                'level' => 9,
                'env_list'=>array(
                    'order_id'=>'*订单号',
                    'consignee_name'=>'*收货人',
                    'consignee_area'=>'*收货地区',
                    'consignee_addr'=>'*收货地址',
                    'consignee_tel'=>'电话',
                    'consignee_mobile'=>'*手机',
                    'dlycorp_name'=>'*物流公司名称',
                    'dlycorp_code'=>'物流公司代码',
                    'dlycorp_website'=>'物流公司官网',
                    'logistics_no'=>'*物流运单号',
                    'timestr'=>'*发货时间(年-月-日 时:分:秒)',
                ),
            ) ,
            'orders-payed' => array(
                'label' => ('订单付款成功时') ,
                'level' => 9,
                'env_list'=>array(
                    'order_id'=>'*订单号',
                    'bill_id'=>'*支付单流水号',
                    'pay_app_name'=>'支付方式名称',
                    'out_trade_no'=>'第三方支付平台流水号',
                    'money'=>'*支付金额',
                    'timestr'=>'*支付时间(年-月-日 时:分:秒)',
                ),
            ) ,
            'orders-refund' => array(
                'label' => ('订单退款成功时') ,
                'level' => 9,
                'env_list'=>array(
                    'order_id'=>'*订单号',
                    'bill_id'=>'*退款单流水号',
                    'money'=>'*退款金额',
                    'timestr'=>'*退款时间(年-月-日 时:分:秒)',
                ),
            ) ,
            'orders-end' => array(
                'label' => ('订单完成后') ,
                'level' => 9,
                'env_list'=>array(
                    'order_id'=>'*订单号',
                    'timestr'=>'*完成时间(年-月-日 时:分:秒)',
                ),
            ) ,
            'orders-cancel' => array(
                'label' => ('订单作废后') ,
                'level' => 9,
                'env_list'=>array(
                    'order_id'=>'*订单号',
                    'timestr'=>'*作废时间(年-月-日 时:分:秒)',
                ),
            ) ,

        );

        //actions 扩展
        foreach (vmc::servicelist('b2c.messenger.actions') as $service) {
            if (is_object($service)) {
                if (method_exists($service, 'get_actions')) {
                    $data = $service->get_actions();
                }
            }
            $actions = array_merge($actions, (array) $data);
        }


        return $actions;
    }
    /**
     * 获得消息发送器列表
     */
    public function get_sender_list()
    {
        $sender_list = array();
        foreach (vmc::servicelist('b2c_messenger_sender') as $key => $v) {
            $sender_list[$key] = (array)$v;
            $sender_list[$key]['methods'] = get_class_methods($v);
        }
        return $sender_list;
    }

    /**
     * 初始化消息发送器
     * @param $sender_class 消息发送器class_name
     */
    public function init_sender($sender_class)
    {
        if(!is_object($sender_class)){
            $sender = vmc::singleton($sender_class);
        }else{
            $sender = $sender_class;
        }
        if (method_exists($sender, 'getOptions') || method_exists($sender, 'getoptions')) {
            $sender->config = $this->getOptions($sender_class, true);
        }
        if (method_exists($sender, 'outgoingConfig') || method_exists($sender, 'outgoingconfig')) {
            $sender->outgoingOptions = $this->outgoingConfig($sender_class, true);
        }
        if (!$sender->_isReady) {
            if (method_exists($sender, 'ready')) {
                $sender->ready($sender->config);
            }
            if (method_exists($sender, 'finish')) {
                if (!$this->_finishCall) {
                    register_shutdown_function(array($this,
                        '_finish',
                    ));
                    $this->_finishCall = array();
                }
                $this->_finishCall[] = $sender;
            }
            $sender->_isReady = true;
        }

        return $sender;
    }



    /**
     * 消息入口.
     *
     * @param mixed $action_name 消息action_name
     * @param mixed $tmpl_data   用于组织消息内容的数据
     * @param mixed array $target = array('member_id'=>'','mobile'=>'','email'=>'')
     */
    public function trigger($action_name, $tmpl_data,$target = array())
    {
        $actions = $this->get_actions();
        $action = $actions[$action_name];
        if(!$action)return;
        $level = $action['level'];
        $label = $action['label'];
        $sender_list = $this->get_sender_list();
        foreach ($sender_list as $sender_class => $sender) {
            $tmpl_name = 'messenger:'.$sender_class.'/'.$action_name;
            //发送过滤
            if(isset($action[$sender_class]['exclude']) && in_array($sender_class,$action[$sender_class]['exclude'])){
                continue;
            }
            if($this->app->getConf('messenger_disabled_'.$sender_class.'_'.$action_name)!==true
            ||$action['lock'] ){
                if ($sender && !empty($target)) {
                    //开始执行
                    if($level>10){
                        $this->action($tmpl_name, $tmpl_data, $target);
                    }else{
                        //进入队列执行
                        $queue_params = array(
                            'tmpl_name'=>$tmpl_name,
                            'tmpl_data'=>$tmpl_data,
                            'target'=>$target
                        );
                        system_queue::instance()->publish('b2c_tasks_messenger', 'b2c_tasks_messenger', $queue_params);
                    }

                }
            }

        }
    }

    /**
     * 消息发送.
     *
     * @param $tmpl_name 消息发送命令字符串 messenger:$sender_class/$action_name
     * @param $tmpl_data 消息内容模板数据填充
     * @param $target 发送目标
     */
    public function action($tmpl_name,$tmpl_data,$target)
    {
        $info = explode(':',$tmpl_name);
        list($sender_class,$action_name) = explode('/',$info[1]);

        $sender = $this->init_sender($sender_class);
        //组织消息内容
        $title = $this->app->getConf('messenger.title.'.$sender_class.'.'.$action_name,app::get('site')->getConf('site_name'));
        $title = $this->fill_title($title, $tmpl_data);
        $content = $this->mdl_mem_systmpl->fetch($tmpl_name, $tmpl_data);
        $config = $sender->config;
        $config = array_merge((array)$config,array(
            'tmpl_data'=>$tmpl_data,
            'action_name'=>$action_name,
            'action_name_alias'=>$this->app->getConf('messenger_'.$action_name.'_alias')
        ));

        return $sender->send($target, $title, $content, $config);
    }


    public function _finish()
    {
        foreach ($this->_finishCall as $obj) {
            $obj->finish($obj->config);
        }
    }

    /**
     * 获得指定模板内容.
     *
     * @param $sender_class 消息发送者类名
     * @param $action_name 消息事件名称
     */
    public function get_tmpl($sender_class, $action_name)
    {
        return $this->mdl_mem_systmpl->get('messenger:'.$sender_class.'/'.$action_name);
    }

    /**
     * 填充有变量的标题
     */
    public function fill_title($title,$data = false)
    {
        if(!$data || !is_array($data))return $title;
        preg_match_all('/<\{\$(\S+)\}>/iU', $title, $result);
        foreach ($result[1] as $k => $v) {
            $v = explode('.', $v);
            foreach ($v as $key => $val) {
                $data = $data[$val];
                if (is_array($data)) {
                    continue;
                } else {
                    $title = str_replace($result[0][$k], $data, $title);
                }
            }
        }
        return $title;
    }
    /**
     * 保存消息模板
     */
    public function save_tmpl($sender_class, $action_name, $data)
    {
        $this->app->setConf('messenger.title.'.$sender_class.'.'.$action_name, $data['title']);
        return $this->mdl_mem_systmpl->set('messenger:'.$sender_class.'/'.$action_name, $data['content']);
    }

    /**
     * 获得配置项
     */
    public function getOptions($class_name, $valueOnly = false)
    {
        $obj = vmc::singleton($class_name);
        if (method_exists($obj, 'getOptions') || method_exists($obj, 'getoptions')) {
            $options = $obj->getOptions(); #print_r($options);exit;
            foreach ($options as $key => $value) {
                $app = app::get('desktop');
                $v = $app->getConf('email.config.'.$key);
                if ($valueOnly) {
                    $options[$key] = (is_null($v)) ? $options[$key] : $v;
                } else {
                    $options[$key]['value'] = (is_null($v)) ? $options[$key]['value'] : $v;
                }
            }

            return $options;
        }
    }

}
