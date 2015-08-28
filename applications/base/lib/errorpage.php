<?php
class base_errorpage {
    static private function _var_export($text) {
        ob_start();
        var_dump($text);
        $text = ob_get_contents();
        ob_end_clean();
        return $text;
    }
    static public function exception_handler($exception) {
        if (defined('ENVIRONMENT') && constant('ENVIRONMENT') != 'PRODUCTION') {
            self::_exception_handler($exception);
        } else {
            self::system_crash();
        }
    }
    static private function _exception_handler($exception) {
        foreach (vmc::serviceList('base_exception_handler') as $service) {
            if (method_exists($service, 'pre_display')) {
                $service->pre_display($content);
            }
        }
        $message = $exception->getMessage();
        $code = $exception->getCode();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTrace();
        $trace_message = $exception->getTraceAsString();
        $trace_message = null;
        $root_path = realpath(ROOT_DIR);
        $output = ob_end_clean();
        $position = str_replace($root_path, '&raquo;', $file) . ':' . $line;
        $i = 0;
        $trace = array_reverse($trace);
        $count_trace = count($trace);
        foreach ($trace as $t) {
            if (!($t['class'] == 'vmc' && $t['function'] == 'exception_error_handler')) {
                $t['file'] = str_replace($root_path, 'ROOT:', $t['file']);
                $basename = basename($t['file']);
                if ($i == $count_trace - 1) {
                    $trace_message.= '<li style="color:#333;font-weight:bold;"><strong style="color:#f00">&raquo;</strong>';
                } else {
                    $trace_message.= '<li style="color:#999;"><strong>&nbsp;</strong>';
                }
                if ($t['args']) {
                    //                            var_dump(debug_backtrace());
                    $args = array();
                    foreach ($t['args'] as $arg_info) {
                        if (is_array($arg_info) || (is_string($arg_info) && strlen($arg_info) > 20)) {
                            $args[] = "<a href='javascript:;'  onclick=\"alert(this.nextSibling.innerHTML)\">...</a><span style='display:none'>" . self::_var_export($arg_info) . "</span>";
                        } else if (is_object($arg_info)) {
                            $arg_detail = self::_var_export($arg_info);
                            $arg_info = get_class($arg_info);
                            $args[] = "object(<a href='javascript:;' onclick=\"alert(this.nextSibling.innerHTML)\">$arg_info</a><span style='display:none'>$arg_detail</span>)";
                        } else {
                            $args[] = var_export(htmlspecialchars($arg_info) , 1);
                        }
                    }
                    $args = implode(',', $args);
                } else {
                    $args = '';
                }
                if ($t['line'] && $basename) {
                    $trace_message.= "[{$basename}:{$t['line']}]&nbsp;{$t['class']}{$t['type']}{$t['function']}({$args})</li>";
                } else {
                    $trace_message.= "{$t['class']}{$t['type']}{$t['function']}({$args})</li>";
                }
                $i++;
            }
        }
        $output = <<<EOF
        <h1>$message
            <small>$position</small>
        </h1>
        <ol>
        $trace_message
        </ol>

EOF;
        self::output($output, 'Track');
    }
    static function system_crash() {
        self::output('', '服务异常稍后再试');
    }
    static function system_offline() {
        self::output('', 'System is offline');
    }
    static protected function output($body, $title = '', $status_code = 500) {
        //header('Connection:close',1,500);
        $date = date(DATE_RFC822);
        $html = <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
        	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        	<title>Error: $title</title>
        </head>
        <body style="font-size:14px;font-family:verdana;border-top:10px #f00 solid;padding:100px;margin:0;background:#efefef;">
            <pre>
            $body
            </pre>
            <hr>
            $date
        </body>
        </html>
HTML;
        echo str_pad($html, 1024);
        exit;
    }
} //End Class
