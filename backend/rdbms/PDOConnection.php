<?php 
namespace backend\rdbms;

// PDOConnection.php

use \PDO;
// use \Exception;

class PDOConnection extends PDO
{
    // these settings are usually stored in an settings.ini file
    public function __construct($host=DATABASE_HOST,$port=DATABASE_PORT, $dbName=DATABASE_NAME)
    {
        $dns = "mysql:host=$host;port=$port;dbname=$dbName;charset=UTF8;";
        parent::__construct($dns, DATABASE_USER, DATABASE_PASSWORD);
        $this ->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );  
    }
}


