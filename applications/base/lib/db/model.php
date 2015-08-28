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


class base_db_model implements base_interface_model
{
    public $filter_use_like = false;
    public function __construct($app)
    {
        $this->app = $app;
        $this->db = vmc::database();
        $this->schema = $this->get_schema();
        $this->metaColumn = isset($this->schema['metaColumn']) ? $this->schema['metaColumn'] : null;
        $this->idColumn = $this->schema['idColumn'];
        $this->textColumn = $this->schema['textColumn'];
        $this->skipModifiedMark = ($this->schema['ignore_cache'] === true) ? true : false;
        if (!is_array($this->idColumn) && array_key_exists('extra', $this->schema['columns'][$this->idColumn])) {
            $this->idColumnExtra = $this->schema['columns'][$this->idColumn]['extra'];
        }
        //end check
    }

    public function table_name($real = false)
    {
        $class_name = get_class($this);
        $table_name = substr($class_name, 5 + strpos($class_name, '_mdl_'));
        if ($real) {
            return vmc::database()->prefix.$this->app->app_id.'_'.$table_name;
        } else {
            return $table_name;
        }
    }

    public function getList($cols = '*', $filter = array(), $offset = 0, $limit = -1, $orderby = null)
    {
        if ($orderby) {
            $sql_order = ' ORDER BY '.(is_array($orderby) ? implode($orderby, ' ') : $orderby);
        } else {
            $sql_order = '';
        }
        $rows = $this->db->selectLimit('select '.$cols.' from `'.$this->table_name(1)
            .'` where '.$this->filter($filter).$sql_order, $limit, $offset);

        $this->tidy_data($rows, $cols);

        return $rows;
    }

    public function getRow($cols = '*', $filter = array(), $orderType = null)
    {
        $data = $this->getList($cols, $filter, 0, 1, $orderType);
        if ($data) {
            return $data['0'];
        } else {
            return false;
        }
    }

    public function tidy_data(&$rows, $cols = '*')
    {
        if ($rows) {
            $need_tidy = false;
            $tidy_type = array('serialize');
            $def_columns = $this->_columns();
            if (rtrim($cols) === '*') {
                $columns = $def_columns;
            } else {
                $tmp = explode(',', $cols);
                foreach ($tmp as $col) {
                    $col = trim($col);
                    if (preg_match('/\S+ as \S+/i', $col)) {
                        $array = preg_split('/ as /i', $col);
                        $ex_key = str_replace('`', '', trim($array[1]));
                        $ex_real = str_replace('`', '', trim($array[0]));
                        $columns[$ex_key] = $def_columns[$ex_real];
                    } else {
                        $ex_key = str_replace('`', '', $col);
                        $columns[$ex_key] = $def_columns[$ex_key];
                    }
                }
            }
            $curRow = current($rows);
            foreach ($columns as $k => $v) {
                if (in_array($v['type'], $tidy_type) && array_key_exists($k, $curRow)) {
                    $need_columns[] = $k;
                    $need_tidy = true;
                }
            }
            if ($need_tidy) {
                foreach ($rows as $key => $row) {
                    foreach ($need_columns as $column) {
                        switch (trim($columns[$column]['type'])) {
                            case 'serialize':
                                $rows[$key][$column] = unserialize($row[$column]);
                            default:
                        }
                    }
                }
            }
        }
    }//End Function

    public function filter($filter)
    {
        if (is_array($filter)) {
            foreach ($filter as $k => $v) {
                if (!isset($this->schema['columns'][$k])) {
                    logger::notice('存在未知过滤字段:'.$k);
                    unset($filter[$k]);
                }
            }
        }

        return base_db_tools::filter2sql($filter);
    }

    public function count($filter = null)
    {
        $row = $this->db->select('SELECT count('.$this->idColumn.') as _count FROM `'.$this->table_name(1).'` WHERE '.$this->filter($filter));

        return intval($row[0]['_count']);
    }

    public function get_schema()
    {
        $table = $this->table_name();
        if (!isset($this->__exists_schema[$this->app->app_id][$table])) {
            if (!isset($this->table_define)) {
                $this->table_define = new base_application_dbtable();
            }
            $this->__exists_schema[$this->app->app_id][$table] = $this->table_define->detect($this->app, $table)->load(false);
        }

        return $this->__exists_schema[$this->app->app_id][$table];
    }

    public function _columns()
    {
        $schema = new base_application_dbtable();
        $dbinfo = $schema->detect($this->app, $this->table_name())->load();

        return (array) $dbinfo['columns'];
    }
    public function searchOptions()
    {
        $columns = array();
        foreach ($this->_columns() as $k => $v) {
            if (isset($v['searchtype']) && $v['searchtype']) {
                $columns[$k] = $v['label'];
            }
        }

        return $columns;
    }

    public function replace($data, $filter)
    {
        $exits_row = $this->count($filter);
        if($exits_row){
            $where = base_db_tools::filter2sql($filter);
            $sql = base_db_tools::getupdatesql($this->table_name(1), $data, $where);
        }else{
            return $this->insert($data);
        }
        return $this->db->exec($sql, $this->skipModifiedMark);
    }

    public function insert(&$data)
    {
        $cols = $this->_columns();
        $insertValues = array();
        $table_fields = $this->db->fetch_colum('DESCRIBE '.$this->table_name(1),0);
        $table_fields_type = $this->db->fetch_colum('DESCRIBE '.$this->table_name(1),1);
        $table_fields = array_combine($table_fields, $table_fields_type);
        foreach ($table_fields as $db_col => $db_type) {
            $k = $db_col;
            $p = $cols[$db_col];

            //如果是必填，那么参数如果是空则使用默认
            if ($p['required'] && ($data[$k] === '' || is_null($data[$k]))) {
                unset($data[$k]);
            }

            if (!isset($p['default']) && $p['required'] && $p['extra'] != 'auto_increment') {
                if (!isset($data[$k])) {
                    trigger_error(($p['label'] ? $p['label'] : $k).'不能为空！', E_USER_ERROR);
                }
            }

            if ($data[$k] !== false) {
                if ($p['type'] == 'last_modify') {
                    $insertValues[$k] = time();
                } elseif ($p['depend_col']) {
                    $dependColVal = explode(':', $p['depend_col']);
                    if ($data[$dependColVal[0]] == $dependColVal[1]) {
                        switch ($dependColVal[2]) {
                            case 'now':
                                $insertValues[$k] = time();
                                break;
                        }
                    }
                }
            }

            if (isset($data[$k])) {
                if ($p['type'] == 'serialize' && !$this->is_serialized($data[$k])) {
                    $data[$k] = serialize($data[$k]);
                }
                if (!isset($data[$k]) && $p['required'] && isset($p['default'])) {
                    $data[$k] = $p['default'];
                }
                $insertValues[$k] = base_db_tools::quotevalue($this->db, $data[$k], $db_type);
            }
        }

        $strValue = implode(',', $insertValues);
        $strFields = implode('`,`', array_keys($insertValues));
        $sql = 'INSERT INTO `'.$this->table_name(true).'` ( `'.$strFields.'` ) VALUES ( '.$strValue.' )';

        if ($sql && $this->db->exec($sql, $this->skipModifiedMark)) {
            $insert_id = $this->db->lastinsertid();
            if ($this->idColumnExtra == 'auto_increment' && $insert_id) {
                $data[$this->idColumn] = $insert_id;
            } else {
                if (is_array($this->idColumn)) {
                    return true;
                } else {
                    $insert_id = $data[$this->idColumn];
                }
            }

            return $insert_id;
        } else {
            return false;
        }
    }

    public function update($data, $filter = array(), $mustUpdate = null)
    {
        if (count((array) $data) == 0) {
            return true;
        }
        $UpdateValues = array();
        foreach ($this->_columns() as $k => $v) {
            if (!empty($mustUpdate)) {
                if (!array_key_exists($k, $mustUpdate)) {
                    continue;
                } else {
                    unset($mustUpdate[$k]);
                }
            }

            //如果是必填，那么参数如果是空则使用默认
            if ($v['required'] && array_key_exists($k, $data) && ($data[$k] === '' || is_null($data[$k]))) {
                unset($data[$k]);
            }
            if (array_key_exists($k, $data)) {
                $UpdateValues[] = '`'.$k.'`= '.base_db_tools::quotevalue($this->db, $data[$k], $v['type']);
            }
            if ($data[$k] !== false) {
                if ($v['type'] == 'last_modify') {
                    $UpdateValues[] = '`'.$k.'` = '.time().' ';
                } elseif (isset($v['depend_col']) && !empty($v['depend_col'])) {
                    $dependColVal = explode(':', $v['depend_col']);
                    if ($data[$dependColVal[0]] == $dependColVal[1]) {
                        switch ($dependColVal[2]) {
                            case 'now':
                                    $UpdateValues[] = '`'.$k.'` = '.time().' ';
                                break;
                        }
                    }
                }
            }
        }
        if (!empty($mustUpdate)) {
            foreach ($mustUpdate as $mpk => $mpv) {
                $UpdateValues[] = '`'.$mpk.'`= NULL';
            }
        }
        $where_sql = (is_array($filter) ? base_db_tools::filter2sql($filter) : $filter);
        $sql = 'update `'.$this->table_name(1).'` set '.implode(',', $UpdateValues).' where '.$where_sql;
        if (!stripos($where_sql, 'AND')) {
            logger::warning('全表更新操作被拦截!SQL:'.$sql);
            return false;
        }
        if (count($UpdateValues) > 0) {
            return $this->db->exec($sql, $this->skipModifiedMark);
        }
    }

    /**
     * delete.
     *
     * 根据条件删除条目
     * 不可以由pipe控制
     * 可以广播事件
     *
     * @param mixed $filter
     * @param mixed $named_action
     */
    public function delete($filter)
    {
        $where_sql = $this->filter($filter);
        $sql = 'DELETE FROM `'.$this->table_name(1).'` where '.$where_sql;
        if (!stripos($where_sql, 'AND')) {
            logger::warning('全表删除操作被拦截!SQL:'.$sql);

            return false;
        }
        if ($this->db->exec($sql, $this->skipModifiedMark)) {
            return true;
        } else {
            return false;
        }
    }



    /*
     *对数据库结构数据save
     *$dbData db结构
     *$mustUpdate db结构
     */
    final public function db_save(&$dbData, $mustUpdate = null, $mustInsert = false)
    {
        $doMethod = 'update';
        $filter = array();

        foreach ((array) $this->idColumn as $idv) {
            if (!$dbData[$idv]) {
                $doMethod = 'insert';
                break;
            }
            $filter[$idv] = $dbData[$idv];
        }

        $where = array();
        if ($filter) {
            foreach ($filter as $k => $v) {
                $where[] = $k.' = "'.$v.'"';
            }
        }

        if (!$mustInsert && $doMethod == 'update' && $this->db->selectrow('SELECT '.implode(',', (array) $this->idColumn).' FROM `'.$this->table_name(true).'` WHERE '.implode(' AND ', $where))) {
            return $this->update($dbData, $filter, $mustUpdate);
        }

        return $this->insert($dbData);
    }

    public function save(&$dbData, $mustUpdate = null, $mustInsert = false)
    {
        return $this->db_save($dbData, $mustUpdate, $mustInsert);
    }

    final public function db_dump($filter, $field = '*')
    {
        if (!isset($filter)) {
            return;
        }
        if (!is_array($filter)) {
            $filter = array($this->idColumn => $filter);
        }
        $tmp = $this->getList($field, $filter, 0, 1);
        if(!$tmp)return array();
        reset($tmp);
        $data = current($tmp);

        return $data;
    }

    public function dump($filter, $field = '*')
    {
        return $this->db_dump($filter, $field);
    }

    public function is_serialized($data)
    {
        if (!isset($data) || !is_string($data)) {
            return false;
        }
        $data = trim($data);
        if ('N;' == $data) {
            return true;
        }
        if (!preg_match('/^([adObis]):/', $data, $badions)) {
            return false;
        }
        switch ($badions[1]) {
             case 'a' :
             case 'O' :
             case 's' :
                 if (preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data)) {
                     return true;
                 }
                 break;
             case 'b' :
             case 'i' :
             case 'd' :
                 if (preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data)) {
                     return true;
                 }
                 break;
         }

        return false;
    }
}
