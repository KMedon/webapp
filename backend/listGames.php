<?php
// listGames.php
header("Content-Type: application/json");
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php'; // for user authentication check (optional, see below)

try {
    // Ensure user is authenticated (if required)
    // checkAuthenticated(); // example function from auth.php

    // Connect to DB
    $conn = new PDO("mysql:host=".DATABASE_HOST.";dbname=".DATABASE_NAME, DATABASE_USER, DATABASE_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Pagination & sorting
    $first     = isset($_GET['first']) ? (int)$_GET['first'] : 0;
    $rows      = isset($_GET['rows'])  ? (int)$_GET['rows']  : 10;
    $sortField = isset($_GET['sortField']) && $_GET['sortField'] !== '' ? $_GET['sortField'] : 'title';
    $sortOrder = isset($_GET['sortOrder']) && $_GET['sortOrder'] !== '' ? $_GET['sortOrder'] : 'ASC';

    // Filters
    $json       = file_get_contents('php://input');
    $filterData = json_decode($json, true);  // e.g. { "title": "Chess" }

    $whereClause = "WHERE 1=1";
    if (!empty($filterData['title'])) {
        $whereClause .= " AND title LIKE :title";
    }

    // Count total
    $countSql = "SELECT COUNT(*) as total FROM games $whereClause";
    $countStmt = $conn->prepare($countSql);
    if (!empty($filterData['title'])) {
        $countStmt->bindValue(':title', '%'.$filterData['title'].'%', PDO::PARAM_STR);
    }
    $countStmt->execute();
    $totalRow = $countStmt->fetch(PDO::FETCH_ASSOC);
    $totalRecords = $totalRow ? (int)$totalRow['total'] : 0;

    // Fetch rows
    $sql = "SELECT id, title, description, iframe_url, created_at
            FROM games
            $whereClause
            ORDER BY $sortField $sortOrder
            LIMIT :first, :rows";

    $stmt = $conn->prepare($sql);
    if (!empty($filterData['title'])) {
        $stmt->bindValue(':title', '%'.$filterData['title'].'%', PDO::PARAM_STR);
    }
    $stmt->bindValue(':first', $first, PDO::PARAM_INT);
    $stmt->bindValue(':rows', $rows, PDO::PARAM_INT);

    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'result' => 'SUCCESS',
        'data' => $data,
        'totalRecords' => $totalRecords
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'result' => 'ERROR',
        'message' => $e->getMessage()
    ]);
}
