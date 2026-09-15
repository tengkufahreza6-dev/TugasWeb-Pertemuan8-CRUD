<?php
class Database {
    private static $instance = null;
    private $conn;

    // Masukkan detail dari cPanel InfinityFree
    private $host = "sql101.infinityfree.com"; // Sesuaikan Hostname kamu
    private $user = "if0_42923419";            // Sesuaikan Username kamu
    private $pass = "eqHotQXo2AihnmR";           // Sesuaikan Password kamu
    private $dbname = "if0_42923419_crud";     // Sesuaikan Database Name kamu

    private function __construct() {
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->dbname . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->conn = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            die("Koneksi Database Gagal: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}