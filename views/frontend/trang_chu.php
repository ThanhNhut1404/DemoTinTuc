<?php
// Tránh warning nếu controller chưa truyền biến
$banners = isset($banners) && is_array($banners) ? $banners : [];
$quangCaoTrai = isset($quangCaoTrai) && is_array($quangCaoTrai) ? $quangCaoTrai : [];
$quangCaoPhai = isset($quangCaoPhai) && is_array($quangCaoPhai) ? $quangCaoPhai : [];
$chuyenMuc = isset($chuyenMuc) && is_array($chuyenMuc) ? $chuyenMuc : [];
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Trang chủ - Website Tin Tức</title>
    <link rel="stylesheet" href="../views/frontend/frontend.css">
</head>
<style>
    .tin-link {
        display: block;
        text-decoration: none;
        color: inherit;
    }

    .tin-link .tin {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 12px;
        background: #fafafa;
        border-radius: 8px;
        overflow: hidden;
        transition: 0.3s;
        border: 1px solid #eee;
    }

    .tin-link .tin:hover {
        background: #f1f9ff;
        transform: scale(1.01);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .tin img {
        width: 160px;
        height: 110px;
        object-fit: cover;
        border-radius: 8px 0 0 8px;
    }

    .tin .title {
        font-weight: bold;
        color: #005fa3;
        margin-bottom: 5px;
    }

    .tin .title:hover {
        color: #d9534f;
    }
</style>

<body>

    <!-- 🎞️ BANNER SLIDE -->
    <div class="banner-container">
        <?php foreach ($banners as $index => $b): ?>
            <div class="banner-slide <?= $index === 0 ? 'active' : '' ?>">
                <a href="<?= htmlspecialchars($b['lien_ket']) ?>" target="_blank">
                    <img src="<?= htmlspecialchars($b['hinh_banner']) ?>" alt="<?= htmlspecialchars($b['mo_ta'] ?? '') ?>">
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="banner-dots">
        <?php foreach ($banners as $index => $b): ?>
            <span class="dot <?= $index === 0 ? 'active' : '' ?>" onclick="showBanner(<?= $index ?>)"></span>
        <?php endforeach; ?>
    </div>

    <header>
        <nav class="auth-nav">
            <a href="index.php?action=login" class="auth-link">Đăng nhập</a>
            <a href="index.php?action=register" class="auth-link">Đăng ký</a>
        </nav>
        <h1>🌐 Website Tin Tức</h1>
        <p>Cập nhật tin tức mới nhất, nổi bật và hấp dẫn mỗi ngày</p>
    </header>

    <main>
        <!-- menu chuyên mục -->
        <aside class="category-list">
            <div class="section">
                <h2>Chuyên mục</h2>
                <ul class="category-menu">
                    <?php foreach ($chuyenMuc as $cm): ?>
                        <li>
                            <!-- 👉 Chuyển sang trang chuyên mục riêng -->
                            <a href="index.php?action=chuyenmuc&id=<?= htmlspecialchars($cm['id']) ?>">
                                <?= htmlspecialchars($cm['ten_chuyen_muc']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>

        <!-- NỘI DUNG CHÍNH -->
        <div class="content">
            <div class="section">
                <h2>🔥 Top 5 tin nổi bật</h2>
                <div class="slide">
                    <?php foreach ($tinNoiBat as $tin): ?>
                        <div class="slide-item">
                            <img src="<?= htmlspecialchars($tin['anh_dai_dien']) ?>" alt="">
                            <div class="info">
                                <a href="index.php?action=chi_tiet_bai_viet&id=<?= $tin['id'] ?>"
                                    style="text-decoration:none;color:#005fa3;font-weight:bold;">
                                    <?= htmlspecialchars($tin['tieu_de']) ?>
                                </a>
                                <small>Ngày đăng: <?= htmlspecialchars($tin['ngay_dang']) ?> | 👁 <?= htmlspecialchars($tin['luot_xem']) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="section">
                <h2>🆕 Tin mới nhất</h2>
                <?php foreach ($tinMoiNhat as $tin): ?>
                    <a href="index.php?action=chi_tiet_bai_viet&id=<?= $tin['id'] ?>" class="tin-link">
                        <div class="tin">
                            <img src="<?= htmlspecialchars($tin['anh_dai_dien']) ?>" alt="">
                            <div>
                                <p class="title"><?= htmlspecialchars($tin['tieu_de']) ?></p>
                                <small>📅 Ngày đăng: <?= htmlspecialchars($tin['ngay_dang']) ?></small>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>


            <div class="section">
                <h2>📈 Tin xem nhiều</h2>
                <?php foreach ($tinXemNhieu as $tin): ?>
                    <a href="index.php?action=chi_tiet_bai_viet&id=<?= $tin['id'] ?>" class="tin-link">
                        <div class="tin">
                            <img src="<?= htmlspecialchars($tin['anh_dai_dien']) ?>" alt="">
                            <div>
                                <p class="title"><?= htmlspecialchars($tin['tieu_de']) ?></p>
                                <small><?= htmlspecialchars($tin['luot_xem']) ?> lượt xem</small>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- QUẢNG CÁO PHẢI -->
        <aside>
            <?php foreach ($quangCaoPhai as $index => $qc): ?>
                <div class="qc-item qc-right <?= $index === 0 ? 'active' : '' ?>">
                    <a href="<?= htmlspecialchars($qc['lien_ket']) ?>" target="_blank">
                        <img src="<?= htmlspecialchars($qc['hinh_anh']) ?>" alt="<?= htmlspecialchars($qc['tieu_de']) ?>">
                    </a>
                </div>
            <?php endforeach; ?>
        </aside>
    </main>

    <footer>
        © <?= date('Y') ?> Website Tin Tức. All rights reserved.
    </footer>

    <script>
        // --- Banner ---
        let currentBanner = 0;
        let banners = document.querySelectorAll('.banner-slide');
        let dots = document.querySelectorAll('.dot');

        function showBanner(n) {
            banners.forEach((slide, i) => {
                slide.classList.toggle('active', i === n);
                dots[i].classList.toggle('active', i === n);
            });
            currentBanner = n;
        }

        function nextBanner() {
            currentBanner = (currentBanner + 1) % banners.length;
            showBanner(currentBanner);
        }
        setInterval(nextBanner, 4000);

        // --- Quảng cáo ---
        const leftAds = document.querySelectorAll('.qc-left');
        const rightAds = document.querySelectorAll('.qc-right');
        let adIndex = 0;

        function showAds(list, idx) {
            list.forEach((el, i) => el.style.display = (i === idx ? 'block' : 'none'));
        }
        showAds(leftAds, 0);
        showAds(rightAds, 0);
        setInterval(() => {
            adIndex++;
            showAds(leftAds, adIndex % leftAds.length);
            showAds(rightAds, adIndex % rightAds.length);
        }, 5000);
    </script>

</body>

</html>