<?php
class Database {
    private static $instance = null;
    private $conn;

    private $host;
    private $user;
    private $pass;
    private $dbname;
    private $port;

    private function __construct() {
        $this->host   = getenv('MYSQLHOST') ?: "localhost";
        $this->user   = getenv('MYSQLUSER') ?: "root";
        $this->pass   = getenv('MYSQLPASSWORD') ?: "";
        $this->dbname = getenv('MYSQLDATABASE') ?: "inventaris_db";
        $this->port   = getenv('MYSQLPORT') ?: "3306";

        try {
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->dbname . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->conn = new PDO($dsn, $this->user, $this->pass, $options);

            // AUTO-MIGRATION: Jalankan schema.sql otomatis jika tabel belum ada di server
            $this->autoMigrate();

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

    private function autoMigrate() {
        try {
            // Cek apakah tabel 'produk' sudah ada
            $check = $this->conn->query("SHOW TABLES LIKE 'produk'");
            if ($check->rowCount() == 0) {
                // Jika belum ada, baca file schema.sql dan eksekusi
                $schemaFile = __DIR__ . '/../schema.sql';
                if (file_exists($schemaFile)) {
                    $sql = file_get_contents($schemaFile);
                    $this->conn->exec($sql);
                }
            }
        } catch (Exception $e) {
            // Abaikan jika sudah ada
        }
    }
}