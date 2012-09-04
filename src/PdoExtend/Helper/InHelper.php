<?php

namespace PdoExtend\Helper;
use \RangeException;

class InHelper {

    private $baseName;
    private $fields;
    private $type;

    public function __construct($baseName, array $fields,
            $type = \PDO::PARAM_STR) {
        if(count($fields) == 0) {
            throw new RangeException();
        }
        $this->baseName = $baseName;
        $this->fields = array_values($fields);
        $this->type = $type;
    }

    public function getFields() {
        // we think this is a complicated way to do to an implode, please refactor :-)
        $count = count($this->fields);
        $sql = ' (';
        for ($i = ($count - 1);
                $i >= 0;
                $i--) {
            $sql .= ':' . $this->baseName . sprintf("%03d", $i);
            if($i > 0) {
                $sql .= ', ';
            }
        }
        $sql .= ') ';

        return $sql;
    }

    public function getStatement(\PDOStatement $statement) {
        $i = 0;
        foreach ($this->fields as $field) {
            $statement->bindValue(':' . $this->baseName . sprintf("%03d", $i), $field,
                    $this->type);
            $i++;
        }

        return $statement;
    }

}