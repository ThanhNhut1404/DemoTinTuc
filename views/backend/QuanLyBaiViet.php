<?php
// Bài viết management menu + sub-fragment loader
use Website\TinTuc\Models\ChuyenMucModel;
use Website\TinTuc\Models\TagModel;
use Website\TinTuc\Models\ChuyenMucChaModel;

$sub = $_GET['sub'] ?? null;
$subFragments = [
    'danhsach' => __DIR__ . '/danhsach_baiviet.php',
    'them' => __DIR__ . '/them_baiviet.php',
    'sua' => __DIR__ . '/sua_baiviet.php',
    'duyet' => __DIR__ . '/duyet_baiviet.php',
    'lich' => __DIR__ . '/lich_dang_bai.php',
    // các fragment khác có thể thêm vào đây
];

// Fetch categories for dropdowns in 'them' and 'sua' subfragments
if (!isset($chuyenMucList)) {
    $chuyenMucModel = new ChuyenMucModel();
    $chuyenMucList = $chuyenMucModel->getAll();
}

// Fetch parent categories (chuyen_muc_cha) so we can display readable names in the 'them' fragment
if (!isset($chuyenMucChaList)) {
    try {
        $cmChaModel = new ChuyenMucChaModel();
        $chuyenMucChaList = $cmChaModel->getAll();
    } catch (\Exception $e) {
        $chuyenMucChaList = [];
    }
}

// Fetch tags for dropdowns in 'them' and 'sua' subfragments
if (!isset($tagList)) {
    $tagModel = new TagModel();
    $tagList = $tagModel->getAll();
}
?>
<div class="card">
    <h2>Quản lý bài viết</h2>
    <p>Danh sách bài viết, chỉnh sửa, xóa, thêm mới.</p>

    <div class="menu-links">
        <a href="admin.php?action=bai_viet&sub=danhsach" class="tag">📄 Danh sách bài viết</a>
        <a href="admin.php?action=bai_viet&sub=them" class="tag">✏️ Thêm bài viết</a>
        <a href="admin.php?action=bai_viet&sub=duyet" class="tag">✔️ Duyệt bài viết</a>
        <a href="admin.php?action=bai_viet&sub=lich" class="tag">⏰ Lịch đăng bài</a>
    </div>

    <div class="fragment">
        <?php
        if ($sub && isset($subFragments[$sub]) && file_exists($subFragments[$sub])) {
            include $subFragments[$sub];
        } elseif ($sub) {
            echo "<div class=\"card\">Fragment không tìm thấy: " . htmlspecialchars($sub) . "</div>";
        } else {
            // mặc định hiển thị danh sách
            if (file_exists(__DIR__ . '/danhsach_baiviet.php')) {
                include __DIR__ . '/danhsach_baiviet.php';
            } else {
                echo "<div class=\"card\">Chưa có nội dung</div>";
            }
        }
        ?>
    </div>
</div>

<style>
.card {
    background: #fff;
    padding: 20px 25px;
    border-radius: 16px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    margin-top: 20px;
}

.card h2 {
    color: #007bff;
    font-size: 24px;
    margin-bottom: 10px;
}

.card p {
    color: #555;
    margin-bottom: 20px;
}

/* Menu link giống nút “Bộ lọc”, “Tìm kiếm” */
.menu-links {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.menu-links .tag {
    background: #0d6efd;
    color: #fff;
    padding: 8px 14px;
    border-radius: 6px;
    text-decoration: none;
    border: none;
    transition: 0.2s;
}

.menu-links .tag:hover {
    background: #0b5ed7;
}

/* Fragment content */
.fragment {
    margin-top: 18px;
}

</style>
