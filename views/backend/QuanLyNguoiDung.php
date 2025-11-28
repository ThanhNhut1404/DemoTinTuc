<?php
use Website\TinTuc\Models\BaiVietModel;
use Website\TinTuc\Models\ThanhVienModel;
use Website\TinTuc\Models\BinhLuanModel;

// Cho phép controller truyền sẵn các biến; nếu chưa có thì tự lấy an toàn
if (!isset($countPosts) || !isset($countUsers) || !isset($countComments) || !isset($totalViews)) {
    try {
        $bv = new BaiVietModel();
        $tv = new ThanhVienModel();
        $bl = new BinhLuanModel();
        $countPosts = (int)$bv->countAll();
        $countUsers = (int)$tv->countAll();
        $countComments = (int)$bl->countAll();
        $totalViews = (int)$bv->totalViews();
    } catch (\Throwable $e) {
        $countPosts = $countUsers = $countComments = $totalViews = 0;
    }
}
?>

<div class="card dashboard">
    <h2>Trang Tổng quan</h2>
    <div class="stats-grid">
        <div class="stat-card">
            <p class="stat-title">Thống kê bài viết</p>
            <p class="stat-value"><?= (int)$countPosts ?></p>
            <p class="stat-note">Số bài viết hiện có</p>
        </div>
        <div class="stat-card">
            <p class="stat-title">Thống kê người dùng</p>
            <p class="stat-value"><?= (int)$countUsers ?></p>
            <p class="stat-note">Tổng người dùng</p>
        </div>
        <div class="stat-card">
            <p class="stat-title">Thống kê bình luận</p>
            <p class="stat-value"><?= (int)$countComments ?></p>
            <p class="stat-note">Tổng bình luận</p>
        </div>
        <div class="stat-card">
            <p class="stat-title">Lượt xem</p>
            <p class="stat-value"><?= (int)$totalViews ?></p>
            <p class="stat-note">Tổng lượt xem tất cả bài viết</p>
        </div>
    </div>
</div>
