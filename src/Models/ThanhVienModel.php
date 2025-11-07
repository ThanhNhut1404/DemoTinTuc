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
                // optional columns: avatar, gender and dob (use strict match and allow null)
                $this->cols['avatar'] = $this->findColumnStrict($cols, ['avatar', 'anh_dai_dien', 'avatar_url', 'anh', 'hinh_anh']);
                $this->cols['gioi_tinh'] = $this->findColumnStrict($cols, ['gioi_tinh', 'gender', 'sex']);
                $this->cols['ngay_sinh'] = $this->findColumnStrict($cols, ['ngay_sinh', 'dob', 'birth_date', 'birthday']);
                return;
            } catch (\Exception $e) {
                continue;
            }
        }
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
        // fallback to first column if nothing matches (preserve previous behavior)
        return $cols[0] ?? 'id';
    }

    /**
     * Find a matching column case-insensitively, but return null if none match.
     */
    private function findColumnStrict(array $cols, array $candidates)
    {
        foreach ($candidates as $cand) {
            foreach ($cols as $c) {
                if (strcasecmp($c, $cand) === 0) {
                    return $c;
                }
            }
        }
        return null;
    }

    // Lấy danh sách tất cả người dùng
    // Lấy danh sách tất cả người dùng
    public function getAll(?string $role = null, ?string $status = null, ?string $gender = null) {
        $idCol = $this->cols['id'];
        $nameCol = $this->cols['ho_ten'];
        $emailCol = $this->cols['email'];
        $roleCol = $this->cols['quyen'];
        $statusCol = $this->cols['trang_thai'];
        $genderCol = $this->cols['gioi_tinh'] ?? null;
        $dobCol = $this->cols['ngay_sinh'] ?? null;

        // build select list dynamically to include optional columns
        $selectParts = [
            sprintf("`%s` AS id", $idCol),
        ];
        // optional avatar before ho_ten
        $avatarCol = $this->cols['avatar'] ?? null;
        if ($avatarCol) $selectParts[] = sprintf("`%s` AS avatar", $avatarCol);
        $selectParts[] = sprintf("`%s` AS ho_ten", $nameCol);
        if ($genderCol) $selectParts[] = sprintf("`%s` AS gioi_tinh", $genderCol);
        if ($dobCol) $selectParts[] = sprintf("`%s` AS ngay_sinh", $dobCol);
        $selectParts[] = sprintf("`%s` AS email", $emailCol);
        $selectParts[] = sprintf("`%s` AS quyen", $roleCol);
        $selectParts[] = sprintf("`%s` AS trang_thai", $statusCol);
        $selectSql = implode(', ', $selectParts);

        // Build WHERE clauses dynamically for role/status/gender (single-value filters)
        $whereParts = [];
        $params = [];
        if ($role !== null && $role !== '') {
            $whereParts[] = sprintf("LOWER(`%s`) = LOWER(:role)", $roleCol);
            $params[':role'] = $role;
        }
        if ($status !== null && $status !== '' && $statusCol) {
            $whereParts[] = sprintf("LOWER(`%s`) = LOWER(:status)", $statusCol);
            $params[':status'] = $status;
        }
        if ($gender !== null && $gender !== '' && $genderCol) {
            $whereParts[] = sprintf("LOWER(`%s`) = LOWER(:gender)", $genderCol);
            $params[':gender'] = $gender;
        }

        if (count($whereParts) > 0) {
            $sql = sprintf("SELECT %s FROM `%s` WHERE %s ORDER BY `%s` DESC", $selectSql, $this->table, implode(' AND ', $whereParts), $idCol);
            $stmt = $this->conn->prepare($sql);
            foreach ($params as $k => $v) {
                $stmt->bindValue($k, $v, PDO::PARAM_STR);
            }
        } else {
            $sql = sprintf("SELECT %s FROM `%s` ORDER BY `%s` DESC", $selectSql, $this->table, $idCol);
            $stmt = $this->conn->prepare($sql);
        }

        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->normalizeRows($rows);
    }

    // Tìm kiếm người dùng theo tên hoặc email
    // Tìm kiếm người dùng theo tên hoặc email
    public function search(string $keyword, ?string $role = null, ?string $status = null, ?string $gender = null) {
        $idCol = $this->cols['id'];
        $nameCol = $this->cols['ho_ten'];
        $emailCol = $this->cols['email'];
        $roleCol = $this->cols['quyen'];
        $statusCol = $this->cols['trang_thai'];
        $genderCol = $this->cols['gioi_tinh'] ?? null;
        $dobCol = $this->cols['ngay_sinh'] ?? null;

        $selectParts = [
            sprintf("`%s` AS id", $idCol),
        ];
        // optional avatar before ho_ten
        $avatarCol = $this->cols['avatar'] ?? null;
        if ($avatarCol) $selectParts[] = sprintf("`%s` AS avatar", $avatarCol);
        $selectParts[] = sprintf("`%s` AS ho_ten", $nameCol);
        if ($genderCol) $selectParts[] = sprintf("`%s` AS gioi_tinh", $genderCol);
        if ($dobCol) $selectParts[] = sprintf("`%s` AS ngay_sinh", $dobCol);
        $selectParts[] = sprintf("`%s` AS email", $emailCol);
        $selectParts[] = sprintf("`%s` AS quyen", $roleCol);
        $selectParts[] = sprintf("`%s` AS trang_thai", $statusCol);
        $selectSql = implode(', ', $selectParts);
        $like = '%' . $keyword . '%';

        // build where clauses dynamically: first the name/email LIKE part, then optional filters
        $whereParts = [];
        $params = [':kw' => $like];
        $whereParts[] = sprintf("(`%s` LIKE :kw OR `%s` LIKE :kw)", $nameCol, $emailCol);

        // build where clauses dynamically: first the name/email LIKE part, then optional filters
        $whereParts = [];
        $params = [':kw' => $like];
        $whereParts[] = sprintf("(`%s` LIKE :kw OR `%s` LIKE :kw)", $nameCol, $emailCol);

        if ($role !== null && $role !== '') {
            $whereParts[] = sprintf("LOWER(`%s`) = LOWER(:role)", $roleCol);
            $params[':role'] = $role;
        }
        if ($status !== null && $status !== '' && $statusCol) {
            $whereParts[] = sprintf("LOWER(`%s`) = LOWER(:status)", $statusCol);
            $params[':status'] = $status;
        }
        if ($gender !== null && $gender !== '' && $genderCol) {
            $whereParts[] = sprintf("LOWER(`%s`) = LOWER(:gender)", $genderCol);
            $params[':gender'] = $gender;
        }

        $sql = sprintf("SELECT %s FROM `%s` WHERE %s ORDER BY `%s` DESC", $selectSql, $this->table, implode(' AND ', $whereParts), $idCol);
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, PDO::PARAM_STR);
        }

        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->normalizeRows($rows);
    }

    /**
     * Normalize row values (especially trang_thai) to consistent display values
     * @param array $rows
     * @return array
     */
    private function normalizeRows(array $rows): array
    {
        foreach ($rows as &$r) {
            $val = $r['trang_thai'] ?? '';
            $low = mb_strtolower(trim((string)$val), 'UTF-8');
            if ($low === '' || in_array($low, ['hoat_dong', 'hoạt_động', 'active', 'hoạt động', 'hoat dong'])) {
                $r['trang_thai'] = 'Hoat_dong';
            } elseif (in_array($low, ['khoa', 'bi_khoa', 'locked'])) {
                $r['trang_thai'] = 'Khoa';
            } else {
                $r['trang_thai'] = $val === null ? 'Hoat_dong' : $val;
            }
        }
        return $rows;
    }

    // Khóa / mở khóa tài khoản
    public function toggleStatus($id) {
        $statusCol = $this->cols['trang_thai'];
        // Toggle between common variants (hoat_dong/bi_khoa) or (active/locked)
        // Toggle considering different value variants (case-insensitive).
            // If current value is a variant of 'khoa' (locked), set to 'Hoat_dong', otherwise set to 'Khoa'.
            // Use LOWER for case-insensitive comparison and consider common variants.
            $sql = sprintf(
                "UPDATE `%s` SET `%s` = (CASE WHEN LOWER(`%s`) IN ('khoa','bi_khoa','locked') THEN 'Hoat_dong' ELSE 'Khoa' END) WHERE `%s` = ?",
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
