<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once __DIR__ . '/findEntity.php';  // Ensure the correct path to your function file
require('config.php');  // Include database configuration file

try {
    // Get JSON input from request body
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Validate input
    $id = isset($data['id']) ? $data['id'] : null;
    $name = isset($data['name']) ? $data['name'] : null;
    $email = isset($data['email']) ? $data['email'] : null;
    $password = isset($data['password']) ? $data['password'] : null;
    $address = isset($data['address']) ? $data['address'] : null;
    $city = isset($data['city']) ? $data['city'] : null;
    $born_date = isset($data['born_date']) ? $data['born_date'] : null;
    $user_role = isset($data['user_role']) ? $data['user_role'] : null;
    $userPhoto = isset($data['userPhoto']) ? $data['userPhoto'] : null;

    // Check for required fields
    if (!$id || !$name || !$email || !$user_role) {
        throw new Exception("Missing required fields");
    }

    $previousUserRegister = findEntity("users", ["*"], ['id' => ['=',$id, PDO::PARAM_INT]]);
    $photo_id = $previousUserRegister['data'][0]['photo_id'];

    if ($photo_id !== null && empty($userPhoto)) {
        deletePicture($photo_id);
        $photo_id = null;
    } elseif ($photo_id !== null && !empty($userPhoto)) {
        $photo_id = savePicture($userPhoto);
    } elseif ($photo_id === null && !empty($userPhoto)) {
        $photo_id = savePicture($userPhoto);
    }
    // Hash password only if it's provided
    $hashed_password = $password ? password_hash($password, PASSWORD_BCRYPT) : null;

    // Establish a database connection
    $conn = new PDO("mysql:host=" . DATABASE_HOST . ";dbname=" . DATABASE_NAME, DATABASE_USER, DATABASE_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare the SQL query
    $sql = "UPDATE users SET 
                name = :name,
                email = :email,
                address = :address,
                city = :city,
                born_date = :born_date,
                user_role = :user_role,
                photo_id = :photo_id";
    if ($hashed_password) {
        $sql .= ", password = :password";
    }
    $sql .= " WHERE id = :id";

    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':born_date', $born_date);
    $stmt->bindParam(':user_role', $user_role);
    if ($hashed_password) {
        $stmt->bindParam(':password', $hashed_password);
    }

    // Execute the statement
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(["result" => "SUCCESS", "message" => "User updated successfully"]);
    } else {
        throw new Exception("Could not update the user");
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["result" => "ERROR", "message" => "Database error: " . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["result" => "ERROR", "message" => $e->getMessage()]);
}
