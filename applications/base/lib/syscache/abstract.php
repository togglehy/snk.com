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


class base_syscache_abstract
{
    private $_last_modify = null;
    private $_prev_modify = null;
    private function _get_prefix()
    {
        return 'syscache_last_modified.'.get_class($this);
    }
    private function _get_prev_prefix(){
        return 'syscache_prev_modified.'.get_class($this);
    }
    public function set_last_modify()
    {
        $this->set_prev_modify();

        $last_modify = time();
        if (base_kvstore::instance('system')->store($this->_get_prefix(), $last_modify)) {
            $this->_last_modify = $last_modify;

            return true;
        }

        return false;
    }

    public function get_last_modify()
    {
        if (!isset($this->_last_modify)) {
            if (base_kvstore::instance('system')->fetch($this->_get_prefix(), $last_modify) === true &&
                !is_null($last_modify)) {
                $this->_last_modify = $last_modify;
            } else {
                $this->_last_modify = 123450001;
            }
        }

        return $this->_last_modify;
    }

    public function set_prev_modify(){
        $pm = $this->get_last_modify();
        if (base_kvstore::instance('system')->store($this->_get_prev_prefix(), $pm)) {
            return true;
        }
        return false;
    }

    public function get_prev_modify(){

        if (!isset($this->_prev_modify)) {
            if (base_kvstore::instance('system')->fetch($this->_get_prev_prefix(), $prev_modify) === true &&
                !is_null($prev_modify)) {
                $this->_prev_modify = $prev_modify;
            } else {
                $this->_prev_modify = 1;
            }
        }

        return $this->_prev_modify;
    }
}
