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



class base_syscache_adapter_chdb extends base_syscache_adapter_abstract implements base_interface_syscache_adapter{

    private $_controller = null;

    protected $_handler = null;

    private function _get_pathname() {
        if(!is_dir(TMP_DIR)){
            utils::mkdir_p(TMP_DIR);
        }
        return TMP_DIR.'/'.$this->get_key().'.chdb';
    }

    private function _get_prev_pathname(){
        if (!is_dir(TMP_DIR)) {
            utils::mkdir_p(TMP_DIR);
        }
        return TMP_DIR.'/'.$this->get_prev_key().'.chdb';
    }

    public function init_data(){
        try {
            $this->_controller = new chdb($this->_get_pathname());
        } catch (Exception $e){
            return false;
        }
        return true;
    }


    public function create($data){
        foreach( (array)$data as $k => $v ){
            $data[$k] = serialize($v);
        }
        chdb_create($this->_get_pathname(), $data);
        $this->_controller = new chdb($this->_get_pathname());
        if(is_file($this->_get_prev_pathname()) && $this->_get_prev_pathname()!=$this->_get_pathname()){
            try{
                unlink($this->_get_prev_pathname());
            }catch(Exception $e){
                logger::error($e->getMessage());
            }

        }
        return true;
    }

    public function get($key){
        $value = $this->_controller->get($key);
        if (is_null($value)) {
            return null;
        }
        return unserialize($value)?unserialize($value):null;
    }

    public function status(){

        return array(
            '缓存适配器'=>'chdb',
            '存储位置'=>str_replace(ROOT_DIR,'ROOT_DIR',$this->_get_pathname()),
            '最后更新时间'=>date('Y-m-d H:i:s',$this->_handler->get_last_modify())
        );
    }

    public function refresh(){

    }
}
