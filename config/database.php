<?php
// File Path: portfolio-api/config/database.php

class Database {
    // --- Get credentials from Environment Variables provided by Render ---
    private $host;
    private $db_name;
    private $username;
    private $password;

    public function __construct() {
        $this->host = getenv('DB_HOST');
        $this->db_name = getenv('DB_NAME');
        $this->username = getenv('DB_USER');
        $this->password = getenv('DB_PASS');
    }
    // ---------------------------------------------------------------

    private $conn;

    public function connect() {
        $this->conn = null;
        // The DSN (Data Source Name) is built from the environment variables
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name;
        try {
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            // In a production environment, you should log this error, not display it.
            // For now, this helps with debugging if the connection fails.
            die("Database Connection Error: " . $e->getMessage());
        }
        return $this->conn;
    }
}
?>