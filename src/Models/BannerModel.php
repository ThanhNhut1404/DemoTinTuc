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
        // Ensure `trang_thai` exists so toggle and filters work even if migration wasn't run yet.
        $this->ensureTrangThaiColumnExists();
    }

    private function ensureTrangThaiColumnExists()
    {
        try {
            $stmt = $this->db->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'banner' AND COLUMN_NAME = 'trang_thai'");
            $stmt->execute();
            $exists = (bool)$stmt->fetch(PDO::FETCH_ASSOC);
            if (! $exists) {
                // Add column as ENUM('on','off') default 'off' after ngay_tao
                $this->db->exec("ALTER TABLE banner ADD COLUMN trang_thai ENUM('on','off') NOT NULL DEFAULT 'off' AFTER ngay_tao");
            }
        } catch (\Exception $e) {
            // If anything goes wrong here (e.g. no permission), silently ignore — admin will need to run migration manually.
        }
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
            if (!isset($r['lien_ket'])) $r['lien_ket'] = '';
            if (!isset($r['trang_thai'])) $r['trang_thai'] = 'active';
        }
        unset($r);

        return $rows;
    }

    public function getOnBanners() {
        $stmt = $this->db->query("SELECT * FROM banner WHERE trang_thai = 'on' ORDER BY id DESC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$r) {
            if (!isset($r['hinh_banner'])) {
                if (isset($r['hinh_anh'])) $r['hinh_banner'] = $r['hinh_anh'];
                elseif (isset($r['hinh'])) $r['hinh_banner'] = $r['hinh'];
                else $r['hinh_banner'] = '';
            }
            if (!isset($r['mo_ta']) && isset($r['tieu_de'])) $r['mo_ta'] = $r['tieu_de'];
            if (!isset($r['mo_ta'])) $r['mo_ta'] = '';
            if (!isset($r['lien_ket'])) $r['lien_ket'] = '';
            if (!isset($r['trang_thai'])) $r['trang_thai'] = 'off';
        }
        unset($r);
        return $rows;
    }

    // Backwards compatibility wrapper
    public function getActiveBanners()
    {
        return $this->getOnBanners();
    }

    // Compatibility with admin controller patterns
    public function all()
    {
        return $this->getAllBanners();
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM banner WHERE id = ?");
        $stmt->execute([(int)$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            if (!isset($row['hinh_banner'])) {
                if (isset($row['hinh_anh'])) $row['hinh_banner'] = $row['hinh_anh'];
                elseif (isset($row['hinh'])) $row['hinh_banner'] = $row['hinh'];
                else $row['hinh_banner'] = '';
            }
            if (!isset($row['mo_ta']) && isset($row['tieu_de'])) {
                // Normalize older schema where tieu_de stored description
                $row['mo_ta'] = $row['tieu_de'];
            }
            if (!isset($row['mo_ta'])) $row['mo_ta'] = '';
            if (!isset($row['lien_ket'])) $row['lien_ket'] = '';
            if (!isset($row['trang_thai'])) $row['trang_thai'] = 'active';
        }
        return $row;
    }

    public function create(array $data)
    {
        $sql = "INSERT INTO banner (hinh_banner, lien_ket, mo_ta, trang_thai, ngay_tao) VALUES (:hinh_banner, :lien_ket, :mo_ta, :trang_thai, :ngay_tao)";
        $stmt = $this->db->prepare($sql);
        try {
            $stmt->execute([
                ':hinh_banner' => $data['hinh_banner'] ?? '',
                ':lien_ket' => $data['lien_ket'] ?? '',
                ':mo_ta' => $data['mo_ta'] ?? '',
                ':trang_thai' => $data['trang_thai'] ?? 'active',
                ':ngay_tao' => $data['ngay_tao'] ?? date('Y-m-d H:i:s'),
            ]);
        } catch (\PDOException $e) {
            // If column 'trang_thai' doesn't exist, try to add it and retry once
            $msg = $e->getMessage();
            if (stripos($msg, 'Unknown column') !== false || stripos($msg, 'trang_thai') !== false) {
                try { $this->db->exec("ALTER TABLE banner ADD COLUMN trang_thai VARCHAR(20) NOT NULL DEFAULT 'active' AFTER mo_ta"); } catch (\Exception $ex) {}
                $stmt->execute([
                    ':hinh_banner' => $data['hinh_banner'] ?? '',
                    ':lien_ket' => $data['lien_ket'] ?? '',
                    ':mo_ta' => $data['mo_ta'] ?? '',
                    ':trang_thai' => $data['trang_thai'] ?? 'active',
                    ':ngay_tao' => $data['ngay_tao'] ?? date('Y-m-d H:i:s'),
                ]);
            } else {
                throw $e;
            }
        }
        return $this->db->lastInsertId();
    }

    public function update($id, array $data)
    {
        $sql = "UPDATE banner SET hinh_banner = :hinh_banner, lien_ket = :lien_ket, mo_ta = :mo_ta, trang_thai = :trang_thai WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        try {
            return $stmt->execute([
                ':hinh_banner' => $data['hinh_banner'] ?? '',
                ':lien_ket' => $data['lien_ket'] ?? '',
                ':mo_ta' => $data['mo_ta'] ?? '',
                ':trang_thai' => $data['trang_thai'] ?? 'active',
                ':id' => (int)$id,
            ]);
        } catch (\PDOException $e) {
            $msg = $e->getMessage();
            if (stripos($msg, 'Unknown column') !== false || stripos($msg, 'trang_thai') !== false) {
                try { $this->db->exec("ALTER TABLE banner ADD COLUMN trang_thai VARCHAR(20) NOT NULL DEFAULT 'active' AFTER mo_ta"); } catch (\Exception $ex) {}
                return $stmt->execute([
                    ':hinh_banner' => $data['hinh_banner'] ?? '',
                    ':lien_ket' => $data['lien_ket'] ?? '',
                    ':mo_ta' => $data['mo_ta'] ?? '',
                    ':trang_thai' => $data['trang_thai'] ?? 'active',
                    ':id' => (int)$id,
                ]);
            }
            throw $e;
        }
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM banner WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }
}
