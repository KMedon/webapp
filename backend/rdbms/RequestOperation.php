<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of RequestOperation
 *
 * @author abstr
 */

namespace backend\rdbms;


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
