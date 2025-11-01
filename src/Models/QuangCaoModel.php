<?php
namespace Website\TinTuc\Models;

use Website\TinTuc\Database;
use PDO;

class QuangCaoModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getQuangCaoTrangChu() {
        $sql = "SELECT * FROM quang_cao WHERE vi_tri = 'trang_chu'";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
