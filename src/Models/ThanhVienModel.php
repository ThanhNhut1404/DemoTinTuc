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
        return $cols[0] ?? 'id';
    }

    // Lấy danh sách tất cả người dùng
    public function getAll(?string $role = null) {
        $idCol = $this->cols['id'];
        $nameCol = $this->cols['ho_ten'];
        $emailCol = $this->cols['email'];
        $roleCol = $this->cols['quyen'];
        $statusCol = $this->cols['trang_thai'];

        if ($role !== null && $role !== '') {
            $sql = sprintf(
                "SELECT `%s` AS id, `%s` AS ho_ten, `%s` AS email, `%s` AS quyen, `%s` AS trang_thai 
                 FROM `%s` WHERE `%s` = :role ORDER BY `%s` DESC",
                $idCol, $nameCol, $emailCol, $roleCol, $statusCol, 
                $this->table, $roleCol, $idCol
            );
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':role', $role, PDO::PARAM_STR);
        } else {
            $sql = sprintf(
                "SELECT `%s` AS id, `%s` AS ho_ten, `%s` AS email, `%s` AS quyen, `%s` AS trang_thai 
                 FROM `%s` ORDER BY `%s` DESC",
                $idCol, $nameCol, $emailCol, $roleCol, $statusCol, 
                $this->table, $idCol
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
                "SELECT `%s` AS id, `%s` AS ho_ten, `%s` AS email, `%s` AS quyen, `%s` AS trang_thai 
                 FROM `%s` WHERE (`%s` LIKE :kw OR `%s` LIKE :kw) 
                 AND `%s` = :role ORDER BY `%s` DESC",
                $idCol, $nameCol, $emailCol, $roleCol, $statusCol, 
                $this->table, $nameCol, $emailCol, $roleCol, $idCol
            );
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':kw', $like, PDO::PARAM_STR);
            $stmt->bindValue(':role', $role, PDO::PARAM_STR);
        } else {
            $sql = sprintf(
                "SELECT `%s` AS id, `%s` AS ho_ten, `%s` AS email, `%s` AS quyen, `%s` AS trang_thai 
                 FROM `%s` WHERE `%s` LIKE :kw OR `%s` LIKE :kw ORDER BY `%s` DESC",
                $idCol, $nameCol, $emailCol, $roleCol, $statusCol, 
                $this->table, $nameCol, $emailCol, $idCol
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
        $sql = sprintf(
            "UPDATE `%s` SET `%s` = IF(LOWER(`%s`) IN ('hoat_dong','active','hoat dong','hoạt_động','khoa'), 'Khoa', 'Hoat_dong') 
             WHERE `%s` = ?",
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

    // Tìm người dùng theo email
    public function findByEmail($email)
    {
        $emailCol = $this->cols['email'];
        $idCol = $this->cols['id'];
        $nameCol = $this->cols['ho_ten'];
        $roleCol = $this->cols['quyen'];
        $statusCol = $this->cols['trang_thai'];

        $sql = sprintf(
            "SELECT `%s` AS id, `%s` AS ho_ten, `%s` AS email, `%s` AS quyen, `%s` AS trang_thai 
             FROM `%s` WHERE `%s` = :email LIMIT 1",
            $idCol, $nameCol, $emailCol, $roleCol, $statusCol,
            $this->table, $emailCol
        );
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật mật khẩu người dùng (đã sửa lỗi)
    public function updatePassword($email, $hashedPassword)
    {
        $emailCol = $this->cols['email'];
        $sql = sprintf(
            "UPDATE `%s` SET `mat_khau` = :password WHERE `%s` = :email",
            $this->table, $emailCol
        );
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':password' => $hashedPassword,
            ':email' => $email
        ]);
    }
}
