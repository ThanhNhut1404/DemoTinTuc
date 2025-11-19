<?php
use Website\TinTuc\Models\BaiVietModel;
use Website\TinTuc\Models\ThanhVienModel;
use Website\TinTuc\Models\BinhLuanModel;

$bv = new BaiVietModel();
$tv = new ThanhVienModel();
$bl = new BinhLuanModel();

$countPosts = $bv->countAll();
$countUsers = $tv->countAll();
$countComments = $bl->countAll();
$totalViews = $bv->totalViews();
?>

<div class="card">
    <h2>Tổng quan</h2>
    <div style="display:flex;gap:12px;margin-top:12px;flex-wrap:wrap">
        <div style="flex:1;min-width:180px;background:#fff;border:1px solid #eef2f6;padding:12px;border-radius:8px;">
            <h3>Thống kê bài viết</h3>
            <p style="font-size:20px;font-weight:700;margin:6px 0;"><?= $countPosts ?></p>
            <small>Số bài viết hiện có</small>
        </div>

        <div style="flex:1;min-width:180px;background:#fff;border:1px solid #eef2f6;padding:12px;border-radius:8px;">
            <h3>Thống kê người dùng</h3>
            <p style="font-size:20px;font-weight:700;margin:6px 0;"><?= $countUsers ?></p>
            <small>Tổng người dùng</small>
        </div>

        <div style="flex:1;min-width:180px;background:#fff;border:1px solid #eef2f6;padding:12px;border-radius:8px;">
            <h3>Thống kê bình luận</h3>
            <p style="font-size:20px;font-weight:700;margin:6px 0;"><?= $countComments ?></p>
            <small>Tổng bình luận</small>
        </div>

        <div style="flex:1;min-width:180px;background:#fff;border:1px solid #eef2f6;padding:12px;border-radius:8px;">
            <h3>Lượt xem</h3>
            <p style="font-size:20px;font-weight:700;margin:6px 0;"><?= $totalViews ?></p>
            <small>Tổng lượt xem tất cả bài viết</small>
        </div>
    </div>
</div>
