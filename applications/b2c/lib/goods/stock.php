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


class b2c_goods_stock
{
    public function refresh(&$msg)
    {
        //同步products 表 SKU信息到stock
        $SQL_copy = "INSERT INTO vmc_b2c_stock(sku_bn,`title`,barcode,last_modify) SELECT bn,CONCAT(`name`,' ',IFNULL(`spec_info`,'')),barcode,UNIX_TIMESTAMP(CURRENT_TIMESTAMP) FROM vmc_b2c_products WHERE vmc_b2c_products.bn NOT IN (SELECT sku_bn FROM `vmc_b2c_stock`)";
        //清除products 表 没有的库存记录
        $SQL_clean = 'DELETE FROM vmc_b2c_stock WHERE vmc_b2c_stock.sku_bn NOT IN (SELECT bn FROM `vmc_b2c_products`)';
        $re1 = vmc::database()->exec($SQL_copy);
        $re2 = vmc::database()->exec($SQL_clean);
        if (!$re1['rs'] || !$re2['rs']) {
            $msg = '异常';

            return false;
        }
        $msg = '同步完成';

        return true;
    }
    public function update_stock($filter, $col, $nums = 0, $operation, &$msg)
    {
        $where = base_db_tools::filter2sql($filter);

        $op_map = array(
            'plus' => '+',
            'minus' => '-',
            'multiple' => '*',
        );
        if($op_map[$operation] == '-'){
                $exist_stock = app::get('b2c')->model('stock')->getRow($col,$filter);
                if($exist_stock[$col]<=0){
                    return true;
                }
        }
        if ($operation) {
            $set = $col.$op_map[$operation].(string)intval($nums);
        } else {
            $set = $nums?$nums:0;
        }
        $SQL_update = "UPDATE `vmc_b2c_stock` SET $col = $set,last_modify=UNIX_TIMESTAMP(CURRENT_TIMESTAMP)  WHERE $where";
        
        if (!vmc::database()->exec($SQL_update,true)) {
            $msg = '异常';

            return false;
        }
        $msg = '操作成功';

        return true;
    }

    //查询可售卖库存
    private function get_stock($filter)
    {
        $mdl_stock = app::get('b2c')->model('stock');
        $stock = $mdl_stock->getRow('quantity-freez_quantity as num', $filter);

        return $stock['num'];
    }
    //判断某购买数量是否可行
    public function is_available_stock($bn, $num, &$abs_stock)
    {
        $abs_stock = $this->get_stock(array('sku_bn' => $bn));

        return $abs_stock >= $num;
    }

     /**
      * 冻结库存.
      * @param $params  array(array('sku'=>'123456','quantity'=>1));
      */
     public function freeze($params, &$msg)
     {
         $db = vmc::database();
         $trans = $db->beginTransaction();
         foreach ($params as $key => $item) {
            if(!$this->is_available_stock($item['sku'],$item['quantity'],$abs_stock)){
                $msg = 'sku:'.$item['sku'].'库存不足,无法完成冻结操作!,ASB_STOCK:'.$abs_stock.',sku:'.$item['sku'].',quantity:'.$item['quantity'];
                $db->rollback();
                return false;
            }
            if(!$this->update_stock(array(
                'sku_bn'=>$item['sku']
                ),'freez_quantity',$item['quantity'],'plus',$msg)){
                $db->rollback();
                return false;
            }
         }
         $db->commit();
         return true;
     }

      /**
       * 批量解冻库存.
       */
      public function unfreeze($params, &$msg)
      {
          $db = vmc::database();
          $trans = $db->beginTransaction();
          foreach ($params as $key => $item) {
             if(!$this->update_stock(array(
                 'sku_bn'=>$item['sku']
                 ),'freez_quantity',$item['quantity'],'minus',$msg)){
                 $db->rollback();
                 return false;
             }
          }
          $db->commit();
          return true;
      }

      /**
       * 出库.
       */
      public function delivery($params, &$msg)
      {
          $db = vmc::database();
          $trans = $db->beginTransaction();
          foreach ($params as $key => $item) {
             if(!$this->is_available_stock($item['sku'],$item['quantity'],$abs_stock)){
                 $msg = 'sku:'.$item['sku'].'可售库存不足!,可售库存:'.$abs_stock;
                 $db->rollback();
                 return false;
             }
             if(!$this->update_stock(array(
                 'sku_bn'=>$item['sku']
             ),'quantity',$item['quantity'],'minus',$msg)){
                 $db->rollback();
                 return false;
             }
          }
          $db->commit();
          return true;

      }
      /**
       * 回滚.
       */
      public function returned($params, &$msg)
      {
          $db = vmc::database();
          $trans = $db->beginTransaction();
          foreach ($params as $key => $item) {
             if(!$this->update_stock(array(
                 'sku_bn'=>$item['sku']
             ),'quantity',$item['quantity'],'plus',$msg)){
                 $db->rollback();
                 return false;
             }
          }
          $db->commit();
          return true;
      }
}
