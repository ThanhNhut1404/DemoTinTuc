<?php
namespace Website\TinTuc\Models;

use Website\TinTuc\Database; // hoặc trực tiếp PDO như trước nếu bạn không dùng Database class
use PDO;

class BannerModel {
    private $db;

    public function __construct() {
        // Nếu bạn có lớp Database, dùng nó; nếu không, dùng PDO trực tiếp
        // $this->db = Database::getInstance()->getConnection();
        $this->db = new PDO("mysql:host=localhost;dbname=website_tin_tuc;charset=utf8", "root", "");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getAllBanners() {
        $stmt = $this->db->query("SELECT * FROM banner ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
