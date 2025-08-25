<?php
// File Path: portfolio-api/utils/createUser.php
// IMPORTANT: DELETE THIS FILE AFTER YOU USE IT!

// We need to navigate up one directory to find the config folder
require_once '../config/database.php';

// --- YOUR ADMIN DETAILS ---
$admin_name = 'YIMNAI JIRES';
$admin_email = 'admin@example.com';
$admin_password = 'password123';
// --------------------------

echo "<pre>"; // For cleaner output in the browser

$database = new Database();
$db = $database->connect();

if ($db) {
    try {
        // HASH the password for security. This will use your XAMPP server's PHP config.
        $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
        
        echo "Generated Hash: " . $hashed_password . "\n\n";

        $query = 'INSERT INTO users (name, email, password) VALUES (:name, :email, :password)';
        $stmt = $db->prepare($query);

        $stmt->bindParam(':name', $admin_name);
        $stmt->bindParam(':email', $admin_email);
        $stmt->bindParam(':password', $hashed_password);

        if ($stmt->execute()) {
            echo "SUCCESS: Admin user created successfully!\n";
            echo "Email:    " . $admin_email . "\n";
            echo "Password: " . $admin_password . "\n";
        } else {
            echo "ERROR: Could not execute the insert query.";
        }
    } catch (PDOException $e) {
        echo "DATABASE ERROR: " . $e->getMessage();
    }
} else {
    echo "ERROR: Could not connect to the database.";
}

echo "</pre>";
?>