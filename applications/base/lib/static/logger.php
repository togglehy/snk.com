<?php
/**
 * 类 logger 实现了一个简单的日志记录服务
 */
class logger {
    /*
     * @var array $_log_types
     * @access static private
    */
    static private $__log_levels = array(
        LOG_SYS_EMERG => 'EMERG',
        LOG_SYS_ALERT => 'ALERT',
        LOG_SYS_CRIT => 'CRIT',
        LOG_SYS_ERR => 'ERR',
        LOG_SYS_WARNING => 'WARNING',
        LOG_SYS_NOTICE => 'NOTICE',
        LOG_SYS_INFO => 'INFO',
        LOG_SYS_DEBUG => 'DEBUG'
    );
    static private $__log_level = null;
    static private $__default_log_level = LOG_SYS_INFO;
    static public function __init() {
        if (self::$__log_level === null) {
            if (defined('LOG_LEVEL') && array_key_exists(LOG_LEVEL, self::$__log_levels)) {
                self::$__log_level = LOG_LEVEL;
            } else {
                self::$__log_level = self::$__default_log_level;
            }
        }
    }
    static public function get_log_level() {
        self::__init();
        return self::$__log_level;
    }
    /*
     * 记录 LOG_SYS_EMERG 类型的日志
     * @var string $message
     * @var bool $keepline
    */
    static public function emerg($message) {
        logger::log($message, LOG_SYS_EMERG);
    }
    /*
     * 记录 LOG_CRIT 类型的日志
     * @var string $message
     * @var bool $keepline
    */
    static public function alert($message) {
        logger::log($message, LOG_SYS_ALERT);
    }
    /*
     * 记录 LOG_CRIT 类型的日志
     * @var string $message
     * @var bool $keepline
    */
    static public function crit($message) {
        logger::log($message, LOG_SYS_CRIT);
    }
    /*
     * 记录 LOG_ERR 类型的日志
     * @var string $message
     * @var bool $keepline
    */
    static public function error($message) {
        logger::log($message, LOG_SYS_ERR);
    }
    /*
     * 记录 LOG_WARNING 类型的日志
     * @var string $message
     * @var bool $keepline
    */
    static public function warning($message) {
        logger::log($message, LOG_SYS_WARNING);
    }
    /*
     * 记录 LOG_NOTICE 类型的日志
     * @var string $message
     * @var bool $keepline
    */
    static public function notice($message) {
        logger::log($message, LOG_SYS_NOTICE);
    }
    /*
     * 记录 LOG_INFO 类型的日志
     * @var string $message
     * @var bool $keepline
    */
    static public function info($message) {
        logger::log($message, LOG_SYS_INFO);
    }
    /*
     * 记录 LOG_DEBUG 类型的日志
     * @var string $message
     * @var bool $keepline
    */
    static public function debug($message) {
        logger::log($message, LOG_SYS_DEBUG);
    }
    /*
     * 通用记录日志函数
     * @var string $message
     * @var int $log_level
    */
    static public function log($message, $log_level = LOG_SYS_INFO) {
        self::__init();
        if (vmc::$console_output) {
            if ($log_level < LOG_SYS_DEBUG) {
                echo $message = $message . "\n";
            }
        }
        if ($log_level <= self::$__log_level) {
            //日志格式：时间 日志内容 日志级别标示
            $ip = base_request::get_remote_addr();
            $message = sprintf("%s\t%s\t%s\t%s\n", $ip, date("Y-m-d H:i:s") , self::$__log_levels[$log_level], $message);

            switch (LOG_TYPE) {
                case 3:
                    if (defined('LOG_FILENAME')) {
                        $logfilename = str_replace('{date}', date("Ymd") , LOG_FILENAME);
                        $logfilename = str_replace('{hour}', date("H") , $logfilename);
                        $logfilename = str_replace('{level}', self::$__log_levels[$log_level] , $logfilename);
                    } else {
                        $logfilename = date("YmdH") . '.php';
                    }
                    if (defined('LOG_DIR')) {
                        $logfile = LOG_DIR .'/'. $logfilename;
                    } else {
                        $logfile = DATA_DIR . '/logs/' . $logfilename;
                    }
                    if (!file_exists($logfile)) {
                        if (!is_dir(dirname($logfile))) utils::mkdir_p(dirname($logfile));
                        file_put_contents($logfile, (defined(LOG_HEAD_TEXT)) ? LOG_HEAD_TEXT : '<' . '?php exit()?' . ">\n");
                    }
                    @error_log($message, 3, $logfile);
                    break;
                case 2:
                    @error_log($message, 0);
                case 0:
                default:
                    @syslog($log_level, $message);
                } //End Switch

        }
    }
    static function appned() {
    }
}
