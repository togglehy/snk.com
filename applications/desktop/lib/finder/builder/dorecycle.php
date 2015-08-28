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


class desktop_finder_builder_dorecycle extends desktop_finder_builder_prototype
{
    public function main()
    {
        $this->controller->begin();

        $o = $this->app->model($this->object->table_name());
        $o->filter_use_like = true;

        $this->dbschema = $this->object->get_schema();

        $pkey = $this->dbschema['idColumn'];

        $pkey_value = $_POST[$pkey];
        $filter = array($pkey => $pkey_value);
        if ($_POST['isSelectedAll'] == '_ALL_') {  //edit by 矫雷 （点此选择全部) 分开写的应该统一函数处理/@modify(修复高级筛选，全选后删除不了的错误@lujy)
            $_filter = $_POST;
            unset($_filter['isSelectedAll']);
            if ($_filter['_finder']) {
                unset($_filter['_finder']);
            }
            if (is_null($filter[$pkey])) {
                $filter = $_filter;
            } else {
                $filter = array_merge($_filter, $filter);
            }
        } else {
            $filter[$this->dbschema['idColumn']] = $_POST[$this->dbschema['idColumn']];
        }
        if ($_GET['view']) {
            $views = $this->get_views();
            $filter = array_merge((array) $views[$_GET['view']]['filter'], (array) $filter);
        }

        $recycle = vmc::singleton('desktop_system_recycle');

        $result = $recycle->dorecycle(get_class($this->object), $filter);

        if ($result) {
            $this->controller->end(true, '删除成功');
        } else {
            $this->controller->end(false, $o->recycle_msg ? $o->recycle_msg : '删除失败！');
        }
    }
}
