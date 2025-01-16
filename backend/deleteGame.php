<?php
// deleteGame.php
header("Content-Type: application/json");
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/auth.php';
checkAuthenticated(); 


try {
    // checkAuthenticated();

    $conn = new PDO("mysql:host=".DATABASE_HOST.";dbname=".DATABASE_NAME, DATABASE_USER, DATABASE_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if ($id <= 0) {
        throw new Exception("Missing or invalid ID.");
    }

    $stmt = $conn->prepare("DELETE FROM games WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['result' => 'SUCCESS', 'message' => 'Game deleted successfully']);
    } else {
        throw new Exception("Failed to delete game.");
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'result' => 'ERROR',
        'message' => $e->getMessage()
    ]);
}
