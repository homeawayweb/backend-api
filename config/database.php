<?php
// File Path: portfolio-api/config/database.php
// --- CORS HEADERS (must be at the very top, before any output) ---
header("Access-Control-Allow-Origin: http://localhost:5173"); 
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
class Database {
    // --- UPDATE THESE DETAILS ---
    private $host = '127.0.0.1';
    private $db_name = 'portfolio'; // The same database you created for Laravel
    private $username = 'root';
    private $password = '';
    // --------------------------

    private $conn;

    // The connect method
    public function connect() {
        $this->conn = null;

        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name;

        try {
            $this->conn = new PDO($dsn, $this->username, $this->password);
            // Set PDO to throw exceptions on error
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            // In a real app, you would log this error, not echo it
            echo 'Connection Error: ' . $e->getMessage();
        }

        return $this->conn;
    }
}
?>