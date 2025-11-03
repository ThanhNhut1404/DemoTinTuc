<?php
// tránh warning nếu controller chưa truyền biến
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
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f8;
            color: #333;
        }

        header {
            background: linear-gradient(135deg, #0077cc, #005fa3);
            color: white;
            padding: 20px 50px;
            text-align: center;
        }

        h1 {
            margin: 0;
            font-size: 28px;
        }

        /* === BANNER === */
        .banner-container {
            position: relative;
            width: 100%;
            height: 350px;
            overflow: hidden;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
        }

        .banner-slide {
            display: none;
            width: 100%;
            height: 100%;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        .banner-slide.active {
            display: block;
            opacity: 1;
        }

        .banner-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .banner-dots {
            text-align: center;
            margin: 10px 0 20px 0;
        }

        .dot {
            height: 12px;
            width: 12px;
            margin: 4px;
            background-color: #bbb;
            border-radius: 50%;
            display: inline-block;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .dot.active {
            background-color: #0077cc;
        }

        /* === MAIN === */
        main {
            width: 95%;
            max-width: 1400px;
            margin: 30px auto;
            display: grid;
            grid-template-columns: 180px 1fr 180px;
            gap: 25px;
        }

        .section {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
            padding: 20px;
            margin-bottom: 25px;
        }

        .section h2 {
            color: #005fa3;
            border-left: 5px solid #0077cc;
            padding-left: 10px;
            margin-bottom: 15px;
        }

        .tin {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            gap: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .tin img {
            width: 120px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
        }

        .tin b {
            display: block;
            font-size: 16px;
            color: #333;
        }

        .tin small {
            color: #888;
            font-size: 13px;
        }

        .slide {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 15px;
        }

        .slide-item {
            background-color: #fafafa;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.2s ease;
        }

        .slide-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .slide-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .slide-item .info {
            padding: 10px;
        }

        .slide-item .info b {
            font-size: 15px;
            color: #005fa3;
        }

        .qc-item {
            text-align: center;
            margin-bottom: 15px;
        }

        .category-list {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
            padding: 15px;
        }

        .category-menu {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .category-menu li {
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 6px;
        }

        .category-menu li:last-child {
            border-bottom: none;
        }

        .category-menu a {
            text-decoration: none;
            color: #0077cc;
            font-weight: 500;
            transition: color 0.3s;
        }

        .category-menu a:hover {
            color: #005fa3;
        }

        .qc-item img {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        footer {
            background: #222;
            color: #aaa;
            text-align: center;
            padding: 15px;
            margin-top: 30px;
        }

        .auth-nav {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .auth-link {
            text-decoration: none;
            color: #fff;
            background-color: #28a745;
            /* xanh lá nhẹ */
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            font-family: 'Segoe UI', sans-serif;
            transition: all 0.3s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .auth-link:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }
    </style>
</head>

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
        <!-- menu chuyên mục  -->
        <aside class="category-list">
            <div class="section">
                <h2>Chuyên mục</h2>
                <ul class="category-menu">
                    <?php foreach ($chuyenMuc as $cm): ?>
                        <li>
                            <a href="#" class="link-chuyen-muc" data-id="<?= htmlspecialchars($cm['id']) ?>">
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
                                <a href="index.php?action=chi_tiet_bai_viet&id=<?= $tin['id'] ?>" style="text-decoration:none;color:#005fa3;font-weight:bold;">
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
                    <div class="tin">
                        <img src="<?= htmlspecialchars($tin['anh_dai_dien']) ?>" alt="">
                        <div>
                            <b><?= htmlspecialchars($tin['tieu_de']) ?></b>
                            <small>Ngày đăng: <?= htmlspecialchars($tin['ngay_dang']) ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="section">
                <h2>📈 Tin xem nhiều</h2>
                <?php foreach ($tinXemNhieu as $tin): ?>
                    <div class="tin">
                        <img src="<?= htmlspecialchars($tin['anh_dai_dien']) ?>" alt="">
                        <div>
                            <b><?= htmlspecialchars($tin['tieu_de']) ?></b>
                            <small><?= htmlspecialchars($tin['luot_xem']) ?> lượt xem</small>
                        </div>
                    </div>
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

        // --- Quảng cáo trái & phải ---
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
        // --- Chuyên mục động (AJAX) ---
        document.querySelectorAll('.link-chuyen-muc').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.dataset.id;
                const contentDiv = document.querySelector('.content');
                // Hiển thị đang tải
                contentDiv.innerHTML = "<div class='section'><p>⏳ Đang tải bài viết...</p></div>";
                fetch(`index.php?action=load_chuyen_muc&id=${id}`)
                    .then(res => res.text())
                    .then(html => {
                        contentDiv.innerHTML = html + `
                            <div style="margin-top:15px;">
                                <button onclick="location.reload()" 
                                    style="background:#0077cc;color:white;padding:8px 14px;border:none;border-radius:6px;cursor:pointer;">
                                    ← Quay lại trang chủ
                                </button>
                            </div>`;
                    })
                    .catch(err => {
                        console.error(err);
                        contentDiv.innerHTML = "<div class='section'><p>Lỗi khi tải chuyên mục. Vui lòng thử lại.</p></div>";
                    });
            });
        });
    </script>
</body>

</html>