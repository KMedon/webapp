<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once __DIR__ . '/pictureManager.php';
require_once __DIR__ . '/videoManager.php';
require('config.php');  // Include the config file for database credentials

try {
    // Create a connection to the database
    $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);

    // Check the connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Get the incoming request data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Validate that required fields are not empty
    $requiredFields = ['name', 'email', 'password', 'user_role', 'address', 'city', 'born_date'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Extract data and hash the password
    $name = $data['name'];
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_BCRYPT); 
    $role = $data['user_role'];
    $address = $data['address'];
    $city = $data['city'];
    $born_date = $data['born_date'];
    $photoData = isset($data['photo']) ? $data['photo'] : '';

    $photo_id = null;
    if (!empty($photoData)) {
        $photo_id = savePicture($photoData); 
    }

    $videoData = isset($data['video']) ? $data['video'] : null;
    if ($videoData) {
        $video_id = saveVideo($videoData);
    }

    // Prepare the SQL statement with the correct column names
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, user_role, address, city, born_date, photo_id, video_id) VALUES (?, ?, ?, ?, ?, ?, ?,?,?)");
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("sssssssss", $name, $email, $password, $role, $address, $city, $born_date, $photo_id, $video_id);

    // Execute the query and check if successful
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            'result' => 'SUCCESS',
            'message' => 'User saved successfully!']);
    } else {
        throw new Exception("Failed to save user: " . $stmt->error);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    // Set a 400 response code and return the error message in JSON
    http_response_code(400);
    echo json_encode([
        'result' => 'ERROR',
        'message' => 'Failed to save user!',
        'error' => $e->getMessage()
    ]);
}
