<?php
require_once '../../config/cors.php';
$upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/portfolio/portfolio-api/uploads/projects/';
$base_url = 'https://portfoliofront-h9zn.vercel.app/portfolio-api/uploads/projects/';
if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit(); }
if (!isset($_FILES['projectImage']) || $_FILES['projectImage']['error'] !== UPLOAD_ERR_OK) { http_response_code(400); echo json_encode(['message' => 'Image upload error.']); exit(); }
$file = $_FILES['projectImage'];
// (Add robust validation here for production)
$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$new_filename = uniqid('project_', true) . '.' . $file_extension;
if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_filename)) {
    http_response_code(201); echo json_encode(['message' => 'Image uploaded.', 'url' => $base_url . $new_filename]);
} else { http_response_code(500); echo json_encode(['message' => 'Failed to save image.']); }
?>