<?php

abstract class base_syscache_adapter_abstract
{
    public function init($handler)
    {
        $this->_handler = $handler;

        return $this->init_data();
    }

    /*
     * 生成经过处理的唯一key
     * @var string $key
     * @access public
     * @return string
     */
    protected function get_key()
    {
        $modify = $this->_handler->get_last_modify();
        $handler_class = explode('_',get_class($this->_handler));
        $handler_name = array_pop($handler_class);
        $key = 'vmc-syscache-'.$handler_name.'-'.date('YmdHis', $modify);
        return $key;
    }

    /*
     * 生成经过处理的唯一key
     * @var string $key
     * @access public
     * @return string
     */
    protected function get_prev_key()
    {
        $modify = $this->_handler->get_prev_modify();
        $handler_class = explode('_',get_class($this->_handler));
        $handler_name = array_pop($handler_class);
        $key = 'vmc-syscache-'.$handler_name.'-'.date('YmdHis', $modify);
        return $key;
    }
}
