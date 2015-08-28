<?php

class b2c_mcache{

    function get_cache_methods() {
        return array(
            array(
                'name' => '商品详情页',
                'app'=>'b2c',
                'ctl'=>'mobile_product',
                'act'=>'index',
                'expires'=>'300'),
            array(
                'name' => '首页',
                'app'=>'mobile',
                'ctl'=>'index',
                'act'=>'index',
                'expires'=>'300'),
        );
    }
}
