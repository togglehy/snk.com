<?php

class desktop_finder_builder_view extends desktop_finder_builder_prototype
{
    public $use_buildin_set_tag = false;
    public $use_buildin_recycle = false;
    public $use_buildin_export = false;
    public $use_buildin_import = false;
    public $use_buildin_filter = false;
    public $use_buildin_setcol = true;
    public $use_buildin_selectrow = true;

    public $object_select_model = false;
    public $selectrow_type = 'checkbox';

    public $max_actions = 10;
    public $filter = array();
    public $delete_confirm_tip = '';
    public $base_query_string = '';
    /**
     * @var 全局变量,控制视图
     */
    private $__view = array();
    public function main()
    {
        $this->html_script = '';
        $this->html_header = '';
        $this->html_body = '';
        $this->html_footer = '';
        $this->html_pager = '';
        $this->html_actions = '';
        $this->short_object_name = substr($this->object_name, strpos($this->object_name, '_mdl_') + 5);
        $this->__view = $this->get_views();
        if (count($this->__view) && $this->use_view_tab) {
            $this->tab_view_count = 0;
            foreach ((array) $this->__view as $view) {
                if ($view['addon']) {
                    $this->tab_view_count += $view['addon'];
                }
            }
            if (!$_GET['view']) {
                $default_view = reset($this->__view);
                $view_filter = $default_view['filter'];
            } else {
                $view_filter = (array) $this->__view[$_GET['view']]['filter'];
                // 如果有view_tab的情况下,则将当前view的总数记下，减少同一finder两次进行count计算，增加性能
                if ($this->__view[$_GET['view']]['addon'] != '_FILTER_POINT_') {
                    $this->current_view_count = $this->__view[$_GET['view']]['addon'];
                }
            }
        }
        $this->__view_filter = $view_filter;
        if ($_GET['filter']) {
            $get_filter = (array) $_GET['filter'];
            if (!is_array($_GET['filter'])) {
                if (isset($_GET['filter']) && $_GET['filter'] = (array) unserialize(urldecode($_GET['filter']))) {
                    $get_filter = (array) $_GET['filter'];
                }
            }
        }
        #if( $_POST ) $view_filter = array();
        $this->params = array_merge((array) $this->base_filter, (array) $get_filter, (array) $view_filter, (array) $_POST);
        /* 用于打开的input_object object_base_filter **/
        if (isset($_GET['obj_filter']) && $_GET['obj_filter']) {
            $this->params = array_merge($this->params, array(
            'obj_filter' => $_GET['obj_filter'],
        ));
        }
        unset($this->params['_finder']);
        foreach ($this->params as $k => $v) {
            if (!is_array($v) && $v !== false) {
                $this->params[$k] = urldecode(trim($v));
            }
            if ($this->params[$k] === '') {
                unset($this->params[$k]);
            }
        }

        $this->getColumns();
        $this->getOrderBy();
        $this->pagelimit = $this->getPageLimit();
        if ($this->detail_pages) {
            $this->detail_url = $this->url.'&action=detail';
        }
        $render = $this->render = new base_render(app::get('desktop'));
        $render->pagedata['title'] = $this->title;
        $render->pagedata['name'] = $this->name;
        $render->pagedata['url'] = $this->url;

        // finder列表顶部自定义显示html信息
        if ($this->finder_extra_view) {
            $render->pagedata['finder_extra'] = '';
            foreach ($this->finder_extra_view as $view) {
                $_render = new base_render(app::get($view['app']));
                $_render->pagedata = $render->pagedata;
                $_render->pagedata['extra_pagedata'] = $view['extra_pagedata'];
                $render->pagedata['finder_extra'] .= $_render->fetch($view['view']);
            }
        }

        $this->createView();
        $this->_pager();
        $output = '';
        if (!$this->object_select_model) {
            $output .= $this->controller->sidebar_active();
        } else {
            $render->pagedata['object_select_model'] = 'true';
            $render->pagedata['selectrow_type'] = $this->selectrow_type;
        }
        $this->_actions();
        $this->html_subheader = $render->fetch('finder/view/subheader.html');
        $this->_header($this->html_subheader);
        $this->_footer($this->html_pager);
        $this->html_pager = $render->fetch('finder/view/pager.html');
        $this->html_body = $render->fetch('finder/view/body.html');
        $output .= $render->fetch('finder/view/finder_title.html').$this->html_actions.$this->html_header.$this->html_body.$this->html_footer;
        echo $output;

        return $output;
    }
    public function _script()
    {
    }
    public function _header($subheader = '')
    {
        $render = $this->render;
        //$render->pagedata['inputhtml'] = $this->toinput($this->params);
        $render->pagedata['subheader'] = $subheader;
        $query = $_GET;
        unset($query['page']);
        $query = utils::http_build_query($query);
        $render->pagedata['query'] = $query;
        //$this->html_header = $render->fetch('finder/view/header.html');
    }
    public function _subheader()
    {
    }
    public function _body()
    {
    }
    public function _footer($pager)
    {
        $render = $this->render;
        $render->pagedata['pager'] = $pager;
        $this->html_footer = $render->fetch('finder/view/footer.html');
    }
    public function _pager()
    {
        $pre_btn_addon = $this->pager_info['current'] > 1 ? '' : 'disabled="disabled"';
        $next_btn_addon = $this->pager_info['current'] < $this->pager_info['total'] ? '' : 'disabled="disabled"';
        $nextpage = $this->pager_info['current'] + 1;
        $prevpage = $this->pager_info['current'] - 1;
        $from = $this->pagelimit * ($this->pager_info['current'] - 1) + 1;
        $to = $from + $this->pager_info['list'] - 1;
        $pager = $this->ui->desktoppager(array(
            'current' => $this->pager_info['current'],
            'total' => $this->pager_info['total'],
            'link' => 'javascript:;" data-go="%d',
        ));
        $plimit_sel = '';
        foreach ($this->plimit_in_sel as $sel) {
            $checkcode = $this->pagelimit == $sel ? 'active' : '';

            $plimit_sel .= <<<EOF
            <li class="{$checkcode}" data-limit="$sel">
                <a href="javascript:;">每页最多展示{$sel}条</a>
            </li>
EOF;
        }
        $render = $this->render;
        $render->pagedata['plimit'] = $this->pagelimit;
        $render->pagedata['plimit_sel'] = $plimit_sel;
        $render->pagedata['from'] = $from;
        $render->pagedata['to'] = $to;
        $render->pagedata['pre_btn_addon'] = $pre_btn_addon;
        $render->pagedata['next_btn_addon'] = $next_btn_addon;
        $render->pagedata['pager'] = $pager;
        $this->html_pager = $render->fetch('finder/view/pager.html');
    }
    public function _actions()
    {
        $finder_name = $this->name;
        $custom_actions = $this->actions;
        if ($this->use_buildin_set_tag) {
            $_tagaction = array(
                'label' => ('标签') ,
                'icon' => 'fa-tags',
                'data-submit' => $this->url.'&action=tag',
                'data-target' => '#finder_actions_modal_'.$this->name,
            );
            $actions[] = $_tagaction;
        }
        if ($this->use_buildin_recycle) {
            $actions[] = array(
                'label' => '删除' ,
                'icon' => 'fa-trash-o',
                'data-confirm' => $this->delete_confirm_tip ? $this->delete_confirm_tip : '确定删除选中项？' ,
                'data-submit' => $this->url.'&action=dorecycle',
            );
        }
        if ($this->use_buildin_export) {
            $export_url = 'index.php?app=importexport&ctl=admin_export&act=export_view&_params[app]='.$this->app->app_id.'&_params[mdl]='.$this->object_name;
            $actions[] = array(
                'label' => '导出' ,
                'icon' => 'fa-cloud-download',
                'data-submit' => $export_url.'&action=export',
                'data-target' => '#finder_actions_modal_'.$this->name,
            );
        }
        if ($this->use_buildin_import) {
            $import_url = 'index.php?app=importexport&ctl=admin_import&act=import_view&_params[app]='.$this->app->app_id.'&_params[mdl]='.$this->object_name;
            $actions[] = array(
                'label' => '导入' ,
                'icon' => 'fa-cloud-upload',
                'href' => $import_url.'&action=import',
                'data-target' => '#finder_actions_modal_'.$this->name,
                'data-toggle' => 'modal',
            );
        }
        foreach ((array) $this->service_object as $object) {
            $actions = array_merge((array) $actions, (array) $object->actions);
        }
        foreach (vmc::servicelist('finder_actions.'.$this->object_name) as $key => $service_object) {
            if (method_exists($service_object, 'action_modify')) {
                $service_object->action_modify($actions);
            }
        }
        $max_custom_action = $this->max_actions;
        $i = 0;
        if (isset($custom_actions) && $custom_actions) {
            foreach ($custom_actions as $key => $item) {
                if (!$item['href']) {
                    $item['href'] = 'javascript:void(0);';
                }
                $show_actions[] = $item;
                if ($i++ == $max_custom_action - 1) {
                    break;
                }
            }
        }
        $render = $this->render;
        $render->pagedata['custom_actions'] = $show_actions;
        $render->pagedata['actions'] = $actions;
        $render->pagedata['finder_aliasname'] = $this->finder_aliasname;
        $render->pagedata['finder_name'] = $finder_name;
        $render->pagedata['use_buildin_filter'] = $this->use_buildin_filter;
        $render->pagedata['use_buildin_setcol'] = $this->use_buildin_setcol;

        if (method_exists($this->object, 'searchOptions')) {
            $searchOptions = $this->object->searchOptions();
        }
        if($this->has_tag){
            if(!isset($searchOptions)){
                $searchOptions = array();
            }
            $searchOptions = array_merge($searchOptions,array('tag_name'=>'标签'));
        }

        if (is_array($searchOptions) && $this->__view_filter) {
            foreach ($searchOptions as $key => $val) {
                if (isset($this->__view_filter[$key])) {
                    unset($searchOptions[$key]);
                }
            }
        }
        $render->pagedata['searchOptions'] = $searchOptions;
        $render->pagedata['__search_options_default_label'] = current($searchOptions);

        $this->html_actions = $render->fetch('finder/view/actions.html');
    }
    public function toinput($params)
    {
        $html = null;
        $this->_toinput($params['from'], $ret, $params['name']);
        foreach ((array) $ret as $k => $v) {
            $html .= '<input type="hidden" name="'.$k.'" value="'.$v."\" />\n";
        }

        return $html;
    }
    public function _toinput($data, &$ret, $path = null)
    {
        foreach ((array) $data as $k => $v) {
            $d = $path ? $path.'['.$k.']' : $k;
            if (is_array($v)) {
                $this->_toinput($v, $ret, $d);
            } else {
                $ret[$d] = $v;
            }
        }
    }
    public function createView()
    {
        $page = $_GET['page'] ? $_GET['page'] : 1;
        $allCols = $this->all_columns();
        $modifiers = array();
        $filter_builder = new desktop_finder_builder_filter_render();
        //$fb_return_data = $filter_builder->main($this->object->table_name() , $this->app, $filter, $this->controller, true);
        $type_modifier = array();
        $key_modifier = array();
        $object_modifier = array();
        $modifier_object = new modifiers();
        $tmparr_columns = array();
        foreach ($this->columns as $col) {
            if (isset($allCols[$col])) {
                $colArray[$col] = &$allCols[$col];
                if (method_exists($this->object, 'modifier_'.$col)) {
                    $key_modifier[$col] = 'modifier_'.$col;
                } elseif (is_string($colArray[$col]['type'])) {
                    if (substr($colArray[$col]['type'], 0, 6) == 'table:') {
                        $object_modifier[$colArray[$col]['type']] = array();
                    } elseif (method_exists($modifier_object, $colArray[$col]['type'])) {
                        $type_modifier[$colArray[$col]['type']] = array();
                    }
                }
                if (isset($allCols[$col]['sql'])) {
                    $sql[] = $allCols[$col]['sql'].' as '.$col;
                } elseif ($col == '_tag_') {
                    $sql[] = $dbschema['idColumn'].' as _tag_';
                } else {
                    $sql[] = '`'.$col.'`';
                }
                $label = $colArray[$col]['label'];
                $orderby_sign = '<i class="fa fa-sort text-default"></i>';
                if ($this->orderBy == $col) {
                    if ($this->orderType == 'desc') {
                        $orderby_sign = '<i class="fa fa-sort-desc"></i>';
                    } else {
                        $orderby_sign = '<i class="fa fa-sort-asc"></i>';
                    }
                } elseif (!isset($allCols[$col]['orderby']) || $allCols[$col]['orderby'] !== true) {
                    $orderby_sign = '';
                } elseif (strpos($col, 'column_') === false) {
                } elseif (strpos($col, 'column_') !== false && $allCols[$col]['order_field']) {
                    if ($this->orderBy == $allCols[$col]['order_field']) {
                        if ($this->orderType == 'desc') {
                            $orderby_sign = '<i class="fa fa-sort-desc"></i>';
                        } else {
                            $orderby_sign = '<i class="fa fa-sort-asc"></i>';
                        }
                    }
                    $col = $allCols[$col]['order_field'];
                } else {
                    $orderby_sign = '';
                }
                if ($orderby_sign != '') {
                    $column_th_html_classname = 'order-able';
                } else {
                    $column_th_html_classname = '';
                }
                if ($column_th_html_classname) {
                    $column_th_html .= <<<EOF
    <th data-key="{$col}" data-order="{$this->orderType}" class="$column_th_html_classname">
        {$label}&nbsp;{$orderby_sign}
    </th>
EOF;
                } else {
                    $column_th_html .= <<<EOF
    <th data-key="{$col}">
        {$label}&nbsp;{$orderby_sign}
    </th>
EOF;
                }

                if (false && $fcol = $fb_return_data['filter_cols'][$col]) {
                    $filter_column_html .= <<<EOF
        <th>
            {$fcol['addon']}{$fcol['inputer']}
        </th>
EOF;
                }
            }
        }
        foreach ((array) $this->service_object as $k => $object) {
            if ($object->addon_cols) {
                $object->col_prefix = '_'.$k.'_';
                foreach (explode(',', $object->addon_cols) as $col) {
                    $sql[] = $col.' as '.$object->col_prefix.$col;
                }
            }
        }
        $sql = (array) $sql;
        if (!isset($colArray[$this->dbschema['idColumn']])) {
            array_unshift($sql, $this->dbschema['idColumn']);
        }
        if ($this->params === -1) {
            $list = array();
        } else {
            $this->object->filter_use_like = true;
            $count_method = $this->object_method['count'];
            // 如果有view_tab的情况下,则直接使用计算顶部view_tab的addon时记下的总数，减少同一finder两次进行count计算，增加性能
            $item_count = $this->current_view_count ? $this->current_view_count : $this->object->$count_method($this->params);
            $total_pages = ceil($item_count / $this->pagelimit);
            if ($page < 0 || ($page > 1 && $page > $total_pages)) {
                $page = 1;
            }
            $getlist_method = $this->object_method['getlist'];
            $order = $this->orderBy ? $this->orderBy.' '.$this->orderType : '';
            if ($this->orderBy) {
                if (in_array($this->orderBy, $this->object->metaColumn)) {
                    //meta排序暂时不做修改
                } else {
                    list(, $obj_name, $fkey) = explode(':', $this->object->schema['columns'][$this->orderBy]['type']);
                    if ($obj_name) {
                        if ($p = strpos($obj_name, '@')) {
                            $app_id = substr($obj_name, $p + 1);
                            $obj_name = substr($obj_name, 0, $p);
                            $o = app::get($app_id)->model($obj_name);
                        } else {
                            $o = $this->object->app->model($obj_name);
                        }
                        $o_idColumn = $o->getList($o->idColumn, array(), 0, -1, $o->schema['textColumn'].' '.$this->orderType);
                        foreach ($o_idColumn as $o_k => $o_v) {
                            $filed = $filed.",'".$o_v[$o->idColumn]."'";
                        }
                        $order = ' FIELD('.$this->orderBy.$filed.')';
                    }
                }
            }

            $list = $this->object->$getlist_method(implode(',', $sql), $this->params, ($page - 1) * $this->pagelimit, $this->pagelimit, $order);
            /*当有row_style 定义时，Finder多取一次数据*/
            if ($this->row_style_func) {
                $abs_list = $this->object->$getlist_method('*', $this->params, ($page - 1) * $this->pagelimit, $this->pagelimit, $order);
                $abs_list = utils::array_change_key($abs_list, $this->dbschema['idColumn']);
                foreach ($list as $k => $item) {
                    $list[$k]['@row'] = $abs_list[$item[$this->dbschema['idColumn']]];
                }
            }

            $body = $this->item_list_body($page, $list, $colArray, $key_modifier, $object_modifier, $type_modifier);
            $count = count($list);
            $total_pages = ceil($item_count / max($count, $this->pagelimit));
            $this->pager_info = array(
                'current' => $page,
                'list' => $count,
                'count' => $item_count,
                'total' => $total_pages ? $total_pages : 1,
            );
            $this->object->filter_use_like = false;
        }
        if ($this->detail_url) {
            $detail_th_html = '<th class="text-center">'.'查看'.'</th>';
            $filter_td_html = '<td></td>';
        }

        $render = $this->render;
        $render->pagedata['detail_th_html'] = $detail_th_html;
        $render->pagedata['column_th_html'] = $column_th_html;
        $render->pagedata['pinfo'] = $this->pager_info;
        $render->pagedata['body'] = $body;
        $render->pagedata['filter_td_html'] = $filter_td_html;
        $render->pagedata['filter_column_html'] = $filter_column_html;
        $render->pagedata['use_buildin_selectrow'] = $this->use_buildin_selectrow;
    }
    public function &item_list_body(&$page, &$list, &$colArray, &$key_modifier, &$object_modifier, &$type_modifier, $ident = 'col')
    {
        $body = array();

        if (!$list) {
            return '';
        }
        if (is_array($this->detail_pages)) {
            $default_detail = '&finderview='.key($this->detail_pages);
        }
        foreach ($list as $i => $row) {
            $row_style = array();
            if ($this->row_style_func) {
                foreach ($this->row_style_func as $object) {
                    $row_style[] = $object->row_style($row);
                }
            }
            $zebra_class = $i % 2 ? 'even' : 'odd';

            $id = htmlspecialchars($row[$this->dbschema['idColumn']]);
            $body[] = '<tr class="'.$zebra_class.' '.implode(';', $row_style).'" item-id="'.$id.'">';
            $tag = $this->has_tag ? (' tags="'.htmlspecialchars($row['_tags']).'"') : '';

            if ($this->use_buildin_selectrow) {
                $select_name = $this->dbschema['idColumn'].'[]';
                if ($this->selectrow_type == 'checkbox') {
                    $body[] = <<<HTML
                    <td>
                        <label class="control-label">
                                <input type="checkbox" name="$select_name"  value="$id">
                        </label>
                    </td>
HTML;
                } elseif ($this->selectrow_type == 'radio') {
                    $body[] = <<<HTML
                    <td>
                        <label class="control-label">
                            <input type="radio"   name="$select_name"  value="$id">
                        </label>
                    </td>
HTML;
                }
            }
            if ($this->detail_url) {
                if ($this->base_query_string) {
                    $this->base_query_string = '&'.$this->base_query_string;
                }

                $body[] = <<<EOF
    <td class="text-center" style="cursor:pointer;">
    <i class="fa text-default fa-plus-square font-grey-gallery" data-detail="{$this->detail_url}&id={$id}{$default_detail}{$this->base_query_string}"></i>
</td>
EOF;
            }
            //$funcs = $this->func_columns();
            foreach ((array) $colArray as $k => $col) {
                $body[] = '<td key="'.$k.'" '.($col['editable'] ? 'class="editable"' : '').'>';
                if ($col['type'] == 'func') {
                    $row['idColumn'] = $this->dbschema['idColumn'];
                    $row['app_id'] = $row['app_id'] ? $row['app_id'] : $this->app->app_id;
                    $row['tag_type'] = $row['tag_type'] ? $row['tag_type'] : $this->short_object_name;
                    $body[] = $a = $col['ref'][0]->{$col['ref'][1]}($row);
                } elseif (isset($key_modifier[$k])) {
                    $this->object->pkvalue = $row[$this->dbschema['idColumn']];
                    $body[] = $this->object->{$key_modifier[$k]}($row[$k], $row);
                } elseif (is_array($col['type']) && !is_null($row[$k])) {
                    $body[] = &$col['type'][$row[$k]];
                } elseif (isset($object_modifier[$col['type']])) {
                    $object_modifier[$col['type']][$row[$k]] = $row[$k];
                    $body[] = &$object_modifier[$col['type']][$row[$k]];
                } elseif (isset($type_modifier[$col['type']])) {
                    $type_modifier[$col['type']][$row[$k]] = $row[$k];
                    $body[] = &$type_modifier[$col['type']][$row[$k]];
                } else {
                    $body[] = $row[$k];
                }
                $body[] = '</td>';
            }
            $body[] = '</tr>';
        }

        if ($type_modifier) {
            $type_modifier_object = new modifiers();
            foreach ($type_modifier as $type => $val) {
                if ($val) {
                    $type_modifier_object->$type($val);
                }
            }
        }
        foreach ($object_modifier as $target => $val) {
            if ($val) {
                list(, $obj_name, $fkey) = explode(':', $target);
                if ($p = strpos($obj_name, '@')) {
                    $app_id = substr($obj_name, $p + 1);
                    $obj_name = substr($obj_name, 0, $p);
                    $o = app::get($app_id)->model($obj_name);
                } else {
                    $o = $this->object->app->model($obj_name);
                }
                if (!$fkey) {
                    $fkey = $o->textColumn;
                }
                $rows = $o->getList($o->idColumn.','.$fkey, array(
                    $o->idColumn => $val,
                ));
                foreach ($rows as $r) {
                    $object_modifier[$target][$r[$o->idColumn]] = $r[$fkey];
                }
                $app_id = null;
            }
        }
        $body = implode('', $body);

        return $body;
    }
}
