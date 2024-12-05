<?php
require_once __DIR__ . '/PDOConnection.php';

function listEntities($table, $fields = ["*"], $first = 0, $rows = 10, $sortField = '', $sortOrder = 'ASC', $filterData = null) {
    $pdo = getPDOConnection();
    $commaDelimitedFields = implode(", ", $fields);

    $sql = "SELECT $commaDelimitedFields FROM $table";
    $filterConditions = buildFilterConditions($filterData);
    $sql .= " " . $filterConditions;        
    if (!empty($sortField)) {
        $sql .= " ORDER BY $sortField $sortOrder";
    }
    $sql .= " LIMIT :rows OFFSET :first";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':rows', $rows, PDO::PARAM_INT);
    $stmt->bindParam(':first', $first, PDO::PARAM_INT);

    performFilterBindings($stmt, $filterData);
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

function buildFilterConditions($filter = null) {
    // Start with a default true condition to easily append filters
    $whereConditions = "WHERE (true) ";  
    if ($filter && is_array($filter)) {
        foreach ($filter as $field => $condition) {
            $value = $condition['value'];
            $matchMode = $condition['matchMode'];

            if ($value !== null && $value !== '') {
                // Handle different match modes
                switch ($matchMode) {
                    case 'equals':
                        $whereConditions .= " AND $field = :$field";
                        break;
                    case 'startsWith':
                        $whereConditions .= " AND $field LIKE :$field";
                        break;
                    case 'contains':
                        $whereConditions .= " AND $field LIKE :$field";
                        break;
                    case 'endsWith':
                        $whereConditions .= " AND $field LIKE :$field";
                        break;
                    // Add more match modes as necessary
                    default:
                        break;
                }
            }
        }
    }
    return $whereConditions;
}

function performFilterBindings(&$sqlStatement, $filter = null) {
    if ($filter && is_array($filter)) {
        foreach ($filter as $field => $condition) {
            $value = $condition['value'];

            // Only bind non-null and non-empty values
            if ($value !== null && $value !== '') {
                // Use different bindings depending on match mode
                switch ($condition['matchMode']) {
                    case 'equals':
                        $sqlStatement->bindParam(":$field", $value, PDO::PARAM_STR);
                        break;
                    case 'startsWith':
                        $startsWithValue = $value . '%';
                        $sqlStatement->bindParam(":$field", $startsWithValue, PDO::PARAM_STR);
                        break;
                    case 'contains':
                        $containsValue = '%' . $value . '%';
                        $sqlStatement->bindParam(":$field", $containsValue, PDO::PARAM_STR);
                        break;
                    case 'endsWith':
                        $endsWithValue = '%' . $value;
                        $sqlStatement->bindParam(":$field", $endsWithValue, PDO::PARAM_STR);
                        break;                    
                }
            }
        }
    }
}
