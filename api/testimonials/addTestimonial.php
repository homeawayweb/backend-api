<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit(); }
$upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/portfolio/portfolio-api/uploads/avatars/';
$base_url = 'https://portfoliofront-h9zn.vercel.app/portfolio-api/uploads/avatars/';
if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }
$database = new Database(); $db = $database->connect();
if ($db) {
    $data = $_POST;
    if (empty($data['author_name']) || empty($data['quote'])) { http_response_code(400); echo json_encode(['message' => 'Client name and quote are required.']); exit(); }
    $avatar_url = $data['avatar_url'] ?? null;
    if (isset($_FILES['avatarFile']) && $_FILES['avatarFile']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['avatarFile']; $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)); $new_filename = uniqid('avatar_', true) . '.' . $file_extension;
        if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_filename)) { $avatar_url = $base_url . $new_filename; }
    }
    $query = 'INSERT INTO testimonials SET author_name = :author_name, author_title = :author_title, quote = :quote, avatar_url = :avatar_url, rating = :rating, country = :country, status = :status';
    $stmt = $db->prepare($query); $stmt->bindParam(':author_name', htmlspecialchars(strip_tags($data['author_name']))); $stmt->bindParam(':author_title', htmlspecialchars(strip_tags($data['author_title'] ?? ''))); $stmt->bindParam(':quote', htmlspecialchars(strip_tags($data['quote']))); $stmt->bindParam(':avatar_url', $avatar_url); $stmt->bindParam(':rating', intval($data['rating']), PDO::PARAM_INT); $stmt->bindParam(':country', htmlspecialchars(strip_tags($data['country'] ?? ''))); $stmt->bindParam(':status', htmlspecialchars(strip_tags($data['status'])));
    if ($stmt->execute()) { http_response_code(201); echo json_encode(['message' => 'Testimonial added.']); }
}
?>