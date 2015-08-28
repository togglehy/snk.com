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


class desktop_finder_builder_column extends desktop_finder_builder_prototype {
    function main() {
        $finder_aliasname = $_GET['finder_aliasname'] ? $_GET['finder_aliasname'] : $_POST['finder_aliasname'];
        if(!$finder_aliasname || $finder_aliasname=='')$finder_aliasname = 'default';
        if ($_POST['col']) {
            $finder_aliasname = $finder_aliasname . '.' . $this->controller->user->user_id;
            $cols = $this->app->setConf('view.' . $this->object_name . '.' . $finder_aliasname, implode(',', $_POST['col']));

            if ($_POST['allcol']) {
                $this->app->setConf('listorder.' . $this->object_name . '.' . $finder_aliasname, implode(',', $_POST['allcol']));
            }
            header('Content-Type:application/json; charset=utf-8');
            echo '{"success":"' . '设置成功' . '"}';
        } else {
            $in_use = array_flip($this->getColumns());
            $all_columns = $this->all_columns();
            $listorder = explode(',', $this->app->getConf('listorder.' . $this->object_name . '.' . $finder_aliasname . '.' . $this->controller->user->user_id));
            if ($listorder) {
                $ordered_columns = array();
                foreach ($listorder as $col) {
                    if (isset($all_columns[$col])) {
                        $ordered_columns[$col] = $all_columns[$col];
                        unset($all_columns[$col]);
                    }
                }
                $all_columns = array_merge((array)$ordered_columns, (array)$all_columns);
                $ordered_columns = null;
            }
            $render = new base_render(app::get('desktop'));
            $render->pagedata['columns'] = $all_columns;
            $render->pagedata['use'] = array_keys($in_use);
            $render->pagedata['action'] = 'index.php?'.$_SERVER['QUERY_STRING'];
            $render->display('finder/column.html');
        }
    }
}
