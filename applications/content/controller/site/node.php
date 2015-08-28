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


class content_ctl_site_node extends site_controller {
    public function index() {
        $id = $this->_request->get_param(0);
        if ($id < 1) {
            vmc::singleton('site_router')->http_status(404); //exit;
        }
        $node = vmc::singleton('content_article_node');
        $info = $node->get_node($id, true);
        if ($info['ifpub'] != 'true') {
            $this->splash('error',null, $info['node_name'].'未发布');
        }
        $runtime_path = $node->get_node_path($id, true);
        $this->get_seo_info($info, $runtime_path);
        $this->set_tmpl('node');
        $this->set_tmpl_file($info['setting']['site_template']);
        $this->page('content_node:' . $info['node_id']);
    }

    private function get_seo_info($aInfo, $aPath) {
        is_array($info) or $info = array();
        is_array($aPath) or $aPath = array();
        //title keywords description
        $title = array();
        $title[] = $aInfo['seo_title'] ? $aInfo['seo_title'] : $aPath[count($aPath) - 1]['title'];
        if (!$aInfo['seo_title']) {
            $title[] = $this->site_name ? $this->site_name : app::get('site')->getConf('site_name');
        }
        $title = array_filter($title);
        $this->title = implode('-', $title);
        $this->description = $aInfo['seo_description'] ? $aInfo['seo_description'] : $this->pagedata['title'];
        if ($aInfo['seo_keywords']) {
            $this->keywords = $aInfo['seo_keywords'];
        } else {
            $keyword = array();
            foreach ($aPath as $row) {
                $keyword[] = $row['title'];
            }
            $this->keywords = implode('-', $keyword);
        }
    }
}
?>
