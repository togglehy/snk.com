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

/**
 * VMCSHOP 配置文件
 */
# 当前部署环境
define('ENVIRONMENT','PRODUCTION'); # DEVELOPMENT|TESTING
# TOKEN
define('VMCSHOP_TOKEN','vmcshop');


# 目录位置
define('ROOT_DIR', realpath(dirname(__FILE__).'/../'));
define('DATA_DIR', ROOT_DIR.'/data');
define('THEME_DIR', ROOT_DIR.'/themes');
define('THEME_M_DIR', ROOT_DIR.'/themes_m');
define('PUBLIC_DIR', ROOT_DIR.'/public');
define('PROCESS_DIR', ROOT_DIR.'/process'); #系统任务处理脚本目录
define('APP_DIR', ROOT_DIR.'/applications');
define('TMP_DIR', ROOT_DIR.'/tmp');
#define('EXTENDS_DIR',false); #扩展目录


# 日志系统 logger, 默认: LOG_SYS_INFO
#define('LOG_LEVEL',LOG_SYS_EMERG);
#define('LOG_LEVEL',LOG_SYS_ALERT);
#define('LOG_LEVEL',LOG_SYS_CRIT);
#define('LOG_LEVEL',LOG_SYS_ERR);
#define('LOG_LEVEL',LOG_SYS_WARNING);
define('LOG_LEVEL',LOG_SYS_NOTICE);
#define('LOG_LEVEL',LOG_SYS_INFO);
#define('LOG_LEVEL',LOG_SYS_DEBUG);
define('LOG_TYPE', 3); #3时用到LOG_FILE，0时用回调系统级日志函数不存储文件
define('LOG_DIR',ROOT_DIR.'/logs');
define('LOG_FILENAME', '/{level}/{date}/{hour}.php'); #各种级别/每天/每小时

# 数据库
define('DB_CHARSET', 'utf8');
define('DB_USER', 'usernamehere');  # 数据库用户名
define('DB_PASSWORD', 'yourpasswordhere'); # 数据库密码
define('DB_NAME', 'putyourdbnamehere');    # 数据库名
define('DB_HOST', 'localhost'); # 数据库HOST
#define('DB_PCONNECT',1); #数据库持续连接
#define('DB_SLAVE_NAME',DB_NAME);
#define('DB_SLAVE_USER',DB_USER);
#define('DB_SLAVE_PASSWORD',DB_PASSWORD);
#define('DB_SLAVE_HOST',DB_HOST);

# 缓存
define('WITHOUT_CACHE',false); #是否禁用缓存体系
define('WITHOUT_PAGE_CACHE',true);#是否禁用全页缓存
define('CACHE_ADAPTER', 'base_cache_filesystem');
define('FILESYSTEM_CACHE_MAX','64M'); #文件系统缓存最大空间占用

# KV
define('KV_ADAPTER', 'base_kvstore_filesystem');
define('KV_PERSISTENT', true);#kv是否要持久化到数据库
define('KV_PREFIX', 'vmc-default');

# URL REWRITE配置
define('URL_REWRITE',false);

# 文件存储
define('FILE_STORAGER','filesystem');
#define('FILE_STORAGER','oss'); #阿里云存储[需先开通图片图片处理服务和动态尺寸API]
#define('FILE_STORAGER','upyun'); #www.upyun.com


# 应用资源文件HOST
#define('APP_STATICS_HOST', 'http://statics.demo.com;http://statics2.demo.com');

# 内部HTTP请求代理
#define('HTTP_PROXY','127.0.0.1:8888');

# 脚本每次可申请最大内存数
@ini_set('memory_limit','32M');

# SESSION 配置
define('SESS_NAME', 's');   #SESSION 客户端COOKIE KEY
define('SESS_CACHE_EXPIRE', 60);  #SESSION 客户端COOKIE 过期时间 (分钟)

# syscache后端存储处理类
define('SYSCACHE_ADAPTER', 'base_syscache_adapter_filesystem');
#define('SYSCACHE_ADAPTER', 'base_syscache_adapter_chdb');
