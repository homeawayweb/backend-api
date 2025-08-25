<?php
require_once '../../config/cors.php';
$upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/portfolio/portfolio-api/uploads/resume/';
$base_url = 'https://portfoliofront-h9zn.vercel.app/portfolio-api/uploads/resume/';
if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit(); }
if (!isset($_FILES['resumeFile']) || $_FILES['resumeFile']['error'] !== UPLOAD_ERR_OK) { http_response_code(400); echo json_encode(['message' => 'Resume upload error.']); exit(); }
$file = $_FILES['resumeFile'];
$original_filename = basename($file['name']);
if (move_uploaded_file($file['tmp_name'], $upload_dir . $original_filename)) {
    http_response_code(201); echo json_encode(['message' => 'Resume uploaded.', 'url' => $base_url . $original_filename, 'filename' => $original_filename]);
} else { http_response_code(500); echo json_encode(['message' => 'Failed to save resume.']); }
?>