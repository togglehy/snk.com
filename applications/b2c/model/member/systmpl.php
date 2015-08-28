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


class b2c_mdl_member_systmpl extends dbeav_model
{
    public function __construct($app)
    {
        parent::__construct($app);
    }
    public function fetch($tplname, $data = null)
    {
        $aTmpl = explode(':', $tplname);
        $render = $this->app->render();
        foreach ($data as $key => $val) {
            $render->pagedata[$key] = $val;
        }
        
        if (count($aTmpl) != 1) {
            $aRet = $this->getList('*', array(
                'active' => 'true',
                'tmpl_name' => $tplname,
            ));
            if ($aRet) {
                return $render->fetch('messenger:'.$tplname);
            }
            $aTp = explode('/', $aTmpl[1]);
            $aLast = explode('_', $aTp[0]);
            $app_id = $aLast[0];
            $obj_app_id = vmc::service('b2c_messenger_tpl_appid');
            if ($obj_app_id && method_exists($obj_app_id, 'get_app_id')) {
                $obj_app_id->get_app_id($aTp[1], $app_id);
            }
            $aLast[0] = 'admin';
            $dir = implode('/', $aLast);

            return $render->fetch($dir.'/'.$aTp[1].'.html', $app_id);
        } else {
            $aRet = $this->getList('*', array(
                'active' => 'true',
                'tmpl_name' => '/'.$tplname,
            ));
            if ($aRet) {
                return $render->fetch('messenger:'.'/'.$tplname);
            }

            return $render->fetch($tplname.'.html');
        }
    }

    public function _file($name)
    {
        if ($p = strpos($name, ':')) {
            $type = substr($name, 0, $p);
            $name = substr($name, $p + 1);
            if ($type == 'messenger') {
                $aTmp = explode('/', $name);
                $tmpl = explode('_', $aTmp[0]);
                $app_id = $tmpl[0];
                $tmpl[0] = 'view/admin';
                $html_dir = implode('/', $tmpl).'/'.$aTmp[1];

                $obj_app_id = vmc::service('b2c_messenger_tpl_appid');
                if ($obj_app_id && method_exists($obj_app_id, 'get_app_id')) {
                    $obj_app_id->get_app_id($aTmp[1], $app_id);
                }
                $path = APP_DIR.'/'.$app_id.'/'.$html_dir.'.html';
                return $path;
            }
        } else {
            return APP_DIR.'/b2c/view/'.$name.'.html';
        }
    }
    public function get($name)
    {
        $tmpl_row = $this->getRow('*', array(
            'active' => 'true',
            'tmpl_name' => $name,
        ));
        $filemtime = filemtime($this->_file($name));
        if ($tmpl_row && $tmpl_row['edittime'] >= $filemtime) {
            return $tmpl_row['content'];
        } else {
            $body = file_get_contents($this->_file($name));
            $this->set($name, $body);

            return $body;
        }
    }

    public function tpl_src($matches)
    {
        return '<{'.html_entity_decode($matches[1]).'}>';
    }
    public function set($name, $body)
    {
        //file_put_contents($this->_file($name),$body);
        $body = str_replace(array(
            '&lt;{',
            '}&gt;',
        ), array(
            '<{',
            '}>',
        ), $body);
        $body = preg_replace_callback('/<{(.+?)}>/', array(&$this,
            'tpl_src',
        ), $body);
        $sdf['tmpl_name'] = $name;
        $sdf['edittime'] = time();
        $sdf['active'] = 'true';
        $sdf['content'] = $body;
        $rs = $this->save($sdf);

        return $rs;
    }
}
