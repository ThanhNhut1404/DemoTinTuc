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
        'mat_khau' => 'mat_khau', // phải trùng tên cột trong DB
        'quyen' => 'quyen',
        'trang_thai' => 'trang_thai'
    ];

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
        $this->detectTableAndColumns(['users', 'nguoi_dung']);
    }

    private function detectTableAndColumns(array $candidates)
    {
        foreach ($candidates as $t) {
            try {
                $stmt = $this->conn->query("SELECT 1 FROM `$t` LIMIT 1");
                $this->table = $t;
                $cols = [];
                $desc = $this->conn->query("DESCRIBE `$t`");
                foreach ($desc->fetchAll(PDO::FETCH_COLUMN) as $colName) {
                    $cols[] = $colName;
                }
                $this->cols['id'] = $this->findColumn($cols, ['id', 'ID']);
                $this->cols['ho_ten'] = $this->findColumn($cols, ['ho_ten', 'name', 'full_name', 'ten']);
                $this->cols['email'] = $this->findColumn($cols, ['email', 'email_address']);
                $this->cols['quyen'] = $this->findColumn($cols, ['quyen', 'vai_tro', 'role', 'permission']);
                $this->cols['trang_thai'] = $this->findColumn($cols, ['trang_thai', 'trangthai', 'status']);
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
                if (strcasecmp($c, $cand) === 0) return $c;
            }
        }
        return $cols[0] ?? 'id';
    }

    private function findColumnStrict(array $cols, array $candidates)
    {
        foreach ($candidates as $cand) {
            foreach ($cols as $c) {
                if (strcasecmp($c, $cand) === 0) return $c;
            }
        }
        return null;
    }

    // =========================
    // Thêm hàm findById
    // =========================
    /**
     * Lấy thông tin người dùng theo ID
     */
    public function findById($id)
    {
        $idCol = $this->cols['id'];
        $sql = sprintf("SELECT * FROM `%s` WHERE `%s` = ?", $this->table, $idCol);
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll(?string $role = null, ?string $status = null, ?string $gender = null) {
        $idCol = $this->cols['id'];
        $nameCol = $this->cols['ho_ten'];
        $emailCol = $this->cols['email'];
        $roleCol = $this->cols['quyen'];
        $statusCol = $this->cols['trang_thai'];
        $genderCol = $this->cols['gioi_tinh'] ?? null;
        $dobCol = $this->cols['ngay_sinh'] ?? null;

        $selectParts = [sprintf("`%s` AS id", $idCol)];
        $avatarCol = $this->cols['avatar'] ?? null;
        if ($avatarCol) $selectParts[] = sprintf("`%s` AS avatar", $avatarCol);
        $selectParts[] = sprintf("`%s` AS ho_ten", $nameCol);
        if ($genderCol) $selectParts[] = sprintf("`%s` AS gioi_tinh", $genderCol);
        if ($dobCol) $selectParts[] = sprintf("`%s` AS ngay_sinh", $dobCol);
        $selectParts[] = sprintf("`%s` AS email", $emailCol);
        $selectParts[] = sprintf("`%s` AS quyen", $roleCol);
        $selectParts[] = sprintf("`%s` AS trang_thai", $statusCol);
        $selectSql = implode(', ', $selectParts);

        $whereParts = [];
        $params = [];
        if ($role) { $whereParts[] = sprintf("LOWER(`%s`) = LOWER(:role)", $roleCol); $params[':role'] = $role; }
        if ($status && $statusCol) { $whereParts[] = sprintf("LOWER(`%s`) = LOWER(:status)", $statusCol); $params[':status'] = $status; }
        if ($gender && $genderCol) { $whereParts[] = sprintf("LOWER(`%s`) = LOWER(:gender)", $genderCol); $params[':gender'] = $gender; }

        if (count($whereParts) > 0) {
            $sql = sprintf("SELECT %s FROM `%s` WHERE %s ORDER BY `%s` DESC", $selectSql, $this->table, implode(' AND ', $whereParts), $idCol);
            $stmt = $this->conn->prepare($sql);
            foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
        } else {
            $sql = sprintf("SELECT %s FROM `%s` ORDER BY `%s` DESC", $selectSql, $this->table, $idCol);
            $stmt = $this->conn->prepare($sql);
        }

        $stmt->execute();
        return $this->normalizeRows($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function countAll()
    {
        $sql = sprintf("SELECT COUNT(*) FROM `%s`", $this->table);
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    // Tìm kiếm người dùng theo tên hoặc email
    public function search(string $keyword, ?string $role = null, ?string $status = null, ?string $gender = null) {
        $idCol = $this->cols['id'];
        $nameCol = $this->cols['ho_ten'];
        $emailCol = $this->cols['email'];
        $roleCol = $this->cols['quyen'];
        $statusCol = $this->cols['trang_thai'];
        $genderCol = $this->cols['gioi_tinh'] ?? null;
        $dobCol = $this->cols['ngay_sinh'] ?? null;

        $selectParts = [sprintf("`%s` AS id", $idCol)];
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

        $whereParts = [sprintf("(`%s` LIKE :kw OR `%s` LIKE :kw)", $nameCol, $emailCol)];
        $params = [':kw' => $like];
        if ($role) { $whereParts[] = sprintf("LOWER(`%s`) = LOWER(:role)", $roleCol); $params[':role'] = $role; }
        if ($status && $statusCol) { $whereParts[] = sprintf("LOWER(`%s`) = LOWER(:status)", $statusCol); $params[':status'] = $status; }
        if ($gender && $genderCol) { $whereParts[] = sprintf("LOWER(`%s`) = LOWER(:gender)", $genderCol); $params[':gender'] = $gender; }

        $sql = sprintf("SELECT %s FROM `%s` WHERE %s ORDER BY `%s` DESC",
            $selectSql, $this->table, implode(' AND ', $whereParts), $idCol
        );

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->execute();

        return $this->normalizeRows($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

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

    public function toggleStatus($id) {
        $statusCol = $this->cols['trang_thai'];
        $sql = sprintf(
            "UPDATE `%s` SET `%s` = (CASE WHEN LOWER(`%s`) IN ('khoa','bi_khoa','locked') 
            THEN 'Hoat_dong' ELSE 'Khoa' END) WHERE `%s` = ?",
            $this->table, $statusCol, $statusCol, $this->cols['id']
        );
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function updateRole($id, $role) {
        $roleCol = $this->cols['quyen'];
        $idCol = $this->cols['id'];
        $sql = sprintf("UPDATE `%s` SET `%s` = ? WHERE `%s` = ?", $this->table, $roleCol, $idCol);
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$role, $id]);
    }

    /**
     * Xóa người dùng theo ID
     */
    public function deleteById($id)
    {
        $idCol = $this->cols['id'];
        $sql = sprintf("DELETE FROM `%s` WHERE `%s` = ?", $this->table, $idCol);
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function layThongTinNguoiDung($id) {
        $idCol = $this->cols['id'];
        $sql = sprintf("SELECT * FROM `%s` WHERE `%s` = ?", $this->table, $idCol);
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function capNhatThongTin($id, $hoTen, $email, $anh = null, $ngaySinh = null, $gioiTinh = null) {
        $emailCol = $this->cols['email'];
        $idCol = $this->cols['id'];
        $checkSql = sprintf("SELECT `%s` FROM `%s` WHERE `%s` = ? AND `%s` != ?", $emailCol, $this->table, $emailCol, $idCol);
        $check = $this->conn->prepare($checkSql);
        $check->execute([$email, $id]);
        if ($check->fetch()) { throw new \Exception("❌ Email này đã được sử dụng bởi tài khoản khác!"); }

        $nameCol = $this->cols['ho_ten'];
        $avatarCol = $this->cols['avatar'] ?? null;
        $dobCol = $this->cols['ngay_sinh'] ?? null;
        $genderCol = $this->cols['gioi_tinh'] ?? null;

        $setParts = [];
        $params = [];
        $setParts[] = sprintf("`%s` = ?", $nameCol); $params[] = $hoTen;
        $setParts[] = sprintf("`%s` = ?", $emailCol); $params[] = $email;
        if ($anh && $avatarCol) { $setParts[] = sprintf("`%s` = ?", $avatarCol); $params[] = $anh; }
        if ($dobCol) { $setParts[] = sprintf("`%s` = ?", $dobCol); $params[] = $ngaySinh; }
        if ($genderCol) { $setParts[] = sprintf("`%s` = ?", $genderCol); $params[] = $gioiTinh; }

        if (empty($setParts)) return false;

        $sql = sprintf("UPDATE `%s` SET %s WHERE `%s` = ?", $this->table, implode(', ', $setParts), $idCol);
        $params[] = $id;
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function findByEmail(string $email)
    {
        $emailCol = $this->cols['email'];
        $sql = sprintf("SELECT * FROM `%s` WHERE `%s` = ?", $this->table, $emailCol);
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Return user row mapped to logical column names (id, ho_ten, email, mat_khau, quyen, ...)
     */
    public function findByEmailNormalized(string $email)
    {
        $row = $this->findByEmail($email);
        if (!$row) return false;
        $normalized = [];
        foreach ($this->cols as $logical => $actual) {
            $normalized[$logical] = $row[$actual] ?? null;
        }
        return $normalized;
    }

    public function updatePassword(string $email, string $hashedPassword)
    {
        $emailCol = $this->cols['email'];
        $realCols = array_values($this->cols);
        $passwordCol = $this->findColumnStrict($realCols, ['mat_khau', 'password', 'pass', 'mk']);

        if (!$passwordCol) {
            throw new \Exception("Không tìm thấy cột mật khẩu trong bảng {$this->table}");
        }
        $sql = sprintf("UPDATE `%s` SET `%s` = ? WHERE `%s` = ?", $this->table, $passwordCol, $emailCol);
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$hashedPassword, $email]);
    }

    public function createResetToken($email)
{
    $token = bin2hex(random_bytes(32));
    $expires = date("Y-m-d H:i:s", strtotime("+30 minutes"));

    $sql = "UPDATE nguoi_dung SET reset_token=?, reset_expires=? WHERE email=?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$token, $expires, $email]);

    if ($stmt->rowCount() > 0) {
        return $token;
    }

    return false;
}

public function validateResetToken($token)
{
    $sql = "SELECT * FROM nguoi_dung WHERE reset_token=? AND reset_expires > NOW()";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$token]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function resetPasswordByToken($token, $password)
{
    $sql = "UPDATE nguoi_dung 
            SET mat_khau=?, reset_token=NULL, reset_expires=NULL 
            WHERE reset_token=?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$password, $token]);
}


    // =========================
    // Thêm hàm updateProfile
    // =========================
    public function updateProfile($id, $ho_ten, $so_dien_thoai, $dia_chi, $avatar = null)
    {
        // Thư mục upload
        $uploadDir = __DIR__ . "/../../public/uploads/avatars/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $avatarName = null;

        if ($avatar && isset($avatar['size']) && $avatar['size'] > 0) {
            $ext = pathinfo($avatar['name'], PATHINFO_EXTENSION);

            $avatarName = "avatar_" . $id . "_" . time() . "." . $ext;

            $avatarPath = $uploadDir . $avatarName;

            if (!move_uploaded_file($avatar['tmp_name'], $avatarPath)) {
                return false;
            }

            $sql = "UPDATE users 
                    SET ho_ten = ?, so_dien_thoai = ?, dia_chi = ?, avatar = ?
                    WHERE id = ?";
            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([$ho_ten, $so_dien_thoai, $dia_chi, $avatarName, $id]);
        }

        // Không đổi avatar
        $sql = "UPDATE users 
                SET ho_ten = ?, so_dien_thoai = ?, dia_chi = ?
                WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([$ho_ten, $so_dien_thoai, $dia_chi, $id]);
    }
}
