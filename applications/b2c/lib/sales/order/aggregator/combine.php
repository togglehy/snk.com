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



class b2c_sales_order_aggregator_combine extends b2c_sales_order_aggregator {
    public function getItem() {
        // 其实一个aggregator 只有一条记录的哈
        return array(
            'b2c_sales_order_aggregator_combine' => array(
                'name' => ('条件组合') ,
                'object' => 'b2c_sales_order_aggregator_combine',
                'support' => array(
                    'aggregator' => 'all',
                    'item' => array(
                        'order' => ('购物车属性') ,
                    )
                ) ,
            )
        );
    }
    protected function _init_aggregator() {
        if (is_null($this->_aAggregator)) {
            $aResult = array();
            /*
            // 这里会产生死循环的递归操作
            // 暂时使用vmc::servicelist 内部的代码进行处理
            foreach(vmc::servicelist($this->aggregator_apps) as $object) {
                //if(get_class($this))
                $aResult = array_merge($aResult,$object->getItem());
            }*/
            // 多个的时候也会出现递归的问题 这个得好好想想解决方案 2010-05-17 14:27 wubin
            // todo 这里只是暂时的处理 等待解决方案 2010-05-14 15:21
            $apps = app::get('base')->model('app_content')->getlist('content_path,app_id', array(
                'content_type' => 'service',
                'content_name' => $this->aggregator_apps
            ));
            foreach ($apps as $row) {
                if ($row['content_path']) {
                    if ($row['content_path'] == get_class($this)) {
                        $aResult = array_merge($aResult, $this->getItem());
                    } else {
                        $aResult = array_merge($aResult, vmc::singleton($row['content_path'], app::get($row['app_id']))->getItem());
                    }
                }
            }
            $this->_aAggregator = $aResult;
        }
    }
}
?>
