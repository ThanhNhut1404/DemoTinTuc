<?php
include __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('img_url')) {
    function img_url($path) { return '/Demotintuc/' . ltrim($path, '/'); }
}
if (!function_exists('base_url')) {
    function base_url($path = '') { return '/Demotintuc/' . ltrim($path, '/'); }
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>ID bài viết không hợp lệ.</div></div>";
    exit;
}
$id = (int)$_GET['id'];

// Tăng lượt xem
if (!isset($_SESSION['views'][$id]) || (time() - ($_SESSION['views'][$id] ?? 0)) >= 30) {
    $stmt = $conn->prepare("UPDATE bai_viet SET luot_xem = luot_xem + 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $_SESSION['views'][$id] = time();
}

// Lấy bài viết chính
$stmt = $conn->prepare("SELECT b.*, n.ho_ten AS tac_gia FROM bai_viet b LEFT JOIN nguoi_dung n ON b.id_tac_gia = n.id WHERE b.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "<div class='container mt-5'><div class='alert alert-warning text-center p-5 fs-3'>Bài viết không tồn tại.</div></div>";
    exit;
}
$bv = $result->fetch_assoc();
$stmt->close();

// Kiểm tra thích/lưu
$yeu_thich = $da_luu = false;
if (isset($_SESSION['id_nguoi_dung'])) {
    $uid = (int)$_SESSION['id_nguoi_dung'];
    foreach (['yeu_thich', 'luu_bai'] as $table) {
        $stmt = $conn->prepare("SELECT 1 FROM $table WHERE id_bai_viet = ? AND id_nguoi_dung = ?");
        $stmt->bind_param("ii", $id, $uid);
        $stmt->execute();
        $stmt->store_result();
        if ($table === 'yeu_thich') $yeu_thich = $stmt->num_rows > 0;
        if ($table === 'luu_bai') $da_luu = $stmt->num_rows > 0;
        $stmt->close();
    }
}

// Bình luận
$stmt = $conn->prepare("SELECT bl.noi_dung, u.ho_ten AS ten_nguoi_dung, bl.ngay_binh_luan FROM binh_luan bl JOIN nguoi_dung u ON bl.id_nguoi_dung = u.id WHERE bl.id_bai_viet = ? ORDER BY bl.ngay_binh_luan DESC");
$stmt->bind_param("i", $id);
$stmt->execute();
$binh_luan = $stmt->get_result();
$stmt->close();

// Bài liên quan
$stmt = $conn->prepare("SELECT id, tieu_de, anh_dai_dien, mo_ta_ngan FROM bai_viet WHERE id != ? AND anh_dai_dien IS NOT NULL AND anh_dai_dien != '' ORDER BY ngay_dang DESC LIMIT 6");
$stmt->bind_param("i", $id);
$stmt->execute();
$related = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($bv['tieu_de']); ?> - DemoTinTuc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background:#f8f9fa; padding-top:76px; font-family:'Segoe UI',sans-serif; }
        .article-img { border-radius:20px; max-height:560px; object-fit:cover; width:100%; box-shadow:0 15px 35px rgba(0,0,0,0.15); }
        .card { border:none; border-radius:20px; overflow:hidden; box-shadow:0 8px 25px rgba(0,0,0,0.1); }

        /* HIỆU ỨNG NẢY SIÊU ĐẸP CHO TẤT CẢ NÚT & BÀI LIÊN QUAN */
        .bounce-item, .related-item, .btn-bounce {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
            position: relative;
            overflow: hidden;
        }
        .bounce-item::before, .related-item::before, .btn-bounce::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(13,110,253,0.15), rgba(255,107,107,0.15));
            opacity: 0;
            transition: opacity 0.4s;
            border-radius: inherit;
            pointer-events: none;
        }
        .bounce-item:hover, .related-item:hover, .btn-bounce:hover {
            transform: translateY(-10px) scale(1.05) !important;
            box-shadow: 0 25px 50px rgba(0,0,0,0.25) !important;
            z-index: 10;
        }
        .bounce-item:hover::before, .related-item:hover::before, .btn-bounce:hover::before { opacity: 1; }

        .bounce-item:active, .related-item:active, .btn-bounce:active {
            transform: translateY(-6px) scale(1.04) !important;
            animation: megaBounce 0.7s;
        }
        @keyframes megaBounce {
            0%, 100%   { transform: translateY(-10px) scale(1.05); }
            50%        { transform: translateY(8px) scale(1.10); }
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sticky-sidebar { top: 90px; }
        @media (max-width: 992px) { .sticky-sidebar { position: static !important; } }
    </style>
</head>
<body>

<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container my-5">
    <div class="row g-5">

        <!-- CỘT TRÁI -->
        <div class="col-lg-8">

            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4">
                    <strong><?= htmlspecialchars($_SESSION['flash_message']); ?></strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['flash_message']); ?>
            <?php endif; ?>

            <!-- Bài viết chính -->
            <article class="card shadow-lg mb-5">
                <div class="card-body p-4 p-lg-5">
                    <h1 class="display-5 fw-bold mb-4"><?= htmlspecialchars($bv['tieu_de']); ?></h1>
                    <div class="text-muted small mb-4 d-flex flex-wrap gap-4">
                        <span>Ngày đăng: <?= date('d/m/Y', strtotime($bv['ngay_dang'])); ?></span>
                        <span>Tác giả: <?= htmlspecialchars($bv['tac_gia'] ?? 'Ẩn danh'); ?></span>
                        <span>Lượt xem: <?= number_format($bv['luot_xem']); ?> lượt xem</span>
                    </div>

                    <?php if (!empty($bv['anh_dai_dien'])): ?>
                        <img src="<?= img_url($bv['anh_dai_dien']); ?>" class="article-img mb-5" alt="<?= htmlspecialchars($bv['tieu_de']); ?>">
                    <?php endif; ?>

                    <?php if (!empty($bv['mo_ta_ngan'])): ?>
                        <p class="lead fs-4 text-muted mb-5"><?= htmlspecialchars($bv['mo_ta_ngan']); ?></p>
                    <?php endif; ?>

                    <div class="content lh-lg fs-5 text-dark mb-5"><?= $bv['noi_dung']; ?></div>

                    <hr class="my-5">

                    <!-- NÚT TƯƠNG TÁC - CÓ NẢY CỰC MẠNH -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-4">
                        <div>
                            <?php if (isset($_SESSION['id_nguoi_dung'])): ?>
                                <form method="post" action="<?= base_url('controllers/chi_tiet_bai_viet.php') ?>" class="d-inline">
                                    <input type="hidden" name="id_bai_viet" value="<?= $id ?>">
                                    <input type="hidden" name="action" value="yeu_thich">
                                    <button class="btn <?= $yeu_thich ? 'btn-danger' : 'btn-outline-danger'; ?> btn-lg px-5 btn-bounce bounce-item">
                                        Nút thích: <?= $yeu_thich ? 'Đã thích' : 'Thích' ?>
                                    </button>
                                </form>

                                <form method="post" action="<?= base_url('controllers/chi_tiet_bai_viet.php') ?>" class="d-inline ms-3">
                                    <input type="hidden" name="id_bai_viet" value="<?= $id ?>">
                                    <input type="hidden" name="action" value="luu_bai">
                                    <button class="btn <?= $da_luu ? 'btn-primary' : 'btn-outline-primary'; ?> btn-lg px-5 btn-bounce bounce-item">
                                        Nút lưu: <?= $da_luu ? 'Đã lưu' : 'Lưu bài' ?>
                                    </button>
                                </form>
                            <?php else: ?>
                                <a href="<?= base_url('views/auth/login.php') ?>" class="btn btn-outline-danger btn-lg px-5 btn-bounce bounce-item"> Thích</a>
                                <a href="<?= base_url('views/auth/login.php') ?>" class="btn btn-outline-primary btn-lg px-5 ms-3 btn-bounce bounce-item"> Lưu bài</a>
                            <?php endif; ?>
                        </div>
                        <a href="<?= base_url('public/') ?>" class="btn btn-outline-secondary btn-lg px-5 btn-bounce bounce-item">
                            Quay lại trang chủ
                        </a>
                    </div>
                </div>
            </article>

            <!-- Bình luận -->
            <div class="card shadow-lg">
                <div class="card-body p-4 p-lg-5">
                    <h4 class="mb-4 text-primary">Bình luận (<?= $binh_luan->num_rows; ?>)</h4>

                    <?php if ($binh_luan->num_rows > 0): ?>
                        <?php while ($c = $binh_luan->fetch_assoc()): ?>
                            <div class="border-bottom pb-3 mb-3">
                                <strong class="text-primary"><?= htmlspecialchars($c['ten_nguoi_dung']); ?></strong>
                                <small class="text-muted ms-2"><?= date('d/m/Y H:i', strtotime($c['ngay_binh_luan'])); ?></small>
                                <p class="mt-2 mb-0"><?= nl2br(htmlspecialchars($c['noi_dung'])); ?></p>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-muted fst-italic">Chưa có bình luận nào. Hãy là người đầu tiên!</p>
                    <?php endif; ?>

                    <hr class="my-4">

                    <?php if (isset($_SESSION['id_nguoi_dung'])): ?>
                        <form action="<?= base_url('controllers/chi_tiet_bai_viet.php') ?>" method="post">
                            <input type="hidden" name="id_bai_viet" value="<?= $id ?>">
                            <input type="hidden" name="action" value="binh_luan">
                            <textarea name="noi_dung" class="form-control mb-3" rows="4" placeholder="Viết bình luận của bạn..." required></textarea>
                            <button type="submit" class="btn btn-primary btn-lg px-5 btn-bounce bounce-item">
                                Gửi bình luận
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <a href="<?= base_url('views/auth/login.php') ?>" class="btn btn-outline-primary btn-lg px-5 btn-bounce bounce-item">
                                Đăng nhập để bình luận
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: BÀI VIẾT LIÊN QUAN (NẢY CỰC MẠNH) -->
        <div class="col-lg-4">
            <div class="sticky-sidebar">
                <?php if ($related->num_rows > 0): ?>
                    <div class="card shadow-lg">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 fw-bold">Bài viết liên quan</h5>
                        </div>
                        <div class="card-body p-3">
                            <?php while ($r = $related->fetch_assoc()): ?>
                                <a href="chi_tiet_bai_viet.php?id=<?= $r['id']; ?>"
                                   class="text-decoration-none text-dark d-block mb-4 related-item bounce-item">
                                    <div class="row g-3 align-items-center">
                                        <?php if (!empty($r['anh_dai_dien'])): ?>
                                            <div class="col-4">
                                                <img src="<?= img_url($r['anh_dai_dien']); ?>" class="img-fluid rounded shadow-sm" style="height:88px; object-fit:cover;" alt="">
                                            </div>
                                            <div class="col-8">
                                                <h6 class="fw-bold mb-1 line-clamp-2 text-primary"><?= htmlspecialchars($r['tieu_de']); ?></h6>
                                                <?php if (!empty($r['mo_ta_ngan'])): ?>
                                                    <small class="text-muted line-clamp-2 d-block"><?= htmlspecialchars($r['mo_ta_ngan']); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="col-12">
                                                <h6 class="fw-bold line-clamp-2 text-primary"><?= htmlspecialchars($r['tieu_de']); ?></h6>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>