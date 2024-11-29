<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require('config.php');  // Include the config file for database credentials

// Create connection using config values
$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, title, url FROM media WHERE type = 'music'";
$result = $conn->query($sql);

$music = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $music[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($music);

$conn->close();

