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


class desktop_finder_builder_filter_render
{
    public function __contruct($finder_aliasname)
    {
        $this->finder_aliasname = $finder_aliasname;
    }
    public function main($object_name, $app, $filter = null, $controller = null, $cusrender = null)
    {
        if (strpos($_GET['object'], '@') !== false) {
            $tmp = explode('@', $object_name);
            $app = app::get($tmp[1]);
            $object_name = $tmp[0];
        }
        $object = $app->model($object_name);
        require APP_DIR.'/base/datatypes.php';
        $this->dbschema = $object->get_schema();
        foreach (vmc::servicelist('extend_filter_'.get_class($object)) as $extend_filter) {
            $colums = $extend_filter->get_extend_colums($this->finder_aliasname);
            if ($colums[$object_name]) {
                $this->dbschema['columns'] = array_merge((array) $this->dbschema['columns'], (array) $colums[$object_name]['columns']);
            }
        }

        foreach ($this->dbschema['columns'] as $c => $v) {
            if (!$v['filtertype']) {
                continue;
            }
            if (isset($filter[$c])) {
                continue;
            }

            if (!is_array($v['type']) && strpos($v['type'], 'table:') !== false) {
                $columns[$c]['inputer'] = $this->create_objectselect($c, $v);
                continue;
            }

            if (!is_array($v['type'])) {
                if (strpos($v['type'], 'decimal') !== false && $v['filtertype'] == 'number') {
                    $v['type'] = 'number';
                }
            }
            $columns[$c] = $v;

            if ($v['type'] == 'last_modify') {
                $v['type'] = 'time';
            }
            $params = array(
                    'type' => $v['type'],
                    'name' => $c,
                );
            if ($v['type'] == 'bool' && $v['default']) {
                $params = array_merge(array('value' => $v['default']), $params);
            }
            if ($this->name_prefix) {
                $params['name'] = $this->name_prefix.'['.$params['name'].']';
            }
            if ($v['type'] == 'region') {
                $params['app'] = 'ectools';
            }
            if ($v['default_value']) {
                $params['value'] = $v['default_value'];
            }
            $params['label'] = $columns[$c]['label'];

            $inputer = $this->create_input($params);
            $columns[$c]['inputer'] = $inputer;
        }

        $render = new base_render(app::get('desktop'));

        // if($object->has_tag){
        //     $render->pagedata['app_id'] = $app->app_id;
        //     $render->pagedata['tag_type'] = $object_name;
        //     $tag_inputer = $render->fetch('finder/tag_inputer.html');
        //     $columns['tag'] = array('filtertype'=>true,'filterdefault'=>true,'label'=>'标签','inputer'=>$tag_inputer);
        // }
        $render->pagedata['columns'] = $columns;
        $render->pagedata['datatypes'] = $datatypes;

        $render->display('finder/finder_filter.html');
    }

    private function create_input($params)
    {
        if (is_array($params['type'])) {
            $_return = "<select data-label='".$params['label']."' class='form-control' name='".$params['name']."'><option></option>";
            foreach ($params['type'] as $k => $v) {
                $_return .= "<option value='$k'>$v</option>";
            }
            $_return .= '</select>';

            return $_return;
        }

        if (strpos($params['type'], 'int') !== false && $params['type'] != 'intbool' && $params['name'] != $this->dbschema['idColumn']) {
            $_return = <<<EOF
            <div class="form-group row">
                <div class="col-md-6">
                    <input data-label="{$params['label']}最少" type="number" name="{$params['name']}|bthan" class="form-control" placeholder="最少">
                </div>
                <div class="col-md-6">
                    <input data-label="{$params['label']}最多" type="number" name="{$params['name']}|sthan" class="form-control" placeholder="最多">
                </div>
            </div>
EOF;

            return $_return;
        }

        switch ($params['type']) {
            case 'money':
                $_return = <<<EOF
                <div class="form-group row">
						<div class="input-icon col-md-6">
                            <i class="fa fa-yen"></i>
							<input data-label="{$params['label']}最底" type="number" name="{$params['name']}|bthan" class="form-control" placeholder="最底">
						</div>
                        <div class="input-icon col-md-6">
                            <i class="fa fa-yen"></i>
                            <input data-label="{$params['label']}最高" type="number" name="{$params['name']}|sthan" class="form-control" placeholder="最高">
                        </div>
                </div>
EOF;
                break;
            case 'number':
            $_return = <<<EOF
            <div class="form-group row">
                <div class="col-md-6">
                    <input data-label="{$params['label']}最少" type="number" name="{$params['name']}|bthan" class="form-control" placeholder="最少">
                </div>
                <div class="col-md-6">
                    <input data-label="{$params['label']}最多" type="number" name="{$params['name']}|sthan" class="form-control" placeholder="最多">
                </div>
            </div>
EOF;
            break;
            case 'bool':
            $_return = <<<EOF
            <select data-label="是否{$params['label']}" class="form-control"  name="{$params['name']}">
                <option></option>
                <option value="true">是</option>
                <option value="false">否</option>
            </select>
EOF;
            break;
            case 'intbool':
            $_return = <<<EOF
            <select data-label="是否{$params['label']}" class="form-control"  name="{$params['name']}">
                <option></option>
                <option value="1">是</option>
                <option value="0">否</option>
            </select>
EOF;
            case 'tinybool':
            $_return = <<<EOF
<select data-label="是否{$params['label']}" class="form-control"  name="{$params['name']}">
    <option></option>
    <option value="Y">是</option>
    <option value="N">否</option>
</select>
EOF;
            break;
            case 'time':
            $_return = <<<EOF
            <div class="form-group">
                <div class="input-group date form-datetime">
                                                <input type="text" data-label="{$params['label']}最早" name="{$params['name']}|bthan"  readonly class="form-control" placeholder="最早">
                                                <span class="input-group-btn">
                                                    <button class="btn default date-reset" type="button"><i class="fa fa-times"></i></button>
                                                    <button class="btn default date-set" type="button"><i class="fa fa-clock-o"></i></button>
                                                </span>
                </div>
                <div class="input-group date form-datetime">
                                            <input type="text" data-label="{$params['label']}最晚" readonly name="{$params['name']}|sthan" class="form-control" placeholder="最晚">
                                            <span class="input-group-btn">
                                                <button class="btn default date-reset" type="button"><i class="fa fa-times"></i></button>
                                                <button class="btn default date-set" type="button"><i class="fa fa-clock-o"></i></button>
                                            </span>
                </div>
            </div>
EOF;
            break;
            // default:
            // $_return = "<textarea>".var_export($params,1)."</textarea>";
            default:
            $_return = <<<EOF
                <div class="form-gruop">
                    <input type="text" data-label="{$params['label']}包含" name="{$params['name']}" class="form-control" placeholder="">
                </div>
EOF;
                break;
        }

        return $_return;
    }

    public function create_objectselect($c, $v)
    {
        return '';
    }
}
