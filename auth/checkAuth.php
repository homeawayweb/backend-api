<?php
// File Path: portfolio-api/auth/checkAuth.php
require_once '../config/cors.php';
require_once '../config/database.php';

// Get the token from the Authorization header (e.g., "Bearer a1b2c3d4...")
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? null;
if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401); echo json_encode(['isAuthenticated' => false]); exit();
}
$token = $matches[1];

$database = new Database(); $db = $database->connect();
if ($db) {
    $query = 'SELECT id, name FROM users WHERE api_token = :token AND token_expires_at > NOW() LIMIT 1';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':token', $token);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        http_response_code(200);
        echo json_encode(['isAuthenticated' => true, 'user' => $user]);
    } else {
        http_response_code(401);
        echo json_encode(['isAuthenticated' => false]);
    }
}
?>