<?php
$page = $page ?? 1;
$totalPages = $totalPages ?? 1;
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($tenChuyenMuc ?? 'Chuyên mục') ?> - Website Tin Tức</title>
    <link rel="stylesheet" href="../views/frontend/frontend.css">
</head>

<body>
    <!-- === HEADER === -->
    <header>
        <h1><?= htmlspecialchars($tenChuyenMuc ?? 'Chuyên mục') ?></h1>
        <div class="auth-nav">
            <a href="index.php" class="auth-link">🏠 Trang chủ</a>
        </div>
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
                    <div class="tin">
                        <img src="<?= htmlspecialchars($tin['anh_dai_dien'] ?? 'uploads/no_image.png') ?>" alt="">
                        <div>
                            <a href="index.php?action=chi_tiet_bai_viet&id=<?= $tin['id'] ?>">
                                <b><?= htmlspecialchars($tin['tieu_de']) ?></b>
                            </a>
                            <small>📅 <?= htmlspecialchars($tin['ngay_dang']) ?> | 👁 <?= htmlspecialchars($tin['luot_xem']) ?></small>
                            <p><?= htmlspecialchars(mb_substr(strip_tags($tin['noi_dung']), 0, 100)) ?>...</p>
                        </div>
                    </div>
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
            <h2>🎯 Quảng cáo</h2>
            <div class="qc-item"><img src="uploads/ads1.jpg" alt="Quảng cáo 1"></div>
            <div class="qc-item"><img src="uploads/ads2.jpg" alt="Quảng cáo 2"></div>
            <div class="qc-item"><img src="uploads/ads3.jpg" alt="Quảng cáo 3"></div>
        </aside>
    </main>

    <!-- === FOOTER === -->
    <footer>
        © <?= date('Y') ?> Website Tin Tức. All rights reserved.
    </footer>
</body>

</html>