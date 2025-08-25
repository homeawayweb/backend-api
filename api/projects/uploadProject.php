<?php
require_once '../../config/cors.php';
$upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/portfolio/hosted-projects/';
$base_url = 'https://portfoliofront-h9zn.vercel.app/hosted-projects/';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit(); }
if (!isset($_FILES['projectFile']) || $_FILES['projectFile']['error'] !== UPLOAD_ERR_OK) { http_response_code(400); echo json_encode(['message' => 'File upload error.']); exit(); }
$file = $_FILES['projectFile'];
if (strtolower(pathinfo(basename($file['name']), PATHINFO_EXTENSION)) !== 'zip') { http_response_code(415); echo json_encode(['message' => 'Only .zip files are allowed.']); exit(); }
$project_folder_name = pathinfo(basename($file['name']), PATHINFO_FILENAME) . '-' . time();
$project_dir = $upload_dir . $project_folder_name;
if (!mkdir($project_dir, 0777, true)) { http_response_code(500); echo json_encode(['message' => 'Failed to create directory.']); exit(); }
$zip = new ZipArchive;
if ($zip->open($file['tmp_name']) === TRUE) {
    $zip->extractTo($project_dir); $zip->close();
    $scanned_dir = array_diff(scandir($project_dir), array('..', '.'));
    if (count($scanned_dir) === 1) {
        $inner_folder_path = $project_dir . '/' . reset($scanned_dir);
        if (is_dir($inner_folder_path)) {
            $files_to_move = glob($inner_folder_path . '/{,.}*', GLOB_BRACE);
            foreach ($files_to_move as $file_path) {
                if (basename($file_path) === '.' || basename($file_path) === '..') continue;
                rename($file_path, $project_dir . '/' . basename($file_path));
            }
            rmdir($inner_folder_path);
        }
    }
    http_response_code(201); echo json_encode(['message' => 'Project uploaded!', 'url' => $base_url . $project_folder_name . '/']);
} else { rmdir($project_dir); http_response_code(500); echo json_encode(['message' => 'Failed to unzip file.']); }
?>