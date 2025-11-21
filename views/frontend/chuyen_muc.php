<?php
$page = $page ?? 1;
$totalPages = $totalPages ?? 1;
use Website\TinTuc\Models\QuangcaoModel;
$qcModel = new QuangcaoModel();
$dsQuangCao = $qcModel->getQuangCaoTheoViTri('Sidebar');
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($tenChuyenMuc ?? 'Chuyên mục') ?> - Website Tin Tức</title>
    <link rel="stylesheet" href="../views/frontend/frontend.css">
    <style>
    /* Local tweaks for category page */
    .search-container { margin-right: auto; }
    .search-input { padding: 8px 12px; border-radius: 6px; border: none; width: 280px; }
    .search-button { margin-left:6px; padding:8px 10px; border-radius:6px; background:#0069d9; color:#fff; border:none; cursor:pointer; }
    .tin-link { display:block; text-decoration:none; color:inherit; }
    .tin { padding:12px; transition: transform .18s ease, box-shadow .18s ease; }
    .tin:hover { transform: translateY(-4px); box-shadow: 0 6px 18px rgba(0,0,0,0.06); }
    .title { font-size:1.05em; color:#005fa3; margin:0 0 6px 0; }
    </style>
</head>

<body>
    <!-- === HEADER === -->
    <header>
        <nav class="auth-nav">

          <form id="searchForm" action="index.php" method="get" class="search-container">
    <input type="hidden" name="action" value="search">

    <div class="search-wrapper">
        <input type="text" id="searchBox" name="q" placeholder="Bạn muốn tìm gì hôm nay?" autocomplete="off" class="search-input">
        <button type="submit" class="search-button">🔍</button>
    </div>
</form>

            <a href="index.php" class="auth-link">🏠 Trang chủ</a>
            <a href="index.php?action=login" class="auth-link">Đăng nhập</a>
        </nav>
        <h1><?= htmlspecialchars($tenChuyenMuc ?? 'Chuyên mục') ?></h1>
        <p style="color:#555;">Danh sách bài viết theo chuyên mục - <?= htmlspecialchars($tenChuyenMuc ?? '') ?></p>
    </header>

    <!-- === MAIN CONTENT === -->
    <main>
        <!-- Cột trái: Danh sách chuyên mục -->
        <aside class="category-list">
            <h2>📂 Chuyên mục</h2>
            <ul class="category-menu">
                <?php

                use Website\TinTuc\Models\ChuyenMucModel;

                $chuyenMucModel = new ChuyenMucModel();
                $dsChuyenMuc = $chuyenMucModel->getAll();
                foreach ($dsChuyenMuc as $cm):
                ?>
                    <li>
                        <a href="index.php?action=chuyenmuc&id=<?= $cm['id'] ?>"
                            <?= ($cm['id'] == $chuyenMuc['id']) ? 'style="font-weight:bold;color:#005fa3;"' : '' ?>>
                            <?= htmlspecialchars($cm['ten_chuyen_muc']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>

        <!-- Cột giữa: Danh sách bài viết -->
        <section class="section">
            <!-- Bộ lọc bài viết -->
            <div class="filter-bar" style="margin-bottom:15px;text-align:right;">
                <form method="get" action="index.php">
                    <input type="hidden" name="action" value="chuyenmuc">
                    <input type="hidden" name="id" value="<?= $chuyenMuc['id'] ?>">

                    <label for="filter">Sắp xếp theo:</label>
                    <select name="filter" id="filter" onchange="this.form.submit()" style="padding:6px 10px;border-radius:4px;">
                        <option value="moi_nhat" <?= ($filter == 'moi_nhat') ? 'selected' : '' ?>>🕓 Mới nhất</option>
                        <option value="xem_nhieu" <?= ($filter == 'xem_nhieu') ? 'selected' : '' ?>>👁 Xem nhiều nhất</option>
                        <option value="binh_luan" <?= ($filter == 'binh_luan') ? 'selected' : '' ?>>💬 Bình luận nhiều nhất</option>
                    </select>
                </form>
            </div>

            <h2>📰 <?= htmlspecialchars($tenChuyenMuc ?? '') ?></h2>

            <?php if (empty($baiViet)): ?>
                <p>❌ Chưa có bài viết nào trong chuyên mục này.</p>
            <?php else: ?>
                <?php foreach ($baiViet as $tin): ?>
                    <a href="index.php?action=chi_tiet_bai_viet&id=<?= $tin['id'] ?>" class="tin-link">
                        <div class="tin">
                            <img src="<?= htmlspecialchars($tin['anh_dai_dien'] ?? 'uploads/no_image.png') ?>" alt="<?= htmlspecialchars($tin['tieu_de']) ?>">
                            <div>
                                <h3 class="title"><?= htmlspecialchars($tin['tieu_de']) ?></h3>
                                <small>📅 <?= htmlspecialchars($tin['ngay_dang']) ?> | 👁 <?= htmlspecialchars($tin['luot_xem']) ?></small>
                                <p><?= htmlspecialchars(mb_substr(strip_tags($tin['noi_dung']), 0, 140)) ?>...</p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Phân trang -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination" style="text-align:center;margin-top:15px;">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="index.php?action=chuyenmuc&id=<?= $chuyenMuc['id'] ?>&page=<?= $i ?>"
                            class="page-link <?= $i == $page ? 'active' : '' ?>"
                            style="display:inline-block;padding:6px 12px;margin:2px;border-radius:6px;
                      border:1px solid #0077cc;text-decoration:none;
                      <?= $i == $page ? 'background:#0077cc;color:white;' : 'color:#0077cc;' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Cột phải: Quảng cáo -->
        <aside class="category-list">
            <h2>Quảng cáo</h2>
            <div class="quangcao-sidebar">
                <?php foreach ($dsQuangCao as $index => $qc): ?>
                    <?php
                    // Nếu không có ảnh thì dùng ảnh mặc định
                    $img = !empty($qc['hinh_anh']) ? $qc['hinh_anh'] : 'uploads/default_ads.jpg';
                    // Nếu không có link thì để #
                    $link = !empty($qc['lien_ket']) ? $qc['lien_ket'] : '#';
                    ?>
                    <div class="qc-item <?= $index >= 4 ? 'hidden' : '' ?>">
                        <a href="<?= htmlspecialchars($link) ?>" target="_blank">
                            <img src="<?= htmlspecialchars($img) ?>" alt="Quảng cáo">
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </aside>
    </main>

    <!-- === FOOTER === -->
    <footer>
        © <?= date('Y') ?> Website Tin Tức. All rights reserved.
    </footer>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let items = document.querySelectorAll(".qc-item");
            let visibleCount = 4;
            let startIndex = 0;

            setInterval(() => {
                // Ẩn tất cả
                items.forEach(item => item.classList.add("hidden"));

                // Hiển thị 4 quảng cáo tiếp theo
                for (let i = 0; i < visibleCount; i++) {
                    let idx = (startIndex + i) % items.length;
                    items[idx].classList.remove("hidden");
                }

                startIndex = (startIndex + visibleCount) % items.length;
            }, 5000); // đổi quảng cáo mỗi 5 giây
        });
    </script>
</body>

</html>