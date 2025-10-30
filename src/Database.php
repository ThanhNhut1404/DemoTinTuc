<?php
namespace Website\TinTuc;

use PDO;
use PDOException;

class Database
{
    private $host = '127.0.0.1';
    private $db   = 'website_tin_tuc';
    private $user = 'root';
    private $pass = '';
    private $charset = 'utf8mb4';
    private $pdo;
    private static $instance = null;
    private function __construct()
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            die("❌ Kết nối database thất bại: " . $e->getMessage());
        }
    }
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function getConnection()
    {
        return $this->pdo;
    }
}
