<?php

namespace Website\TinTuc\Models;

use Website\TinTuc\Database;
use PDO;
use PDOException;

class BaiVietModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    /**
     * Check whether a column exists in `bai_viet` table. Returns boolean.
     */
    private function columnExists(string $col): bool
    {
        $sql = "SHOW COLUMNS FROM bai_viet LIKE :col";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['col' => $col]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (bool)$row;
    }

    /**
     * Detect a tag column name if present in the table. Returns column name or null.
     */
    private function detectTagColumn(): ?string
    {
        $candidates = ['id', 'the_tag', 'tag', 'id_tag'];
        foreach ($candidates as $c) {
            if ($this->columnExists($c)) {
                return $c;
            }
        }
        return null;
    }

    // --- Lấy toàn bộ bài viết ---
    public function all()
    {
        $sql = "SELECT bv.*, cm.ten_chuyen_muc FROM bai_viet bv LEFT JOIN chuyen_muc cm ON bv.id_chuyen_muc = cm.id ORDER BY bv.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- Lấy các bài chờ duyệt ---
    public function getPending()
    {
        // Chuẩn hóa: kiểm tra giá trị chuỗi 'cho_duyet' (không phân biệt hoa thường)
        $sql = "SELECT bv.*, cm.ten_chuyen_muc FROM bai_viet bv LEFT JOIN chuyen_muc cm ON bv.id_chuyen_muc = cm.id WHERE LOWER(TRIM(bv.trang_thai)) = 'cho_duyet' OR bv.trang_thai = '0' ORDER BY bv.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- Lấy bài viết theo lịch đăng (ngày đăng trong tương lai) ---
    public function getScheduled()
    {
        $sql = "SELECT bv.*, cm.ten_chuyen_muc FROM bai_viet bv LEFT JOIN chuyen_muc cm ON bv.id_chuyen_muc = cm.id WHERE bv.ngay_dang > NOW() AND LOWER(TRIM(bv.trang_thai)) != 'tu_choi' ORDER BY bv.ngay_dang ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Publish scheduled posts whose scheduled time has arrived.
     * Only publish posts that are currently waiting for approval ('Cho_duyet').
     * Returns number of posts updated.
     */
    public function publishDueScheduled(): int
    {
        try {
            $sql = "UPDATE bai_viet SET trang_thai = 'Da_dang' WHERE ngay_dang <= NOW() AND (LOWER(TRIM(trang_thai)) = 'cho_duyet' OR trang_thai = 'Cho_duyet')";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log('publishDueScheduled error: ' . $e->getMessage());
            return 0;
        }
    }

    // --- Cập nhật trạng thái của bài viết ---
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE bai_viet SET trang_thai = :trang_thai WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['trang_thai' => $status, 'id' => (int)$id]);
    }

    // --- Tìm bài viết theo ID ---
    public function find($id)
    {
        $sql = "SELECT * FROM bai_viet WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // --- Thêm bài viết ---
    public function create($data)
    {
        // Normalize status: capitalize first word to match DB enum (Nhap, Cho_duyet, Da_dang)
        $status = $data['trang_thai'] ?? 'Nhap';
        $status = ucfirst(strtolower(str_replace('_', ' ', $status)));
        $status = str_replace(' ', '_', $status);

        // Detect which tag column (if any) exists in the table
        $tagColumn = null;
        if ($this->columnExists('id_the_tag')) {
            $tagColumn = 'id_the_tag';
        } elseif ($this->columnExists('tag')) {
            $tagColumn = 'tag';
        } elseif ($this->columnExists('the_tag')) {
            $tagColumn = 'the_tag';
        }

        // Build column list and placeholders dynamically
        $columns = ['tieu_de','mo_ta_ngan','noi_dung','anh_dai_dien','id_chuyen_muc','id_tac_gia','la_noi_bat','trang_thai','ngay_dang'];
        $placeholders = [':tieu_de',':mo_ta_ngan',':noi_dung',':anh_dai_dien',':id_chuyen_muc',':id_tac_gia',':la_noi_bat',':trang_thai',':ngay_dang'];
        if ($tagColumn) {
            // insert tag column just after id_chuyen_muc for compatibility
            array_splice($columns, 5, 0, $tagColumn);
            array_splice($placeholders, 5, 0, ':tag_val');
        }

        $sql = "INSERT INTO bai_viet (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $this->conn->prepare($sql);

        $params = [
            'tieu_de' => $data['tieu_de'] ?? '',
            'mo_ta_ngan' => $data['mo_ta_ngan'] ?? '',
            'noi_dung' => $data['noi_dung'] ?? '',
            'anh_dai_dien' => $data['anh_dai_dien'] ?? '',
            // validate id_chuyen_muc: if missing or invalid, set to NULL to satisfy FK constraints
            'id_chuyen_muc' => null,
            'id_tac_gia' => $data['id_tac_gia'] ?? null,
            'la_noi_bat' => $data['la_noi_bat'] ?? 0,
            'trang_thai' => $status,
            'ngay_dang' => $data['ngay_dang'] ?? date('Y-m-d H:i:s'),
        ];

        // validate and set id_chuyen_muc only if the category exists
        $rawChuyen = $data['id_chuyen_muc'] ?? null;
        if ($rawChuyen !== null && $rawChuyen !== '' && (int)$rawChuyen > 0) {
            $check = $this->conn->prepare("SELECT COUNT(*) FROM chuyen_muc WHERE id = ?");
            $check->execute([(int)$rawChuyen]);
            if ($check->fetchColumn() > 0) {
                $params['id_chuyen_muc'] = (int)$rawChuyen;
            }
        }

        if ($tagColumn) {
            $params['tag_val'] = $data['tag'] ?? ($data['id_the_tag'] ?? null);
        }

        $stmt->execute($params);
    }

    // --- Cập nhật bài viết ---
    public function update($id, $data)
    {
        // Normalize status: capitalize first word to match DB enum (Nhap, Cho_duyet, Da_dang)
        $status = $data['trang_thai'] ?? 'Nhap';
        $status = ucfirst(strtolower(str_replace('_', ' ', $status)));
        $status = str_replace(' ', '_', $status);

        $data['id'] = $id;
        // Detect tag column presence
        $tagColumn = null;
        if ($this->columnExists('id_the_tag')) {
            $tagColumn = 'id_the_tag';
        } elseif ($this->columnExists('tag')) {
            $tagColumn = 'tag';
        } elseif ($this->columnExists('the_tag')) {
            $tagColumn = 'the_tag';
        }

        // Build SET clauses dynamically
        $sets = [
            'tieu_de = :tieu_de',
            'mo_ta_ngan = :mo_ta_ngan',
            'noi_dung = :noi_dung',
            'anh_dai_dien = :anh_dai_dien',
            'id_chuyen_muc = :id_chuyen_muc',
            'id_tac_gia = :id_tac_gia',
        ];
        if ($tagColumn) {
            $sets[] = "{$tagColumn} = :tag_val";
        }
        $sets = array_merge($sets, [
            'la_noi_bat = :la_noi_bat',
            'trang_thai = :trang_thai',
            'ngay_dang = :ngay_dang'
        ]);

        $sql = "UPDATE bai_viet SET " . implode(', ', $sets) . " WHERE id=:id";
        $stmt = $this->conn->prepare($sql);

        $params = [
            'tieu_de' => $data['tieu_de'] ?? '',
            'mo_ta_ngan' => $data['mo_ta_ngan'] ?? '',
            'noi_dung' => $data['noi_dung'] ?? '',
            'anh_dai_dien' => $data['anh_dai_dien'] ?? '',
            // default to NULL; will validate below
            'id_chuyen_muc' => null,
            'id_tac_gia' => $data['id_tac_gia'] ?? null,
            'la_noi_bat' => $data['la_noi_bat'] ?? 0,
            'trang_thai' => $status,
            'ngay_dang' => $data['ngay_dang'] ?? date('Y-m-d H:i:s'),
            'id' => $id,
        ];

        // Validate id_chuyen_muc for update as well
        $rawChuyen = $data['id_chuyen_muc'] ?? null;
        if ($rawChuyen !== null && $rawChuyen !== '' && (int)$rawChuyen > 0) {
            $check = $this->conn->prepare("SELECT COUNT(*) FROM chuyen_muc WHERE id = ?");
            $check->execute([(int)$rawChuyen]);
            if ($check->fetchColumn() > 0) {
                $params['id_chuyen_muc'] = (int)$rawChuyen;
            } else {
                // leave as null to avoid FK violation
                $params['id_chuyen_muc'] = null;
            }
        } else {
            $params['id_chuyen_muc'] = null;
        }

        if ($tagColumn) {
            $params['tag_val'] = $data['tag'] ?? ($data['id_the_tag'] ?? null);
        }

        $stmt->execute($params);
    }

    // --- Xóa bài viết ---
    public function delete($id)
    {
        $sql = "DELETE FROM bai_viet WHERE id=:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();
    }

    // --- Tin mới nhất ---
    public function getTinMoiNhat($limit = 5)
    {
        $sql = "SELECT * FROM bai_viet WHERE trang_thai = 'da_dang' ORDER BY ngay_dang DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- Tin nổi bật ---
    public function getTinNoiBat($limit = 5)
    {
        $sql = "SELECT * FROM bai_viet WHERE la_noi_bat = 1 ORDER BY ngay_dang DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- Tin xem nhiều ---
    public function getTinXemNhieu($limit = 5)
    {
        $sql = "SELECT * FROM bai_viet ORDER BY luot_xem DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- Tin theo chuyên mục (toàn bộ, không phân trang) ---
    public function getTinTheoChuyenMuc($id_chuyen_muc)
    {
        try {
            $sql = "SELECT bv.*, cm.ten_chuyen_muc FROM bai_viet bv LEFT JOIN chuyen_muc cm ON bv.id_chuyen_muc = cm.id WHERE bv.id_chuyen_muc = :id ORDER BY bv.ngay_dang DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['id' => $id_chuyen_muc]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi getTinTheoChuyenMuc: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy bài viết theo danh sách id chuyên mục (dùng khi nhóm theo chuyên mục cha)
     * @param array $chuyenMucIds
     * @param int $limit
     * @return array
     */
    public function getTinTheoChuyenMucList(array $chuyenMucIds, $limit = 6)
    {
        if (empty($chuyenMucIds)) return [];
        // Prepare a dynamic placeholder list
        $placeholders = implode(',', array_fill(0, count($chuyenMucIds), '?'));
        $sql = "SELECT bv.*, cm.ten_chuyen_muc FROM bai_viet bv LEFT JOIN chuyen_muc cm ON bv.id_chuyen_muc = cm.id WHERE bv.id_chuyen_muc IN ($placeholders) ORDER BY bv.ngay_dang DESC LIMIT ?";
        $stmt = $this->conn->prepare($sql);

        // Bind each chuyen_muc id as integer to avoid being treated as string
        $pos = 1;
        foreach ($chuyenMucIds as $id) {
            $stmt->bindValue($pos, (int)$id, PDO::PARAM_INT);
            $pos++;
        }

        // Bind LIMIT as integer (important: some MySQL/MariaDB versions error if LIMIT is quoted)
        $stmt->bindValue($pos, (int)$limit, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- Lấy chi tiết bài viết ---
    public function getById($id)
    {
        $sql = "SELECT * FROM bai_viet WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // --- Tăng lượt xem ---
    public function tangLuotXem($id)
    {
        $sql = "UPDATE bai_viet SET luot_xem = luot_xem + 1 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
    }

    // --- Lấy bài viết theo chuyên mục (có phân trang) ---
    public function getByChuyenMuc($chuyenMucId, $limit, $offset)
    {
        // Không bind LIMIT/OFFSET bằng tham số trong MySQL cũ để tránh lỗi
        $sql = "SELECT * FROM bai_viet 
                WHERE id_chuyen_muc = :id 
                ORDER BY ngay_dang DESC 
                LIMIT $limit OFFSET $offset";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', (int)$chuyenMucId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function layBaiVietYeuThich($idNguoiDung)
    {
        $stmt = $this->conn->prepare("
            SELECT bv.tieu_de, bv.ngay_dang
            FROM yeu_thich yt
            JOIN bai_viet bv ON bv.id = yt.id_bai_viet
            WHERE yt.id_nguoi_dung = ?");
        $stmt->execute([$idNguoiDung]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function layBaiVietDaLuu($idNguoiDung)
    {
        $stmt = $this->conn->prepare("
            SELECT bv.tieu_de, bv.ngay_dang
            FROM luu_bai_viet lbv
            JOIN bai_viet bv ON bv.id = lbv.id_bai_viet
            WHERE lbv.id_nguoi_dung = ?");
        $stmt->execute([$idNguoiDung]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function countByChuyenMuc($id)
    {
        $sql = "SELECT COUNT(*) FROM bai_viet WHERE id_chuyen_muc = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function search($q)
    {
        $sql = "SELECT * FROM bai_viet
                WHERE (tieu_de LIKE :q OR noi_dung LIKE :q)
                AND trang_thai = 'da_dang'
                ORDER BY ngay_dang DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':q', '%' . $q . '%', PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
public function searchAll($keyword)
{
    $sql = "SELECT * FROM baiviet 
            WHERE tieu_de LIKE :keyword
               OR noi_dung LIKE :keyword
            ORDER BY id DESC";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['keyword' => "%$keyword%"]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

    // ======================
    // 2. COUNT SEARCH
    // ======================
    public function countSearch($q)
    {
        $sql = "SELECT COUNT(*) AS total
                FROM bai_viet
                WHERE (tieu_de LIKE :q OR noi_dung LIKE :q)
                AND trang_thai = 'da_dang'";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':q', '%' . $q . '%', PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['total'];
    }

    // ======================
    // 3. SUGGEST KHÔNG LIMIT
    // (nhưng vẫn giữ limit nhẹ 10 để tránh 1000 gợi ý)
    // ======================
    public function suggest($q)
    {
        $sql = "SELECT id, tieu_de FROM bai_viet
                WHERE (tieu_de LIKE :q OR noi_dung LIKE :q)
                AND trang_thai = 'da_dang'
                ORDER BY ngay_dang DESC
                LIMIT 10";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':q', '%' . $q . '%', PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByChuyenMucFilter($chuyenMucId, $limit, $offset, $filter)
{
    // Mặc định: mới nhất
    $orderBy = "ngay_dang DESC";

    if ($filter === 'xem_nhieu') {
        $orderBy = "luot_xem DESC";
    } elseif ($filter === 'binh_luan') {
        // Nếu có bảng bình luận thì dùng COUNT để sắp xếp
        $orderBy = "(SELECT COUNT(*) FROM binh_luan WHERE binh_luan.id_bai_viet = bv.id) DESC";
    }

    $sql = "SELECT bv.* 
            FROM bai_viet bv
            WHERE bv.id_chuyen_muc = :id
            ORDER BY $orderBy
            LIMIT $limit OFFSET $offset";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':id', (int)$chuyenMucId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    // --- Đếm tổng số bài viết (cho dashboard) ---
    public function countAll(): int
    {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) FROM bai_viet");
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('Lỗi countAll BaiVietModel: ' . $e->getMessage());
            return 0;
        }
    }

    // --- Tổng lượt xem tất cả bài viết ---
    public function totalViews(): int
    {
        try {
            $stmt = $this->conn->query("SELECT COALESCE(SUM(luot_xem),0) FROM bai_viet");
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('Lỗi totalViews BaiVietModel: ' . $e->getMessage());
            return 0;
        }
    }


}


