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


class desktop_ctl_email extends desktop_controller {
    function __construct($app) {
        parent::__construct($app);
    }
    function setting() {
        $this->pagedata['inputs'] = $this->get_inputs();
        $this->page('email/config.html');
    }
    function get_inputs() {
        return array(
            'sendway' => array(
                'label' => '发送方式' ,
                'type' => 'hidden',
                'value' =>'smtp',
            ) ,
            'usermail' => array(
                'label' => '发信人邮箱' ,
                'type' => 'email',
                'value' => $this->app->getConf('email.config.usermail')
            ) ,
            'smtpserver' => array(
                'label' => 'smtp服务器地址' ,
                'type' => 'input',
                'value' => $this->app->getConf('email.config.smtpserver')
            ) ,
            'smtpport' => array(
                'label' => 'smtp服务器端口' ,
                'type' => 'number',
                'value' => $this->app->getConf('email.config.smtpport')?$this->app->getConf('email.config.smtpport'):25
            ) ,
            'smtpuname' => array(
                'label' => 'smtp用户名' ,
                'type' => 'input',
                'value' => $this->app->getConf('email.config.smtpuname')
            ) ,
            'smtppasswd' => array(
                'label' => 'smtp密码' ,
                'type' => 'password',
                'value' => $this->app->getConf('email.config.smtppasswd')
            )
        );
    }
    function save_conf() {
        # $this->begin('index.php?app=desktop&ctl=email&act=setting');
        $this->begin();
        foreach ($_POST as $key => $value) {
            $this->app->setConf('email.config.' . $key, $value);
        }
        $this->end(true, '配置保存成功');
    }

    function test() {
        $this->begin();
        $usermail = $_POST['usermail']; //发件账户
        $smtpport = $_POST['smtpport']; //端口号
        $smtpserver = $_POST['smtpserver']; //邮件服务器
        $acceptor = $_POST['acceptor']; //收件人邮箱
        $_POST['shopname'] = $_POST['sitename'] = app::get('site')->getConf('site_name');
        $subject = '测试邮件-'.app::get('site')->getConf('site_name');
        $body = "当你收到这封邮件,代表邮件发送配置正常。";
        switch ($_POST['sendway']) {
            case 'smtp':
                $email = vmc::singleton('desktop_email_email');

                if ($email->ready($_POST)) {
                    $res = $email->send($acceptor, $subject, $body, $_POST);
                    if ($res){
                        $this->end(true,'测试邮件成功发出，请到'.$acceptor.'邮箱查收！');
                    }
                    if ($email->errorinfo) {
                        $err = $email->errorinfo;
                        $loginfo.= "<br>" . $err['error'];
                        $this->end(false,$loginfo);
                    }
                } else {
                    $this->end(false,'发送失败!');
                }

            case 'mail':
                ini_set('SMTP', $smtpserver);
                ini_set('smtp_port', $smtpport);
                ini_set('sendmail_from', $usermail);
                $email = vmc::singleton('desktop_email_email');
                $subject = $email->inlineCode($subject);
                $header = array(
                    'Return-path' => '<' . $usermail . '>',
                    'Date' => date('r') ,
                    'From' => $email->inlineCode(app::get('site')->getConf('site_name')) . '<' . $usermail . '>',
                    'MIME-Version' => '1.0',
                    'Content-Type' => 'text/html; charset=UTF-8; format=flowed',
                    'Content-Transfer-Encoding' => 'base64'
                );
                $body = chunk_split(base64_encode($body));
                $header = $email->buildHeader($header);
                if (mail($acceptor, $subject, $body, $header)) {
                    $this->end(true,'发送成功!');
                } else {
                    $this->end(false,'发送失败!');
                }

            }
    }

    function email_display(){
        $this->pagedata = $_POST;
        $this->display('email/display.html');
    }
}
?>
