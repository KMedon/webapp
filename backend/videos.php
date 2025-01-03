<?php
// videos.php

require_once __DIR__ . '/config.php';

// 1) Connect to DB
$conn = new PDO("mysql:host=".DATABASE_HOST.";dbname=".DATABASE_NAME, DATABASE_USER, DATABASE_PASSWORD);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 2) Read GET params for pagination/sorting
$first     = isset($_GET['first'])     ? (int)$_GET['first']     : 0;
$rows      = isset($_GET['rows'])      ? (int)$_GET['rows']      : 10;
$sortField = isset($_GET['sortField']) && $_GET['sortField'] !== '' 
               ? $_GET['sortField'] 
               : 'title';  // fallback column

$sortOrder = isset($_GET['sortOrder']) && $_GET['sortOrder'] !== '' 
               ? $_GET['sortOrder'] 
               : 'ASC';

// 3) Read POST body for filters (search, etc.)
$json       = file_get_contents('php://input');
$filterData = json_decode($json, true);  // e.g. { title: 'beatles' }

// 4) Build WHERE clause if you want search
$whereClause = "WHERE 1=1";
if (!empty($filterData['title'])) {
    $whereClause .= " AND title LIKE :title";
}
if (!empty($filterData['artist'])) {
    $whereClause .= " AND artist LIKE :artist";
}

// 5) Count total
$countSql = "SELECT COUNT(*) as total FROM videos $whereClause";
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

// 6) Fetch actual rows
$sql = "SELECT id, title, artist, url, created_at
        FROM videos
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

// 7) Return JSON
echo json_encode([
    'result'       => 'SUCCESS',
    'data'         => $data,
    'totalRecords' => $totalRecords
]);
