<?php
use backend\library\Route;

if (isset($_REQUEST["module"])) {
    if (!Route::foundAnyModule()) {
        $the_module = $_REQUEST['module'];
        echo "<H2>Module not found: <span style=\"color: green; background-color:yellow\">$the_module</span></H2>";
        die;
    }
}



if (isset($_REQUEST["service"])) {
    if (!Route::foundAnyRoute()) {
        $the_service = $_REQUEST['service'];
        $the_module = @$_REQUEST['module'];
        echo "<H2>Service not found: <span style=\"color: green; background-color:yellow\">$the_service</span></H2>";
        echo "<H2>Route: <span style=\"color: green; background-color:yellow\">$the_module</span></H2>";
        die;
    }
}


if (!isset($_REQUEST["service"])  && !isset($_REQUEST["module"])) {    
    echo "<H2>No module specified</h2>";    
}


if (!isset($_REQUEST["service"])) {
    echo "<H2>No service not specified</h2>";
}

