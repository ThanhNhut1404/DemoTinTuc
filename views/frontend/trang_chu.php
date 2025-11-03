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
<link rel="stylesheet" href="../views/frontend/frontend.css">
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
                            <a href="index.php?action=chi_tiet_bai_viet&id=<?= $tin['id'] ?>" style="text-decoration:none;color:#005fa3;font-weight:bold;">
                                <?= htmlspecialchars($tin['tieu_de']) ?>
                            </a>
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
                            <a href="index.php?action=chi_tiet_bai_viet&id=<?= $tin['id'] ?>" style="text-decoration:none;color:#005fa3;font-weight:bold;">
                                <?= htmlspecialchars($tin['tieu_de']) ?>
                            </a>
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

    // --- Chuyên mục động (AJAX có phân trang) ---
    document.querySelectorAll('.link-chuyen-muc').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            loadChuyenMuc(id, 1); // lần đầu load trang 1
        });
    });

    // Hàm tải chuyên mục với phân trang
    function loadChuyenMuc(id, page = 1) {
        const contentDiv = document.querySelector('.content');
        contentDiv.innerHTML = "<div class='section'><p>⏳ Đang tải bài viết...</p></div>";

        fetch(`index.php?action=load_chuyen_muc&id=${id}&page=${page}`)
            .then(res => res.text())
            .then(html => {
                contentDiv.innerHTML = html + `
                    <div style="margin-top:15px;text-align:center;">
                        <button onclick="location.reload()" 
                            style="background:#0077cc;color:white;padding:8px 14px;border:none;border-radius:6px;cursor:pointer;">
                            ← Quay lại trang chủ
                        </button>
                    </div>`;
                
                // Gắn lại sự kiện cho các nút phân trang sau khi load HTML mới
                document.querySelectorAll('.page-link').forEach(btn => {
                    btn.addEventListener('click', e => {
                        e.preventDefault();
                        const p = parseInt(btn.dataset.page);
                        loadChuyenMuc(id, p);
                    });
                });
            })
            .catch(err => {
                console.error(err);
                contentDiv.innerHTML = "<div class='section'><p>Lỗi khi tải chuyên mục. Vui lòng thử lại.</p></div>";
            });
    }
</script>

</body>

</html>