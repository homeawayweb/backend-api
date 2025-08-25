<?php
// File Path: portfolio-api/auth/logout.php

// Use the same cookie configuration as the login script.
session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => '',
    'secure' => false, // Set to TRUE for production HTTPS
    'httponly' => true,
    'samesite' => 'Lax' // Use 'Lax' for localhost HTTP, 'None' for production HTTPS
]);
session_start();

require_once '../config/cors.php';

// Unset all of the session variables.
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();

http_response_code(200);
echo json_encode(['message' => 'Logout successful.']);
?>