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




class b2c_order_createfinish
{

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * 订单创建完成时
     * @params array - 订单完整数据，含ITEMS
     * @return boolean - 执行成功与否
     */
    public function exec($sdf,&$msg='')
    {
        logger::debug($sdf['order_id'].'createfinish exec');


        if($sdf['is_cod'] == 'Y'){
            $freeze_data = array();
            foreach ($sdf['items'] as $key => $item) {

                //购买数量计数
                vmc::singleton('b2c_openapi_goods',false)->counter(array(
                    'goods_id'=>$item['goods_id'],
                    'buy_count'=>$item['nums'],
                    'buy_count_sign'=>md5($item['goods_id'].'buy_count'.($item['nums'] * 1024))//计数签名
                ));
                //组织库存冻结数据
                $freeze_data[] = array(
                    'sku'=>$item['bn'],
                    'quantity'=>$item['nums']
                );
            }

            //库存冻结
            if(!vmc::singleton('b2c_goods_stock')->freeze($freeze_data,$msg)){
                logger::error('库存冻结异常!ORDER_ID:'.$order_sdf['order_id'].','.$msg);
            }
        }

        return true;
    }


}
