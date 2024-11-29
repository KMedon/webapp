<?php
// Model.php
namespace backend\library;

use backend\rdbms\PDOConnection;

class Model {
    static private $_pdoConnection = null; 

    public function __construct()
    {
        
    }

    final public static function getPdoConnection() : PDOConnection{
        if(null !== static::$_pdoConnection){
            return static::$_pdoConnection;
        }
        static::$_pdoConnection =  new PDOConnection();
        return static::$_pdoConnection;
    }

    final public static function beginTransaction(){
        static::getPdoConnection()->beginTransaction();
    }

    final public static function rollBack(){
        static::getPdoConnection()->rollBack();
    }

    final public static function commit(){
        static::getPdoConnection()->commit();
    }
}
