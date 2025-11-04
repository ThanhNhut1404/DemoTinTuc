<?php
namespace Website\TinTuc\Models;

use Website\TinTuc\Database;
use PDO;

class ThanhVienModel {
    private $conn;
     
    private $table = 'users';
    // logical -> actual column name mapping
    private $cols = [
        'id' => 'id',
        'ho_ten' => 'ho_ten',
        'email' => 'email',
        'quyen' => 'quyen',
        'trang_thai' => 'trang_thai'
    ];

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
        // Detect which table to use (prefer 'users', fallback to 'nguoi_dung')
        $this->detectTableAndColumns(['users', 'nguoi_dung']);
    }

    private function detectTableAndColumns(array $candidates)
    {
        foreach ($candidates as $t) {
            try {
                $stmt = $this->conn->query("SELECT 1 FROM `$t` LIMIT 1");
                // table exists, use it
                $this->table = $t;
                // describe columns
                $cols = [];
                $desc = $this->conn->query("DESCRIBE `$t`");
                foreach ($desc->fetchAll(PDO::FETCH_COLUMN) as $colName) {
                    $cols[] = $colName;
                }
                // map logical columns to actual names heuristically
                $this->cols['id'] = $this->findColumn($cols, ['id', 'ID']);
                $this->cols['ho_ten'] = $this->findColumn($cols, ['ho_ten', 'name', 'full_name', 'ten']);
                $this->cols['email'] = $this->findColumn($cols, ['email', 'email_address']);
                // include Vietnamese conventional column names like 'vai_tro'
                $this->cols['quyen'] = $this->findColumn($cols, ['quyen', 'vai_tro', 'role', 'permission']);
                // status column candidates (Vietnamese and English)
                $this->cols['trang_thai'] = $this->findColumn($cols, ['trang_thai', 'trangthai', 'status']);
                return;
            } catch (\Exception $e) {
                // table does not exist, try next
                continue;
            }
        }
        // if none found, keep defaults (will likely error later)
    }

    private function findColumn(array $cols, array $candidates)
    {
        foreach ($candidates as $cand) {
            foreach ($cols as $c) {
                if (strcasecmp($c, $cand) === 0) {
                    return $c;
                }
            }
        }
        // fallback to first column if nothing matches
        return $cols[0] ?? 'id';
    }

    // Lấy danh sách tất cả người dùng
    /**
     * Lấy danh sách tất cả người dùng, có thể lọc theo vai trò
     * @param string|null $role nếu truyền null hoặc empty thì không lọc
     */
    public function getAll(?string $role = null) {
        // Build select using detected table and columns; alias to logical names used by view
        $idCol = $this->cols['id'];
        $nameCol = $this->cols['ho_ten'];
        $emailCol = $this->cols['email'];
        $roleCol = $this->cols['quyen'];
        $statusCol = $this->cols['trang_thai'];

        if ($role !== null && $role !== '') {
            $sql = sprintf(
                "SELECT `%s` AS id, `%s` AS ho_ten, `%s` AS email, `%s` AS quyen, `%s` AS trang_thai FROM `%s` WHERE `%s` = :role ORDER BY `%s` DESC",
                $idCol, $nameCol, $emailCol, $roleCol, $statusCol, $this->table, $roleCol, $idCol
            );
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':role', $role, PDO::PARAM_STR);
        } else {
            $sql = sprintf(
                "SELECT `%s` AS id, `%s` AS ho_ten, `%s` AS email, `%s` AS quyen, `%s` AS trang_thai FROM `%s` ORDER BY `%s` DESC",
                $idCol, $nameCol, $emailCol, $roleCol, $statusCol, $this->table, $idCol
            );
            $stmt = $this->conn->prepare($sql);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tìm kiếm người dùng theo tên hoặc email
    public function search(string $keyword, ?string $role = null) {
        $idCol = $this->cols['id'];
        $nameCol = $this->cols['ho_ten'];
        $emailCol = $this->cols['email'];
        $roleCol = $this->cols['quyen'];
        $statusCol = $this->cols['trang_thai'];
        $like = '%' . $keyword . '%';

        if ($role !== null && $role !== '') {
            $sql = sprintf(
                "SELECT `%s` AS id, `%s` AS ho_ten, `%s` AS email, `%s` AS quyen, `%s` AS trang_thai FROM `%s` WHERE (`%s` LIKE :kw OR `%s` LIKE :kw) AND `%s` = :role ORDER BY `%s` DESC",
                $idCol, $nameCol, $emailCol, $roleCol, $statusCol, $this->table,
                $nameCol, $emailCol, $roleCol, $idCol
            );
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':kw', $like, PDO::PARAM_STR);
            $stmt->bindValue(':role', $role, PDO::PARAM_STR);
        } else {
            $sql = sprintf(
                "SELECT `%s` AS id, `%s` AS ho_ten, `%s` AS email, `%s` AS quyen, `%s` AS trang_thai FROM `%s` WHERE `%s` LIKE :kw OR `%s` LIKE :kw ORDER BY `%s` DESC",
                $idCol, $nameCol, $emailCol, $roleCol, $statusCol, $this->table,
                $nameCol, $emailCol, $idCol
            );
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':kw', $like, PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Khóa / mở khóa tài khoản
    public function toggleStatus($id) {
        $statusCol = $this->cols['trang_thai'];
        // Toggle between common variants (hoat_dong/bi_khoa) or (active/locked)
        // Toggle considering different value variants (case-insensitive).
        // If current value is 'hoat_dong' or 'active' (any case) -> set to 'Khoa'
        // else set to 'Hoat_dong' for compatibility with your schema.
        $sql = sprintf(
            "UPDATE `%s` SET `%s` = IF(LOWER(`%s`) IN ('hoat_dong','active','hoat dong','hoạt_động','khoa'), 'Khoa', 'Hoat_dong') WHERE `%s` = ?",
            $this->table, $statusCol, $statusCol, $this->cols['id']
        );
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Cập nhật quyền người dùng
    public function updateRole($id, $role) {
        $roleCol = $this->cols['quyen'];
        $idCol = $this->cols['id'];
        $sql = sprintf("UPDATE `%s` SET `%s` = ? WHERE `%s` = ?", $this->table, $roleCol, $idCol);
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$role, $id]);
    }
    public function layThongTinNguoiDung($id) {
        $stmt = $this->conn->prepare("SELECT * FROM nguoi_dung WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

  public function capNhatThongTin($id, $hoTen, $email, $anh = null, $ngaySinh = null, $gioiTinh = null) {
    // 🔹 Kiểm tra email trùng lặp (ngoại trừ chính mình)
    $check = $this->conn->prepare("SELECT id FROM nguoi_dung WHERE email = ? AND id != ?");
    $check->execute([$email, $id]);
    if ($check->fetch()) {
        throw new \Exception("❌ Email này đã được sử dụng bởi tài khoản khác!");
    }

    // 🔹 Nếu có ảnh mới
    if ($anh) {
        $sql = "UPDATE nguoi_dung 
                SET ho_ten = ?, email = ?, anh_dai_dien = ?, ngay_sinh = ?, gioi_tinh = ? 
                WHERE id = ?";
        $params = [$hoTen, $email, $anh, $ngaySinh, $gioiTinh, $id];
    } else {
        // 🔹 Không có ảnh mới
        $sql = "UPDATE nguoi_dung 
                SET ho_ten = ?, email = ?, ngay_sinh = ?, gioi_tinh = ? 
                WHERE id = ?";
        $params = [$hoTen, $email, $ngaySinh, $gioiTinh, $id];
    }

    $stmt = $this->conn->prepare($sql);
    $stmt->execute($params);
}


}
