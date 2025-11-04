<?php
namespace Website\TinTuc\Models;

use Website\TinTuc\Database;
use PDO;

class ChuyenMucModel extends Database
{
    private $db;

    public function __construct() {
        $this->db = $this->connect();
    }

    public function getAll() {
        $sql = "SELECT * FROM chuyen_muc ORDER BY thu_tu ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT * FROM chuyen_muc WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
