<?php
// Tránh warning nếu controller chưa truyền biến
$banners = isset($banners) && is_array($banners) ? $banners : [];
$quangCaoTrai = isset($quangCaoTrai) && is_array($quangCaoTrai) ? $quangCaoTrai : [];
$quangCaoPhai = isset($quangCaoPhai) && is_array($quangCaoPhai) ? $quangCaoPhai : [];
$chuyenMuc = isset($chuyenMuc) && is_array($chuyenMuc) ? $chuyenMuc : [];
$tinNoiBat = isset($tinNoiBat) && is_array($tinNoiBat) ? $tinNoiBat : [];
$tinMoiNhat = isset($tinMoiNhat) && is_array($tinMoiNhat) ? $tinMoiNhat : [];
$tinXemNhieu = isset($tinXemNhieu) && is_array($tinXemNhieu) ? $tinXemNhieu : [];
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

    /* 🎯 Thêm CSS cho dropdown chuyên mục */
    header {
        text-align: center;
        background: #f8f9fa;
        padding-bottom: 10px;
        border-bottom: 3px solid #005fa3;
    }

    .auth-nav {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        background: #005fa3;
        padding: 10px 20px;
        gap: 15px;
        position: relative;
    }

    .auth-link {
        color: #fff;
        text-decoration: none;
        font-weight: bold;
        background: #007bff;
        padding: 6px 12px;
        border-radius: 5px;
        transition: 0.2s;
    }

    .auth-link:hover {
        background: #004a99;
    }

    /* ----- Dropdown ----- */
    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-toggle {
        cursor: pointer;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        top: 40px;
        background: white;
        border: 1px solid #ddd;
        border-radius: 6px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        list-style: none;
        margin: 0;
        padding: 5px 0;
        z-index: 1000;
        min-width: 180px;
    }

    .dropdown-menu li a {
        display: block;
        padding: 10px 15px;
        color: #005fa3;
        text-decoration: none;
        transition: background 0.2s;
    }

    .dropdown-menu li a:hover {
        background: #f1f9ff;
        color: #d9534f;
    }

    .dropdown.show .dropdown-menu {
        display: block;
        animation: fadeIn 0.2s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    header h1 {
    font-size: 2.2em;
    font-weight: 800;
    color: #005fa3;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.1);
    margin-top: 15px;
    letter-spacing: 1px;
}

header p {
    font-size: 1.05em;
    color: #333;
    font-style: italic;
    margin-top: 4px;
    margin-bottom: 15px;
    background: linear-gradient(to right, #e3f2fd, #ffffff);
    display: inline-block;
    padding: 6px 16px;
    border-radius: 8px;
    border: 1px solid #cfe2ff;
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
           <form id="search-form" action="index.php" method="get" autocomplete="off" class="search-box">
    <input type="hidden" name="action" value="search">

    <div class="search-input-wrapper">
        <input id="search-input" type="text" name="q" placeholder="Bạn muốn tìm gì hôm nay?">
        <button type="submit" class="search-btn">🔍</button>
        
    </div>
    <div id="search-suggestions" class="suggest-box" style="display:none;"></div>
    
</form>

            <a href="index.php?action=login" class="auth-link">Đăng nhập</a>
            <a href="index.php?action=register" class="auth-link">Đăng ký</a>

            <!-- 🔽 Nút chuyên mục -->
            <div class="dropdown">
                <a href="#" class="auth-link dropdown-toggle">Chuyên mục ▾</a>
                <ul class="dropdown-menu">
                    <?php foreach ($chuyenMuc as $cm): ?>
                        <li>
                            <a href="index.php?action=chuyenmuc&id=<?= htmlspecialchars($cm['id']) ?>">
                                <?= htmlspecialchars($cm['ten_chuyen_muc']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
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
                <!-- NỘI DUNG phu -->
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
        document.addEventListener("DOMContentLoaded", function() {
            const dropdown = document.querySelector(".dropdown");
            const toggle = dropdown.querySelector(".dropdown-toggle");

            toggle.addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropdown.classList.toggle("show");
            });

            // Ẩn dropdown khi click ra ngoài
            document.addEventListener("click", function() {
                dropdown.classList.remove("show");
            });
        });
    </script>

</body>

</html>