<?php
// Parent category view: display all child categories with their article counts
use Website\TinTuc\Models\QuangcaoModel;
use Website\TinTuc\Models\BaiVietModel;
use Website\TinTuc\Models\BgWallpaperModel;

$qcModel = new QuangcaoModel();
$dsQuangCao = $qcModel->getQuangCaoTheoViTri('Sidebar');

$allAds = array_values(array_filter($dsQuangCao));
$ads = [];
if (!empty($allAds)) {
    $take = array_slice($allAds, 0, 4);
    while (count($take) < 4 && !empty($allAds)) {
        $take = array_merge($take, $allAds);
        $take = array_slice($take, 0, 4);
    }
    $ads = $take;
}

$bgModel = new BgWallpaperModel();
$activeWallpaper = $bgModel->getActive();
$wallpaperUrl = '';
if (!empty($activeWallpaper) && !empty($activeWallpaper['duong_dan_file'])) {
    $wallpaperUrl = '/Demotintuc/public/uploads/wallpapers/' . htmlspecialchars($activeWallpaper['duong_dan_file']);
}

// Get all parent categories for navigation
$cmChaModel = new \Website\TinTuc\Models\ChuyenMucChaModel();
$chuyenMucCha = $cmChaModel->getAll();

// Get all categories for children map
$cmModel = new \Website\TinTuc\Models\ChuyenMucModel();
$dsChuyenMuc = $cmModel->getAll();
$childrenMap = [];
foreach ($dsChuyenMuc as $c) {
    if (!empty($c['id_cha'])) {
        $childrenMap[$c['id_cha']][] = $c;
    }
}

// Helper: chuẩn hóa đường dẫn ảnh
if (!function_exists('img_url')) {
    function img_url($src)
    {
        $src = trim((string)$src);
        if ($src === '') return 'uploads/no_image.png';
        if (preg_match('#^(https?:)?//#i', $src)) return $src;
        if (strpos($src, '/') === 0) return $src;
        if (stripos($src, 'uploads/') !== false) {
            $pos = stripos($src, 'uploads/');
            return substr($src, $pos);
        }
        return 'uploads/' . ltrim($src, '/');
    }
}

// Count articles per child category and fetch articles for each
$baiVietModel = new BaiVietModel();
$articleCounts = [];
$articlesByChild = [];

// Ensure $chuyenMucCon is an array
$chuyenMucCon = isset($chuyenMucCon) && is_array($chuyenMucCon) ? $chuyenMucCon : [];

foreach ($chuyenMucCon as $child) {
    $count = $baiVietModel->countByChuyenMuc($child['id']);
    $articleCounts[$child['id']] = $count;
    
    // Fetch articles for this child category (limit 50 per category)
    $articles = $baiVietModel->getByChuyenMucFilter($child['id'], 50, 0, 'moi_nhat');
    $articlesByChild[$child['id']] = $articles;
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($tenChuyenMucCha ?? 'Chuyên mục cha') ?> - Website Tin Tức</title>
    <link rel="stylesheet" href="../views/frontend/frontend.css">
    <style>
    body {
        background-image: <?= !empty($wallpaperUrl) ? "url('" . $wallpaperUrl . "')" : "''" ?>;
        background-size: cover;
        background-attachment: fixed;
        background-position: center;
    }

    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.20);
        pointer-events: none;
        z-index: -1;
    }

    /* Layout */
    main { display:flex; gap:12px; align-items:stretch; max-width:1200px; margin:12px auto; padding:0 10px; }
    main > section { width:72%; }
    main > aside { width:26%; display:flex; flex-direction:column; box-sizing:border-box; }

    /* Category grid */
    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 16px;
        margin: 20px 0;
    }

    .category-card {
        background: white;
        padding: 16px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        text-decoration: none;
        color: inherit;
        transition: all 0.3s ease;
        border: 1px solid #e6e6e6;
    }

    .category-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.12);
        border-color: #005fa3;
    }

    .category-card h3 {
        margin: 0 0 8px 0;
        color: #005fa3;
        font-size: 1.1em;
    }

    .category-card .article-count {
        color: #666;
        font-size: 0.9em;
    }

    /* Category bar */
    .category-bar {
        background: white;
        border-bottom: 1px solid #e6e6e6;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        margin-bottom: 12px;
    }

    .category-bar .cat-list {
        list-style: none;
        display: flex;
        gap: 18px;
        max-width: 1200px;
        margin: 0 auto;
        padding: 10px 12px;
        align-items: center;
        /* make dropdowns escape the list bounds on larger screens */
        overflow: visible;
    }

    .category-bar .cat-item { position: relative; }

    .category-bar .cat-link {
        color: #333;
        text-decoration: none;
        padding: 8px 6px;
        font-weight: 600;
        display: inline-block;
    }

    .category-bar .cat-link:hover { color: #005fa3; }

    .category-bar .cat-link.active { color: #005fa3; border-bottom: 2px solid #005fa3; }

    .category-bar .cat-dropdown {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        background: #fff;
        border: 1px solid #eee;
        border-radius: 6px;
        box-shadow: 0 6px 18px rgba(10,20,30,0.08);
        min-width: 220px;
        z-index: 99999;
        padding: 8px 0;
    }

    .category-bar .cat-dropdown ul { list-style:none; margin:0; padding:0; }
    .category-bar .cat-dropdown li a { display:block; padding:8px 14px; color:#333; text-decoration:none; }
    .category-bar .cat-dropdown li a:hover { background:#f4f8ff; color:#005fa3; }
    .category-bar .cat-item:hover .cat-dropdown { display: block; }

    /* Ad styling */
    .ad-img { width:100%; height:100%; object-fit:cover; border-radius:0; display:block; }
    .ad-slot { overflow:hidden; background:transparent; padding:0; border-radius:0; box-shadow: none; margin:0; flex:1 1 0; }
    main > aside > div { display:flex; flex-direction:column; gap:10px; height:100%; margin-top:0; padding:0; }

    /* Responsive */
    @media (max-width: 1200px) {
        .category-grid { grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); }
    }

    @media (max-width: 768px) {
        main { flex-direction: column; }
        main > section { width: 100%; }
        main > aside { width: 100%; }
        .category-grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); }
    }

    /* keep the category list scrollable on small screens */
    @media (max-width: 900px) {
        .category-bar .cat-list { overflow: auto; }
    }

    /* Article list with scrollbar */
    .articles-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 16px;
        max-height: 600px;
        overflow-y: auto;
        padding: 12px 8px;
        border-radius: 8px;
        background: #f9f9f9;
        border: 1px solid #e6e6e6;
    }

    .articles-container::-webkit-scrollbar {
        width: 10px;
    }

    .articles-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .articles-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .articles-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .article-item {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        text-decoration: none;
        color: inherit;
    }

    .article-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.12);
    }

    .article-item img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        display: block;
    }

    .article-item .content {
        padding: 12px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .article-item .title {
        font-weight: 600;
        color: #005fa3;
        margin: 0 0 8px 0;
        font-size: 0.95em;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .article-item .meta {
        font-size: 0.85em;
        color: #999;
        margin-top: auto;
    }

    </style>
</head>

<body>
    <!-- === HEADER === -->
    <header>
        <nav class="auth-nav" style="justify-content:space-between;align-items:center;">
            <div>
                <a href="index.php" class="auth-link">🏠 Trang chủ</a>
            </div>
        </nav>
        <div style="text-align:center;padding:18px 0 6px 0;">
            <h1 style="margin:6px 0 4px;font-size:34px;letter-spacing:0.6px;color:#fff;text-shadow:0 2px 8px rgba(0,0,0,0.25);">
                <?= htmlspecialchars($tenChuyenMucCha ?? 'Chuyên mục cha') ?>
            </h1>
            <p style="color:#e8f0fb;margin:0;font-weight:500;">Chọn chuyên mục con để xem bài viết</p>
        </div>
    </header>

    <!-- Category navigation bar -->
    <nav class="category-bar">
        <ul class="cat-list">
            <?php foreach ($chuyenMucCha as $parent): ?>
                <li class="cat-item">
                    <a href="index.php?action=chuyenmuccha&id=<?= $parent['id'] ?>" 
                       class="cat-link <?= (isset($chuyenMucCha) && $parent['id'] == $id) ? 'active' : '' ?>">
                       <?= htmlspecialchars($parent['ten_chuyen_muc']) ?>
                    </a>
                    <div class="cat-dropdown">
                        <ul>
                            <?php if (!empty($childrenMap[$parent['id']])): ?>
                                <?php foreach ($childrenMap[$parent['id']] as $child): ?>
                                    <li><a href="index.php?action=chuyenmuc&id=<?= $child['id'] ?>"><?= htmlspecialchars($child['ten_chuyen_muc']) ?></a></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="no-child">(Chưa có chuyên mục con)</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <!-- === MAIN CONTENT === -->
    <main>
        <!-- Left: Category list + Articles -->
        <section class="section">
            <h2>📚 <?= htmlspecialchars($tenChuyenMucCha ?? 'Chuyên mục') ?></h2>

            <?php if (empty($chuyenMucCon)): ?>
                <p>❌ Chuyên mục cha này chưa có chuyên mục con nào.</p>
            <?php else: ?>
                <div class="category-grid">
                    <?php foreach ($chuyenMucCon as $child): ?>
                        <a href="index.php?action=chuyenmuc&id=<?= $child['id'] ?>" class="category-card">
                            <h3><?= htmlspecialchars($child['ten_chuyen_muc']) ?></h3>
                            <div class="article-count">
                                📄 <?= htmlspecialchars($articleCounts[$child['id']] ?? 0) ?> bài viết
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Articles Section with Scrollbar -->
            <div style="margin-top: 30px;">
                <h2>📰 Bài viết theo chuyên mục con</h2>
                
                <?php if (empty($chuyenMucCon)): ?>
                    <p>❌ Chưa có bài viết nào.</p>
                <?php else: ?>
                    <?php foreach ($chuyenMucCon as $child): ?>
                        <div style="margin-top: 24px;">
                            <h3 style="color: #005fa3; border-bottom: 2px solid #005fa3; padding-bottom: 8px; margin: 0 0 12px 0;">
                                <?= htmlspecialchars($child['ten_chuyen_muc']) ?>
                            </h3>
                            
                            <?php if (empty($articlesByChild[$child['id']])): ?>
                                <p style="color: #999; font-style: italic;">Chưa có bài viết nào trong chuyên mục này.</p>
                            <?php else: ?>
                                <div class="articles-container">
                                    <?php foreach ($articlesByChild[$child['id']] as $article): ?>
                                        <a href="index.php?action=chi_tiet_bai_viet&id=<?= $article['id'] ?>" class="article-item">
                                            <img src="<?= htmlspecialchars(img_url($article['anh_dai_dien'] ?? '')) ?>" alt="<?= htmlspecialchars($article['tieu_de']) ?>">
                                            <div class="content">
                                                <h3 class="title"><?= htmlspecialchars($article['tieu_de']) ?></h3>
                                                <div class="meta">
                                                    📅 <?= date('d/m/Y', strtotime($article['ngay_dang'] ?? 'now')) ?> | 👁 <?= htmlspecialchars($article['luot_xem'] ?? 0) ?>
                                                </div>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Right: Ads -->
        <aside class="category-list">
            <div style="margin-top:14px;">
                <div class="ad-slot" data-ad-slot="2" style="margin-bottom:10px;">
                    <a class="ad-link" href="#" target="_blank"><img class="ad-img" src="" alt=""></a>
                </div>
                <div class="ad-slot" data-ad-slot="3">
                    <a class="ad-link" href="#" target="_blank"><img class="ad-img" src="" alt=""></a>
                </div>
            </div>
        </aside>
    </main>

    <!-- === FOOTER === -->
    <footer>
        © <?= date('Y') ?> Website Tin Tức. All rights reserved.
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ads = <?= json_encode($ads) ?> || [];
            function normalizeImgPath(p) {
                if (!p) return 'uploads/default_ads.jpg';
                if (p.startsWith('http') || p.startsWith('/')) return p;
                if (p.startsWith('uploads/') || p.startsWith('../uploads/')) return p;
                return 'uploads/' + p;
            }

            function populateSlots(startIndex = 0) {
                const slots = document.querySelectorAll('.ad-slot');
                for (let i = 0; i < slots.length; i++) {
                    const slot = slots[i];
                    const ad = ads[(startIndex + i) % Math.max(ads.length, 1)];
                    const link = slot.querySelector('.ad-link');
                    const img = slot.querySelector('.ad-img');
                    if (ad) {
                        link.href = ad.lien_ket && ad.lien_ket.trim() !== '' ? ad.lien_ket : '#';
                        img.src = normalizeImgPath(ad.hinh_anh);
                        img.alt = ad.ten_quang_cao || 'Quảng cáo';
                    } else {
                        link.href = '#';
                        img.src = 'uploads/default_ads.jpg';
                        img.alt = 'Quảng cáo';
                    }
                }
            }
            let adIdx = 0;
            if (ads.length > 0) populateSlots(adIdx);
            setInterval(() => {
                adIdx = (adIdx + 2) % Math.max(ads.length, 2);
                populateSlots(adIdx);
            }, 5000);
        });
    </script>
</body>

</html>
