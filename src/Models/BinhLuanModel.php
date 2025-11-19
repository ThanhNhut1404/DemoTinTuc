<?php
namespace Website\TinTuc\Models;
use PDO;
use Website\TinTuc\Database;

class BinhLuanModel {
    private $conn;
    public function __construct() {
    $db = new \Website\TinTuc\Database();
    $this->conn = $db->connect();
}


    public function layBinhLuanTheoNguoiDung($idNguoiDung) {
        $stmt = $this->conn->prepare("
            SELECT b.id, b.noi_dung, b.ngay_binh_luan, bv.tieu_de
            FROM binh_luan b
            JOIN bai_viet bv ON bv.id = b.id_bai_viet
            WHERE b.id_nguoi_dung = ?
            ORDER BY b.ngay_binh_luan DESC
        ");
        $stmt->execute([$idNguoiDung]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function xoaBinhLuan($id) {
        $stmt = $this->conn->prepare("DELETE FROM binh_luan WHERE id = ?");
        $stmt->execute([$id]);
    }

    // --- Tổng số bình luận ---
    public function countAll()
    {
        $sql = "SELECT COUNT(*) FROM binh_luan";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
}
