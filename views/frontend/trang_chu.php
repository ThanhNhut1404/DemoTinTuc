<?php
// Tránh warning nếu controller chưa truyền biến
$banners = isset($banners) && is_array($banners) ? $banners : [];
$quangCaoTrai = isset($quangCaoTrai) && is_array($quangCaoTrai) ? $quangCaoTrai : [];
$quangCaoPhai = isset($quangCaoPhai) && is_array($quangCaoPhai) ? $quangCaoPhai : [];
$chuyenMuc = isset($chuyenMuc) && is_array($chuyenMuc) ? $chuyenMuc : [];
$tinNoiBat = isset($tinNoiBat) && is_array($tinNoiBat) ? $tinNoiBat : [];
$tinMoiNhat = isset($tinMoiNhat) && is_array($tinMoiNhat) ? $tinMoiNhat : [];
$tinXemNhieu = isset($tinXemNhieu) && is_array($tinXemNhieu) ? $tinXemNhieu : [];
$baiVietTheoChuyenMuc = isset($baiVietTheoChuyenMuc) && is_array($baiVietTheoChuyenMuc) ? $baiVietTheoChuyenMuc : [];

// Prepare unified ads list (take up to 4 ads from available left/right ad arrays)
$allAds = array_values(array_filter(array_merge($quangCaoTrai, $quangCaoPhai)));
$ads = [];
if (!empty($allAds)) {
    // take first 4, or repeat if less than 4
    $take = array_slice($allAds, 0, 4);
    while (count($take) < 4) {
        $take = array_merge($take, $allAds);
        $take = array_slice($take, 0, 4);
    }
    $ads = $take;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ - Website Tin Tức</title>
    <link rel="stylesheet" href="../views/frontend/frontend.css">
    <style>
        :root {
            --primary: #005fa3;
            --primary-hover: #d9534f;
            --bg-light: #fafafa;
            --border: #eee;
            --shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        /* ===== HEADER & NAV ===== */
        .auth-nav {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            background: var(--primary);
            padding: 12px 20px;
            gap: 15px;
            flex-wrap: wrap;
        }

        .search-box {
            position: relative;
            margin-right: auto;
            max-width: 300px;
        }

        .search-input-wrapper {
            position: relative;
        }

        #search-input {
            width: 100%;
            padding: 8px 35px 8px 12px;
            border: none;
            border-radius: 6px;
            font-size: 0.95em;
        }

        .search-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            font-size: 1.1em;
            cursor: pointer;
        }

        .auth-link {
            color: white;
            text-decoration: none;
            font-weight: 600;
            background: #007bff;
            padding: 7px 14px;
            border-radius: 6px;
            font-size: 0.9em;
            transition: 0.2s;
        }

        .auth-link:hover {
            background: #004a99;
        }

        /* Dropdown Chuyên mục */
        .dropdown {
            position: relative;
        }

        .dropdown-toggle {
            color: white;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 40px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: var(--shadow);
            min-width: 200px;
            z-index: 1000;
            padding: 8px 0;
        }

        .dropdown-menu a {
            display: block;
            padding: 10px 16px;
            color: var(--primary);
            text-decoration: none;
            font-size: 0.95em;
            transition: 0.2s;
        }

        .dropdown-menu a:hover {
            background: #f1f9ff;
            color: var(--primary-hover);
        }

        .dropdown.show .dropdown-menu {
            display: block;
            animation: fadeIn 0.2s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        header {
            text-align: center;
            padding: 20px 15px;
            background: white;
            border-bottom: 4px solid var(--primary);
        }

        header h1 {
            font-size: 2.4em;
            color: var(--primary);
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        header p {
            font-style: italic;
            color: #555;
            background: linear-gradient(to right, #e3f2fd, #fff);
            display: inline-block;
            padding: 6px 18px;
            border-radius: 30px;
            border: 1px solid #cfe2ff;
            font-size: 1.05em;
        }

        /* ===== BANNER SLIDE ===== */
        .banner-container {
            position: relative;
            max-width: 100%;
            overflow: hidden;
            margin: 20px auto;
            border-radius: 12px;
            box-shadow: var(--shadow);
        }

        .banner-slide {
            display: none;
        }

        .banner-slide.active {
            display: block;
        }

        .banner-slide img {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }

        .banner-dots {
            text-align: center;
            margin-top: 10px;
        }

        .dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            margin: 0 6px;
            background: #ccc;
            border-radius: 50%;
            cursor: pointer;
            transition: 0.3s;
        }

        .dot.active, .dot:hover {
            background: var(--primary);
        }

        /* ===== MAIN LAYOUT ===== */
        main {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 15px;
        }

        .content {
            flex: 1;
            min-width: 300px;
        }

        aside {
            width: 300px;
            min-width: 250px;
        }

        .category-list {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            height: fit-content;
        }

        .category-list h2 {
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 1.3em;
            border-left: 4px solid #007bff;
            padding-left: 10px;
        }

        .category-menu {
            list-style: none;
        }

        .category-menu a {
            display: block;
            padding: 10px 0;
            color: #333;
            text-decoration: none;
            border-bottom: 1px dashed #eee;
            transition: 0.2s;
        }

        .category-menu a:hover {
            color: var(--primary-hover);
            padding-left: 5px;
        }

        /* ===== TIN NỔI BẬT - SLIDE ===== */
        .slide {
            display: flex;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: var(--shadow);
            transition: transform 0.6s ease;
            will-change: transform;
            touch-action: pan-y; /* allow vertical scroll on touch, handle horizontal via JS */
            cursor: grab;
        }

        .slide-item {
            min-width: 100%;
            position: relative;
            user-select: none;
            -webkit-user-drag: none;
        }

        /* Top5 grid (show 5 images at once) */
        .top5-grid {
            display: flex;
            gap: 12px;
        }

        .top5-item {
            flex: 0 0 calc((100% - 48px) / 5);
            background: linear-gradient(180deg, #fff, #fafafa);
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 6px 18px rgba(10,20,30,0.06);
            transition: transform .35s cubic-bezier(.2,.8,.2,1), box-shadow .35s ease, filter .35s ease;
            will-change: transform;
        }

        .top5-item img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
            transition: transform .45s ease, filter .35s ease;
        }

        /* top badge removed to keep single title only */

        .top5-item:hover img {
            transform: scale(1.06);
            filter: brightness(.95);
        }

        .top5-item:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 18px 40px rgba(10,20,30,0.12);
        }

        .top5-info {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 12px 14px;
            background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.55) 60% , rgba(0,0,0,0.7) 100%);
            color: white;
            display: flex;
            align-items: flex-end;
            gap: 8px;
        }

        .top5-info h4 {
            margin: 0;
            font-size: 0.98em;
            line-height: 1.2;
            font-weight: 700;
            text-shadow: 0 1px 2px rgba(0,0,0,0.4);
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        /* Title is shown below image (visible) */

        .top5-meta {
            margin-left: auto;
            font-size: 0.85em;
            opacity: 0.9;
        }

        @media (max-width: 992px) {
            .top5-item { flex: 0 0 calc((100% - 24px) / 3); }
        }

        @media (max-width: 768px) {
            .top5-item { flex: 0 0 calc((100% - 12px) / 2); }
            .top5-item img { height: 160px; }
        }

        /* subtle hover focus for keyboard accessibility */
        .top5-item:focus-within {
            outline: 2px solid rgba(0,95,163,0.15);
            transform: translateY(-6px) scale(1.01);
        }

        .slide-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .slide-item .info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            color: white;
            padding: 20px;
        }

        .slide-item .info a {
            color: white;
            font-weight: bold;
            font-size: 1.2em;
            text-decoration: none;
        }

        .slide-item .info a:hover {
            color: #ffeb3b;
        }

        /* ===== TIN MỚI & XEM NHIỀU ===== */
        .section {
            margin-bottom: 30px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: var(--shadow);
        }

        .section h2 {
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 1.4em;
            border-left: 5px solid #007bff;
            padding-left: 10px;
        }

        .tin-link {
            display: block;
            text-decoration: none;
            color: inherit;
            margin-bottom: 12px;
        }

        .tin {
            display: flex;
            gap: 12px;
            padding: 12px;
            background: var(--bg-light);
            border-radius: 10px;
            border: 1px solid var(--border);
            transition: 0.3s;
        }

        .tin:hover {
            background: #f1f9ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .tin img {
            width: 140px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            flex-shrink: 0;
        }

        .tin .title {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 6px;
            font-size: 1.05em;
            line-height: 1.4;
        }

        .tin .title:hover {
            color: var(--primary-hover);
        }

        .tin small {
            color: #666;
            font-size: 0.9em;
        }

        /* ===== QUẢNG CÁO PHẢI ===== */
        .qc-right {
            margin-bottom: 15px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
            display: none;
        }

        .qc-right.active {
            display: block;
        }

        .qc-right img {
            width: 100%;
            height: auto;
            transition: 0.3s;
        }

        .qc-right img:hover {
            transform: scale(1.03);
        }

        /* ===== AD COLUMNS (Left & Right slots) ===== */
        .ad-columns, .ad-columns-right {
            background: white;
            padding: 12px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            gap: 12px;
            height: fit-content;
        }

        .ad-column {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .ad-slot {
            overflow: hidden;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: var(--bg-light);
        }

        .ad-slot .ad-link { display:block; }
        .ad-slot .ad-img {
            display:block;
            width:100%;
            height:600px; /* tăng chiều cao quảng cáo lên gấp 5 lần (120px -> 600px) */
            object-fit:cover;
            transition: transform .25s ease;
        }

        .ad-slot .ad-link:hover .ad-img { transform: scale(1.03); }

        /* ===== CHUYÊN MỤC - SCROLL NGANG ĐẸP ===== */
        .chuyen-muc-wrapper {
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 15px;
        }

        .chuyen-muc-block {
            margin-bottom: 35px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: var(--shadow);
        }

        .chuyen-muc-block h3 {
            color: var(--primary);
            font-size: 1.4em;
            font-weight: 700;
            margin-bottom: 15px;
            border-left: 5px solid #007bff;
            padding-left: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .scroll-controls {
            display: flex;
            gap: 8px;
        }

        .scroll-btn {
            width: 36px;
            height: 36px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 1.2em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }

        .scroll-btn:hover {
            background: #004a99;
            transform: scale(1.1);
        }

        .scroll-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .scroll-container {
            display: flex;
            gap: 16px;
            overflow-x: auto;
            padding: 10px 0;
            scroll-behavior: smooth;
        }

        .scroll-container::-webkit-scrollbar {
            height: 8px;
        }

        .scroll-container::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 4px;
        }

        .scroll-container::-webkit-scrollbar-thumb:hover {
            background: #999;
        }

        .bai-viet-item {
            flex: 0 0 260px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: 0.3s;
            text-align: center;
        }

        .bai-viet-item:hover {
            transform: translateY(-6px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .bai-viet-item img {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }

        .bai-viet-item h4 {
            font-size: 1.05em;
            color: var(--primary);
            margin: 12px 10px 8px;
            line-height: 1.3;
            height: 40px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .bai-viet-item p {
            font-size: 0.9em;
            color: #555;
            margin: 0 10px 12px;
            height: 40px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .bai-viet-item a {
            display: block;
            margin: 0 10px 12px;
            padding: 8px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.9em;
            font-weight: 600;
        }

        .bai-viet-item a:hover {
            background: var(--primary);
        }

        .empty {
            color: #888;
            font-style: italic;
            text-align: center;
            padding: 20px;
        }

        /* ===== FOOTER ===== */
        footer {
            text-align: center;
            padding: 25px;
            background: #222;
            color: #aaa;
            font-size: 0.95em;
            margin-top: 50px;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            main {
                flex-direction: column;
            }
            aside {
                width: 100%;
            }
            .auth-nav {
                justify-content: center;
            }
            .search-box {
                margin-right: 0;
                margin-bottom: 10px;
            }
        }

        @media (max-width: 768px) {
            header h1 {
                font-size: 1.8em;
            }
            .banner-slide img {
                height: 250px;
            }
            .tin img {
                width: 100px;
                height: 80px;
            }
        }
    </style>
</head>


<body>

    <!-- BANNER SLIDE -->
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
            <div class="dropdown">
                <a href="#" class="auth-link dropdown-toggle">Chuyên mục ▾</a>
                <ul class="dropdown-menu">
                    <?php foreach ($chuyenMuc as $cm): ?>
                        <li><a href="index.php?action=chuyenmuc&id=<?= $cm['id'] ?>"><?= htmlspecialchars($cm['ten_chuyen_muc']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </nav>
        <h1>Website Tin Tức</h1>
        <p>Cập nhật tin tức mới nhất, nhanh chóng & chính xác</p>
    </header>

    <main>
        <aside class="ad-columns">
            <div class="ad-column left-ads">
                <div class="ad-slot" data-ad-slot="0">
                    <a class="ad-link" href="#" target="_blank">
                        <img class="ad-img" src="" alt="">
                    </a>
                </div>
                <div class="ad-slot" data-ad-slot="1">
                    <a class="ad-link" href="#" target="_blank">
                        <img class="ad-img" src="" alt="">
                    </a>
                </div>
            </div>
        </aside>

        <div class="content">
            <!-- Tin nổi bật -->
            <div class="section">
                <h2>Top 5 tin nổi bật</h2>
                <div class="top5-grid" id="highlight-grid">
                    <?php foreach ($tinNoiBat as $tin): ?>
                        <article class="top5-item">
                            <a href="index.php?action=chi_tiet_bai_viet&id=<?= $tin['id'] ?>" class="top5-link">
                                <img src="<?= htmlspecialchars($tin['anh_dai_dien']) ?>" alt="<?= htmlspecialchars($tin['tieu_de']) ?>">
                                <div class="top5-info">
                                    <h4><?= htmlspecialchars($tin['tieu_de']) ?></h4>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Tin mới nhất -->
            <div class="section">
                <h2>Tin mới nhất</h2>
                <?php foreach ($tinMoiNhat as $tin): ?>
                    <a href="index.php?action=chi_tiet_bai_viet&id=<?= $tin['id'] ?>" class="tin-link">
                        <div class="tin">
                            <img src="<?= htmlspecialchars($tin['anh_dai_dien']) ?>" alt="">
                            <div>
                                <p class="title"><?= htmlspecialchars($tin['tieu_de']) ?></p>
                                <small>Ngày đăng: <?= date('d/m/Y H:i', strtotime($tin['ngay_dang'])) ?></small>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Tin xem nhiều -->
            <div class="section">
                <h2>Tin xem nhiều</h2>
                <?php foreach ($tinXemNhieu as $tin): ?>
                    <a href="index.php?action=chi_tiet_bai_viet&id=<?= $tin['id'] ?>" class="tin-link">
                        <div class="tin">
                            <img src="<?= htmlspecialchars($tin['anh_dai_dien']) ?>" alt="">
                            <div>
                                <p class="title"><?= htmlspecialchars($tin['tieu_de']) ?></p>
                                <small><?= number_format($tin['luot_xem']) ?> lượt xem</small>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Quảng cáo phải (2 slots) -->
        <aside class="ad-columns-right">
            <div class="ad-column right-ads">
                <div class="ad-slot" data-ad-slot="2">
                    <a class="ad-link" href="#" target="_blank">
                        <img class="ad-img" src="" alt="">
                    </a>
                </div>
                <div class="ad-slot" data-ad-slot="3">
                    <a class="ad-link" href="#" target="_blank">
                        <img class="ad-img" src="" alt="">
                    </a>
                </div>
            </div>
        </aside>
    </main>

    <!-- CHUYÊN MỤC - SCROLL NGANG -->
    <div class="chuyen-muc-wrapper">
        <?php foreach ($chuyenMuc as $cm): ?>
            <?php $baiViet = $baiVietTheoChuyenMuc[$cm['id']] ?? []; ?>
            <div class="chuyen-muc-block">
                <h3>
                    <?= htmlspecialchars($cm['ten_chuyen_muc']) ?>
                    <div class="scroll-controls">
                        <button class="scroll-btn" onclick="scrollLeft('cm-<?= $cm['id'] ?>')">&lt;</button>
                        <button class="scroll-btn" onclick="scrollRight('cm-<?= $cm['id'] ?>')">&gt;</button>
                    </div>
                </h3>
                <div class="scroll-container" id="scroll-cm-<?= $cm['id'] ?>">
                    <?php if (!empty($baiViet)): ?>
                        <?php foreach ($baiViet as $bv): ?>
                            <article class="bai-viet-item">
                                <img src="../uploads/<?= htmlspecialchars($bv['anh_dai_dien']) ?>" alt="">
                                <h4><?= htmlspecialchars($bv['tieu_de']) ?></h4>
                                <p><?= htmlspecialchars($bv['mo_ta_ngan']) ?></p>
                                <a href="index.php?action=chi_tiet_bai_viet&id=<?= $bv['id'] ?>">Xem thêm</a>
                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="empty">Chưa có bài viết nào.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    

    <footer>
        © <?= date('Y') ?> Website Tin Tức. All rights reserved.
    </footer>
    

    <script>
        // Banner Slide
        let currentBanner = 0;
        const banners = document.querySelectorAll('.banner-slide');
        const dots = document.querySelectorAll('.dot');

        function showBanner(n) {
            banners.forEach((s, i) => s.classList.toggle('active', i === n));
            dots.forEach((d, i) => d.classList.toggle('active', i === n));
            currentBanner = n;
        }

        setInterval(() => {
            currentBanner = (currentBanner + 1) % banners.length;
            showBanner(currentBanner);
        }, 5000);

        // Quảng cáo: populate 4 ad slots (2 trái, 2 phải) và xoay theo cặp
        const ads = <?php echo json_encode(isset($ads) ? $ads : []); ?>;
        const adSlots = Array.from(document.querySelectorAll('.ad-slot'));
        let adIdx = 0; // start index in ads

        function normalizeImgPath(src) {
            if (!src) return '';
            // nếu là URL đầy đủ thì giữ nguyên
            if (/^(https?:)?\/\//.test(src) || src.startsWith('/')) return src;
            // nếu có đường dẫn tương đối (chứa /) giữ nguyên
            if (src.indexOf('/') !== -1) return src;
            // nếu chỉ filename -> prefix uploads
            return '../uploads/' + src;
        }

        function populateAdSlots() {
            if (!adSlots.length) return;
            if (!ads || !ads.length) {
                // fallback: hide slots or show placeholder
                adSlots.forEach(s => {
                    const img = s.querySelector('.ad-img');
                    const link = s.querySelector('.ad-link');
                    img.src = '../uploads/default_ads.jpg';
                    link.href = '#';
                });
                return;
            }

            for (let i = 0; i < adSlots.length; i++) {
                const ad = ads[(adIdx + i) % ads.length] || {};
                const link = adSlots[i].querySelector('.ad-link');
                const img = adSlots[i].querySelector('.ad-img');
                link.href = ad['lien_ket'] ? ad['lien_ket'] : '#';
                img.src = normalizeImgPath(ad['hinh_anh'] ? ad['hinh_anh'] : '');
                img.alt = ad['tieu_de'] ? ad['tieu_de'] : 'Quảng cáo';
            }
        }

        // populate initially
        populateAdSlots();

        // rotate every 5s, shift by 2 (luân phiên theo cặp)
        setInterval(() => {
            adIdx = (adIdx + 2) % (ads.length || 1);
            populateAdSlots();
        }, 5000);

        // Dropdown
        document.querySelector('.dropdown-toggle').addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector('.dropdown').classList.toggle('show');
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelector('.dropdown').classList.remove('show');
            }
        });

        // Scroll ngang
        function scrollLeft(id) {
            const container = document.getElementById('scroll-' + id);
            container.scrollBy({ left: -300, behavior: 'smooth' });
        }

        function scrollRight(id) {
            const container = document.getElementById('scroll-' + id);
            container.scrollBy({ left: 300, behavior: 'smooth' });
        }
        // Tự động ẩn nút khi hết nội dung (tùy chọn nâng cao)
    </script>
    
</body>
</html>