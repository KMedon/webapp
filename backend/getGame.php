<?php
// getGame.php
header("Content-Type: application/json");
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

try {
    // checkAuthenticated();

    $conn = new PDO("mysql:host=".DATABASE_HOST.";dbname=".DATABASE_NAME, DATABASE_USER, DATABASE_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) {
        throw new Exception("Invalid or missing ID");
    }

    $stmt = $conn->prepare("SELECT id, title, description, iframe_url FROM games WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $game = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$game) {
        throw new Exception("Game not found.");
    }

    echo json_encode([
        'result' => 'SUCCESS',
        'game' => $game
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['result' => 'ERROR', 'message' => $e->getMessage()]);
}
