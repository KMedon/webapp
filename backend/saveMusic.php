<?php
// saveMusic.php
header("Content-Type: application/json");
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/musicManager.php'; // new

try {
    $conn = new PDO("mysql:host=".DATABASE_HOST.";dbname=".DATABASE_NAME, DATABASE_USER, DATABASE_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // $data might have title, artist, plus a 'fileData' field with base64 MP3
    $title  = $data['title']  ?? '';
    $artist = $data['artist'] ?? '';
    $fileData = $data['fileData'] ?? ''; // base64 from front-end

    if (!$title || !$artist) {
        throw new Exception("Missing required fields: title, artist.");
    }

    // If there's a base64 string for the music
    $musicFileName = null;
    if (!empty($fileData)) {
        $musicFileName = saveMusicFile($fileData); 
    }

    // Store in DB
    $stmt = $conn->prepare("INSERT INTO music (title, artist, file_path) VALUES (:title, :artist, :file_path)");
    $stmt->bindValue(':title', $title);
    $stmt->bindValue(':artist', $artist);
    $stmt->bindValue(':file_path', $musicFileName);

    if ($stmt->execute()) {
        echo json_encode(['result' => 'SUCCESS', 'message' => 'Music saved successfully']);
    } else {
        throw new Exception("Failed to save music.");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['result' => 'ERROR', 'message' => $e->getMessage()]);
}
