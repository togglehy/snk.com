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


 
class desktop_ctl_autocomplete extends base_controller{

    function index(){
        $this->_request = vmc::singleton('base_component_request');
        $params = $this->_request->get_get('params');
        $params = explode(':',$params);
        $svckey = $params[0];
        $cols = explode(',',$params[1]);
        $key = $this->_request->get_get($cols[0]);
        $autocomplete = vmc::servicelist('autocomplete.'.$svckey);
        foreach($autocomplete as $service){
            $return = $service->get_data($key,$cols);
        }
        echo "window.autocompleter_json=".json_encode($return)."";
    }

}
