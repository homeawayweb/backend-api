<?php
// File Path: portfolio-api/api/messages/getMessages.php (FINAL, ROBUST VERSION)

// Add error reporting to see any hidden issues directly in the browser if possible.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Use the proven CORS handling method that works for other pages.
header("Access-Control-Allow-Origin: https://portfoliofront-h9zn.vercel.app/");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../config/database.php';

$database = new Database();
$db = $database->connect();

if ($db) {
    try {
        // Query to get all messages, ordered by most recent first.
        $query = 'SELECT `id`, `name`, `email`, `subject`, `body`, `is_read`, `created_at` FROM `messages` ORDER BY `created_at` DESC';
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // This check is crucial. If the query returns no rows, fetchAll can return false.
        if ($messages === false) {
            $messages = []; // Ensure we always return a valid JSON array.
        }

        // Convert is_read from 0/1 to a true/false boolean for the frontend.
        foreach ($messages as $key => $message) {
            $messages[$key]['is_read'] = (bool)$message['is_read'];
        }

        http_response_code(200);
        echo json_encode($messages);

    } catch (PDOException $e) {
        http_response_code(500);
        // Return a specific error message for easier debugging.
        echo json_encode(['message' => 'Database Query Error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(503);
    echo json_encode(['message' => 'Database connection failed.']);
}
?>