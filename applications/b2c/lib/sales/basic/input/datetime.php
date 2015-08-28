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



class b2c_sales_basic_input_datetime
{
    public $type = 'datetime';
    public function create($aData, $table_info=array()) {
        $html = '';
        $params = array(
            'type'  => 'time',
            'name'  => $aData['name'],
            'id'    => $aData['name'],
            'value' => $aData['default'],
            'required' => 'true'
        );
        $html .= vmc::singleton('base_view_input')->input_time($params);
        return $aData['desc'].$html;
    }
}
