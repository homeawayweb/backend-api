<?php
// File Path: portfolio-api/api/messages/submitMessage.php
ob_start();
// Allow requests from your frontend origin
header("Access-Control-Allow-Origin: https://portfoliofront-h9zn.vercel.app/");

// Allow specific methods
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

// Allow specific headers
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// If it's an OPTIONS request (preflight), return 200 and exit
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
// Include our centralized CORS and header handling logic.
require_once '../../config/cors.php';
// Include the database connection.
require_once '../../config/database.php';

// Check if this is a POST request before proceeding.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'This endpoint only accepts POST requests.']);
    exit();
}

$database = new Database();
$db = $database->connect();

if ($db) {
    $data = json_decode(file_get_contents("php://input"));

    if (!$data || empty($data->name) || empty($data->email) || empty($data->subject) || empty($data->message)) {
        http_response_code(400);
        echo json_encode(['message' => 'Incomplete data. Please fill all required fields.']);
        exit();
    }
    
    $query = 'INSERT INTO messages SET name = :name, email = :email, subject = :subject, body = :body';
    
    try {
        $stmt = $db->prepare($query);
        $stmt->bindParam(':name', htmlspecialchars(strip_tags($data->name)));
        $stmt->bindParam(':email', filter_var($data->email, FILTER_SANITIZE_EMAIL));
        $stmt->bindParam(':subject', htmlspecialchars(strip_tags($data->subject)));
        $stmt->bindParam(':body', htmlspecialchars(strip_tags($data->message)));

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(['message' => 'Message received successfully. Thank you!']);
        } else {
            http_response_code(503);
            echo json_encode(['message' => 'An error occurred while saving the message.']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Database Execution Error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(503);
    echo json_encode(['message' => 'Failed to connect to the database.']);
}
?>