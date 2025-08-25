<?php
require_once '../../config/cors.php';
$upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/portfolio/portfolio-api/uploads/profile/';
$base_url = 'https://portfoliofront-h9zn.vercel.app/portfolio-api/uploads/profile/';
if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit(); }
if (!isset($_FILES['profilePicture']) || $_FILES['profilePicture']['error'] !== UPLOAD_ERR_OK) { http_response_code(400); echo json_encode(['message' => 'Image upload error.']); exit(); }
$file = $_FILES['profilePicture'];
$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$new_filename = 'profile_picture.' . $file_extension;
if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_filename)) {
    http_response_code(201); echo json_encode(['message' => 'Profile picture uploaded.', 'url' => $base_url . $new_filename . '?t=' . time()]);
} else { http_response_code(500); echo json_encode(['message' => 'Failed to save image.']); }
?>