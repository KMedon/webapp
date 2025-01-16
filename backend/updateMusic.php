<?php
header("Content-Type: application/json");
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/musicManager.php';
require_once __DIR__ . '/auth.php';
checkAuthenticated(); 


try {
    $conn = new PDO("mysql:host=".DATABASE_HOST.";dbname=".DATABASE_NAME, DATABASE_USER, DATABASE_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (empty($data['id'])) {
        throw new Exception("Missing music ID");
    }

    $id     = (int) $data['id'];
    $title  = $data['title']  ?? '';
    $artist = $data['artist'] ?? '';
    $fileData = $data['fileData'] ?? '';  // base64 string (if user uploaded a new file)

    // 1) Get the old record to see if there's an old file_path
    $stmt = $conn->prepare("SELECT file_path FROM music WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $oldRow = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$oldRow) {
        throw new Exception("Music record not found");
    }
    $oldFilePath = $oldRow['file_path'];

    // 2) If we have new base64 data, save a new file
    $newFilePath = $oldFilePath; // default to old path
    if (!empty($fileData)) {
        // optional: if you want to remove the old file
        if ($oldFilePath) {
            deleteMusicFile($oldFilePath);
        }
        $newFilePath = saveMusicFile($fileData);
    }

    // 3) Update the DB
    $sql = "UPDATE music 
            SET title = :title, artist = :artist, file_path = :file_path
            WHERE id = :id";
    $updateStmt = $conn->prepare($sql);
    $updateStmt->bindValue(':title', $title);
    $updateStmt->bindValue(':artist', $artist);
    $updateStmt->bindValue(':file_path', $newFilePath);
    $updateStmt->bindValue(':id', $id, PDO::PARAM_INT);

    if ($updateStmt->execute()) {
        echo json_encode(['result' => 'SUCCESS', 'message' => 'Music updated successfully']);
    } else {
        throw new Exception("Failed to update music");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['result' => 'ERROR', 'message' => $e->getMessage()]);
}
