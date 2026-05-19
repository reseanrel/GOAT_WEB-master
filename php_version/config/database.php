<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'pila_pets');
define('DB_USER', 'root');
define('DB_PASS', '');

class Database {
    private static ?Database $instance = null;
    private ?PDO $conn = null;
    
    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $e) {
            $this->conn = null;
            error_log("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection(): ?PDO {
        return $this->conn;
    }
}
