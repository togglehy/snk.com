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


class desktop_view_input {
    function input_image($params) {
        $ui = new base_component_ui($this);
        $domid = $ui->new_dom_id();
        $input_name = $params['name'];
        $input_value = $params['value'];
        if ($input_value) {
            $image_url = base_storager::image_path($input_value);
        }
        $render = app::get('desktop')->render();
        $render->pagedata = $params;
        $render->pagedata['id'] = $domid;
        $render->pagedata['name'] = $input_name;
        $render->pagedata['url'] = $image_url;
        $render->pagedata['image_id'] = $input_value;
        $render->pagedata['tag'] = $params['tag'];
        return $render->fetch('ui/input_image.html');
    }
    function input_object($params) {
        $object = $params['object'];
        if (strpos($params['object'], '@') !== false) {
            list($object, $app_id) = explode('@', $params['object']);
            $params['object'] = $object;
        } elseif ($params['app']) {
            $app_id = $params['app'];
        } else {
            $app_id = $this->app->app_id;
        }
        $params['finder_mdl'] = implode('_', array(
            $app_id,
            'mdl',
            $object
        ));
        $params['multiple'] = $params['multiple']?'true':'false';
        if(isset($params['base_filter'])){
            if(!is_array($params['base_filter'])){
                parse_str($params['base_filter'],$params['base_filter']);
            }
        }
        $render = app::get('desktop')->render();
        $params['domid'] = substr(md5(uniqid()) , 0, 6);

        $app = app::get($app_id);
        $mdl = $app->model($object);
        $dbschema = $mdl->get_schema();
        $params['textcol'] = $params['textcol']?$params['textcol']:$dbschema['textColumn'];


        $render->pagedata = $params;
        return $render->fetch('finder/object_select.html');
    }
    function input_html($params) {
        $id = 'editor_' . substr(md5(rand(0, time())) , 0, 6);
        $render = app::get('desktop')->render();
        $render->pagedata['id'] = $id;
        $render->pagedata['params'] = $params;
        $html = $render->fetch('editor/summernote.html');
        return $html;
    }
    function input_code($params) {

        $render = app::get('desktop')->render();
        $id = 'code_' . substr(md5(rand(0, time())) , 0, 6);
        $params['id'] = $id;
        $params['res_url'] = $app->res_url;
        $render->pagedata = $params;
        return $render->fetch('editor/syntax_highlighter.html');
    }
}
