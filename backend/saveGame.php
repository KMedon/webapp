<?php
// saveGame.php
header("Content-Type: application/json");
require_once __DIR__ . '/config.php';

try {
    // checkAuthenticated(); // if you want only logged-in users

    $conn = new PDO("mysql:host=".DATABASE_HOST.";dbname=".DATABASE_NAME, DATABASE_USER, DATABASE_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (empty($data['title']) || empty($data['iframe_url'])) {
        throw new Exception("Missing required fields (title, iframe_url).");
    }

    $title       = $data['title'];
    $description = $data['description'] ?? '';
    $iframeUrl   = $data['iframe_url'];

    $stmt = $conn->prepare("INSERT INTO games (title, description, iframe_url) VALUES (:title, :description, :iframe_url)");
    $stmt->bindValue(':title', $title);
    $stmt->bindValue(':description', $description);
    $stmt->bindValue(':iframe_url', $iframeUrl);

    if ($stmt->execute()) {
        echo json_encode(['result' => 'SUCCESS', 'message' => 'Game saved successfully']);
    } else {
        throw new Exception("Failed to save game.");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['result' => 'ERROR', 'message' => $e->getMessage()]);
}
