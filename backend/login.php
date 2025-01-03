<?php
// login.php
header("Content-Type: application/json");
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php'; // this starts session as well

try {
    // 1) Parse JSON input
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $email    = isset($data['email']) ? $data['email'] : '';
    $password = isset($data['password']) ? $data['password'] : '';
    if (!$email || !$password) {
        throw new Exception("Missing email or password.");
    }

    // 2) Connect to DB
    $conn = new PDO(
        "mysql:host=".DATABASE_HOST.";dbname=".DATABASE_NAME, 
        DATABASE_USER, 
        DATABASE_PASSWORD
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3) Look up user by email
    $stmt = $conn->prepare("SELECT id, email, password, user_role FROM users WHERE email = :email");
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 4) Verify password
    if (!$user) {
        throw new Exception("User not found with that email.");
    }

    // 'password' in your table is a hashed password (e.g., via password_hash)
    if (!password_verify($password, $user['password'])) {
        throw new Exception("Invalid password.");
    }

    // 5) If valid, store in session
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['user_role'] = $user['user_role']; // store role if needed
    $_SESSION['email']     = $user['email'];

    echo json_encode([
        'result'  => 'SUCCESS',
        'message' => 'Login successful.',
        'user'    => [
            'id'       => $user['id'],
            'email'    => $user['email'],
            'user_role'=> $user['user_role']
        ]
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'result'  => 'ERROR',
        'message' => $e->getMessage()
    ]);
}
