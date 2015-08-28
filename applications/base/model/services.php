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


 
class base_mdl_services extends base_db_model{
    
    function __construct(&$app){
        $this->app = $app;
        $this->columns = array (
            'content_id'=>array(
                'label'=>'',
                'width'=>200,
                'type' => 'number',
                'pkey' => true,
                'extra' => 'auto_increment',
            ),
            'content_type' => 
            array (
                'label'=>'支式',
                'width'=>200,
                'type' => 'varchar(80)',
                'required' => true,
                
                'width' => 100,
                'in_list' => true,
                'default_in_list' => true,
            ),
            'app_id' => 
            array (
                'label'=>'app',
                'width'=>200,
                'type' => 'table:apps',
                'required' => true,
                'width' => 100,
                'in_list' => true,
                'default_in_list' => true,
            ),
            'content_name'=>array(
                'label'=>'',
                'width'=>200,
                'type'=>'varchar(80)',
            ),
            'content_title'=>array(
                'label'=>'',
                'width'=>200,
                'type'=>'varchar(100)',
                'is_title'=>true,
            ),
            'content_path'=>array(
                'label'=>'支式',
                'width'=>200,
                'type'=>'varchar(255)',
            ),
            'disabled'=>array(
                'label'=>'支式',
                'width'=>200,
                'type'=>'bool',
                 
                'default'=>'true'
            )
        );
        
        $this->schema = array(
                'default_in_list'=>array_keys($this->columns),
                'in_list'=>array_keys($this->columns),
                'idColumn'=>'app_id',
                'columns'=>$this->columns
            );
    }
    
    function get_schema(){
        return $this->schema;
    }
    
    function count($filter=''){
        return count(vmc::servicelist('ectools_payment.ectools_mdl_payment_cfgs'));
    }
    
    public function getList($cols='*', $filter='null', $offset=0, $limit=-1, $orderby=null)
    {
        $data = app::get('base')->model('app_content')->getlist('*',$filter);
        return $data;
    }
}
