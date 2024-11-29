<?php
require_once __DIR__ . '/PDOConnection.php';

function findEntity($table, $fields = ["*"], $searchCriteria = []) {
    $pdo = getPDOConnection();

    $commaDelimitedFields = implode(", ", $fields);
    $sql = "SELECT $commaDelimitedFields FROM $table";
    
    if (!empty($searchCriteria)) {
        $sql .= " WHERE ";
        $conditions = [];
        foreach ($searchCriteria as $key => $condition) {
            $operator = $condition[0]; // Operator (e.g., =, LIKE)
            $conditions[] = "$key $operator :$key";
        }
        $sql .= implode(" AND ", $conditions);
    }

    try {
        $stmt = $pdo->prepare($sql);

        // Bind parameters dynamically based on their type
        foreach ($searchCriteria as $key => $condition) {
            $value = $condition[1];
            $paramType = isset($condition[2]) ? $condition[2] : PDO::PARAM_STR; // Default to string
            $stmt->bindValue(":$key", $value, $paramType);
        }

        if ($stmt->execute()) {
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                "result" => "SUCCESS",
                "data" => $data,
                "statusCode" => 200
            ];
        } else {
            return [
                "result" => "ERROR",
                "message" => "Could not fetch entity",
                "statusCode" => 500
            ];
        }
    } catch (PDOException $e) {
        return [
            "result" => "ERROR",
            "message" => "Database error: " . $e->getMessage(),
            "statusCode" => 500
        ];
    }
}
