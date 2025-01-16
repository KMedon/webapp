<?php
// updateVideos.php (UPDATE example)

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
checkAuthenticated(); 


try {
    $conn = new PDO("mysql:host=".DATABASE_HOST.";dbname=".DATABASE_NAME, DATABASE_USER, DATABASE_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (empty($data['id']) || empty($data['title']) || empty($data['artist']) || empty($data['url'])) {
        throw new Exception("Missing required field(s).");
    }

    $id     = $data['id'];
    $title  = $data['title'];
    $artist = $data['artist'];
    $url    = $data['url'];

    $stmt = $conn->prepare("UPDATE videos SET title = :title, artist = :artist, url = :url WHERE id = :id");
    $stmt->bindValue(':title',  $title);
    $stmt->bindValue(':artist', $artist);
    $stmt->bindValue(':url',    $url);
    $stmt->bindValue(':id',     $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['result' => 'SUCCESS', 'message' => 'Video updated successfully']);
    } else {
        throw new Exception("Failed to update video.");
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['result' => 'ERROR', 'message' => $e->getMessage()]);
}
