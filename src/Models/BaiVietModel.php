<?php
namespace Website\TinTuc\Models;

use Website\TinTuc\Database;
use PDO;

class BaiVietModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getTinMoiNhat($limit = 5) {
        $sql = "SELECT * FROM bai_viet WHERE trang_thai = 'da_dang' ORDER BY ngay_dang DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTinNoiBat($limit = 5) {
        $sql = "SELECT * FROM bai_viet WHERE la_noi_bat = 1 ORDER BY ngay_dang DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTinXemNhieu($limit = 5) {
        $sql = "SELECT * FROM bai_viet ORDER BY luot_xem DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTinTheoChuyenMuc($idChuyenMuc, $limit = 5) {
        $sql = "SELECT * FROM bai_viet WHERE id_chuyen_muc = :id ORDER BY ngay_dang DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', (int)$idChuyenMuc, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
