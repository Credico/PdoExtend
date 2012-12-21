<?php

namespace PdoExtend;

class PdoStatementVariableBinder
{

    const NO_MAX_LENGTH = -1;

    public static function bindSql($queryString, array $values, $quoterConnection, $boundParams)
    {
        $sql = $queryString;

        if (count($values) > 0) {
            array_multisort($values, SORT_ASC);
            foreach ($values as $key => $value) {
                $sql = str_replace($key, $quoterConnection->quote($value), $sql);
            }
        }

        if (count($boundParams)) {
            krsort($boundParams);
            foreach ($boundParams as $key => $param) {
                $value = $param['value'];
                if (!is_null($param['type'])) {
                    $value = self::cast($value, $param['type']);
                }
                if ($param['maxlen'] && $param['maxlen'] != self::NO_MAX_LENGTH) {
                    $value = self::truncate($value, $param['maxlen']);
                }
                if (!is_null($value)) {
                    $sql = str_replace($key, $quoterConnection->quote($value), $sql);
                } else {
                    $sql = str_replace($key, 'NULL', $sql);
                }
            }
        }
        return $sql;
    }

    static public function cast($value, $type) {
        switch ($type) {
            case \PDO::PARAM_BOOL:
                return (bool) $value;
                break;
            case \PDO::PARAM_NULL:
                return null;
                break;
            case \PDO::PARAM_INT:
                return (int) $value;
            case \PDO::PARAM_STR:
            default:
                return $value;
        }
    }

    static protected function truncate($value, $length) {
        return substr($value, 0, $length);
    }
}
