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
        }
        unset($r);

        return $rows;
    }

    public function getOnBanners() {
        $stmt = $this->db->query("SELECT * FROM banner WHERE trang_thai = 'on' ORDER BY id DESC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Normalize returned rows so view can consistently use 'hinh_banner' and 'mo_ta'
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

    public function all() {
        return $this->getAllBanners();
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM banner WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            // Normalize fields
            if (!isset($row['hinh_banner'])) {
                if (isset($row['hinh_anh'])) {
                    $row['hinh_banner'] = $row['hinh_anh'];
                } elseif (isset($row['hinh'])) {
                    $row['hinh_banner'] = $row['hinh'];
                } else {
                    $row['hinh_banner'] = '';
                }
            }

            if (!isset($row['mo_ta']) && isset($row['mo_ta_banner'])) {
                $row['mo_ta'] = $row['mo_ta_banner'];
            } elseif (!isset($row['mo_ta']) && isset($row['description'])) {
                $row['mo_ta'] = $row['description'];
            } elseif (!isset($row['mo_ta'])) {
                $row['mo_ta'] = '';
            }
        }

        return $row;
    }

    public function create($data) {
        $stmt = $this->db->prepare(
            "INSERT INTO banner (hinh_banner, mo_ta, lien_ket, trang_thai, ngay_tao) 
             VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['hinh_banner'] ?? '',
            $data['mo_ta'] ?? '',
            $data['lien_ket'] ?? '',
            $data['trang_thai'] ?? 'off',
            $data['ngay_tao'] ?? date('Y-m-d H:i:s')
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare(
            "UPDATE banner SET hinh_banner = ?, mo_ta = ?, lien_ket = ?, trang_thai = ? WHERE id = ?"
        );
        return $stmt->execute([
            $data['hinh_banner'] ?? '',
            $data['mo_ta'] ?? '',
            $data['lien_ket'] ?? '',
            $data['trang_thai'] ?? 'off',
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM banner WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function toggle($id) {
        $banner = $this->find($id);
        if (!$banner) {
            return false;
        }
        $newStatus = ($banner['trang_thai'] ?? 'off') === 'on' ? 'off' : 'on';
        return $this->update($id, [
            'hinh_banner' => $banner['hinh_banner'],
            'mo_ta' => $banner['mo_ta'],
            'lien_ket' => $banner['lien_ket'] ?? '',
            'trang_thai' => $newStatus
        ]);
    }
}
