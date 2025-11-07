<?php

namespace Website\TinTuc\Models;

use Website\TinTuc\Database;
use PDO;
use PDOException;

class BaiVietModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // --- Lấy toàn bộ bài viết ---
    public function all()
    {
        $sql = "SELECT * FROM bai_viet ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- Tìm bài viết theo ID ---
    public function find($id)
    {
        $sql = "SELECT * FROM bai_viet WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // --- Thêm bài viết ---
    public function create($data)
    {
        $sql = "INSERT INTO bai_viet (tieu_de, mo_ta_ngan, noi_dung, anh_dai_dien, id_chuyen_muc, id_tac_gia, la_noi_bat, trang_thai, ngay_dang)
                VALUES (:tieu_de, :mo_ta_ngan, :noi_dung, :anh_dai_dien, :id_chuyen_muc, :id_tac_gia, :la_noi_bat, :trang_thai, :ngay_dang)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($data);
    }

    // --- Cập nhật bài viết ---
    public function update($id, $data)
    {
        $data['id'] = $id;
        $sql = "UPDATE bai_viet 
                SET tieu_de=:tieu_de, mo_ta_ngan=:mo_ta_ngan, noi_dung=:noi_dung, anh_dai_dien=:anh_dai_dien,
                    id_chuyen_muc=:id_chuyen_muc, id_tac_gia=:id_tac_gia, la_noi_bat=:la_noi_bat, trang_thai=:trang_thai, ngay_dang=:ngay_dang
                WHERE id=:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($data);
    }

    // --- Xóa bài viết ---
    public function delete($id)
    {
        $sql = "DELETE FROM bai_viet WHERE id=:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();
    }

    // --- Tin mới nhất ---
    public function getTinMoiNhat($limit = 5)
    {
        $sql = "SELECT * FROM bai_viet WHERE trang_thai = 'da_dang' ORDER BY ngay_dang DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- Tin nổi bật ---
    public function getTinNoiBat($limit = 5)
    {
        $sql = "SELECT * FROM bai_viet WHERE la_noi_bat = 1 ORDER BY ngay_dang DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- Tin xem nhiều ---
    public function getTinXemNhieu($limit = 5)
    {
        $sql = "SELECT * FROM bai_viet ORDER BY luot_xem DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- Tin theo chuyên mục (toàn bộ, không phân trang) ---
    public function getTinTheoChuyenMuc($id_chuyen_muc)
    {
        try {
            $sql = "SELECT * FROM bai_viet WHERE id_chuyen_muc = :id ORDER BY ngay_dang DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['id' => $id_chuyen_muc]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi getTinTheoChuyenMuc: " . $e->getMessage());
            return [];
        }
    }

    // --- Lấy chi tiết bài viết ---
    public function getById($id)
    {
        $sql = "SELECT * FROM bai_viet WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // --- Tăng lượt xem ---
    public function tangLuotXem($id)
    {
        $sql = "UPDATE bai_viet SET luot_xem = luot_xem + 1 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
    }

    // --- Lấy bài viết theo chuyên mục (có phân trang) ---
    public function getByChuyenMuc($chuyenMucId, $limit, $offset)
    {
        // Không bind LIMIT/OFFSET bằng tham số trong MySQL cũ để tránh lỗi
        $sql = "SELECT * FROM bai_viet 
                WHERE id_chuyen_muc = :id 
                ORDER BY ngay_dang DESC 
                LIMIT $limit OFFSET $offset";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', (int)$chuyenMucId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function layBaiVietYeuThich($idNguoiDung) {
        $stmt = $this->conn->prepare("
            SELECT bv.tieu_de, bv.ngay_dang
            FROM yeu_thich yt
            JOIN bai_viet bv ON bv.id = yt.id_bai_viet
            WHERE yt.id_nguoi_dung = ?");
        $stmt->execute([$idNguoiDung]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function layBaiVietDaLuu($idNguoiDung) {
        $stmt = $this->conn->prepare("
            SELECT bv.tieu_de, bv.ngay_dang
            FROM luu_bai_viet lbv
            JOIN bai_viet bv ON bv.id = lbv.id_bai_viet
            WHERE lbv.id_nguoi_dung = ?");
        $stmt->execute([$idNguoiDung]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
