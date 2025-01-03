<?php
// videoManager.php

function saveVideo($base64Video) {
    $videoFolder = __DIR__ . "/videos"; 
    if (!is_dir($videoFolder)) {
        mkdir($videoFolder, 0777, true);
    }

    // Split the base64 data to isolate the actual video bytes
    // Example: "data:video/mp4;base64,iVBORw0KGgoAAA..."
    $videoData = explode(',', $base64Video);
    $decodedVideo = base64_decode($videoData[1]);

    // Generate a unique filename (e.g., vid_12345.mp4)
    // You might want to parse the MIME type to decide on file extension (e.g., mp4, webm, etc.)
    $fileName = uniqid('vid_', true) . ".mp4";
    $filePath = $videoFolder . "/" . $fileName;

    // Save the decoded video data to disk
    file_put_contents($filePath, $decodedVideo);

    return $fileName;
}

function deleteVideo($videoID) {
    $videoFolder = __DIR__ . "/videos";
    if (!is_dir($videoFolder)) {
        mkdir($videoFolder, 0777, true);
    }

    $filePath = $videoFolder . "/" . $videoID;
    if (file_exists($filePath)) {
        unlink($filePath);
    }
}

function getVideo($videoID) {
    $videoFolder = __DIR__ . "/videos";
    $filePath = $videoFolder . "/" . $videoID;

    if (file_exists($filePath)) {
        $fileContents = file_get_contents($filePath);
        // Return base64 version of the video
        $base64Video = base64_encode($fileContents);
        return 'data:video/mp4;base64,' . $base64Video;
    } else {
        return null;
    }
}
