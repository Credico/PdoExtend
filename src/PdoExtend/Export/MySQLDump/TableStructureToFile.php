<?php

namespace PdoExtend\Export\MySQLDump;

use PdoExtend\Export\ExportTableToFileInterface;
use PdoExtend\Structure\TableInterface;

class TableStructureToFile implements ExportTableToFileInterface {
    private $database;
    private $username;
    private $password;
    private $mysqlDumpPath;
    
    public function __construct($username, $password, $database, $mysqlDumpPath = '') {
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->mysqlDumpPath = $mysqlDumpPath;
    }

    public function export(TableInterface $table, $toDirectory, $baseFileName = null) {
        if($baseFileName === null) {
            $baseFileName = $table->getName().'-structure.sql';
        }

	    $command = $this->mysqlDumpPath.'mysqldump -u' . $this->username . ' -p' . quotemeta($this->password) . ' ' . $this->database . ' ' . $table . ' --no-data --create-options --disable-keys --lock-tables --skip-add-drop-table --skip-comments  > "' . $toDirectory . '/' . $baseFileName . '"';
	    $output = array();
		$return = null;
        exec($command, $output, $return);

	    if($return) {
		    throw new \Exception('mysql dump failed: '.implode(PHP_EOL, $output));
	    }
    }

}