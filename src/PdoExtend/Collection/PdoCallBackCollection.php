<?php

namespace PdoExtend\Collection;

/** @deprecated Use ImmediatePdoCallBackCollection instead */
class PdoCallBackCollection extends AbstractCollection implements \Countable {

    private $pdoStatement;
    private $callBack;
    private $count = null;

    public function __construct(\PDOStatement $pdoStatement, $callBack) {
        $this->pdoStatement = $pdoStatement;
        $this->callBack = $callBack;
    }

    public function rewind() {
        $this->pdoStatement->execute();

        parent::rewind();
    }

    protected function fetch() {
        $row = $this->pdoStatement->fetch(\PDO::FETCH_ASSOC);
        if($row === false ) {
            return false;
        } else {
            return \call_user_func_array($this->callBack, array($row));
        }
    }

    
    public function count() {
        if($this->count === null) {
            $this->count = $this->pdoStatement->count();
        }
        return $this->count;
    }
}