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


class base_syscache_adapter_filesystem extends base_syscache_adapter_abstract implements base_interface_syscache_adapter
{
    private $_controller = null;

    protected $_handler = null;

    private $_data = array();

    private function _get_pathname()
    {
        if (!is_dir(TMP_DIR)) {
            utils::mkdir_p(TMP_DIR);
        }

        return TMP_DIR.'/'.$this->get_key();
    }

    private function _get_prev_pathname(){
        if (!is_dir(TMP_DIR)) {
            utils::mkdir_p(TMP_DIR);
        }
        return TMP_DIR.'/'.$this->get_prev_key();
    }

    public function init_data()
    {
        if (file_exists($this->_get_pathname())) {
            $this->_data = unserialize(file_get_contents($this->_get_pathname()));
            return true;
        } else {
            return false;
        }
    }

    public function create($data)
    {

        if (file_put_contents($this->_get_pathname(), serialize($data), LOCK_EX)) {
            if(is_file($this->_get_prev_pathname()) && $this->_get_prev_pathname()!=$this->_get_pathname()){
                try{
                    unlink($this->_get_prev_pathname());
                    logger::debug('unlink :'.$this->_get_prev_pathname());
                }catch(Exception $e){
                    logger::error($e->getMessage());
                }

            }
            return true;
        }
        if(ENVIRONMENT == 'DEVELOPMENT'){
            trigger_error('SYSCACHE 初始化失败!请检查目录:'.TMP_DIR,E_USER_ERROR);
        }
        return false;
    }

    public function get($key)
    {
        return $this->_data[$key];
    }

    public function status(){

        return array(
            '缓存适配器'=>'本地文件系统',
            '缓存对象长度'=>sizeof($this->_data,1),
            '存储位置'=>str_replace(ROOT_DIR,'ROOT_DIR',$this->_get_pathname()),
            '最后更新时间'=>date('Y-m-d H:i:s',$this->_handler->get_last_modify())
        );

    }

    public function refresh(){

    }
}
