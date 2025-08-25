<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit(); }
$upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/portfolio/portfolio-api/uploads/avatars/';
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/portfolio/portfolio-api/uploads/avatars/';
$database = new Database(); $db = $database->connect();
if ($db) {
    $data = $_POST;
    if (empty($data['author_name']) || empty($data['quote'])) { http_response_code(400); echo json_encode(['message' => 'Your name and a testimonial message are required.']); exit(); }
    $avatar_url = null;
    if (isset($_FILES['avatarFile']) && $_FILES['avatarFile']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['avatarFile']; $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)); $new_filename = uniqid('avatar_', true) . '.' . $file_extension;
        if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_filename)) { $avatar_url = $base_url . $new_filename; }
    }
    $query = 'INSERT INTO testimonials SET author_name = :author_name, author_title = :author_title, quote = :quote, avatar_url = :avatar_url, rating = :rating, country = :country, status = "Pending"';
    $stmt = $db->prepare($query); $author_name = htmlspecialchars(strip_tags($data['author_name'])); $author_title = htmlspecialchars(strip_tags($data['author_title'] ?? '')); $quote = htmlspecialchars(strip_tags($data['quote'])); $rating = intval($data['rating']); $country = htmlspecialchars(strip_tags($data['country'] ?? ''));
    $stmt->bindParam(':author_name', $author_name); $stmt->bindParam(':author_title', $author_title); $stmt->bindParam(':quote', $quote); $stmt->bindParam(':avatar_url', $avatar_url); $stmt->bindParam(':rating', $rating, PDO::PARAM_INT); $stmt->bindParam(':country', $country);
    if ($stmt->execute()) { http_response_code(201); echo json_encode(['message' => 'Review submitted successfully! It will be reviewed shortly.']); }
}
?>