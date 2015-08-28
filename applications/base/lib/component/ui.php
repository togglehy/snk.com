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


class base_component_ui
{
    public $base_dir = '';
    public $base_url = '';
    public static $inputer = array();
    public static $_ui_id = 0;
    public $_form_path = array();
    private $_imgbundle = array();
    private $_pageid = null;

    public function __construct($controller, $app_specal = null)
    {
        $this->controller = $controller;
        if ($app_specal) {
            $this->app = $app_specal;
        } else {
            $this->app = $controller->app;
        }
    }

    public function img($params)
    {
        return utils::buildTag($params, 'img');
    }

    public function input($params)
    {
        if ($params['params']) {
            $p = $params['params'];
            unset($params['params']);
            $params = array_merge($p, $params);
        }

        if (is_array($params['type'])) {
            $params['options'] = $params['type'];
            $params['type'] = 'select';
        }
        if (!array_key_exists('value', $params) && array_key_exists('default', $params)) {
            $params['value'] = $params['default'];
        }

        if (!$params['id']) {
            $params['id'] = self::new_dom_id();
        }

        if (substr($params['type'], 0, 6) == 'table:') {
            list(, $params['object'], $params['app']) = preg_split('/[:|@]/', $params['type']);
            $params['type'] = 'object';
            if ($params['name'] == 'cat_id') {
                $params['type'] = 'goodscat';

                return $this->input_element('goodscat', $params);
            }
            if ($this->input_element('object_'.$params['type'])) {
                return $this->input_element('object_'.$params['type'], $params);
            } else {
                return $this->input_element('object', $params);
            }
        } elseif ($this->input_element($params['type'])) {
            return $this->input_element($params['type'], $params);
        } else {
            return $this->input_element('default', $params);
        }
    }

    public function input_element($type, $params = false)
    {
        if (!self::$inputer) {
            if (vmc::is_online()) {
                self::$inputer = vmc::servicelist('html_input');
            } else {
                self::$inputer = array('base_view_input' => new base_view_input());
            }
        }

        if ($params === false) {
            foreach (self::$inputer as $inputer) {
                $inputer->app = $this->app;
                if (method_exists($inputer, 'input_'.$type)) {
                    return true;
                }
            }
        } else {
            foreach (self::$inputer as $inputer) {
                $inputer->app = $this->app;
                if (method_exists($inputer, 'input_'.$type)) {
                    $html = $inputer->{'input_'.$type}($params);
                }
            }

            return $html;
        }

        return false;
    }

    public static function new_dom_id()
    {
        return 'el_'.substr(md5(time()), 0, 6).intval(self::$_ui_id++);
    }

    public function button($params)
    {
        $params['class'] = 'btn btn-default '.$params['class'];
        if ($params['icon']) {
            $icon = '<i class="'.$params['icon'].'"></i>';
            unset($params['icon']);
        }
        $app = $params['app'] ? app::get($params['app']) : $this->app;

        if ($params['label']) {
            $label = htmlspecialchars($app->_($params['label']));
            unset($params['label']);
        }

        $type = $params['type'];
        if ($type == 'link') {
            $element = 'a';
            unset($params['link']);
        } else {
            $element = 'button';
            if ($params['href'] && !strpos($params['href'], 'javascript:')) {
                unset($params['href']);
            }
            if ($type != 'submit') {
                $params['type'] = 'button';
            }
        }

        return utils::buildTag($params, $element, 0).$icon.$label.'</'.$element.'>';
    }

    public function getVer($flag = true, $ver = null)
    {
        return $flag ? '?v'.substr(cachemgr::ask_cache_check_version(true), -4) : ($ver ? '?'.$ver : '');
    }

    public function script($params)
    {
        $default = array(
            'type' => 'text/javascript',
            'charset' => 'utf-8',
        );
        if (isset($params['href']) && !empty($params['href'])) {
            $params['src'] = $params['href'];
            unset($params['href']);
        }
        $app = $params['app'] ? app::get($params['app']) : $this->app;
        $file = $app->res_url.'/javascripts/'.$params['src'];
        unset($params['src']);
        $params = count($params) ? $params + $default : $default;
        $ext = '';
        foreach ($params as $k => $v) {
            $ext .= sprintf('%s="%s" ', $k, $v);
        }
        if (isset($params['content']) && $params['content']) {
            return '<script charset="utf-8">'.file_get_contents($file).'</script>'."\n";
        } else {
            $version = $this->getVer();

            return sprintf('<script src="%s" %s></script>', $file.$version, $ext)."\n";
        }
    }

    public function css($params)
    {
        $default = array(
            'rel' => 'stylesheet',
            // 'media' => 'screen, projection',
            'type' => 'text/css',
        );
        if ($params['src']) {
            $params['href'] = $params['src'];
        }
        $app = $params['app'] ? app::get($params['app']) : $this->app;
        $file = $app->res_url.'/stylesheets/'.$params['href'];
        $params = count($params) ? $params + $default : $default;
        unset($params['app']);
        unset($params['src']);
        unset($params['href']);
        foreach ($params as $k => $v) {
            $ext .= sprintf('%s="%s" ', $k, $v);
        }
        $version = $this->getVer();

        return sprintf('<link href="%s" %s/>', $file.$version, $ext)."\n";
    }//End Function


    public function pager($params)
    {
        if (substr($params['link'], 0, 11) == 'javascript:') {
            $tag = 'span';
            $this->pager_attr = 'onclick';
            $params['link'] = substr($params['link'], 11);
        } else {
            $tag = 'a';
            $this->pager_attr = 'href';
        }

        $this->pager_tag = $tag;

        if (!$params['current']) {
            $params['current'] = 1;
        }
        if (!$params['total']) {
            $params['total'] = 1;
        }
        if ($params['total'] < 2) {
            return '';
        }

        if (!$params['nobutton']) {
            if ($params['current'] > 1) {
                $prev = '<'.$tag.' '.$this->pager_attr.'="'.sprintf($params['link'], $params['current'] - 1)
                    .'" class="prev">&laquo;</'.$tag.'>';
            } else {
                $prev = '<span class="prev disabled">&laquo;</span>';
            }

            if ($params['current'] < $params['total']) {
                $next = '<'.$tag.' '.$this->pager_attr.'="'.sprintf($params['link'], $params['current'] + 1)
                    .'" class="next">&raquo;</'.$tag.'>';
            } else {
                $next = '<span class="next disabled">&raquo;</span>';
            }
        }

        $c = $params['current'];
        $t = $params['total'];
        $v = array();
        $l = $params['link'];

        if ($t < 11) {
            $v[] = $this->pager_link(1, $t, $l, $c);
            //123456789
        } else {
            if ($t - $c < 8) {
                $v[] = $this->pager_link(1, 3, $l);
                $v[] = $this->pager_link($t - 8, $t, $l, $c);
                //12..50 51 52 53 54 55 56 57
            } elseif ($c < 10) {
                $v[] = $this->pager_link(1, max($c + 3, 10), $l, $c);
                $v[] = $this->pager_link($t - 1, $t, $l);
                //1234567..55
            } else {
                $v[] = $this->pager_link(1, 3, $l);
                $v[] = $this->pager_link($c - 2, $c + 3, $l, $c);
                $v[] = $this->pager_link($t - 1, $t, $l);
                //123 456 789
            }
        }
        $links = implode('&hellip;', $v);

        return <<<EOF
    <div class="pager">
     <div class="pagernum">
      {$prev}{$links}{$next}
     </div>
    </div>
EOF;
    }

    private function pager_link($from, $to, $l, $c = null)
    {
        for ($i = $from;$i < $to + 1;$i++) {
            if ($c == $i) {
                $r[] = ' <span class="current">'.$i.'</span> ';
            } else {
                $r[] = ' <'.$this->pager_tag.' '.$this->pager_attr.'="'.sprintf($l, $i).'">'.$i.'</'.$this->pager_tag.'> ';
            }
        }

        return implode(' ', $r);
    }

    public function pageid()
    {
        if (is_null($this->_pageid)) {
            $obj = vmc::singleton('base_component_request');
            $key = md5(sprintf('%s_%s_%s_%s', $obj->get_app_name(), $obj->get_ctl_name(), $obj->get_act_name(), serialize($obj->get_params())));
            $this->_pageid = base_convert(strtolower($key), 16, 10);
            $this->_pageid = substr($this->dec2any($this->_pageid), 4, 8);
        }

        return $this->_pageid;
    }//End Function

    private function dec2any($num, $base = 62, $index = false)
    {
        if (!$base) {
            $base = strlen($index);
        } elseif (!$index) {
            $index = substr('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 0, $base);
        }
        $out = '';
        for ($t = floor(log10($num) / log10($base)); $t >= 0; $t--) {
            $a = floor($num / pow($base, $t));
            $out = $out.substr($index, $a, 1);
            $num = $num - ($a * pow($base, $t));
        }

        return $out;
    }

    public function desktoppager($params)
    {

        // if(substr($params['link'],0,11)=='javascript:'){
        //     $tag = 'a';
        //     $this->pager_attr = 'onclick';
        //     $params['link'] = substr($params['link'],11);
        // }else{
            $tag = 'a';
        $this->pager_attr = 'href';
    //    }

        $this->pager_tag = $tag;

        if (!$params['current']) {
            $params['current'] = 1;
        }
        if (!$params['total']) {
            $params['total'] = 1;
        }
        if ($params['total'] < 2) {
            return '';
        }

        if (!$params['nobutton']) {
            if ($params['current'] > 1) {
                $first = '<li class="prev"><'.$tag.' '.$this->pager_attr.'="'.sprintf($params['link'], 1)
                    .'" ><i class="fa fa-angle-double-left"></i>第一页</'.$tag.'></li>';
                $prev = '<li class="prev"><'.$tag.' '.$this->pager_attr.'="'.sprintf($params['link'], $params['current'] - 1)
                    .'" ><i class="fa fa-angle-left"></i>上一页</'.$tag.'></li>';
            } else {
                $first = '<li class="prev disabled"><a href="javascript:;"><i class="fa fa-angle-double-left"></i>第一页</a></li>';
                $prev = '<li class="prev disabled"><a href="javascript:;"><i class="fa fa-angle-left"></i>上一页</a></li>';
            }

            if ($params['current'] < $params['total']) {
                $next = '<li class="next"><'.$tag.' '.$this->pager_attr.'="'.sprintf($params['link'], $params['current'] + 1)
                    .'" class="next">下一页<i class="fa fa-angle-right"></i></'.$tag.'></li>';
                $last = '<li class="next"><'.$tag.' '.$this->pager_attr.'="'.sprintf($params['link'], $params['total'])
                    .'" class="">最后一页<i class="fa fa-angle-double-right"></i></'.$tag.'></li>';
            } else {
                $next = '<li class="next disabled"><a href="javascript:;">下一页<i class="fa fa-angle-right"></i></a></li>';
                $last = '<li class="next disabled"><a class="disabled">最后一页<i class="fa fa-angle-double-right"></i></a></li>';
            }
        }

        return <<<EOF
    <ul class="pagination">
      {$first}
      {$prev}
      <li class="active disabled"><a href="javascript:;">{$params['current']}</a></li>
      {$next}
      {$last}
    </ul>
EOF;
    }
}
