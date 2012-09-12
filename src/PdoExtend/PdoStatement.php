<?php

namespace PdoExtend;

use PdoExtend\Exception;

class PdoStatement extends \PDOStatement implements \Countable {

    protected $connection;
    protected $bound_params = array();
    const NO_MAX_LENGTH = -1;

    private function __construct(\PDO $connection) {
        $this->connection = $connection;
    }

    public function count() {
        $statement = $this->connection->query('SELECT count(*) FROM (' . $this->getSql() . ') AS tmp_count_query');
        return $statement->fetchColumn();
    }

    public function bindParam($paramno, &$param, $type = \PDO::PARAM_STR, $maxlen = null, $driverdata = null) {
        $this->bound_params[$paramno] = array(
            'value' => &$param,
            'type' => $type,
            'maxlen' => (is_null($maxlen)) ? self::NO_MAX_LENGTH : $maxlen,
                // ignore driver data
        );

        $result = parent::bindParam($paramno, $param, $type, $maxlen, $driverdata);
    }

    public function bindValue($parameter, $value, $data_type = \PDO::PARAM_STR) {
        $this->bound_params[$parameter] = array(
            'value' => $value,
            'type' => $data_type,
            'maxlen' => self::NO_MAX_LENGTH
        );
        parent::bindValue($parameter, $value, $data_type);
    }

    public function getSql($values = array()) {
        return PdoStatementVariableBinder::bindSql($this->queryString, $values, $this->connection, $this->bound_params);
    }

    public function execute($input_parameters = null) {
        try {
            return parent::execute($input_parameters);
        } catch (\PDOException $e) {
            throw new Exception\QueryException($this->getSql(), $e->getMessage(), $e->getCode(), $e);
        }
    }

}