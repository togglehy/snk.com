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


class b2c_mdl_member_goods extends dbeav_model
{

    /**
     * 添加商品到会员收藏夹.
     */
    public function add_fav($member_id = null, $goods_id = null)
    {
        if (!$member_id || !$goods_id) {
            return false;
        }

        $filter['member_id'] = $member_id;
        $filter['goods_id'] = $goods_id;
        $filter['type'] = 'fav';
        if ($this->count($filter) > 0) {
            return true; //已存在
        }
        $gdetail = app::get('b2c')->model('goods')->dump($goods_id, 'goods_id,name,image_default_id', array('product' => array('price', 'image_id
')));
        $product_id = key($gdetail['product']);
        $product = current($gdetail['product']);
        $sdf = array(
           'goods_id' => $goods_id,
           'product_id'=>$product_id,
           'member_id' => $member_id,
           'goods_name' => $gdetail['name'],
           'goods_price' => $product['price'],
           'image_default_id' => $gdetail['image_default_id'],
           'status' => 'ready',
           'create_time' => time(),
           'type' => 'fav',
           'object_type' => 'goods',
          );
        if ($this->save($sdf)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *确认收藏及数量
     */
     public function check_fav($member_id = null,$goods_id = null){
         if (!$goods_id) {
             return false;
         }
         if($member_id){
             $filter['member_id'] = $member_id;
         }
         $filter['goods_id'] = $goods_id;
         $filter['type'] = 'fav';
         if($member_id){
             $fav_yes = $this->count($filter);
             unset($filter['member_id']);
         }
         $fav_count = $this->count($filter);

         return array(
            'is_fav'=>$fav_yes,
            'fav_count'=>$fav_count
         );

     }




}
