<?php
// updateGame.php
header("Content-Type: application/json");
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

try {
    // checkAuthenticated();

    $conn = new PDO("mysql:host=".DATABASE_HOST.";dbname=".DATABASE_NAME, DATABASE_USER, DATABASE_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (empty($data['id']) || empty($data['title']) || empty($data['iframe_url'])) {
        throw new Exception("Missing required fields (id, title, iframe_url).");
    }

    $id          = (int) $data['id'];
    $title       = $data['title'];
    $description = $data['description'] ?? '';
    $iframeUrl   = $data['iframe_url'];

    $stmt = $conn->prepare("UPDATE games SET title = :title, description = :description, iframe_url = :iframe_url WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':title', $title);
    $stmt->bindValue(':description', $description);
    $stmt->bindValue(':iframe_url', $iframeUrl);

    if ($stmt->execute()) {
        echo json_encode(['result' => 'SUCCESS', 'message' => 'Game updated successfully']);
    } else {
        throw new Exception("Failed to update game.");
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['result' => 'ERROR', 'message' => $e->getMessage()]);
}
