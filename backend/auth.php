<?php
// auth.php
session_start();

function checkAuthenticated() {
    if (empty($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['result' => 'ERROR', 'message' => 'Unauthorized']);
        exit;
    }
}
