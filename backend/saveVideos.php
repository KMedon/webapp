<?php
// saveVideos.php (INSERT example)

require_once __DIR__ . '/config.php';

try {
    $conn = new PDO("mysql:host=".DATABASE_HOST.";dbname=".DATABASE_NAME, DATABASE_USER, DATABASE_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Basic validation
    if (empty($data['title']) || empty($data['artist']) || empty($data['url'])) {
        throw new Exception("Missing required field(s).");
    }

    $title  = $data['title'];
    $artist = $data['artist'];
    $url    = $data['url'];

    $stmt = $conn->prepare("INSERT INTO videos (title, artist, url) VALUES (:title, :artist, :url)");
    $stmt->bindValue(':title',  $title);
    $stmt->bindValue(':artist', $artist);
    $stmt->bindValue(':url',    $url);

    if ($stmt->execute()) {
        echo json_encode(['result' => 'SUCCESS', 'message' => 'Video saved successfully']);
    } else {
        throw new Exception("Failed to save video.");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['result' => 'ERROR', 'message' => $e->getMessage()]);
}
