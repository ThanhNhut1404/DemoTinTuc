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
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Normalize returned rows so view can consistently use 'hinh_banner' and 'mo_ta'
        // Some schemas use 'hinh_anh' or 'hinh' as column name. Map them if needed.
        foreach ($rows as &$r) {
            if (!isset($r['hinh_banner'])) {
                if (isset($r['hinh_anh'])) {
                    $r['hinh_banner'] = $r['hinh_anh'];
                } elseif (isset($r['hinh'])) {
                    $r['hinh_banner'] = $r['hinh'];
                } else {
                    $r['hinh_banner'] = '';
                }
            }

            // normalize description field
            if (!isset($r['mo_ta']) && isset($r['mo_ta_banner'])) {
                $r['mo_ta'] = $r['mo_ta_banner'];
            } elseif (!isset($r['mo_ta']) && isset($r['description'])) {
                $r['mo_ta'] = $r['description'];
            } elseif (!isset($r['mo_ta'])) {
                $r['mo_ta'] = '';
            }
        }
        unset($r);

        return $rows;
    }
}
