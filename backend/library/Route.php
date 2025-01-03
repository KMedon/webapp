<?php

namespace backend\library;

use backend\rdbms\RequestResult;
use backend\rdbms\RequestOperation;

class Route
{
    public static $foundTheRoute = false;
    public static $foundTheModule = false;

    public static $moduleHandler  = null;
    public static $moduleName     = null;
    public static $serviceName    = null;
    public static $routeHandler = null;

    public static $payload = null; // to grab json payload from php::input
    
    

    static public function foundAnyRoute() {
        return (Route::$foundTheRoute );
    }

    static public function foundAnyModule() {
        return Route::$foundTheModule;
    }


    static public function isValidParameter($parameter) {
        if (is_string($parameter) && preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $parameter))  {
            return true;
        }
        return false;
    }

    // & meains passing by reference (it is like a pointer in C language)
    static public function makeRoute(array &$routes)
    {
        // get the service from the request
        if (isset($_REQUEST["service"])) {
            $service    = @$_REQUEST["service"];
            $controller = @$routes[$service];

            if($controller == null) {
                $msg = "You must add the service <strong>$service</strong> to the route table inside app.php"; 
                $controller = function () use ($msg) {
                    RequestResult::requestERROR(RequestOperation::ROUTE, $msg)->toJsonEcho();
                };
            }
        } else {
            $controller = function () {
                $msg = "You must request a service that is specified inside the routes table, as <strong>?service=someService</strong>";
                RequestResult::requestERROR(RequestOperation::ROUTE, $msg)->toJsonEcho();
            };
        }
        return $controller;
    }

    static public function route($service, $handler) {
        if (  $service == @$_REQUEST["service"]  ) {
            foreach($_REQUEST as $key => $value) {
                if(Route::isValidParameter($key)) {
                    ${$key} = $value;  // dynamically create variable to echo error message below.
                }else {
                    if(isset($_REQUEST["module"])) {
                       echo "<h2>Module: " .  $_REQUEST["module"] . "</h2>";
                    }
                    echo "<h2>Service: $service, the parameter $key is invalid...";
                    die;
                }
            }            
            Route::$foundTheRoute = true;
            Route::$routeHandler =  $handler;             
        }
        Route::grabPayload();        
    }


    static public function grabPayload() {
        // Get the content type from the request headers
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    
        // Check if the content type is JSON
        if (strpos($contentType, 'application/json') !== false) {
            // Read the raw input stream
            $json = file_get_contents('php://input');
            
            // Decode the JSON into an associative array
            static::$payload = json_decode($json, true);
            
            // Optionally, handle JSON decoding errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Handle the error (e.g., log it or return an error response)
                static::$payload = null; // or handle as needed
            }
        }
    }
    

    static public function web($service, $handler) {
        Route::route($service, $handler);
    }

    static public function routesOfModule($module,  $handler) {
        if($module ==  @$_REQUEST["module"] ) {            
            Route::$foundTheModule = true;
            // vai visitar o handler que contem as rotas dos serviços
            Route::$moduleHandler = $handler;                                    
        }
    }
}
