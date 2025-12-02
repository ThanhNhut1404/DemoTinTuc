<?php
include __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper functions
if (!function_exists('img_url')) {
    function img_url($path)
    {
        return '/Demotintuc/' . ltrim($path, '/');
    }
}
if (!function_exists('base_url')) {
    function base_url($path = '')
    {
        return '/Demotintuc/' . ltrim($path, '/');
    }
}

// === LẤY ID BÀI VIẾT ===
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>ID bài viết không hợp lệ.</div></div>";
    exit;
}
$id = (int)$_GET['id'];
$return_url = "index.php?action=chi_tiet_bai_viet&id=" . $id;
$return_url_encoded = urlencode($return_url);

// === XỬ LÝ BÌNH LUẬN (trước hiển thị trang) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'binh_luan') {
    if (!isset($_SESSION['id_nguoi_dung'])) {
        $_SESSION['flash_message'] = 'Vui lòng đăng nhập để bình luận!';
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    $noi_dung = trim($_POST['noi_dung'] ?? '');
    if (empty($noi_dung)) {
        $_SESSION['flash_message'] = 'Vui lòng nhập nội dung bình luận!';
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    $uid = (int)$_SESSION['id_nguoi_dung'];
    $id_bai_viet = (int)($_POST['id_bai_viet'] ?? 0);

    if ($id_bai_viet !== $id) {
        $_SESSION['flash_message'] = 'Dữ liệu không hợp lệ!';
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO binh_luan (id_bai_viet, id_nguoi_dung, noi_dung, ngay_binh_luan) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $id, $uid, $noi_dung);
    $stmt->execute();
    $stmt->close();

    $_SESSION['flash_message'] = 'Bình luận đã được gửi thành công!';
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// === XỬ LÝ AJAX LIKE / UNLIKE / SAVE / UNSAVE ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do'], $_POST['id_bai_viet'])) {
    header('Content-Type: application/json');

    if (!isset($_SESSION['id_nguoi_dung'])) {
        echo json_encode(['login' => true]);
        exit;
    }

    $uid = (int)$_SESSION['id_nguoi_dung'];
    $post_id = (int)$_POST['id_bai_viet'];
    $action = $_POST['do'];

    // Chỉ cho phép xử lý đúng bài viết đang xem
    if ($post_id !== $id) {
        echo json_encode(['success' => false]);
        exit;
    }

    if ($action === 'like') {
        $stmt = $conn->prepare("INSERT IGNORE INTO yeu_thich (id_nguoi_dung, id_bai_viet) VALUES (?, ?)");
        $stmt->bind_param("ii", $uid, $id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $stmt2 = $conn->prepare("UPDATE bai_viet SET luot_thich = luot_thich + 1 WHERE id = ?");
            $stmt2->bind_param("i", $id);
            $stmt2->execute();
            $stmt2->close();
        }
        $stmt->close();
    } elseif ($action === 'unlike') {
        $stmt = $conn->prepare("DELETE FROM yeu_thich WHERE id_nguoi_dung = ? AND id_bai_viet = ?");
        $stmt->bind_param("ii", $uid, $id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $stmt2 = $conn->prepare("UPDATE bai_viet SET luot_thich = luot_thich - 1 WHERE id = ?");
            $stmt2->bind_param("i", $id);
            $stmt2->execute();
            $stmt2->close();
        }
        $stmt->close();
    } elseif ($action === 'save') {
        $stmt = $conn->prepare("INSERT IGNORE INTO luu_bai_viet (id_nguoi_dung, id_bai_viet) VALUES (?, ?)");
        $stmt->bind_param("ii", $uid, $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => true, 'saved' => true]);
        exit;
    } elseif ($action === 'unsave') {
        $stmt = $conn->prepare("DELETE FROM luu_bai_viet WHERE id_nguoi_dung = ? AND id_bai_viet = ?");
        $stmt->bind_param("ii", $uid, $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => true, 'saved' => false]);
        exit;
    }

    // Trả về số lượt thích mới (cho like/unlike)
    $stmt = $conn->prepare("SELECT luot_thich FROM bai_viet WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['luot_thich'] ?? 0;
    $stmt->close();

    $is_liked = false;
    if ($uid) {
        $stmt = $conn->prepare("SELECT 1 FROM yeu_thich WHERE id_nguoi_dung = ? AND id_bai_viet = ?");
        $stmt->bind_param("ii", $uid, $id);
        $stmt->execute();
        $stmt->store_result();
        $is_liked = $stmt->num_rows > 0;
        $stmt->close();
    }

    echo json_encode([
        'success' => true,
        'liked' => $is_liked,
        'count' => $count
    ]);
    exit;
}

// === TĂNG LƯỢT XEM (chống spam 30 giây/lần) ===
if (!isset($_SESSION['views'][$id]) || (time() - ($_SESSION['views'][$id] ?? 0)) >= 30) {
    $stmt = $conn->prepare("UPDATE bai_viet SET luot_xem = luot_xem + 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $_SESSION['views'][$id] = time();
}

// === LẤY BÀI VIẾT CHÍNH ===
$stmt = $conn->prepare("SELECT b.*, n.ho_ten AS tac_gia, COALESCE(b.luot_thich, 0) AS luot_thich, COALESCE(b.luot_xem, 0) AS luot_xem 
                        FROM bai_viet b 
                        LEFT JOIN nguoi_dung n ON b.id_tac_gia = n.id 
                        WHERE b.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "<div class='container mt-5'><div class='alert alert-warning text-center p-5 fs-3'>Bài viết không tồn tại.</div></div>";
    exit;
}
$bv = $result->fetch_assoc();
$stmt->close();

// === KIỂM TRA ĐÃ THÍCH / ĐÃ LƯU CHƯA ===
$yeu_thich = $da_luu = false;
if (isset($_SESSION['id_nguoi_dung'])) {
    $uid = (int)$_SESSION['id_nguoi_dung'];

    $stmt = $conn->prepare("SELECT 1 FROM yeu_thich WHERE id_bai_viet = ? AND id_nguoi_dung = ?");
    $stmt->bind_param("ii", $id, $uid);
    $stmt->execute();
    $stmt->store_result();
    $yeu_thich = $stmt->num_rows > 0;
    $stmt->close();

    $stmt = $conn->prepare("SELECT 1 FROM luu_bai_viet WHERE id_bai_viet = ? AND id_nguoi_dung = ?");
    $stmt->bind_param("ii", $id, $uid);
    $stmt->execute();
    $stmt->store_result();
    $da_luu = $stmt->num_rows > 0;
    $stmt->close();
}

// === LẤY BÌNH LUẬN ===
$stmt = $conn->prepare("SELECT bl.noi_dung, u.ho_ten AS ten_nguoi_dung, bl.ngay_binh_luan 
                        FROM binh_luan bl 
                        JOIN nguoi_dung u ON bl.id_nguoi_dung = u.id 
                        WHERE bl.id_bai_viet = ? 
                        ORDER BY bl.ngay_binh_luan DESC");
$stmt->bind_param("i", $id);
$stmt->execute();
$binh_luan = $stmt->get_result();
$stmt->close();

// === BÀI VIẾT LIÊN QUAN ===
$stmt = $conn->prepare("SELECT id, tieu_de, anh_dai_dien, mo_ta_ngan 
                        FROM bai_viet 
                        WHERE id != ? 
                        ORDER BY ngay_dang DESC 
                        LIMIT 6");
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
        /* Giữ nguyên toàn bộ CSS đẹp của bạn - không thay đổi */
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
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            animation: fadeInUp 0.6s ease;
        }

        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
        }

        .bounce-item,
        .related-item,
        .btn-bounce {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
            position: relative;
            overflow: hidden;
        }

        .bounce-item::before,
        .related-item::before,
        .btn-bounce::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.1), rgba(0, 198, 255, 0.1));
            opacity: 0;
            transition: opacity 0.4s;
            border-radius: inherit;
            pointer-events: none;
        }

        .bounce-item:hover,
        .related-item:hover,
        .btn-bounce:hover {
            transform: translateY(-8px) scale(1.02) !important;
            box-shadow: 0 20px 50px rgba(13, 110, 253, 0.3) !important;
            z-index: 10;
        }

        .bounce-item:hover::before,
        .related-item:hover::before,
        .btn-bounce:hover::before {
            opacity: 1;
        }

        @keyframes heartbeat {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.4);
            }
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
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sticky-sidebar {
            position: sticky;
            top: 100px;
        }

        .like-btn.active,
        .like-btn:hover {
            background: #e74c3c !important;
            border-color: #e74c3c !important;
            color: white !important;
        }

        .save-btn.active,
        .save-btn:hover {
            background: #0d53bcff !important;
            border-color: #0d6efd !important;
            color: white !important;
        }

        .like-btn.active i {
            animation: heartbeat 0.8s ease;
        }

        @media (max-width: 992px) {
            .sticky-sidebar {
                position: static !important;
                margin-top: 2rem;
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

                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4">
                        <strong><?= htmlspecialchars($_SESSION['flash_message']); ?></strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['flash_message']); ?>
                <?php endif; ?>

                <article class="card shadow-lg mb-5">
                    <div class="card-body p-4 p-lg-5">
                        <h1 class="display-5 fw-bold mb-4"><?= htmlspecialchars($bv['tieu_de']); ?></h1>

                        <div class="text-muted small mb-4 d-flex flex-wrap align-items-center gap-3 border-bottom pb-3">
                            <span><i class="fas fa-calendar-alt me-2"></i><?= date('d/m/Y', strtotime($bv['ngay_dang'])); ?></span>
                            <span><i class="fas fa-user me-2"></i><?= htmlspecialchars($bv['tac_gia'] ?? 'Ẩn danh'); ?></span>
                            <span><i class="fas fa-eye me-2"></i><?= number_format($bv['luot_xem']); ?> lượt xem</span>

                            <div class="ms-auto d-flex gap-3">
                                <!-- NÚT THÍCH -->
                                <?php if (isset($_SESSION['id_nguoi_dung'])): ?>
                                    <button class="btn btn-outline-danger btn-sm rounded-pill like-btn <?= $yeu_thich ? 'active' : '' ?>"
                                        data-id="<?= $id ?>" data-action="<?= $yeu_thich ? 'unlike' : 'like' ?>">
                                        <i class="fa<?= $yeu_thich ? 's' : 'r' ?> fa-heart"></i>
                                        <span class="like-count ms-1"><?= number_format($bv['luot_thich']); ?></span>
                                    </button>
                                <?php else: ?>
                                    <a href="index.php?action=login&return_url=<?= $return_url_encoded ?>" class="text-danger text-decoration-none">
                                        <i class="far fa-heart"></i> <?= number_format($bv['luot_thich']); ?>
                                    </a>
                                <?php endif; ?>

                                <!-- NÚT LƯU -->
                                <?php if (isset($_SESSION['id_nguoi_dung'])): ?>
                                    <button class="btn btn-outline-primary btn-sm rounded-pill save-btn <?= $da_luu ? 'active' : '' ?>"
                                        data-id="<?= $id ?>" data-action="<?= $da_luu ? 'unsave' : 'save' ?>">
                                        <i class="fa<?= $da_luu ? 's' : 'r' ?> fa-bookmark"></i>
                                        <span class="ms-1"><?= $da_luu ? 'Đã lưu' : 'Lưu' ?></span>
                                    </button>
                                <?php else: ?>
                                    <a href="index.php?action=login&return_url=<?= $return_url_encoded ?>" class="text-primary text-decoration-none">
                                        <i class="far fa-bookmark"></i> Lưu
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($bv['mo_ta_ngan'])): ?>
                            <p class="lead fs-5 text-muted mb-4"><?= htmlspecialchars($bv['mo_ta_ngan']); ?></p>
                        <?php endif; ?>

                        <?php if (!empty($bv['anh_dai_dien'])): ?>
                            <div class="text-center mb-5">
                                <img src="<?= img_url($bv['anh_dai_dien']) ?>" alt="<?= htmlspecialchars($bv['tieu_de']) ?>"
                                    class="img-fluid rounded shadow-lg article-img">
                            </div>
                        <?php endif; ?>

                        <div class="article-content fs-5 lh-lg text-justify">
                            <?= $bv['noi_dung'] ?? '' ?>
                        </div>

                        <div class="mt-5 pt-4 border-top text-center">
                            <a href="/Demotintuc/public/" class="btn btn-primary btn-lg px-5 btn-bounce">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại trang chủ
                            </a>
                        </div>
                    </div>
                </article>

                <!-- BÌNH LUẬN -->
                <div class="card shadow-lg">
                    <div class="card-body p-4 p-lg-5">
                        <h4 class="mb-4 text-primary"><i class="fas fa-comments me-2"></i>Bình luận (<?= $binh_luan->num_rows; ?>)</h4>

                        <?php if ($binh_luan->num_rows > 0): ?>
                            <div class="comments-list">
                                <?php while ($c = $binh_luan->fetch_assoc()): ?>
                                    <div class="border-bottom pb-4 mb-4">
                                        <strong class="text-primary"><?= htmlspecialchars($c['ten_nguoi_dung']); ?></strong>
                                        <small class="text-muted ms-2"><i class="fas fa-clock"></i> <?= date('d/m/Y H:i', strtotime($c['ngay_binh_luan'])); ?></small>
                                        <p class="mt-2 mb-0"><?= nl2br(htmlspecialchars($c['noi_dung'])); ?></p>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center py-5 fst-italic">
                                <i class="fas fa-comments fa-3x opacity-25 mb-3 d-block"></i>
                                Chưa có bình luận nào. Hãy là người đầu tiên!
                            </p>
                        <?php endif; ?>

                        <hr class="my-4">

                        <?php if (isset($_SESSION['id_nguoi_dung'])): ?>
                            <form action="" method="post">
                                <input type="hidden" name="id_bai_viet" value="<?= $id ?>">
                                <input type="hidden" name="action" value="binh_luan">
                                <div class="mb-3">
                                    <label class="form-label">Viết bình luận của bạn</label>
                                    <textarea name="noi_dung" class="form-control" rows="4" placeholder="Chia sẻ suy nghĩ của bạn..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg px-5 btn-bounce">
                                    <i class="fas fa-paper-plane me-2"></i>Gửi bình luận
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="text-center py-5 bg-light rounded">
                                <i class="fas fa-lock fa-3x text-muted mb-3"></i>
                                <a href="index.php?action=login&return_url=<?= $return_url_encoded ?>" class="btn btn-primary">Đăng nhập để bình luận</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- CỘT PHẢI: BÀI LIÊN QUAN -->
            <div class="col-lg-4">
                <div class="sticky-sidebar">
                    <?php if ($related_posts->num_rows > 0): ?>
                        <div class="card shadow-lg">
                            <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);">
                                <h5 class="mb-0 fw-bold"><i class="fas fa-lightbulb me-2"></i>Bài viết gợi ý</h5>
                            </div>
                            <div class="card-body p-0">
                                <?php while ($r = $related_posts->fetch_assoc()): ?>
                                    <a href="/Demotintuc/public/index.php?action=chi_tiet_bai_viet&id=<?= $r['id'] ?>"
                                        class="text-decoration-none text-dark d-block border-bottom related-item bounce-item p-3">
                                        <div class="row g-2 align-items-center">
                                            <?php if (!empty($r['anh_dai_dien'])): ?>
                                                <div class="col-4">
                                                    <img src="<?= img_url($r['anh_dai_dien']); ?>" class="img-fluid rounded shadow-sm" style="height:70px; object-fit:cover;">
                                                </div>
                                                <div class="col-8">
                                                    <h6 class="fw-bold mb-1 line-clamp-2 text-primary" style="font-size:0.95rem;">
                                                        <?= htmlspecialchars($r['tieu_de']); ?>
                                                    </h6>
                                                    <?php if (!empty($r['mo_ta_ngan'])): ?>
                                                        <small class="text-muted line-clamp-1 d-block" style="font-size:0.8rem;">
                                                            <?= htmlspecialchars(substr($r['mo_ta_ngan'], 0, 60)); ?>...
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="col-12">
                                                    <h6 class="fw-bold line-clamp-2 text-primary" style="font-size:0.95rem;">
                                                        <?= htmlspecialchars($r['tieu_de']); ?>
                                                    </h6>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.like-btn, .save-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const action = this.dataset.action;

                    fetch('', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams({
                                'do': action,
                                'id_bai_viet': id
                            })
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.login) {
                                window.location.href = 'index.php?action=login';
                                return;
                            }

                            if (this.classList.contains('like-btn')) {
                                const isLiked = data.liked;
                                this.dataset.action = isLiked ? 'unlike' : 'like';
                                this.classList.toggle('active', isLiked);
                                this.querySelector('i').className = isLiked ? 'fas fa-heart' : 'far fa-heart';
                                if (data.count !== undefined) {
                                    this.querySelector('.like-count').textContent = data.count.toLocaleString();
                                }
                            } else {
                                const isSaved = data.saved;
                                this.dataset.action = isSaved ? 'unsave' : 'save';
                                this.classList.toggle('active', isSaved);
                                this.querySelector('i').className = isSaved ? 'fas fa-bookmark' : 'far fa-bookmark';
                                this.querySelector('span.ms-1').textContent = isSaved ? 'Đã lưu' : 'Lưu';
                            }
                        });
                });
            });
        });
    </script>
</body>

</html>