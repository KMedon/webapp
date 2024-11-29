<?php
require_once __DIR__ . "/listEntities.php";

$table = "users";
$fields = ["id","name", "email", "address", "city", "born_date", "user_role"];

// Pagination parameters
$first = isset($_GET['first']) ? (int)$_GET['first'] : 0;
$rows = isset($_GET['rows']) ? (int)$_GET['rows'] : 10;

$sortField = isset($_GET['sortField']) && $_GET['sortField'] !== 'null' ? $_GET['sortField'] : "name"; 
$sortOrder = isset($_GET['sortOrder']) && $_GET['sortOrder'] !== 'null' ? $_GET['sortOrder'] : "ASC"; 


// Call listEntities with pagination and sorting parameters
$result = listEntities($table, $fields, $first, $rows, $sortField, $sortOrder);

header("Content-Type: application/json");
http_response_code($result["statusCode"]);
echo json_encode($result);