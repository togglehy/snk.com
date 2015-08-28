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



class desktop_finder_builder_tag extends desktop_finder_builder_prototype{

    function main(){
        $render = app::get('desktop')->render();
        $tagctl = app::get('desktop')->model('tag');
        $tag_rel = app::get('desktop')->model('tag_rel');
        $tags = $tagctl->getList('tag_id,tag_name,tag_abbr,tag_bgcolor,tag_fgcolor',
        array('tag_type'=>$this->object->table_name(),
            'app_id'=>$this->app->app_id
        ));
        $filter['tag_type'] = $this->object->table_name();
        $rel_mdl = $this->app->model($filter['tag_type']);
        $filter['app_id'] = $this->app->app_id;
        $filter['rel_id|in'] = $_POST[$rel_mdl->idColumn];
        $filter[$this->dbschema['idColumn']] = $_POST['items'];
        $rel_tag_list = $tag_rel->getList('*',$filter);
        if($rel_tag_list){
            foreach($rel_tag_list as $k=>$v){
                $tmp[$v['rel_id']][] = $v['tag_id'];
                $used_tag[$v['tag_id']] = 1;
            }
        }
        $i=0;
        if(isset($tmp) && is_array($tmp)){
            foreach($tmp as $rel_id=>$rel_tags){//计算标签的交集
                if($i++==0){
                    $intersect = $rel_tags;
                }else{
                    $intersect = array_intersect($intersect,$rel_tags);

                }
                if(!$intersect) break;
            }
        }
        $filter = $_POST;
        $count_method = $this->object_method['count'];
        $render->pagedata['count'] = $this->object->$count_method($filter);
        $render->pagedata['tags'] = $tags;
        $render->pagedata['filter'] = serialize($filter);
        $render->pagedata['object_name'] = $this->object_name;
        $render->pagedata['used_tag'] = array_keys((array)$used_tag);
        $render->pagedata['intersect'] = (array)$intersect;
        $render->pagedata['url'] = $this->url;

        echo $render->fetch('finder/tagsetter.html');
    }

}
