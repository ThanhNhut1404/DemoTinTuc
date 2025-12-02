<?php
use Website\TinTuc\Models\ChuyenMucModel;

// Get sub action (danhsach, them, sua, xoa)
$sub = $_GET['sub'] ?? null;
$subFragments = [
    'danhsach' => __DIR__ . '/danh_muc/danhsach_danhmuc.php',
    'them' => __DIR__ . '/danh_muc/them_danhmuc.php',
    'sua' => __DIR__ . '/danh_muc/sua_danhmuc.php',
];

$chuyenMucModel = new ChuyenMucModel();
$danhMucList = $chuyenMucModel->getAll();
?>

<div class="card">
    <h2>Quản lý Danh mục</h2>
    <p>Quản lý danh mục bài viết.</p>

    <div class="menu-links">
        <a href="admin.php?action=danh_muc&sub=danhsach" class="tag">📂 Danh sách danh mục</a>
        <a href="admin.php?action=danh_muc&sub=them" class="tag">➕ Thêm danh mục</a>
        <a href="admin.php?action=danh_muc&sub=sap_xep" class="tag">🔀 Sắp xếp thứ tự</a>
    </div>

    <div class="fragment">
        <?php
        if ($sub && isset($subFragments[$sub]) && file_exists($subFragments[$sub])) {
            include $subFragments[$sub];
        } elseif ($sub) {
            echo "<p>Fragment không tìm thấy: " . htmlspecialchars($sub) . "</p>";
        } else {
            // Mặc định hiển thị danh sách
            if (file_exists(__DIR__ . '/danh_muc/danhsach_danhmuc.php')) {
                include __DIR__ . '/danh_muc/danhsach_danhmuc.php';
            } else {
                echo "<p>Chưa có nội dung</p>";
            }
        }
        ?>
    </div>
</div>

<style>
/* ===== Card Style ===== */
.card {
    background: #ffffff;
    padding: 22px 26px;
    border-radius: 16px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    margin-top: 20px;
}

.card h2 {
    color: #007bff;
    font-size: 24px;
    margin-bottom: 8px;
}

.card p {
    color: #555;
    margin-bottom: 18px;
}

/* ===== Menu Link Style (giống nút Bộ lọc / Tìm kiếm) ===== */
.menu-links {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.menu-links .tag {
    background: #f7f9ff;
    color: #0d6efd;
    padding: 8px 14px;
    border-radius: 8px;
    border: 1px solid #dbe4ff;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.25s ease;
    display: inline-flex;
    align-items: center;
}

.menu-links .tag:hover {
    background: #e7efff;
    border-color: #b8ccff;
}

/* Nếu muốn kiểu button xanh giống "Cập nhật" */
.menu-links .tag.btn-blue {
    background: #0d6efd;
    color: #fff;
    border: none;
}

.menu-links .tag.btn-blue:hover {
    background: #0b5ed7;
}
.menu-links .tag.active {
    background: #0d6efd;
    color: #fff;
    border-color: #0d6efd;
}

/* ===== Content Fragment ===== */
.fragment {
    margin-top: 18px;
}
</style>