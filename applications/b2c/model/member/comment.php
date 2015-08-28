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


class b2c_mdl_member_comment extends dbeav_model
{

    public $has_many = array(
        'images' => 'image_attach@image:contrast:comment_id^target_id',
        'reply' => 'member_comment:contrast:comment_id^for_comment_id',
    );
    public $has_one = array(
        'mark' =>'goods_mark@b2c:contrast:comment_id^comment_id'
    );

    public $subSdf = array(
        'delete' => array(
            'images' => array(
                '*',
            ) ,
            'mark' => array(
                '*',
            ),
            'reply'=>array(
                '*'
            )
        ),
    );

    public function apply_id($type = 'comment'){
        $sign = (($type == 'comment') ? '8' : '9');
        $tb = $this->table_name(1);
        do{
            $microtime = utils::microtime();
            mt_srand($microtime);
            $i = substr(mt_rand() , -3);
            $comment_id =  $sign . (date('y')+date('m')+date('d')).date('His') . $i;
            $row = $this->db->selectrow('SELECT bill_id from '.$tb.' where bill_id ='.$bill_id);
        }while($row);

        return $comment_id;
    }

    public function save(&$sdf, $mustUpdate = null, $mustInsert = false){
        if(!$sdf['comment_id']){
            $sdf['comment_id'] = $this->apply_id($sdf['comment_type']?$sdf['comment_type']:'comment');
        }
        $is_save = parent::save($sdf, $mustUpdate,$mustInsert);
        return $is_save;
    }

    /**
     * 获得分组详细评价列表
     */
    public function groupList($cols='*', $filter=array(), $offset=0, $limit=-1, $orderType=null , $group_by = 'product_id'){

        $orderType = ' createtime DESC'; //fixOrder
        $list = parent::getList($cols='*', $filter, $offset, $limit, $orderType);
        $list = utils::array_change_key($list,$group_by,true);
        foreach ($list as $group => &$items) {
            $items = utils::array_change_key($items,'comment_id');
            foreach ($items as $key => &$comment) {
                if(!empty($comment['for_comment_id'])){
                    $items[$comment['for_comment_id']]['reply'][$comment['comment_id']] = $comment;
                    $items[$comment['for_comment_id']]['lastreply'] = $items[$comment['for_comment_id']]['lastreply']?$items[$comment['for_comment_id']]['lastreply']:$comment['createtime'];
                    unset($items[$key]);
                    continue;
                }
                $comment['mark']  = app::get('b2c')->model('goods_mark')->getRow('mark_star',array('comment_id'=>$comment['comment_id']));
                $comment['images'] = app::get('image')->model('image_attach')->getList('image_id',array('target_id'=>$comment['comment_id']));

            }
        }
        return $list;
    }



}
