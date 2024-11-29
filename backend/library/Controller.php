<?php

namespace backend\library;



abstract class Controller {

    const INSERT = "INSERT";
    const SEE    = "SEE";
    const UPDATE = "UPDATE";
    const DELETE = "DELETE";


    static public $controllerFolder = null;
    static private $_defaultModel = null; 

    public function __construct() {
        //echo __DIR__;
    }    

    final public static function getDefaultModel(){
        if(null !== static::$_defaultModel){
            return static::$_defaultModel;
        }
        static::$_defaultModel =  new Model();
        return static::$_defaultModel;
    }

    
}
