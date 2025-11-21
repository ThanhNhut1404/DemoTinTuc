<?php
class BinhLuanModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
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
}
