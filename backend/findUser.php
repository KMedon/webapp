<?php
require_once __DIR__ . "/findEntity.php";

$table = "users";
$fields = ["id", "name", "email", "password", "address", "city", "born_date", "user_role"];

// Get user ID from the query string
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Define search criteria for finding a specific user
$searchCriteria = [
    "id" => ["=", $id, PDO::PARAM_INT]
];

// Call findEntity function with the specified parameters
$result = findEntity($table, $fields, $searchCriteria);

header("Content-Type: application/json");
http_response_code($result["statusCode"]);
echo json_encode($result);
