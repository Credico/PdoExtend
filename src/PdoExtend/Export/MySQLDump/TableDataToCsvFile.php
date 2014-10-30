<?php

namespace PdoExtend\Export\MySQLDump;

use PdoExtend\Structure\TableInterface;
use PdoExtend\Export\ExportTableToFileInterface;

class TableDataToCsvFile implements ExportTableToFileInterface {
    private $database;
    private $username;
    private $password;
    private $mysqlDumpPath;
    private $tmpDir;
    
    public function __construct($username, $password, $database, $mysqlDumpPath = '', $tmpDir = null) {
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->mysqlDumpPath = $mysqlDumpPath;
        $this->tmpDir = $tmpDir ?: sys_get_temp_dir().'/'.$database;
    }

    public function export(TableInterface $table, $toDirectory, $baseFileName = null) {
        if ($baseFileName === null) {
            $baseFileName = $table->getName().'-data';
        }

        @mkdir($this->tmpDir, 0777, true);
        chmod($this->tmpDir, 0777);

	    $command = 'mysqldump -u' . $this->username . 
			' -p' . quotemeta($this->password) . ' ' . 
			$this->database . ' ' . 
			$table . 
			' --fields-terminated-by=, ' . 
			' --fields-enclosed-by=\'"\' ' . 
			' --fields-escaped-by="\\\\" ' . 
			'--no-create-info ' . 
			'--tab ' . $this->tmpDir;

	    $output = array();
	    $return = null;
        exec($command, $output, $return);
	    if($return) {
		    throw new \Exception('mysql dump failed: '.implode(PHP_EOL, $output));
	    }

        exec('cat ' . $this->tmpDir . '/' . $table . '.txt > ' . $toDirectory . '/' . $baseFileName . '.csv && rm '. $this->tmpDir . '/' . $table . '.txt');
        file_put_contents($toDirectory . '/' . $baseFileName . '.sql',
                $this->getCsvLoad($baseFileName . '.csv', $table,
                        array_keys(iterator_to_array($table, true))));
    }

    private function getCsvLoad($fileToLoad, $table, array $columns) {
        $columnsString = implode('`, `', $columns);
        $str = <<<EOF
SET SQL_LOG_BIN=0;
SET AUTOCOMMIT=0;
SET UNIQUE_CHECKS=0;
SET FOREIGN_KEY_CHECKS=0;

LOAD DATA 
    LOCAL INFILE '$fileToLoad' 
    IGNORE 
    INTO TABLE `$table`
    FIELDS TERMINATED BY ','
	ENCLOSED BY '"'
    LINES TERMINATED BY '\\n' 
    (`$columnsString`);

SET FOREIGN_KEY_CHECKS=1;
SET UNIQUE_CHECKS=1;
SET AUTOCOMMIT=1;
COMMIT;
SET SQL_LOG_BIN=1;

EOF;

        return $str;
    }

}
