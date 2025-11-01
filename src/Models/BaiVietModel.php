<?php

namespace Website\TinTuc\Models;

use Website\TinTuc\Database;
use PDO;

class BaiVietModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function all()
    {
        $sql = "SELECT * FROM bai_viet ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $sql = "SELECT * FROM bai_viet WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) // index 
    {
        $sql = "INSERT INTO bai_viet (tieu_de, mo_ta, noi_dung, anh_dai_dien, id_chuyen_muc, tag, la_noi_bat, trang_thai, ngay_dang)
                VALUES (:tieu_de, :mo_ta, :noi_dung, :anh_dai_dien, :id_chuyen_muc, :tag, :la_noi_bat, :trang_thai, :ngay_dang)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($data);
    }

    public function update($id, $data) // index PoST
    {
        $data['id'] = $id;
        $sql = "UPDATE bai_viet 
                SET tieu_de=:tieu_de, mo_ta=:mo_ta, noi_dung=:noi_dung, anh_dai_dien=:anh_dai_dien,
                    id_chuyen_muc=:id_chuyen_muc, tag=:tag, la_noi_bat=:la_noi_bat, trang_thai=:trang_thai, ngay_dang=:ngay_dang
                WHERE id=:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($data);
    }

    public function delete($id) // index
    {
        $sql = "DELETE FROM bai_viet WHERE id=:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getTinMoiNhat($limit = 5)
    {
        $sql = "SELECT * FROM bai_viet WHERE trang_thai = 'da_dang' ORDER BY ngay_dang DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTinNoiBat($limit = 5)
    {
        $sql = "SELECT * FROM bai_viet WHERE la_noi_bat = 1 ORDER BY ngay_dang DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTinXemNhieu($limit = 5)
    {
        $sql = "SELECT * FROM bai_viet ORDER BY luot_xem DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTinTheoChuyenMuc($idChuyenMuc, $limit = 5)
    {
        $sql = "SELECT * FROM bai_viet WHERE id_chuyen_muc = :id ORDER BY ngay_dang DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', (int)$idChuyenMuc, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
