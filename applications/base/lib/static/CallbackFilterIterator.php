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



 class CallbackFilterIterator extends FilterIterator
{
    protected $callback;

    public function __construct(Iterator $iterator, $callback, $params =null){
        $this->callback = $callback;
        $this->_params = $params;
        parent::__construct($iterator);
    }

    public function accept(){
        return call_user_func(
            $this->callback,
            $this->current(),
            $this->key(),
            $this->getInnerIterator(),
            $this->_params
        );
    }
}
