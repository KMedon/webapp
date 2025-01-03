<?php
// music.php

require_once __DIR__ . '/config.php';

// Connectin to Database
$conn = new PDO("mysql:host=".DATABASE_HOST.";dbname=".DATABASE_NAME, DATABASE_USER, DATABASE_PASSWORD);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Reading GET paramateres for sorting
$first     = isset($_GET['first'])     ? (int)$_GET['first']     : 0;
$rows      = isset($_GET['rows'])      ? (int)$_GET['rows']      : 10;
$sortField = isset($_GET['sortField']) && $_GET['sortField'] !== '' 
               ? $_GET['sortField'] 
               : 'title';  

$sortOrder = isset($_GET['sortOrder']) && $_GET['sortOrder'] !== '' 
               ? $_GET['sortOrder'] 
               : 'ASC';

// Read POST body for filters 
$json       = file_get_contents('php://input');
$filterData = json_decode($json, true);  // e.g. { title: 'beatles' }

// Building WHERE clause if you want search
$whereClause = "WHERE 1=1";
if (!empty($filterData['title'])) {
    $whereClause .= " AND title LIKE :title";
}
if (!empty($filterData['artist'])) {
    $whereClause .= " AND artist LIKE :artist";
}

$countSql = "SELECT COUNT(*) as total FROM music $whereClause";
$countStmt = $conn->prepare($countSql);
if (!empty($filterData['title'])) {
    $countStmt->bindValue(':title', '%'.$filterData['title'].'%');
}
if (!empty($filterData['artist'])) {
    $countStmt->bindValue(':artist', '%'.$filterData['artist'].'%');
}
$countStmt->execute();
$totalRow = $countStmt->fetch(PDO::FETCH_ASSOC);
$totalRecords = $totalRow ? $totalRow['total'] : 0;

// Fetching actual rows
$sql = "SELECT id, title, artist, file_path, created_at
        FROM music
        $whereClause
        ORDER BY $sortField $sortOrder
        LIMIT :first, :rows";

$stmt = $conn->prepare($sql);

// Bind the search if present
if (!empty($filterData['title'])) {
    $stmt->bindValue(':title', '%'.$filterData['title'].'%', PDO::PARAM_STR);
}
if (!empty($filterData['artist'])) {
    $stmt->bindValue(':artist', '%'.$filterData['artist'].'%', PDO::PARAM_STR);
}
$stmt->bindValue(':first', $first, PDO::PARAM_INT);
$stmt->bindValue(':rows', $rows, PDO::PARAM_INT);

$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// JSON
echo json_encode([
    'result'       => 'SUCCESS',
    'data'         => $data,
    'totalRecords' => $totalRecords
]);
