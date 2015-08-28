<?php
class wechat_api {
    public function __construct($app) {
        $this->stage = vmc::singleton('wechat_stage');
    }
    public function api($params) {
        //签名验证，消息有效性验证
        $eid = $params['eid'];
        if ($this->doget($eid)) {
            echo $_GET["echostr"];
        }
        $post_xml = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($post_xml)) {
            $this->dopost($post_xml);
        } else {
            echo "";
        }
    }
    /**
     * 处理微信消息有效验证
     */
    public function doget($eid) {
        //获取到token
        $token = $this->stage->get_token($eid);
        //验证
        if ($this->stage->check_sign($_GET["signature"], $_GET["timestamp"], $_GET["nonce"], $token)) {
            return true;
        } else {
            logger::error('微信消息有效性验证失败!'.var_export($_GET,1));
            return false;
        }
    }
    public function dopost($post_xml) {
        $post_arr = vmc::singleton('mobile_utility_xml')->xml2array($post_xml);
        $post_data = $post_arr['xml'];
        //公众账号ID获取
        $wechat_id = $post_data['ToUserName'];
        $bind = app::get('wechat')->model('bind')->getRow('*', array(
            'wechat_id' => $wechat_id,
            'status' => 'active'
        ));
        //需要解密消息
        if($_GET['encrypt_type'] == 'aes'){
            $obj_crypt = new wechat_crypt($bind['token'],$bind['aeskey'],$bind['appid']);
            if($obj_crypt->decryptMsg($_GET['msg_signature'],$_GET["timestamp"],$_GET["nonce"],$post_xml,$decode_post_xml)===0){
                $post_arr = vmc::singleton('mobile_utility_xml')->xml2array($decode_post_xml);
                $post_data = $post_arr['xml'];
            }else{
                logger::error('微信消息解密失败!'.$post_xml.var_export($_GET,1).var_export($bind,1));
                return;
            }
        }
        if (!empty($bind)) {
            $post_data['bind_id'] = $bind['id'];
            $post_data['eid'] = $bind['eid'];
        } else {
            return;
        }
	//logger::alert(var_export($post_data,1));
        switch ($post_data['MsgType']) {
            case 'event':
                /**
                 * subscribe(订阅)、unsubscribe(取消订阅)
                 * scan 带参数二维码事件
                 * location 上报地理位置事件
                 * click 自定义菜单事件
                 * view  点击菜单跳转链接时的事件推送
                 *
                 */
                $this->stage->event_reply($post_data);
            break;
            default:
                $this->stage->normal_reply($post_data);
        }
    }


    // 微信告警通知接口
    public function alert() {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postArray = vmc::singleton('site_utility_xml')->xml2array($postStr);
        $postData = $postArray['xml'];
        $insertData = array(
            'appid' => $postData['AppId'],
            'errortype' => $postData['ErrorType'],
            'description' => $postData['Description'],
            'alarmcontent' => $postData['AlarmContent'],
            'timestamp' => $postData['TimeStamp'],
        );
        app::get('wechat')->model('alert')->save($insertData);
        // 微信需要返回页面编码也未gbk的success
        echo "<meta charset='GBK'>";
        $rs = 'success';
        echo iconv('UTF-8', 'GBK//TRANSLIT', $rs);
        exit;
        /*告警通知数据格式
         $postData=array(
            'AppId'=>'wxf8b4f85f3a794e77',
            'ErrorType'=>'1001',
            'Description'=>'错误描述',
            'AlarmContent'=>'错误详情',
            'TimeStamp'=>'1393860740',
            'AppSignature'=>'f8164781a303f4d5a944a2dfc68411a8c7e4fbea',
            'SignMethod'=>'sha1'
        );*/
    }
}
