<?php
// File Path: portfolio-api/auth/login.php
require_once '../config/cors.php';
require_once '../config/database.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit(); }

$database = new Database(); $db = $database->connect();
if ($db) {
    $data = json_decode(file_get_contents("php://input"));
    if (!$data || empty($data->email) || empty($data->password)) { http_response_code(400); exit(); }

    $query = 'SELECT id, name, email, password FROM users WHERE email = :email LIMIT 1';
    $stmt = $db->prepare($query); $stmt->bindParam(':email', $data->email); $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($data->password, $user['password'])) {
            // --- TOKEN LOGIC ---
            $token = bin2hex(random_bytes(32)); // Generate a secure random token
            $expires = date('Y-m-d H:i:s', time() + 86400); // Token expires in 24 hours

            $updateQuery = 'UPDATE users SET api_token = :token, token_expires_at = :expires WHERE id = :id';
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(':token', $token);
            $updateStmt->bindParam(':expires', $expires);
            $updateStmt->bindParam(':id', $user['id']);
            $updateStmt->execute();
            
            http_response_code(200);
            echo json_encode([
                'message' => 'Login successful.',
                'user' => ['id' => $user['id'], 'name' => $user['name']],
                'token' => $token // Send the token back to the frontend
            ]);
        } else { http_response_code(401); echo json_encode(['message' => 'Invalid credentials.']); }
    } else { http_response_code(401); echo json_encode(['message' => 'Invalid credentials.']); }
}
?>