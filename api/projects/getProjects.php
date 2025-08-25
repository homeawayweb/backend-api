<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
$database = new Database(); $db = $database->connect();
if ($db) {
    $query = 'SELECT * FROM projects ORDER BY created_at DESC';
    $stmt = $db->prepare($query); $stmt->execute();
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($projects === false) { $projects = []; }
    foreach ($projects as $key => $project) { $projects[$key]['technologies'] = json_decode($project['technologies']) ?? []; }
    echo json_encode($projects);
}
?>