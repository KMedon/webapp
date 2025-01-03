<?php

// Include necessary files
require __DIR__ . '/config.php';  // Assuming this is where your DB connection and configurations are
require __DIR__ . '/routes.php';  // Include the routes

use backend\library\Route;

// Handle the incoming request
$service = isset($_REQUEST['service']) ? $_REQUEST['service'] : null;

if ($service) {
    // Define available routes
    $routes = [
        'music' => function() { require 'music.php'; },
        'videos' => function() { require 'videos.php'; },
        'documents' => function() { require 'documents.php'; }
    ];

    // Check if the requested service has a route
    $controller = Route::makeRoute($routes);  // Pass the $routes array by reference

    if ($controller) {
        $controller();  // Call the appropriate controller (like music.php, videos.php, etc.)
    } else {
        echo json_encode(["error" => "Service not found"]);
    }
} else {
    echo json_encode(["error" => "No service parameter provided"]);
}
