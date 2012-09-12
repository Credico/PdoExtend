<?php

namespace PdoExtend\Tests\Collection;

use PdoExtend\Collection\PdoCallBackCollection;
use PdoExtend\PdoStatementVariableBinder;
use PdoExtend\Collection\ReIterator;

class PdoStatementTest extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function BindNotDependingOnSorting()
    {

        $this->assertEquals("SELECT * FROM facturen WHERE bla = '10' AND sla = '20'", PdoStatementVariableBinder::bindSql("SELECT * FROM facturen WHERE bla = :portfolio1 AND sla = :portfolio10",
            array(),new MyQuoterConnection(), array(
                ":portfolio1" => array('value' =>'10', 'type'=> \PDO::PARAM_INT),
                ":portfolio10" => array('value' =>'20', 'type'=> \PDO::PARAM_INT))));
    }
}

class MyQuoterConnection
{
    public function quote($q)
    {
        return "'$q'";
    }
}