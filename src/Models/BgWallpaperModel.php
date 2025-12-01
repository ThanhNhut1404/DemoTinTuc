<?php
namespace Website\TinTuc\Models;

use Website\TinTuc\Database;
use PDO;

class BgWallpaperModel {
    private $db;
    private $table = 'bg_wallpaper';

    public function __construct() {
        $db = new Database();
        $this->db = $db->connect();
        $this->ensureTableExists();
    }

    private function ensureTableExists() {
        try {
            $stmt = $this->db->query("DESCRIBE {$this->table}");
            $stmt->fetch();
        } catch (\Exception $e) {
            // Table doesn't exist, create it
            $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
                id INT PRIMARY KEY AUTO_INCREMENT,
                ten_wallpaper VARCHAR(255) NOT NULL,
                duong_dan_file VARCHAR(500) NOT NULL,
                mo_ta TEXT,
                trang_thai ENUM('on', 'off') DEFAULT 'off',
                ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
                ngay_cap_nhat DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE(duong_dan_file)
            )";
            $this->db->exec($sql);
        }
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY ngay_tao DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActive() {
        $stmt = $this->db->query("SELECT * FROM {$this->table} WHERE trang_thai = 'on' ORDER BY ngay_tao DESC LIMIT 1");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (ten_wallpaper, duong_dan_file, mo_ta, trang_thai) 
             VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['ten_wallpaper'] ?? '',
            $data['duong_dan_file'] ?? '',
            $data['mo_ta'] ?? '',
            $data['trang_thai'] ?? 'off'
        ]);
    }

    public function update($id, $data) {
        // Nếu set trang_thai = 'on', tắt các cái khác
        if ($data['trang_thai'] === 'on') {
            $this->db->exec("UPDATE {$this->table} SET trang_thai = 'off'");
        }

        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET ten_wallpaper = ?, mo_ta = ?, trang_thai = ? WHERE id = ?"
        );
        return $stmt->execute([
            $data['ten_wallpaper'] ?? '',
            $data['mo_ta'] ?? '',
            $data['trang_thai'] ?? 'off',
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function toggle($id) {
        $wallpaper = $this->find($id);
        if (!$wallpaper) {
            return false;
        }
        // Bật nền này, tắt các cái khác
        return $this->update($id, [
            'ten_wallpaper' => $wallpaper['ten_wallpaper'],
            'mo_ta' => $wallpaper['mo_ta'],
            'trang_thai' => 'on'
        ]);
    }
}
