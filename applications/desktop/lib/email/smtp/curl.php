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


class desktop_email_smtp_curl
{
    public $SMTP_PORT = 25;

    public $CRLF = "\r\n";
    public $do_debug;

    public $curl;
    public $error;
    public $helo_rply;

    public function desktop_email_smtp_curl()
    {
        $this->curl = 0;
        $this->error = null;
        $this->helo_rply = null;
        $this->do_debug = 0;
    }

    private $host;
    private $port;
    private $tval;
    public function Connect($host, $port = 0, $tval = 30)
    {
        $this->error = null;
        $this->host = $host;
        $this->port = $port;
        $this->tval = $tval;

        if (empty($port)) {
            $port = $this->SMTP_PORT;
        }
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $host);
        curl_setopt($this->curl, CURLOPT_PORT, $port);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, $tval);

        if (curl_errno($this->curl)) {
            $this->error = array('error' => 'Failed to connect to server',
                                 'errno' => curl_errno($this->curl),
                                 'errstr' => curl_error($this->curl), );
            if ($this->do_debug) {
                echo 'SMTP -> ERROR: '.$this->error['error'].':'.$this->error['errstr'].'('.$this->error['errno'].')'.$this->CRLF;
            }

            return false;
        }

        return true;
    }

    private $username;
    private $password;
    public function Authenticate($username, $password)
    {
        $this->username = base64_encode($username);
        $this->password = base64_encode($password);

        return true;
    }

    public function Connected()
    {
        return !empty($this->curl);
    }

    public function Close()
    {
        $this->error = null; # so there is no confusion
        $this->helo_rply = null;
        if ($this->Connected()) {
            curl_close($this->curl);
        }
    }

    public function Data($msg_data)
    {
        $this->error = null;

        if (!$this->connected()) {
            $this->error = array('error' => 'Called Data() without being connected');

            return false;
        }

        $content = 'EHLO '.($_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST'] : 'localhost').$this->CRLF;

        $content .= 'AUTH LOGIN'.$this->CRLF.$this->username.$this->CRLF.$this->password.$this->CRLF;
        $content .= 'MAIL FROM:<'.$this->from.'>'.$this->CRLF;
        $content .= 'RCPT TO:<'.$this->to.'>'.$this->CRLF;
        $content .= 'DATA'.$this->CRLF.$msg_data.$this->CRLF;
        $content .= '.'.$this->CRLF.'QUIT'.$this->CRLF;

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $content);
        if ($data = curl_exec($this->curl)) {
            //$data = curl_multi_getcontent($this->curl);
        } else {
            $this->error = array('error' => '请求失败');

            return false;
        }

        if ($this->do_debug) {
            var_dump($data);
        }

        return $this->_parse($data);
    }

    public function _parse($data)
    {
        $arrData = explode("\r\n", $data);

        if (($code = substr($arrData[0], 0, 3)) != 220) {
            $this->error = array('error' => '连接smtp服务器失败','stmp_code' => $code,'stmp_msg' => substr($arrData[0], 3));

            return false;
        }

        if (($code = substr($arrData[1], 0, 3)) != 250) {
            $this->error = array('error' => '连接smtp服务器失败','stmp_code' => $code,'stmp_msg' => substr($arrData[1], 3));

            return false;
        }
        $flag = 0;
        foreach ($arrData as $key => $row) {
            if (($code = substr($arrData[$key], 0, 3)) == 334) {
                $flag = $key;
                break;
            }
        }
        $flag = $flag + 2;
        // 用户名&&密码验证失败
        if (($code = substr($arrData[$flag], 0, 3)) != 235) {
            $this->error = array('error' => '用户名&密码验证失败');

            return false;
        }
        // 发件人
        $flag = $flag + 1;
        if (($code = substr($arrData[$flag], 0, 3)) != 250) {
            $this->error = array('error' => '发件人错误','stmp_code' => $code,'stmp_msg' => substr($arrData[$flag], 3));

            return false;
        }
        // 收件人
        $flag = $flag + 1;
        if (($code = substr($arrData[$flag], 0, 3)) != 250) {
            $this->error = array('error' => '收件人错误','stmp_code' => $code,'stmp_msg' => substr($arrData[$flag], 3));

            return false;
        }
        // 发送成功
        $flag = $flag + 2;
        if (($code = substr($arrData[$flag], 0, 3)) != 250) {
            $this->error = array('error' => '发送失败','stmp_code' => $code,'stmp_msg' => substr($arrData[$flag], 3));

            return false;
        }

        return true;
    }

    public function Expand($name)
    {
        $this->error = null;

        if (!$this->Connected()) {
            $this->error = array('error' => 'Called Expand() without being connected');

            return false;
        }

        return true;
    }

    public function Hello($host = '')
    {
        $this->error = null;

        if (!$this->connected()) {
            $this->error = array('error' => 'Called Hello() without being connected');

            return false;
        }

        return true;
    }

    public function SendHello($hello, $host)
    {
        $this->helo_rply = $hello;

        return true;
    }

    public function Help($keyword = '')
    {
        $this->error = null;

        if (!$this->connected()) {
            $this->error = array('error' => 'Called Help() without being connected');

            return false;
        }

        return $keyword;
    }

    private $from;
    public function Mail($from)
    {
        $this->error = null;

        if (!$this->connected()) {
            $this->error = array(
                    'error' => 'Called Mail() without being connected', );

            return false;
        }

        $this->from = $from;

        if ($this->do_debug) {
            echo 'SMTP -> FROM SERVER:'.$this->CRLF.$this->from;
        }

        return true;
    }

    public function Noop()
    {
        $this->error = null;

        if (!$this->connected()) {
            $this->error = array('error' => 'Called Noop() without being connected');

            return false;
        }

        return true;
    }

    public function Quit($close_on_error = true)
    {
        $this->error = null;

        if (!$this->connected()) {
            $this->error = array('error' => 'Called Quit() without being connected');

            return false;
        }

        return true;
    }

    private $to;
    public function Recipient($to)
    {
        $this->error = null;

        if (!$this->connected()) {
            $this->error = array('error' => 'Called Recipient() without being connected');

            return false;
        }
        $this->to = $to;

        return true;
    }

    public function Reset()
    {
        $this->error = null;

        if (!$this->connected()) {
            $this->error = array('error' => 'Called Reset() without being connected');

            return false;
        }

        $this->Close();

        return $this->Connect($this->host, $this->port, $this->tval);
    }

    public function Send($from)
    {
        $this->error = null;

        if (!$this->connected()) {
            $this->error = array('error' => 'Called Send() without being connected');

            return false;
        }

        return true;
    }

    public function SendAndMail($from)
    {
        $this->error = null;

        if (!$this->connected()) {
            $this->error = array('error' => 'Called SendAndMail() without being connected');

            return false;
        }

        return true;
    }

    public function SendOrMail($from)
    {
        $this->error = null;

        if (!$this->connected()) {
            $this->error = array('error' => 'Called SendOrMail() without being connected');

            return false;
        }

        return true;
    }

    public function Turn()
    {
        $this->error = array('error' => 'This method, TURN, of the SMTP '.
                                        'is not implemented', );
        if ($this->do_debug >= 1) {
            echo 'SMTP -> NOTICE: '.$this->error['error'].$this->CRLF;
        }

        return false;
    }

    public function Verify($name)
    {
        $this->error = null;

        if (!$this->connected()) {
            $this->error = array('error' => 'Called Verify() without being connected');

            return false;
        }

        return true;
    }
} // end class
