<?php
require_once __DIR__ . '/PDOConnection.php';

function listEntities($table, $fields = ["*"], $first = 0, $rows = 10, $sortField = '', $sortOrder = 'ASC') {
    $pdo = getPDOConnection();
    $commaDelimitedFields = implode(", ", $fields);

    $sql = "SELECT $commaDelimitedFields FROM $table";
    if (!empty($sortField)) {
        $sql .= " ORDER BY $sortField $sortOrder";
    }
    $sql .= " LIMIT :rows OFFSET :first";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':rows', $rows, PDO::PARAM_INT);
    $stmt->bindParam(':first', $first, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $countSql = "SELECT COUNT(*) AS total FROM $table";
        $countStmt = $pdo->query($countSql);
        $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        return ["result" => "SUCCESS", "data" => $data, "totalRecords" => $totalRecords, "statusCode" => 200];
    } else {
        return ["result" => "ERROR", "message" => "Failed to list entities", "statusCode" => 500];
    }
}
