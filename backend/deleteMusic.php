<?php
// deleteMusic.php
header("Content-Type: application/json");
require_once __DIR__ . '/auth.php';
checkAuthenticated(); 

require_once __DIR__ . '/config.php';

try {
    $conn = new PDO("mysql:host=".DATABASE_HOST.";dbname=".DATABASE_NAME, DATABASE_USER, DATABASE_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Typically, you'll pass ?id=123 or in the request body
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) {
        throw new Exception("Missing or invalid id");
    }

    $stmt = $conn->prepare("DELETE FROM music WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['result' => 'SUCCESS', 'message' => 'Music deleted successfully']);
    } else {
        throw new Exception("Failed to delete music.");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'result' => 'ERROR',
        'message' => $e->getMessage()
    ]);
}
