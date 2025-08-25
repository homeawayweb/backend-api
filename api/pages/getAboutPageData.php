<?php
// File Path: portfolio-api/api/pages/getAboutPageData.php

require_once '../../config/cors.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->connect();

if ($db) {
    // 1. Fetch ALL relevant details from the settings table
    $settings_query = 'SELECT name, value FROM settings 
                       WHERE name IN (
                         "profilePictureUrl", "resumeUrl", "resumeFilename", "shortBio", 
                         "journeyTitle", "journeyParagraph1", "journeyParagraph2", "journeyParagraph3"
                       )';
    $settings_stmt = $db->prepare($settings_query);
    $settings_stmt->execute();
    $settings_results = $settings_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $profile = [];
    foreach ($settings_results as $row) {
        $profile[$row['name']] = $row['value'];
    }

    // 2. Fetch Experiences
    $exp_query = 'SELECT role, company, period, description FROM experiences ORDER BY display_order ASC';
    $exp_stmt = $db->prepare($exp_query);
    $exp_stmt->execute();
    $experiences = $exp_stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($experiences === false) { $experiences = []; }

    // 3. Fetch Educations
    $edu_query = 'SELECT degree, institution, year FROM educations ORDER BY display_order ASC';
    $edu_stmt = $db->prepare($edu_query);
    $edu_stmt->execute();
    $educations = $edu_stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($educations === false) { $educations = []; }

    // 4. Combine all data into a single JSON object
    $aboutData = [
        'profile' => $profile,
        'experiences' => $experiences,
        'educations' => $educations
    ];
    
    http_response_code(200);
    echo json_encode($aboutData);
}
?>