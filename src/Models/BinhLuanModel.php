<?php
namespace Website\TinTuc\Models;

use Website\TinTuc\Database;
use PDO;

class BinhLuanModel {
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Lấy danh sách bình luận theo bài viết
    public function getByPostId($id_bai_viet) {
        $sql = "SELECT bl.*, nd.ten 
                FROM binh_luan bl
                JOIN nguoi_dung nd ON bl.id_nguoi_dung = nd.id
                WHERE bl.id_bai_viet = ? AND bl.trang_thai = 'Hien'
                ORDER BY bl.ngay_binh_luan DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_bai_viet]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm bình luận
    public function add($id_bai_viet, $id_nguoi_dung, $noi_dung) {
        $sql = "INSERT INTO binh_luan(id_bai_viet, id_nguoi_dung, noi_dung) 
                VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id_bai_viet, $id_nguoi_dung, $noi_dung]);
    }

    // Lấy bình luận theo người dùng
    public function layBinhLuanTheoNguoiDung($idNguoiDung)
    {
        $sql = "SELECT * FROM binh_luan WHERE id_nguoi_dung = ? ORDER BY ngay_binh_luan DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idNguoiDung]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đếm tổng bình luận
    public function countAll()
    {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM binh_luan");
        return (int)$stmt->fetchColumn();
    }

    // Lấy tất cả bình luận (cho admin quản lý)
    public function getAllForAdmin()
    {
        $sql = "SELECT bl.*, nd.ho_ten, bv.tieu_de 
                FROM binh_luan bl
                JOIN nguoi_dung nd ON bl.id_nguoi_dung = nd.id
                JOIN bai_viet bv ON bl.id_bai_viet = bv.id
                ORDER BY bl.ngay_binh_luan DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy bình luận theo trạng thái
    public function getByStatus($trang_thai)
    {
        $sql = "SELECT bl.*, nd.ho_ten, bv.tieu_de 
                FROM binh_luan bl
                JOIN nguoi_dung nd ON bl.id_nguoi_dung = nd.id
                JOIN bai_viet bv ON bl.id_bai_viet = bv.id
                WHERE bl.trang_thai = ?
                ORDER BY bl.ngay_binh_luan DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$trang_thai]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cập nhật trạng thái bình luận (Hien/An)
    public function updateStatus($id, $trang_thai)
    {
        $sql = "UPDATE binh_luan SET trang_thai = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$trang_thai, $id]);
    }

    // Xóa bình luận
    public function delete($id)
    {
        $sql = "DELETE FROM binh_luan WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Lấy bình luận theo ID
    public function find($id)
    {
        $sql = "SELECT bl.*, nd.ho_ten, bv.tieu_de 
                FROM binh_luan bl
                JOIN nguoi_dung nd ON bl.id_nguoi_dung = nd.id
                JOIN bai_viet bv ON bl.id_bai_viet = bv.id
                WHERE bl.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
