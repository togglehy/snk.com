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


class base_db_pdo_mysql extends base_db_abstract implements base_interface_db
{
    protected $_rw_lnk = null;
    protected $_ro_lnk = null;
    protected $_use_transaction = false;
    public $_enable_innodb = false;
    public function __construct()
    {
        parent::__construct();
    }
    public function enable_innodb()
    {
        $status = false;
        $rs = $this->select('show engines');
        foreach ($rs as $row) {
            if ($row['Engine'] == 'InnoDB' && ($row['Support'] == 'YES' || $row['Support'] == 'DEFAULT')) {
                $status = true;
            }
        }

        return $this->_enable_innodb = $status;
    }



    /**
     * 数据库操作核心入口.
     *
     * @param $sql SQL语句
     * @param $skipModifiedMark 是否忽略缓存标记更新
     */
    public function exec($sql, $skipModifiedMark = false, $db_lnk = false, &$stmt = flase)
    {
        if ($this->prefix != 'vmc_') {
            $sql = preg_replace_callback('/([`\s\(,])(vmc_)([0-9a-z\_]+)([`\s\.]{0,1})/is', array(
                $this,
                'fix_dbprefix',
            ), $sql);
        }

        //删除、插入、更新 语句
        if (preg_match('/(?:(delete\s+from)|(insert\s+into)|(update))\s+([]0-9a-z_:"`.@[-]*)/is', $sql, $match)) {
            //直接操作主库
            if (!$db_lnk) {
                $db_lnk = $this->_rw_lnk;
            }
            if(!$db_lnk){
                $db_lnk = $this->_rw_lnk = $this->_rw_conn();
            }
            if (!$skipModifiedMark && cachemgr::enable()) {
                $table = strtoupper(trim(str_replace('`', '', str_replace('"', '', str_replace("'", '', $match[4])))));
                $now = time();
                $pos = strpos($table, strtoupper($this->prefix));
                if ($pos === 0) {
                    $table = substr($table, strlen($this->prefix));
                }
                $exec_count = $db_lnk->exec('UPDATE vmc_base_cache_expires SET expire = "'.$now.'" WHERE type = "DB" AND name = "'.$table.'"');
                if ($exec_count) {
                    //更新数据库表缓存标记
                    cachemgr::set_modified('DB', $table, $now);
                }
            }
            logger::debug('EXEC SQL:'.$sql);
            $affected = $db_lnk->exec($sql);
            if($affected === false){
                $error_info = $db_lnk->errorInfo();
                logger::error('MYSQL_ERROR! SQL:'.$sql);
                logger::error('ERROR_CODE:'.$db_lnk->errorCode().'ERROR_INFO:'.implode('::',(array)$error_info));
                return false;
            }
            return true;
        } else {
            //查询为主
            if ($this->_ro_lnk) {
                $db_lnk = $this->_ro_lnk;
            } else {
                $db_lnk = $this->_ro_conn();
            }
            logger::debug('QUERY SQL:'.$sql);
            $stmt = $db_lnk->query($sql);
            $db_res = array();
            while ($stmt && $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $db_res[] = $row;
            }

            return array(
                'rs' => $db_res,
                'sql' => $sql,
            );
        }
    }
    protected function fix_dbprefix($matchs)
    {
        return $matchs[1].((trim($matchs[1]) == '`') ? $this->prefix.$matchs[3] : '`'.$this->prefix.$matchs[3].'`').$matchs[4];
    } //End Function
    public function select($sql, $skipModifiedMark = false)
    {
        if ($this->_ro_lnk) {
            $db_lnk = $this->_ro_lnk;
        } else {
            $db_lnk = $this->_ro_conn();
        }

        if ($this->prefix != 'vmc_') {
            $sql = preg_replace_callback('/([`\s\(,])(vmc_)([0-9a-z\_]+)([`\s\.]{0,1})/is', array(
                $this,
                'fix_dbprefix',
            ), $sql);
        }
        if (cachemgr::enable() && cachemgr::check_current_co_depth() > 0 && preg_match_all('/FROM\s+([]0-9a-z_:"`.@[-]*)/is', $sql, $matchs)) {
            if (isset($matchs[1])) {
                foreach ($matchs[1] as $table) {
                    if (empty($table)) {
                        continue;
                    }
                    $table = strtoupper(trim(str_replace(array(
                        '`',
                        '"',
                        '\'',
                    ), array(
                        '',
                        '',
                        '',
                    ), $table)));
                    $pos = strpos($table, strtoupper($this->prefix));
                    if ($pos === 0) {
                        $table = substr($table, strlen($this->prefix));
                    } //todo: 真实表名
                    if (!cachemgr::check_current_co_objects_exists('DB', $table)) {
                        cachemgr::check_expires('DB', $table);
                    }
                }
            }
        }
        $rs = $this->exec($sql, $skipModifiedMark, $db_lnk);

        if (!empty($rs['rs'])) {
            return $rs['rs'];
        } else {
            return false;
        }
    }
    public function selectrow($sql)
    {
        $row = $this->selectlimit($sql, 1, 0);

        return $row[0];
    }
    public function selectlimit($sql, $limit = 10, $offset = 0)
    {
        if ($offset >= 0 || $limit >= 0) {
            $offset = ($offset >= 0) ? $offset.',' : '';
            $limit = ($limit >= 0) ? $limit : '18446744073709551615';
            $sql .= ' LIMIT '.$offset.' '.$limit;
        }
        $data = $this->select($sql);

        return $data;
    }

    /**
     * 建立从库连接  _ro_lnk.
     */
    protected function _ro_conn()
    {
        try{

            if (defined('DB_SLAVE_HOST') && $this->_use_transaction !== true) {
                if($this->_ro_lnk)return $this->_ro_lnk;
                $this->_ro_lnk = $this->_connect(DB_SLAVE_HOST, DB_SLAVE_USER, DB_SLAVE_PASSWORD, DB_SLAVE_NAME);
            } elseif ($this->_rw_lnk) {
                $this->_ro_lnk = $this->_rw_lnk;
            } else {
                $this->_ro_lnk = $this->_rw_conn();
            }

        }catch(PDOException $e){
            logger::error('_ro_conn Error:'.$e->getMessage());
            die('Could not connect to the database !');
        }


        return $this->_ro_lnk;
    }
    public function getRows($rs, $row = 10)
    {
        return $rs;
    }
    /**
     * 建立主库连接 _rw_lnk.
     */
    protected function _rw_conn()
    {
        try{
            if($this->_rw_lnk)return $this->_rw_lnk;
            $conn = $this->_rw_lnk = $this->_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        }catch(PDOException $e){
            logger::error('_rw_conn Error:'.$e->getMessage());
            die('Could not connect to the database !');
        }
        return $conn;
    }
    protected function _connect($host, $user, $passwd, $dbname)
    {
        $pdo_options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"
        );
        if (defined('DB_PCONNECT') && constant('DB_PCONNECT')) {
            $pdo_options = array(
                PDO::ATTR_PERSISTENT => true,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"
            );
        }
        if (!$cron = new PDO("mysql:host=$host;dbname=$dbname", $user, $passwd,$pdo_options)) {
            trigger_error('无法连接数据库', E_USER_ERROR);
        }
        $this->dbver = $cron->getAttribute(PDO::ATTR_SERVER_VERSION);

        return $cron;
    }
    public function count($sql)
    {
        $sql = preg_replace(array(
            '/(.*\s)LIMIT .*/i',
            '/^select\s+(.+?)\bfrom\b/is',
        ), array(
            '\\1',
            'select count(*) as c from',
        ), trim($sql));
        $row = $this->select($sql);

        return intval($row[0]['c']);
    }
    /**
     * _whereClause.
     *
     * @param mixed $queryString
     */
    protected function _whereClause($queryString)
    {
        preg_match('/\sWHERE\s(.*)/is', $queryString, $whereClause);
        $discard = false;
        if ($whereClause) {
            if (preg_match('/\s(ORDER\s.*)/is', $whereClause[1], $discard)); elseif (preg_match('/\s(LIMIT\s.*)/is', $whereClause[1], $discard)); else {
     preg_match('/\s(FOR UPDATE.*)/is', $whereClause[1], $discard);
 }
        } else {
            $whereClause = array(
            false,
            false,
        );
        }
        if ($discard) {
            $whereClause[1] = substr($whereClause[1], 0, strlen($whereClause[1]) - strlen($discard[1]));
        }

        return $whereClause[1];
    }
    public function quote($string)
    {
        return $this->_rw_lnk->quote($string);
    }
    public function lastinsertid()
    {
        if ($this->_rw_lnk) {
            $db_lnk = $this->_rw_lnk;
        } elseif ($this->_ro_lnk) {
            $db_lnk = $this->_ro_lnk;
        }

        return $db_lnk->lastInsertId();
    }
    public function affect_row()
    {
        //TODO
    }
    public function errorinfo()
    {
        if ($this->_rw_lnk) {
            $db_lnk = $this->_rw_lnk;
        } elseif ($this->_ro_lnk) {
            $db_lnk = $this->_ro_lnk;
        }

        return $db_lnk->errorInfo();
    }
    public function errorcode()
    {
        if ($this->_rw_lnk) {
            $db_lnk = $this->_rw_lnk;
        } elseif ($this->_ro_lnk) {
            $db_lnk = $this->_ro_lnk;
        }

        return $db_lnk->errorCode();
    } //End Function
    public function beginTransaction()
    {

        if (!$this->_in_transaction) {
            $this->_in_transaction = true;
            if (!$this->_use_transaction) {
                $this->_use_transaction = true;
                if (isset($this->_ro_lnk)) {
                    $this->_ro_conn();
                } //todo:如果已经连上slave，变更ro_lnk至主库，保持连线统一
            } //todo:第一次使用事务后即通知程序当前进程ro_conn至主库

            if (!isset($this->_rw_lnk)) {
                $this->_rw_lnk = $this->_rw_conn();
            }
            logger::debug('beginTransaction...');
            return ($this->_rw_lnk->beginTransaction());
        } else {
            return false;
        }
    }
    public function commit($status = true)
    {
        if (!isset($this->_rw_lnk)) {
            $this->_rw_lnk = $this->_rw_conn();
        }
        logger::debug('commit...');
        return ($this->_rw_lnk->inTransaction() && $this->_rw_lnk->commit());
    }
    public function rollBack()
    {
        if (!isset($this->_rw_lnk)) {
            $this->_rw_lnk = $this->_rw_conn();
        }
        if ($this->_rw_lnk->inTransaction() && $this->_rw_lnk->rollBack()) {
            $this->_in_transaction = false;
            logger::debug('rollBack...');
            return true;
        }

        return false;
    }
    public function close()
    {
        if ($this->_rw_lnk) {
            $this->_rw_lnk = null;

            return true;
        }

        return false;
    }
    public function ping()
    {
        return true;
    }

    public function fetch_colum($sql, $colno = 0)
    {
        if ($this->_ro_lnk) {
            $db_lnk = $this->_ro_lnk;
        } else {
            $db_lnk = $this->_ro_conn();
        }
        $stmt = $db_lnk->query($sql, PDO::FETCH_COLUMN, $colno);

        return $stmt->fetchAll();
    }
}
