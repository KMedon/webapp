<?php

use backend\library\Route;

// Define a route for fetching music data
Route::web('music', function() {
    require 'music.php';  // Fetch music data
});

// Define a route for fetching videos data
Route::web('videos', function() {
    require 'videos.php';  // Fetch video data
});

// Define a route for fetching document data
Route::web('documents', function() {
    require 'documents.php';  // Fetch document data
});
