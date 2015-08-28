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


class b2c_ctl_admin_member_messenger extends desktop_controller
{
     public function __construct($app)
     {
         parent::__construct($app);
         $this->stage = vmc::singleton('b2c_messenger_stage');
     }

    public function index()
    {
        $this->pagedata['sender_list'] = $this->stage->get_sender_list();
        $this->pagedata['actions'] = $this->stage->get_actions();
        foreach ($this->pagedata['sender_list'] as $k => $sender) {
            foreach ($this->pagedata['actions'] as $j => &$action) {
                $action[$k]['disabled'] = $this->app->getConf('messenger_disabled_'.$k.'_'.$j);
                $action['name_alias'] = $this->app->getConf('messenger_'.$j.'_alias');
            }
        }
        $this->page('admin/messenger/index.html');
    }

    public function edtmpl($sender_class, $action_name)
    {
        $sender = $this->stage->init_sender($sender_class);
        $sender = (array) $sender;
        if ($this->pagedata['hasTitle'] = $sender['hasTitle']) {
            $this->pagedata['title'] = $this->app->getConf('messenger.title.'.$sender_class.'.'.$action_name, app::get('site')->getConf('site_name'));
        }
        $this->pagedata['body'] = $this->stage->get_tmpl($sender_class, $action_name);
        $this->pagedata['type'] = $sender['isHtml'] ? 'html' : 'textarea';
        $this->pagedata['sender_class'] = $sender_class;
        $this->pagedata['action_name'] = $action_name;
        $actions = $this->stage->get_actions();
        $this->pagedata['action_label'] = $actions[$action_name]['label'];
        $this->pagedata['env_list'] = $actions[$action_name]['env_list'];
        $this->pagedata['sender_name'] = $sender['name'];
        $this->display('admin/messenger/edtmpl.html');
    }

    public function saveTmpl()
    {
        $this->begin();
        $ret = $this->stage->save_tmpl($_POST['sender_class'], $_POST['action_name'], array(
            'content' => htmlspecialchars_decode($_POST['content']),
            'title' => $_POST['title'],
        ));
        if ($ret) {
            $this->end(true, ('操作成功'));
        } else {
            $this->end(false, ('操作失败'));
        }
    }
    public function recovery_tmpl()
    {
        $this->begin();
        $tmpl_key = 'messenger:'.$_POST['sender_class'].'/'.$_POST['action_name'];
        $flag = $this->app->model('member_systmpl')->update(array('edittime' => 0), array('tmpl_name' => $tmpl_key));
        if ($flag) {
            $this->end(true, ('操作成功'));
        } else {
            $this->end(false, ('操作失败'));
        }
    }

    public function disabled($sender_class, $action_name)
    {
        $this->begin();

        if ($this->app->setConf('messenger_disabled_'.$sender_class.'_'.$action_name, true)) {
            $this->end(true, ('禁用成功'));
        } else {
            $this->end(false, ('禁用失败'));
        }
    }
    public function enabled($sender_class, $action_name)
    {
        $this->begin();
        if ($this->app->setConf('messenger_disabled_'.$sender_class.'_'.$action_name, false)) {
            $this->end(true, ('启用成功'));
        } else {
            $this->end(false, ('启用失败'));
        }
    }
    public function update_actname_alias()
    {
        $this->begin();
        if ($this->app->setConf('messenger_'.$_POST['action_name'].'_alias', $_POST['alias'])) {
            $this->end(true, ('更新失败'));
        } else {
            $this->end(false, ('更新失败'));
        }
    }
}
