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


class b2c_messenger_email
{
    public $name = '电子邮件'; //名称
    public $isHtml = true; //是否html消息
    public $hasTitle = true; //是否有标题


    public function ready($config)
    {
        $this->email = vmc::singleton('desktop_email_email');
        if ($config['sendway'] == 'smtp') {
            if (!$this->email->SmtpConnect($config)) {
                return false;
            }
        }
    }

    /**
     * finish
     * 可选方法，结束发送时触发.
     *
     * @param mixed $config
     */
    public function finish($config)
    {
        if ($config['sendway'] == 'smtp') {
            $this->email->SmtpClose();
        }
    }

    /**
     * send
     * 必有方法,发送时调用.
     *
     * config参数为getOptions取得的所有项的配置结果
     *
     * @param mixed $target ['email']
     * @param mixed $title
     * @param mixed $config
     */
    public function send($target, $title, $content, $config)
    {
        logger::debug(__CLASS__.var_export(func_get_args(), 1));

        $new_msg = array(
            //to
            'member_id'=>$target['member_id'],
            'target'=>$target['email'],
            'subject'=>$title,
            'content'=>$content,
            'createtime'=>time(),
            'msg_type'=>'email',
            'status'=>'sent'
        );
        app::get('b2c')->model('member_msg')->save($new_msg);

        if(!$email_to = $target['email']){
            return false;
        }
        if ($config['sendway'] == 'mail') {
            $this->email = vmc::singleton('desktop_email_email');
        }
        $this->email->Sender = $this->Sender = $config['usermail'];
        $this->email->Subject = $this->Subject = $this->email->inlineCode($title);

        $From = $this->email->inlineCode(app::get('site')->getConf('site_name')).'<'.$config['usermail'].'>';
        $header = array(
            'Return-path' => '<'.$config['usermail'].'>',
            'Date' => date('r'),
            'From' => $From,
            'MIME-Version' => '1.0',
            'Subject' => $this->Subject,
            'To' => $email_to,
            'Content-Type' => 'text/html; charset=UTF-8; format=flowed',
            'Content-Transfer-Encoding' => 'base64',
        );
        $body = chunk_split(base64_encode($content));
        $header = $this->email->buildHeader($header);
        $config['sendway'] = ($config['sendway']) ? $config['sendway'] : 'smtp';
        switch ($config['sendway']) {
            case 'sendmail':
                $result = $this->email->SendmailSend($email_to, $header, $body);
                break;
            case 'mail':
                $result = $this->email->MailSend($email_to, $header, $body);
                break;
            case 'smtp':
                $result = $this->email->SmtpSend($email_to, $header, $body, $config);
                break;
            default:
                $result = false;
                break;
        }

        return $result;
    }

    public function getOptions()
    {
        return array(
            'sendway' => array('label' => ('发送方式'),'type' => 'radio','options' => array('mail' => ('使用本服务器发送'),'smtp' => ('使用外部SMTP发送')),'value' => 'mail'),
            'usermail' => array('label' => ('发信人邮箱'),'type' => 'input','value' => 'yourname@domain.com'),
            'smtpserver' => array('label' => ('smtp服务器地址'),'type' => 'input','value' => 'mail.domain.com'),
            'smtpport' => array('label' => ('smtp服务器端口'),'type' => 'input','value' => '25'),
            'smtpuname' => array('label' => ('smtp用户名'),'type' => 'input','value' => ''),
            'smtppasswd' => array('label' => ('smtp密码'),'type' => 'password','value' => ''),
        );
    }
}
