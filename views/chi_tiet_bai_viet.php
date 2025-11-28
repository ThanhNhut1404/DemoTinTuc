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
$stmt = $conn->prepare("SELECT id, tieu_de, anh_dai_dien, mo_ta_ngan FROM bai_viet WHERE id != ? ORDER BY ngay_dang DESC LIMIT 6");
$stmt->bind_param("i", $id);
$stmt->execute();
$related_posts = $stmt->get_result();
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
        body { 
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding-top: 76px; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .article-img { 
            border-radius: 15px; 
            max-height: 500px; 
            object-fit: cover; 
            width: 100%; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            animation: fadeInUp 0.6s ease;
        }
        
        .card { 
            border: none; 
            border-radius: 15px; 
            overflow: hidden; 
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
            transform: translateY(-5px);
        }

        /* HIỆU Ứng cho nút và bài liên quan */
        .bounce-item, .related-item, .btn-bounce {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
            position: relative;
            overflow: hidden;
        }
        
        .bounce-item::before, .related-item::before, .btn-bounce::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(13,110,253,0.1), rgba(0,198,255,0.1));
            opacity: 0;
            transition: opacity 0.4s;
            border-radius: inherit;
            pointer-events: none;
        }
        
        .bounce-item:hover, .related-item:hover, .btn-bounce:hover {
            transform: translateY(-8px) scale(1.02) !important;
            box-shadow: 0 20px 50px rgba(13,110,253,0.3) !important;
            z-index: 10;
        }
        
        .bounce-item:hover::before, .related-item:hover::before, .btn-bounce:hover::before { 
            opacity: 1; 
        }

        .bounce-item:active, .related-item:active, .btn-bounce:active {
            transform: translateY(-4px) scale(1.01) !important;
            animation: pulse 0.6s;
        }
        
        @keyframes pulse {
            0%, 100% { transform: translateY(-8px) scale(1.02); }
            50% { transform: translateY(2px) scale(1.01); }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .sticky-sidebar { 
            position: sticky;
            top: 100px;
        }
        
        .article-content {
            color: #333;
            line-height: 1.8;
        }
        
        .article-content p {
            margin-bottom: 1.5rem;
        }
        
        .comment-item {
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
            border: none;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #0b5cdb 0%, #0bb5d8 100%);
        }
        
        .comments-list {
            animation: fadeInUp 0.5s ease;
        }
        
        @media (max-width: 992px) { 
            .sticky-sidebar { 
                position: static !important;
                margin-top: 2rem;
            }
        }
        
        @media (max-width: 768px) {
            .article-img {
                max-height: 300px;
            }
            
            h1.display-5 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container my-5">
    <div class="row g-5">
        <!-- CỘT TRÁI: BÀI VIẾT CHÍNH -->
        <div class="col-lg-8">
            <?php if (!empty($bv['anh_dai_dien'])): ?>
                <img src="<?= htmlspecialchars(img_url($bv['anh_dai_dien'])) ?>" alt="Ảnh bài viết" class="img-fluid rounded mb-4 article-img">
            <?php endif; ?>

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
                    
                    <div class="text-muted small mb-4 d-flex flex-wrap gap-3 border-bottom pb-3">
                        <span><i class="fas fa-calendar-alt me-2"></i><?= date('d/m/Y', strtotime($bv['ngay_dang'])); ?></span>
                        <span><i class="fas fa-user me-2"></i><?= htmlspecialchars($bv['tac_gia'] ?? 'Ẩn danh'); ?></span>
                        <span><i class="fas fa-eye me-2"></i><?= number_format($bv['luot_xem']); ?> lượt xem</span>
                    </div>

                    <?php if (!empty($bv['mo_ta_ngan'])): ?>
                        <p class="lead fs-5 text-muted mb-4"><?= htmlspecialchars($bv['mo_ta_ngan']); ?></p>
                    <?php endif; ?>

                    <div class="article-content fs-5 lh-lg">
                        <?= nl2br(htmlspecialchars($bv['noi_dung'] ?? '')); ?>
                    </div>

                    <div class="mt-5 pt-4 border-top">
                        <a href="<?= base_url('public/index.php') ?>" class="btn btn-primary btn-lg px-5 btn-bounce bounce-item">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại trang chủ
                        </a>
                    </div>
                </div>
            </article>

            <!-- Bình luận -->
            <div class="card shadow-lg">
                <div class="card-body p-4 p-lg-5">
                    <h4 class="mb-4 text-primary">
                        <i class="fas fa-comments me-2"></i>Bình luận (<?= !empty($binh_luan) ? $binh_luan->num_rows : 0; ?>)
                    </h4>

                    <?php if (!empty($binh_luan) && $binh_luan->num_rows > 0): ?>
                        <div class="comments-list">
                            <?php while ($c = $binh_luan->fetch_assoc()): ?>
                                <div class="comment-item border-bottom pb-4 mb-4">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-grow-1">
                                            <strong class="text-primary"><?= htmlspecialchars($c['ten_nguoi_dung']); ?></strong>
                                            <small class="text-muted ms-2">
                                                <i class="fas fa-clock me-1"></i><?= date('d/m/Y H:i', strtotime($c['ngay_binh_luan'])); ?>
                                            </small>
                                            <p class="mt-2 mb-0"><?= nl2br(htmlspecialchars($c['noi_dung'])); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted fst-italic text-center py-5">
                            <i class="fas fa-comments fa-3x opacity-25 mb-3 d-block"></i>
                            Chưa có bình luận nào. Hãy là người đầu tiên!
                        </p>
                    <?php endif; ?>

                    <hr class="my-4">

                    <?php if (isset($_SESSION['id_nguoi_dung'])): ?>
                        <form action="<?= base_url('controllers/chi_tiet_bai_viet.php') ?>" method="post">
                            <input type="hidden" name="id_bai_viet" value="<?= $id ?>">
                            <input type="hidden" name="action" value="binh_luan">
                            <div class="form-group mb-3">
                                <label for="noi_dung" class="form-label">Viết bình luận của bạn</label>
                                <textarea name="noi_dung" id="noi_dung" class="form-control" rows="4" placeholder="Chia sẻ suy nghĩ của bạn..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg px-5 btn-bounce bounce-item">
                                <i class="fas fa-paper-plane me-2"></i>Gửi bình luận
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-5 bg-light rounded">
                            <i class="fas fa-lock fa-3x text-muted mb-3 d-block"></i>
                            <a href="<?= base_url('views/login.php') ?>" class="btn btn-primary btn-lg px-5 btn-bounce bounce-item">
                                Đăng nhập để bình luận
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: BÀI VIẾT LIÊN QUAN -->
        <div class="col-lg-4">
            <div class="sticky-sidebar">
                <?php if (!empty($related_posts) && $related_posts->num_rows > 0): ?>
                    <div class="card shadow-lg">
                        <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%); color: white;">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-lightbulb me-2"></i>Bài viết gợi ý
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <?php while ($r = $related_posts->fetch_assoc()): ?>
                                <a href="chi_tiet_bai_viet.php?id=<?= $r['id']; ?>"
                                   class="text-decoration-none text-dark d-block border-bottom related-item bounce-item p-3 transition"
                                   style="transition: all 0.3s ease;">
                                    <div class="row g-2 align-items-center h-100">
                                        <?php if (!empty($r['anh_dai_dien'])): ?>
                                            <div class="col-4">
                                                <img src="<?= img_url($r['anh_dai_dien']); ?>" class="img-fluid rounded shadow-sm" style="height:70px; object-fit:cover; width:100%;" alt="">
                                            </div>
                                            <div class="col-8">
                                                <h6 class="fw-bold mb-1 line-clamp-2 text-primary" style="font-size: 0.95rem;"><?= htmlspecialchars(substr($r['tieu_de'], 0, 50)); ?></h6>
                                                <?php if (!empty($r['mo_ta_ngan'])): ?>
                                                    <small class="text-muted line-clamp-1 d-block" style="font-size: 0.8rem;"><?= htmlspecialchars(substr($r['mo_ta_ngan'], 0, 40)); ?>...</small>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="col-12">
                                                <h6 class="fw-bold line-clamp-2 text-primary" style="font-size: 0.95rem;"><?= htmlspecialchars(substr($r['tieu_de'], 0, 50)); ?></h6>
                                                <small class="text-muted"><?= date('d/m/Y', strtotime($r['ngay_dang'] ?? date('Y-m-d'))); ?></small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>Chưa có bài viết gợi ý
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