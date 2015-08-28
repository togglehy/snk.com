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


class desktop_finder_builder_settag extends desktop_finder_builder_prototype
{
    public function main()
    {
        $tagctl = app::get('desktop')->model('tag');
        $tag_rel = app::get('desktop')->model('tag_rel');
        $tags = $_POST['tag'];
        if ($_POST['filter']) {
            $obj = $this->object;
            $schema = $obj->get_schema();
            $idColumn = $schema['idColumn'];
            $filter = unserialize($_POST['filter']);
            $rows = $obj->getList($idColumn, $filter, 0, -1);
            foreach ($rows as $value) {
                $pkey[] = $value[$idColumn];
            }
        }
        $pkey = (array) $pkey;

        foreach ($tags as $k => $tag) {
            $stat = intval($tag['stat']);

            switch ($stat) {
                case 0: //设置标签
                    $data = array();
                    $data['tag_type'] = $this->object->table_name();
                    $data['tag_name'] = $tag['name'];
                    $data['app_id'] = $this->app->app_id;
                    if(!isset($tag['tag_id'])){
                        $tagctl->save($data);
                    }else{
                        $data['tag_id'] = $tag['tag_id'];
                    }
                    if ($data['tag_id']) {
                        foreach ($pkey as $id) {
                            $data_rel = array(
                                'tag'=>array('tag_id'=>$data['tag_id']),
                                'app_id'=>$this->app->app_id,
                                'tag_type'=>$this->object->table_name()
                            );
                            $data_rel['rel_id'] = $id;
                            $tag_rel->save($data_rel);
                        }
                    }
                continue;
                case 1://取消标签
                    $tag_rel->delete(array('tag_id' => $tag['tag_id'], 'rel_id' => $pkey));
                continue;
                default://什么都不做
                continue;
            }
        }

        header('Content-Type:application/json; charset=utf-8');
        echo '{"success":"'.'标签设置成功'.'"}';
    }

    public function __destruct()
    {
        #↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓记录管理员操作日志@lujy↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        if ($obj_operatorlogs = vmc::service('operatorlog')) {
            if (method_exists($obj_operatorlogs, 'logSetTagInfo')) {
                $obj_operatorlogs->logSetTagInfo();
            }
        }
        #↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑记录管理员操作日志@lujy↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
    }
}
