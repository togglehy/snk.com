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




class desktop_mdl_pam extends dbeav_model{

      function __construct(&$app){
        $this->app = $app;
        $this->columns = array(
                        'passport_name'=>array('label'=>'登录类型','width'=>200),
                        'site_passport_status'=>array('label'=>'启用','type'=>'bool'),
                   );

        $this->schema = array(
                'default_in_list'=>array_keys($this->columns),
                'in_list'=>array_keys($this->columns),
                'idColumn'=>'passport_id',
                'columns'=>$this->columns
            );

    }

    function get_schema(){
        return $this->schema;
    }

    function count($filter=''){
        return count($this->getList());
    }

    public function getList($cols='*', $filter=array('status' => 'false'), $offset=0, $limit=-1, $orderby=null){
            $services = vmc::serviceList('passport');
            foreach($services as $service){
                if($service instanceof pam_interface_passport){
                    $a_temp = $service->get_config();
                    $item['passport_id'] = $a_temp['passport_id']['value'];
                    $item['passport_name'] = $a_temp['passport_name']['value'];
                    $item['shopadmin_passport_status'] = $a_temp['shopadmin_passport_status']['value'];
                    $item['site_passport_status'] = $a_temp['site_passport_status']['value'];
                    $item['passport_version'] = $a_temp['passport_version']['value'];
                    $ret[] = $item;
                }
            }

            return $ret;

    }

}
