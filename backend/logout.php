<?php
// logout.php
header("Content-Type: application/json");
require_once __DIR__ . '/auth.php';

// End session
session_unset();
session_destroy();

echo json_encode(['result' => 'SUCCESS', 'message' => 'Logout successful.']);
