<?php
namespace Website\TinTuc\Models;

use PDO;

class QuangcaoModel
{
    private $db;

    public function __construct()
    {
        $this->db = new PDO("mysql:host=localhost;dbname=website_tin_tuc;charset=utf8", "root", "");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Lấy quảng cáo theo vị trí (Trang_chu, Sidebar)
    public function getQuangCaoTheoViTri($vi_tri)
    {
        $stmt = $this->db->prepare("SELECT * FROM quang_cao WHERE vi_tri = ? ORDER BY id DESC");
        $stmt->execute([$vi_tri]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
