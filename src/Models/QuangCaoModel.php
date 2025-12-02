<?php
namespace Website\TinTuc\Models;

use PDO;

class QuangCaoModel
{
    private $db;

    public function __construct()
    {
        $this->db = new PDO("mysql:host=localhost;dbname=website_tin_tuc;charset=utf8", "root", "");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function all()
    {
        $stmt = $this->db->query("SELECT * FROM quang_cao ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM quang_cao WHERE id = ?");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data)
    {
        $sql = "INSERT INTO quang_cao (tieu_de, hinh_anh, lien_ket, vi_tri, trang_thai, ngay_tao)
                VALUES (:tieu_de, :hinh_anh, :lien_ket, :vi_tri, :trang_thai, :ngay_tao)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':tieu_de' => $data['tieu_de'] ?? '',
            ':hinh_anh' => $data['hinh_anh'] ?? '',
            ':lien_ket' => $data['lien_ket'] ?? '',
            ':vi_tri' => $data['vi_tri'] ?? '',
            ':trang_thai' => $data['trang_thai'] ?? 'on',
            ':ngay_tao' => $data['ngay_tao'] ?? date('Y-m-d H:i:s'),
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, array $data)
    {
        $sql = "UPDATE quang_cao SET tieu_de = :tieu_de, hinh_anh = :hinh_anh, lien_ket = :lien_ket, vi_tri = :vi_tri, trang_thai = :trang_thai WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':tieu_de' => $data['tieu_de'] ?? '',
            ':hinh_anh' => $data['hinh_anh'] ?? '',
            ':lien_ket' => $data['lien_ket'] ?? '',
            ':vi_tri' => $data['vi_tri'] ?? '',
            ':trang_thai' => $data['trang_thai'] ?? 'on',
            ':id' => (int)$id,
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM quang_cao WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }

    public function updateStatus($id, $trang_thai)
    {
        $sql = "UPDATE quang_cao SET trang_thai = :trang_thai WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':trang_thai' => $trang_thai,
            ':id' => (int)$id,
        ]);
    }

    // existing helper used by frontend controller
    public function getQuangCaoTheoViTri($vi_tri)
    {
        $stmt = $this->db->prepare("SELECT * FROM quang_cao WHERE vi_tri = ? AND trang_thai = 'on' ORDER BY id DESC");
        $stmt->execute([$vi_tri]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

