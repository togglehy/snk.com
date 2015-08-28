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


class content_ctl_admin_node extends site_admin_controller {
    
    public function __construct($app) {
        parent::__construct($app);
    }
    public function index() {
        $node_list = vmc::singleton('content_article_node')->get_listmaps();
        if (is_array($node_list)) {
            $obj = app::get('site')->router();
            foreach ($node_list as & $row) {
                    $row['url'] = $obj->gen_url(array(
                        'app' => 'content',
                        'ctl' => 'site_node',
                        'act' => 'index',
                        'arg0' => $row['node_id']
                    ));

            }
        }
        $this->pagedata['list'] = $node_list;
        $this->pagedata['tree_number'] = (is_array($this->pagedata['list'])) ? count($this->pagedata['list']) : 0;
        $this->page("admin/node/index.html");
    } //End Function
    /*
     * 添加节点
    */
    public function add() {
        $parent_id = $this->_request->get_get('parent_id');
        $homepage = $this->_request->get_get('node');
        $this->pagedata['node'] = array(
            'parent_id' => $parent_id,
            'ordernum' => 0
        );
        if ($homepage) $this->pagedata['node']['homepage'] = $homepage['homepage'];
        $selectmaps = vmc::singleton('content_article_node')->get_selectmaps();
        array_unshift($selectmaps, array(
            'node_id' => 0,
            'step' => 1,
            'node_name' => '---无---'
        ));
        $this->pagedata['selectmaps'] = $selectmaps;
        $this->display("admin/node/edit.html");
    } //End Function
    /*
     * 编辑节点
    */
    public function edit() {
        $node_id = $this->_request->get_get('node_id');
        if (empty($node_id)) $this->splash('error', 'index.php?app=content&ctl=admin_node', '错误请求');
        $this->pagedata['node'] = app::get('content')->model('article_nodes')->get_by_id($node_id);
        if (empty($this->pagedata['node'])) $this->splash('error', 'index.php?app=content&ctl=admin_node', '错误请求');
        $selectmaps = vmc::singleton('content_article_node')->get_selectmaps();
        array_unshift($selectmaps, array(
            'node_id' => 0,
            'step' => 1,
            'node_name' => '---无---'
        ));
        $this->pagedata['selectmaps'] = $selectmaps;
        $this->display("admin/node/edit.html");
    } //End Function
    /*
     * 删除节点
    */
    public function remove() {
        $this->begin('index.php?app=content&ctl=admin_node&act=index');
        $node_id = $this->_request->get_get('node_id');
        if (empty($node_id)) $this->end(false, '错误请求');
        if (app::get('content')->model('article_nodes')->delete(array(
            'node_id' => $node_id
        ))) {
            $services = vmc::serviceList('content_article_node');
            foreach ($services AS $service) {
                if ($service instanceof content_interface_node) {
                    $service->remove($node_id);
                }
            }
            $this->end(true, '删除成功');
        } else {
            $this->end(false, '存在子栏目，不能被删除');
        }
    } //End Function
    /*
     * 发布
    */
    public function publish() {
        $this->begin('index.php?app=content&ctl=admin_node&act=index');
        $node_id = $this->_request->get_get('node_id');
        if (empty($node_id)) $this->end(false, '错误请求');
        $pub = ($this->_request->get_get('pub') == 'true') ? true : false;
        if (app::get('content')->model('article_nodes')->publish($pub, array(
            'node_id' => $node_id
        ))) {
            $this->end(true, ($pub ? '发布' : '取消发布') . '成功');
        } else {
            $this->end(false, ($pub ? '发布' : '取消发布') . '失败！请查看父栏目是否已发布');
        }
    } //End Function
    /*
     * 保存添加
    */
    public function save() {
        $this->begin('index.php?app=content&ctl=admin_node&act=index');
        $post = $this->_request->get_post('node');
        $node_id = $this->_request->get_post('node_id');
        if (empty($post)) $this->end(false, '错误请求');
        if ($post['parent_id']) { //存在父类目时，查看父类目是否启用
            $aInfo = vmc::singleton("content_article_node")->get_node($post['parent_id']);
            if ($aInfo['ifpub'] == 'false' && $post['ifpub']) {
                if ($post['ifpub'] != $aInfo['ifpub']) {
                    $post['ifpub'] = $aInfo['ifpub'];
                    $msg = '父栏目未发布！';
                }
            }
        }
        $post['uptime'] = time();
        $post['homepage'] = 'true';
        if ($node_id > 0) {
            $res = app::get('content')->model('article_nodes')->update($post, array(
                'node_id' => $node_id
            ));
            if ($res) {
                $services = vmc::serviceList('content_article_node');
                foreach ($services AS $service) {
                    if ($service instanceof content_interface_node) {
                        $service->update($node_id, $post);
                    }
                }
                $this->end(true, '保存成功!' . $msg);
            } else {
                $this->end(false, '保存失败!' . $msg);
            }
        } else {
            $res = app::get('content')->model('article_nodes')->insert($post);
            if ($res) {
                $services = vmc::serviceList('content_article_node');
                foreach ($services AS $service) {
                    if ($service instanceof content_interface_node) {
                        $service->insert($post);
                    }
                }
                $this->end(true, '添加成功!' . $msg);
            } else {
                $this->end(false, '添加失败!' . $msg);
            }
        }
    } //End Function
    function update() {
        $this->begin('index.php?app=content&ctl=admin_node&act=index');
        $tmp = $_POST['ordernum'];
        is_array($tmp) or $tmp = array();
        $flag = true;
        foreach ($tmp as $key => $val) {
            $filter = array(
                'ordernum' => $val,
                'node_id' => $key
            );
            $flag = $this->app->model('article_nodes')->save($filter);
            if (!$flag) $this->end(false, '修改失败!' . $msg);
        }
        $this->end(true, '修改成功!' . $msg);
    }
} //End Class
