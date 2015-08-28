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


class base_db_tools
{
    public static function getinsertsql($table_name, &$data)
    {
        $db = vmc::database();
        if (!$table_name) {
            trigger_error('getinsertsql UNKNOW TABLE_NAME', E_USER_WARNING);

            return false;
        }
        foreach ((array) $data as $key => $value) {
            $data[strtolower($key)] = $value;
        }
        $table_fields = $db->fetch_colum('DESCRIBE '.$table_name, 0);
        $table_fields_type = $db->fetch_colum('DESCRIBE '.$table_name, 1);
        $table_fields = array_combine($table_fields, $table_fields_type);

        $ready_insert_arr = array();
        foreach ($table_fields as $key => $type) {
            if (isset($data[$key])) {
                $ready_insert_arr[$key] = self::quotevalue($db, $data[$key], $type);
            }
        }
        $strValue = implode(',', array_values($ready_insert_arr));
        $strFields = implode('`,`', array_keys($ready_insert_arr));
        $_return  = 'INSERT INTO '.$tableName.' ( `'.$strFields.'` ) VALUES ( '.$strValue.' )';
        return $_return;
    }

    public static function getupdatesql($table_name, &$data, $whereClause)
    {
        $db = vmc::database();
        if (!$table_name) {
            trigger_error('getupdatesql UNKNOW TABLE_NAME', E_USER_WARNING);

            return false;
        }

        foreach ($data as $key => $value) {
            $data[strtolower($key)] = $value;
        }

        $table_fields = $db->fetch_colum('DESCRIBE '.$table_name, 0);
        $table_fields_type = $db->fetch_colum('DESCRIBE '.$table_name, 1);
        $table_fields = array_combine($table_fields, $table_fields_type);

        $ready_update_str_arr = array();

        foreach ($table_fields as $key => $type) {
            if (isset($data[$key])) {
                $ready_update_str_arr[] = '`'.$key.'`='.self::quotevalue($db, $data[$key], $type);
            }
        }

        if (count($ready_update_str_arr) > 0) {
            $update_str = implode(',', $ready_update_str_arr);
            $sql = 'UPDATE '.$table_name.' SET '.$update_str;
            if (strlen($whereClause) > 0) {
                $sql .= ' WHERE '.$whereClause;
            }else{
                logger::warning('全表更新被拦截'.$sql);
                return '';
            }

            return $sql;
        } else {
            return '';
        }
    }

    public static function db_whereClause($queryString)
    {
        preg_match('/\sWHERE\s(.*)/is', $queryString, $whereClause);

        $discard = false;
        if ($whereClause) {
            if (preg_match('/\s(ORDER\s.*)/is', $whereClause[1], $discard)); elseif (preg_match('/\s(LIMIT\s.*)/is', $whereClause[1], $discard)); else {
     preg_match('/\s(FOR UPDATE.*)/is', $whereClause[1], $discard);
 }
        } else {
            $whereClause = array(false,false);
        }

        if ($discard) {
            $whereClause[1] = substr($whereClause[1], 0, strlen($whereClause[1]) - strlen($discard[1]));
        }

        return $whereClause[1];
    }

    public static function filter2sql($filter)
    {
        $where = array('1');
        if ($filter) {
            foreach ($filter as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $m) {
                        if ($m !== '_ANY_' && $m !== '' && $m != '_ALL_') {
                            $ac[] = $k.'=\''.$m.'\'';
                        } else {
                            $ac = array();
                            break;
                        }
                    }
                    if (count($ac) > 0) {
                        $where[] = '('.implode($ac, ' or ').')';
                    }
                } else {
                    $where[] = '`'.$k.'` = "'.str_replace('"', '\\"', $v).'"';
                }
            }
        }

        return implode(' AND ', $where);
    }

    public static function quotevalue(&$db, $value, $valuedef)
    {
        if (null === $value) {
            return 'null';
        }

        switch ($valuedef) {
        case 'bool':
            return '\''.((strtolower($value) != 'false' && $value || (is_int($value) && $value > 0)) ? 'true' : 'false').'\'';
            break;

        case 'real':
        case 'int':
            $value = trim($value);
            if ($value === '') {
                return 'null';
            } else {
                return $value;
            }
            break;
        case 'serialize':
            return $db->quote(serialize($value));
            break;
        default:
            if (is_array($value) || is_object($value)) {
                return $db->quote(serialize($value));
            } else {
                return $db->quote($value);
            }
            break;
        }
    }
}
