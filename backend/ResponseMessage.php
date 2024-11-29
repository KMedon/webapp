<?php

enum RequestOperation: string {
    case  SUCCESS =  "SUCCESS";
    case  ERROR   =  "ERROR";
    
    case  SELECT  =  "SELECT";
    case  UPDATE  =  "UPDATE";
    case  DELETE  =  "DELETE";
    case  INSERT  =  "INSERT";
    case  QUERY   =  "QUERY";

    case  ROUTE   =  "ROUTE";  
    case  LOGIN   =  "LOGIN";  
}


enum RequestResult: string {
    case  SUCCESS =  "SUCCESS";
    case  ERROR   =  "ERROR";
    
    case  SELECT  =  "SELECT";
    case  UPDATE  =  "UPDATE";
    case  DELETE  =  "DELETE";
    case  INSERT  =  "INSERT";
    case  QUERY   =  "QUERY";

    case  ROUTE   =  "ROUTE";  
    case  LOGIN   =  "LOGIN";  
}




class ResponseMessage {
    public $subject;
    public $body;
    public $requestOperation;
    public $requestResult;
    public $alertMessage;

    static function responseFactory($subject, $body, RequestOperation $requestOperation= null, $alertMessage=null, RequestResult $requestResult = null) {
        $response = new ResponseMessage();
        $response->subject = $subject;
        $response->body   = $body;
        $response->requestOperation = $requestOperation;
        $response->requestResult = $requestResult;
        $response->alertMessage = $alertMessage;
    }

    function toJson() {                
        return json_encode($this);
    }

}