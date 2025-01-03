<?php
// musicManager.php

function saveMusicFile($base64Music) {
    $musicFolder = __DIR__ . "/music";
    if (!is_dir($musicFolder)) {
        mkdir($musicFolder, 0777, true);
    }

    // $base64Music = "data:audio/mp3;base64,AAAFBfj42P...."
    $parts = explode(',', $base64Music);
    $decoded = base64_decode($parts[1]);

    // Generate unique name
    $fileName = uniqid('mus_', true) . ".mp3";
    $filePath = $musicFolder . "/" . $fileName;

    file_put_contents($filePath, $decoded);

    return $fileName; // Return just the name
}

function deleteMusicFile($filename) {
    $musicFolder = __DIR__ . "/music";
    $filePath = $musicFolder . "/" . $filename;
    if (file_exists($filePath)) {
        unlink($filePath);
    }
}
