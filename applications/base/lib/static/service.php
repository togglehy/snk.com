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


class service implements Iterator
{
    public function __construct($service_define, $filter = null)
    {
        $this->iterator = new ArrayIterator($service_define['list']);
        $this->interface = $service_define['interface'];
        $this->filter = $filter;
        $this->valid();
    }

    public function current()
    {
        return $this->current_object;
    }

    public function rewind()
    {
        $this->iterator->rewind();
    }

    public function key()
    {
        return $this->iterator->current();
    }

    public function next()
    {
        return $this->iterator->next();
    }

    public function valid()
    {
        while ($this->iterator->valid()) {
            if ($this->filter()) {
                return true;
            } else {
                $this->iterator->next();
            }
        };

        return false;
    }

    private function filter()
    {
        if ($this->filter) {
            $current = $this->iterator->current();
            if (is_array($this->filter) && !in_array($current, $this->filter)) {
                $this->iterator->next();
            }
            if (!is_array($this->filter) && $this->filter != $current) {
                $this->iterator->next();
            }
        }
        $current = $this->iterator->current();
        if ($current) {
            try {
                $this->current_object = vmc::singleton($current);
            } catch (Exception $e) {
                //trigger_error('Miss class '.$current);
                return false;
            }
            if ($this->current_object) {
                if ($this->interface && $this->current_object instanceof $this->interface) {
                    return false;
                }

                return true;
            }
        }

        return false;
    }
}
