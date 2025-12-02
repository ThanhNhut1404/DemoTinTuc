<?php
// Fragment: danh sách nền website
$sub = $_GET['sub'] ?? '';
if ($sub === 'create' || $sub === 'edit') {
    include __DIR__ . '/QuanLyBgWallpaper_form.php';
    return;
}
?>
<style>
    .wallpaper-card {
        padding: 24px;
        background: #f7f9fc;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    .wallpaper-card h2 {
        margin-top: 0;
        font-size: 24px; /* match .member-title */
        margin-bottom: 12px;
        color: #1f2937;
    }

    .wallpaper-card .btn-add {
        background: #007bff; /* match .member-title color */
        color: #fff;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 12px; /* match form-inline buttons */
        height: 38px; /* same height as .form-inline .btn-search */
        box-sizing: border-box;
        font-weight: 400; /* not bold per request */
        font-size: 24px; /* keep larger text to match heading */
        margin-bottom: 12px;
        border: none;
    }
    .wallpaper-card .btn-add:hover { background: #0056d6 }

    .wallpaper-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
    }

    .wallpaper-item {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        overflow: hidden;
        transition: all 0.3s;
    }

    .wallpaper-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }

    .wallpaper-preview {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
        background: #f3f4f6;
    }

    .wallpaper-info {
        padding: 12px;
    }

    .wallpaper-name {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 4px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .wallpaper-desc {
        font-size: 0.85rem;
        color: #6b7280;
        margin-bottom: 8px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .wallpaper-status {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .wallpaper-status.active {
        background: #ecfdf5;
        color: #047857;
    }

    .wallpaper-status.inactive {
        background: #fff5f5; /* pale red background to match 'off' state */
        color: #b91c1c; /* strong red text like 'Đang dùng' typography but red */
    }

    .wallpaper-actions {
        display: flex;
        gap: 8px;
        padding-top: 8px;
        border-top: 1px solid #e5e7eb;
    }

    .wallpaper-actions a {
        flex: 1;
        padding: 6px 10px;
        text-align: center;
        text-decoration: none;
        font-size: 0.85rem;
        border-radius: 6px;
        color: #0d6efd;
        transition: all 0.2s;
    }

    .wallpaper-actions a:hover {
        background: #f3f4f6;
    }

    .wallpaper-actions a.delete {
        color: #ef4444;
    }

    .wallpaper-actions a.delete:hover {
        background: #fee2e2;
    }

    /* Activate link: green like the 'Đang dùng' label */
    .wallpaper-actions a.activate { color: #047857 }
    .wallpaper-actions a.activate:hover { background: #ecfdf5 }

    /* Make action links bold: Kích hoạt, Sửa, Xóa */
    .wallpaper-actions a.activate,
    .wallpaper-actions a.edit,
    .wallpaper-actions a.delete { font-weight: 700 }

    /* Style 'Sửa' to match the member-title blue (not filled) */
    .wallpaper-actions a.edit {
        background: transparent;
        color: #007bff; /* match .member-title */
        border-radius: 6px;
        padding: 6px 10px;
    }
    .wallpaper-actions a.edit:hover { background: #eef6ff }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
        color: #6b7280;
    }

    .empty-state .icon {
        font-size: 3rem;
        margin-bottom: 15px;
    }
</style>

<div class="wallpaper-card">
    <h2 class="member-title">Danh sách Background</h2>
    
    <?php if (isset($_GET['updated'])): ?>
        <div style="padding:10px;background:#e6ffee;border:1px solid #90ee90;margin-bottom:15px;border-radius:8px; color:#0a7a2a;">Cập nhật thành công.</div>
    <?php endif; ?>
    
    <p><a href="admin.php?action=bg_wallpaper_create" class="btn-add">Thêm Background</a></p>

    <?php if (empty($wallpapers)): ?>
        <div class="empty-state">
            <div class="icon">🖼️</div>
            <p>Chưa có Background mới nào.</p>
        </div>
    <?php else: ?>
        <div class="wallpaper-grid">
            <?php foreach ($wallpapers as $w): ?>
                <div class="wallpaper-item">
                    <?php if (!empty($w['duong_dan_file'])): ?>
                        <img src="uploads/wallpapers/<?= htmlspecialchars($w['duong_dan_file']) ?>" class="wallpaper-preview" alt="">
                    <?php else: ?>
                        <div class="wallpaper-preview" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                    <?php endif; ?>

                    <div class="wallpaper-info">
                        <div class="wallpaper-name"><?= htmlspecialchars($w['ten_wallpaper']) ?></div>
                        <div class="wallpaper-desc"><?= htmlspecialchars($w['mo_ta'] ?? '') ?></div>
                        <span class="wallpaper-status <?= $w['trang_thai'] === 'on' ? 'active' : 'inactive' ?>">
                            <?= $w['trang_thai'] === 'on' ? '✓ Đang dùng' : 'x Đang tắt' ?>
                        </span>
                        <div class="wallpaper-actions">
                            <?php if ($w['trang_thai'] !== 'on'): ?>
                                    <a href="admin.php?action=bg_wallpaper_toggle&id=<?= $w['id'] ?>" class="activate" title="Kích hoạt">Kích hoạt</a>
                                <?php endif; ?>
                            <a href="admin.php?action=bg_wallpaper_edit&id=<?= $w['id'] ?>" class="edit" title="Sửa">Sửa</a>
                            <a href="admin.php?action=bg_wallpaper_delete&id=<?= $w['id'] ?>" class="delete" onclick="return confirm('Bạn muốn xóa Background này?')" title="Xóa">Xóa</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
