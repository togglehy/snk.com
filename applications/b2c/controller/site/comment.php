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

class b2c_ctl_site_comment extends b2c_frontpage
{
    public $noCache = true;
    public function __construct(&$app)
    {
        parent::__construct($app);
    }

    public function form($order_id, $product_id, $type = 'comment')
    {
        $this->verify_member();
        $this->member = $this->get_current_member();
        $this->_response->set_header('Cache-Control', 'no-store');
        $mdl_order = app::get('b2c')->model('orders');
        $mdl_mcomment = app::get('b2c')->model('member_comment');
        $exits_comment = $mdl_mcomment->groupList('*', array('order_id' => $order_id, 'comment_type' => $type));

        $order = $mdl_order->dump($order_id, '*', array('items' => array('*')));
        if ($order['member_id'] != $this->member['member_id']) {
            $this->splash('error', '', '非法操作!');
        }
        if ($order['ship_status'] == '0' || $order['status'] == 'dead') {
            $this->splash('error', '', '暂不能评价!');
        }
        $this->pagedata['order'] = $order;
        $this->pagedata['exits_comment'] = $exits_comment;
        $this->pagedata['member_avatar'] = $this->member['avatar'];
        $this->title = '评价#'.$order['order_id'].' 商品';
        $this->set_tmpl('comment_form');
        $this->page('site/comment/form.html');
    }

    public function save($type = 'comment')
    {
        $this->verify_member();
        $this->member = $this->get_current_member();
        $mdl_mcomment = app::get('b2c')->model('member_comment');
        $new_comment_id = $mdl_mcomment->apply_id($type);

        $fipt_idx = -1;
        $max_conf = $this->app->getConf('comment_image_size').'M';
        $max_size = utils::parse_str_size($max_conf); //byte
        if (!$_POST) {
            $this->end(false, '缺少参数！', 'json_response');
        }
        //新评价
        if ($comment_data = $_POST[$type]) {
            /**
             * $word = array($product_id => $content);.
             */
            foreach ($comment_data['word'] as $goods_id => $word) {
                $product_id = key($word);
                $content = current($word);
                $new_comment = array(
                    'comment_id' => $new_comment_id++,
                    'goods_id' => $goods_id,
                    'product_id' => $product_id,
                    'member_id' => $this->member['member_id'],
                    'order_id' => $_POST['order_id'],
                    'author_name' => $this->member['uname'],
                    'createtime' => time(),
                    'content' => htmlspecialchars($content),
                );
                $new_comment['content'] = preg_replace("/\n/is", '<br>', $new_comment['content']);
                if (empty($new_comment['content']) || trim($new_comment['content']) == '') {
                    continue;
                }
                $new_comment['title'] = substr(preg_replace('/\s/','',$new_comment['content']),0,100).'...';
                if ($mark = $comment_data['mark'][$goods_id]) {
                    //评分
                    $new_comment['mark'] = array(
                        'mark_star' => floatval($mark),
                        'goods_id' => $goods_id,
                        'comment_id' => $new_comment['comment_id'],
                    );
                }
                //晒一晒处理
                $image_upload_arr = array(
                    'name' => $_FILES[$type]['name'][$product_id],
                    'tmp_name' => $_FILES[$type]['tmp_name'][$product_id],
                    'error' => $_FILES[$type]['error'][$product_id],
                    'size' => $_FILES[$type]['size'][$product_id],
                    'type' => $_FILES[$type]['type'][$product_id],
                );
                $ready_upload = array();
                $success_upload_images = array();
                //图片上传验证
                foreach ($image_upload_arr['tmp_name'] as $i => $v) {
                    $fipt_idx++;
                    if (!isset($v) || empty($v)) {
                        continue;
                    }
                    $size = $image_upload_arr['size'][$i];
                    if (isset($image_upload_arr['error'][$i]) && !empty($image_upload_arr['error'][$i]) && $image_upload_arr['error'][$i] > 0) {
                        $this->_upload_error($fipt_idx, '文件上传失败!'.$image_upload_arr['error'][$i]);
                    }
                    if ($size > $max_size) {
                        $this->_upload_error($fipt_idx, '文件大小不能超过'.$max_conf);
                    }
                    list($w, $h, $t) = getimagesize($v);
                    if (!in_array($t, array(1, 2, 3, 6))) {
                        //1 = GIF,2 = JPG，3 = PNG,6 = BMP
                        $this->_upload_error($fipt_idx, '文件类型错误');
                    }
                    $ready_upload[] = array(
                        'tmp' => $v,
                        'name' => $image_upload_arr['name'][$i],
                    );
                }
                $mdl_image = app::get('image')->model('image');
                foreach ($ready_upload as $k => $item) {
                    $image_id = $mdl_image->store($item['tmp'], null, null, $item['name']);//保存图片
                    $mdl_image->rebuild($image_id, array('L', 'XS'));//缩略图片
                    $new_comment['images'][] = array(
                         'target_type' => 'comment',
                         'image_id' => $image_id,
                         'target_id' => $new_comment['comment_id'],
                     );
                    logger::info('前台评价晒一晒图片上传操作'.'TMP_NAME:'.$item['tmp'].',FILE_NAME:'.$item['name']);
                }

                if (!$mdl_mcomment->save($new_comment)) {
                    $this->_send('error', '提交失败!');
                }else{
                    //商品评价计数
                    vmc::singleton('b2c_openapi_goods',false)->counter(array(
                        'goods_id'=>$goods_id,
                        'comment_count'=>1,
                        'comment_count_sign'=>md5($goods_id.'comment_count'.(1 * 1024))//计数签名
                    ));
                }
            }
        }

        //评价追加、回复
        if (isset($_POST['reply']) && !empty($_POST['reply'])) {
            foreach ($_POST['reply'] as $key => $value) {
                $new_reply = array(
                    'comment_id' =>$new_comment_id++,
                    'for_comment_id' => $key,
                    'order_id' => $_POST['order_id'],
                    'goods_id' => $value['goods_id'],
                    'product_id' => $value['product_id'] ,
                    'member_id' => $this->member['member_id'],
                    'author_name' => $this->member['uname'],
                    'createtime' => time(),
                    'content' => htmlspecialchars($value['content']),
                );
                if (empty($new_reply['content']) || trim($new_reply['content']) == '') {
                    continue;
                }
                $new_reply['title'] = substr(preg_replace('/\s/','',$new_reply['content']),0,100).'...';
                if (!$mdl_mcomment->save($new_reply)) {
                    $this->_send('error', '追加评价失败');
                }else{
                    //更新最后回复时间
                    $mdl_mcomment->update(array('lastreply'=>time()),array('comment_id'=>$key));
                    //商品评价计数
                    vmc::singleton('b2c_openapi_goods',false)->counter(array(
                        'goods_id'=>$value['goods_id'],
                        'comment_count'=>1,
                        'comment_count_sign'=>md5($value['goods_id'].'comment_count'.(1 * 1024))//计数签名
                    ));
                }
            }
        }

        $this->_send('success', '提交成功');
    }

    public function show($goods_id,$page=1){
        $this->_response->set_header('Cache-Control', 'no-store');
        $mdl_mcomment = app::get('b2c')->model('member_comment');
        $limit = 20;
        $filter = array(
            'goods_id'=>$goods_id,
            'display'=>'true'
        );
        $comment_list = $mdl_mcomment->groupList('*',$filter,($page - 1) * $limit, $limit,'','goods_id');
        $count = $mdl_mcomment->count($filter);
        $this->pagedata['comment_list'] = $comment_list[$goods_id];
        $this->pagedata['comment_count'] = $count;
        $this->pagedata['pager'] = array(
            'total' => ceil($count / $limit) ,
            'current' => $page,
            'link' => array(
                'app' => 'b2c',
                'ctl' => 'site_comment',
                'act' => 'show',
                'args' => array(
                    $goods_id,
                    ($token = time()),
                ) ,
            ) ,
            'token' => $token,
        );
        if($this->_request->is_ajax()){
            //商品详情内展示
            $this->display('site/comment/list.html');
        }else{
            //单独展示
            $goods_detail = vmc::singleton('b2c_goods_stage')->detail($goods_id);
            $this->pagedata['goods_detail'] = $goods_detail;
            $this->title = $goods_detail['name'].' 评价\口碑';
            $this->set_tmpl('comment_show');
            $this->page('site/comment/show.html');
        }

    }


    private function _send($result, $msg)
    {
        if ($result == 'success') {
            echo json_encode(array(
                'success' => '成功',
                'data' => $msg,
            ));
        } else {
            echo json_encode(array(
                'error' => $msg,
                'data' => '',
            ));
        }
        exit;
    }
    private function _upload_error($index, $error)
    {
        echo json_encode(array(
            'fipt_idx' => $index,
            'error' => $error,
        ));
        exit;
    }
}
