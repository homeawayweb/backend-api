<?php
// File Path: portfolio-api/api/dashboard/getDashboardStats.php (FINAL)

// --- Universal CORS and Error Reporting ---
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../config/database.php';

// --- HELPER FUNCTION to calculate "time ago" ---
function time_ago($timestamp) {
    if (!$timestamp) return 'some time ago';
    $time_ago = strtotime($timestamp);
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;
    $minutes = round($seconds / 60);
    $hours = round($seconds / 3600);
    $days = round($seconds / 86400);
    $weeks = round($seconds / 604800);
    $months = round($seconds / 2629440);
    $years = round($seconds / 31553280);

    if ($seconds <= 60) return "Just Now";
    else if ($minutes <= 60) return ($minutes == 1) ? "1 minute ago" : "$minutes minutes ago";
    else if ($hours <= 24) return ($hours == 1) ? "1 hour ago" : "$hours hours ago";
    else if ($days <= 7) return ($days == 1) ? "Yesterday" : "$days days ago";
    else if ($weeks <= 4.3) return ($weeks == 1) ? "A week ago" : "$weeks weeks ago";
    else if ($months <= 12) return ($months == 1) ? "A month ago" : "$months months ago";
    else return ($years == 1) ? "One year ago" : "$years years ago";
}

$database = new Database();
$db = $database->connect();

if ($db) {
    try {
        $totalProjects = $db->query('SELECT COUNT(*) FROM projects')->fetchColumn();
        $approvedTestimonials = $db->query("SELECT COUNT(*) FROM testimonials WHERE status = 'approved'")->fetchColumn();
        $totalSkills = $db->query('SELECT COUNT(*) FROM skills')->fetchColumn();
        $unreadMessages = $db->query("SELECT COUNT(*) FROM messages WHERE is_read = 0")->fetchColumn();
        
        $dashboardStats = [
            ['titleKey' => 'totalProjects', 'value' => $totalProjects ?: 0, 'change' => '', 'changeText' => 'in total', 'changeType' => 'positive', 'icon' => 'projects'],
            ['titleKey' => 'pendingMessages', 'value' => $unreadMessages ?: 0, 'change' => '', 'changeText' => 'unread', 'changeType' => 'positive', 'icon' => 'clients'],
            ['titleKey' => 'approvedTestimonials', 'value' => $approvedTestimonials ?: 0, 'change' => '', 'changeText' => 'approved', 'changeType' => 'positive', 'icon' => 'testimonials'],
            ['titleKey' => 'totalSkills', 'value' => $totalSkills ?: 0, 'change' => '', 'changeText' => 'in total', 'changeType' => 'positive', 'icon' => 'visits']
        ];
        
        $activityQuery = "(SELECT 'project' as type, title as text, created_at as time FROM projects ORDER BY created_at DESC LIMIT 2) UNION ALL (SELECT 'testimonial' as type, CONCAT('New testimonial from ', author_name) as text, created_at as time FROM testimonials ORDER BY created_at DESC LIMIT 2) UNION ALL (SELECT 'message' as type, CONCAT('New message from ', name) as text, created_at as time FROM messages ORDER BY created_at DESC LIMIT 2) ORDER BY time DESC LIMIT 5";
        $activityStmt = $db->prepare($activityQuery);
        $activityStmt->execute();
        $rawActivities = $activityStmt->fetchAll(PDO::FETCH_ASSOC);

        $recentActivity = [];
        if ($rawActivities) {
            foreach ($rawActivities as $activity) {
                $icon = 'project';
                if ($activity['type'] === 'testimonial' || $activity['type'] === 'message') { 
                    $icon = 'testimonial'; 
                }
                $recentActivity[] = [
                    'icon' => $icon,
                    'text' => $activity['text'],
                    'time' => time_ago($activity['time']) 
                ];
            }
        }
        
        $response = [
            'dashboardStats' => $dashboardStats,
            'recentActivity' => $recentActivity
        ];
        
        http_response_code(200);
        echo json_encode($response);

    } catch(PDOException $e) { 
        http_response_code(500); 
        echo json_encode(['message' => 'DB Error in getDashboardStats: ' . $e->getMessage()]); 
    }
} else { 
    http_response_code(503); 
    echo json_encode(['message' => 'DB Connection Failed.']); 
}
?>