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


class b2c_sales_solution_process {

    public function getTemplateList($is_order = false) {
        $_return = array();
        foreach (vmc::servicelist('b2c_promotion_solution_tpl_apps') as $object) {
            if (method_exists($object, 'get_status')) {
                if (!$object->get_status()) {
                    continue;
                }
            }
            if($is_order){
                if($object->type == 'goods')continue;
                $name = '订单'.$object->name;

            }else{
                if($object->type == 'order')continue;
                $name = $object->name;

            }

            $_return[ get_class($object) ] = $name;
        }

        return $_return;
    }
    public function getTemplate($tpl_name, $aData = array() , $type = 'order') {
        $oTC = vmc::singleton($tpl_name);
        $oTC->type = $type;
        $t = $oTC->config($aData[$tpl_name]);
        return ($type == 'goods' ? ('商品') : ('订单')) . $oTC->config($aData[$tpl_name]);
    }
    public function getType($aData = array() , $key) {
        if (!$aData) return false;
        return $aData[$key]['type'];
    }
}
