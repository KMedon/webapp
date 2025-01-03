<?php
// getMusic.php
header("Content-Type: application/json");
require_once __DIR__ . '/config.php';

try {
    $conn = new PDO("mysql:host=".DATABASE_HOST.";dbname=".DATABASE_NAME, DATABASE_USER, DATABASE_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // e.g. ?id=123
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) {
        throw new Exception("Invalid or missing ID.");
    }

    $stmt = $conn->prepare("SELECT id, title, artist, file_path, created_at FROM music WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $music = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$music) {
        throw new Exception("Music not found.");
    }

    echo json_encode([
        'result' => 'SUCCESS',
        'music' => $music
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['result' => 'ERROR', 'message' => $e->getMessage()]);
}
