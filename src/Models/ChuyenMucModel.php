<?php
namespace Website\TinTuc\Models;

use Website\TinTuc\Database;
use PDO;

class ChuyenMucModel extends Database
{
    public $db;

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

    public function add($ten, $mo_ta = null, $id_cha = null) {
        // Compute next thu_tu separately to avoid MySQL 1093 error (same-table INSERT SELECT)
        $sql = "SELECT COALESCE(MAX(thu_tu), 0) + 1 AS next_order FROM chuyen_muc";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $next = $row && isset($row['next_order']) ? (int)$row['next_order'] : 1;

        $sql = "INSERT INTO chuyen_muc (ten_chuyen_muc, mo_ta, id_cha, thu_tu) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$ten, $mo_ta, $id_cha ?: null, $next]);
    }

    public function update($id, $ten, $mo_ta = null, $id_cha = null) {
        $sql = "UPDATE chuyen_muc SET ten_chuyen_muc = ?, mo_ta = ?, id_cha = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$ten, $mo_ta, $id_cha ?: null, $id]);
    }

    public function delete($id) {
        $sql = "DELETE FROM chuyen_muc WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function updateOrder($items) {
        foreach ($items as $index => $id) {
            $sql = "UPDATE chuyen_muc SET thu_tu = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$index + 1, $id]);
        }
        return true;
    }

    public function getChildren($parentId) {
        $sql = "SELECT * FROM chuyen_muc WHERE id_cha = ? ORDER BY thu_tu ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$parentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

