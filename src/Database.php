<?php
namespace Website\TinTuc;

use PDO;
use PDOException;

class Database {
    // --- Cấu hình ---
    private $host = "localhost";
    private $dbname = "website_tin_tuc";
    private $username = "root";
    private $password = "";
    private $conn;

    // --- Kết nối dạng đối tượng ---
    public function connect() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (PDOException $e) {
            die("Lỗi kết nối DB: " . $e->getMessage());
        }
    }

    // --- Kết nối dạng static (singleton) ---
    private static $instance = null;

    public static function getConnection() {
        if (self::$instance === null) {
            try {
                self::$instance = new PDO(
                    "mysql:host=localhost;dbname=website_tin_tuc;charset=utf8",
                    "root",
                    ""
                );
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Lỗi kết nối DB (static): " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
