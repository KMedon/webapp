<?php


namespace backend\library;

use backend\library\Route;
use backend\library\View;


//require DOCUMENT_ROOT .  "/config.php";
require __DIR__ .  "/../routes.php";

class App
{
    function showRootNotFoundMessage()
    {
        // helper when developing and calling routes not implemented...
        if (!Route::foundAnyRoute()) {
            
            echo View::showServiceNotFoundMessage();
            //include __DIR__ . "/backend/mvc/layouts/views/route_not_found_html.php";
        }
    }


    public function start() {
        if(Route::foundAnyModule()) {
            (Route::$moduleHandler)();
        }
        if(Route::foundAnyRoute() ) {
            (Route::$routeHandler)();
        } else {
            $this->showRootNotFoundMessage();
        }
    }
}
