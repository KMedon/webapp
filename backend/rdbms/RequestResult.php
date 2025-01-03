<?php

namespace backend\rdbms;

use backend\rdbms\PDOConnection;
use PDOStatement;
use PDO;


class RequestResult {

    public $operation;
    public $result     = "nil";
    public $id         = "-1";

    public $idName     = "nil";
    public $idValue    = "-1";

    public $rowCount   = "-1";
    public $data       =  array(); 
    public $msg        = "nil";
    public $resultTypes= [];  

    public $customData=[];
    
    function __construct()
    {
        $this->resultTypes = [
            "SUCCESS" => "SUCCESS",
            "ERROR" => "ERROR"
        ];
    }

    static function requestSUCCESS(RequestOperation $operation, PDOConnection $pdoConnection=null, PDOStatement $statement=null, String $msg)  {
        
        $sqlResult = new RequestResult();
        $sqlResult->operation = $operation;
        $sqlResult->result    = "SUCCESS";
        if($pdoConnection!=null) {
            $sqlResult->id        = $pdoConnection->lastInsertId();
        }
        if($statement!= null) {
            $sqlResult->rowCount  = $statement->rowCount();
        }    
        if($statement!= null) {
            $sqlResult->data      = $statement->fetchAll(PDO::FETCH_ASSOC);
        }         
        $sqlResult->msg       = $msg;
        return $sqlResult;
    }

    static function requestERROR(RequestOperation $operation, String $errorSmg)  {
        $reqResult = new RequestResult();
        $reqResult->operation    = $operation;
        $reqResult->result       = "ERROR";
        $reqResult->msg          = $errorSmg;
        return $reqResult;
    }

    static function requestGENERIC(RequestOperation $operation, $result, String $msg)  {
        $reqResult = new RequestResult();
        $reqResult->operation    = $operation;
        $reqResult->result       = $result;
        $reqResult->msg          = $msg;
        return $reqResult;
    }
    
    function getData() {
       
        return $this->data;
    }
    
    function each($callback) : RequestResult{
       forEach($this->data as $key => $value){
           $callback($key, $value);
       }
       return $this;
    }



    public function filterOutput($fieldsToRender = null) {        
        if ($fieldsToRender != null && isset($this->data) && is_array($this->data)) {
            if ($fieldsToRender != null && isset($this->data) && is_array($this->data)) {
                // Iterate over each element in the data array
                foreach ($this->data as &$item) {
                    // Keep only the fields that are in $fieldsToRender
                    $item = array_intersect_key($item, array_flip($fieldsToRender));
                }
            }
        }
    }


    function toJsonEcho($fieldsToRender = null) {
        
        $this->filterOutput($fieldsToRender);
        // TO avoid having  RequestOperation inside JSON
        $this->operation  = '' . $this->operation->value;                
        header('Content-Type: application/json;charset=iso-8859-1');        
        echo json_encode($this);
    }
    

    function toJson($fieldsToRender = null) {
        $this->filterOutput($fieldsToRender);
        // TO avoid having  RequestOperation inside JSON
        $this->operation  = '' . $this->operation->value;                
        
        return json_encode($this);
    }
    
}



// RequestResult::requestGENERIC(RequestOperation::ROUTE, "SUCCESS", "Demo Request Result")->toJsonEcho();
