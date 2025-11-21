<?php
// Bài viết management menu + sub-fragment loader
$sub = $_GET['sub'] ?? null;
$subFragments = [
    'danhsach' => __DIR__ . '/danhsach_baiviet.php',
    'them' => __DIR__ . '/them_baiviet.php',
    'sua' => __DIR__ . '/sua_baiviet.php',
    'duyet' => __DIR__ . '/duyet_baiviet.php',
    // các fragment khác có thể thêm vào đây
];
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
.menu-links {
    margin-top: 15px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.menu-links .tag {
    display: inline-block;
    padding: 8px 12px;
    background: #f0f0f0;
    border-radius: 6px;
    text-decoration: none;
    color: #333;
    border: 1px solid #ddd;
}

.menu-links .tag:hover {
    background: #e8e8e8;
    border-color: #ccc;
}
.fragment { margin-top: 18px; }
</style>
