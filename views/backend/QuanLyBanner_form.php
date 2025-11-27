<?php
// Fragment: form thêm/sửa banner
$isEdit = (isset($_GET['sub']) && $_GET['sub'] === 'edit');
$banner = $isEdit ? ($banner ?? null) : null;
?>
<div class="card">
    <h2 style="margin-top:0"><?= $isEdit ? 'Sửa banner' : 'Thêm banner' ?></h2>

    <form method="post" action="admin.php?action=<?= $isEdit ? 'banner_update' : 'banner_store' ?><?= $isEdit ? '&id=' . intval($banner['id']) : '' ?>" enctype="multipart/form-data" style="display:grid;grid-template-columns:1fr 320px;gap:18px">
        <div>
            <div class="form-group">
                <label>Liên kết</label>
                <input type="text" name="lien_ket" value="<?= htmlspecialchars($banner['lien_ket'] ?? '') ?>" style="width:100%;padding:8px;border:1px solid #eee;border-radius:6px" />
            </div>

            <div class="form-group">
                <label>Mô tả</label>
                <textarea name="mo_ta" rows="3" style="width:100%;padding:8px;border:1px solid #eee;border-radius:6px"><?= htmlspecialchars($banner['mo_ta'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label style="display:block">Trạng thái (on/off)</label>
                <input type="text" name="trang_thai" value="<?= htmlspecialchars($banner['trang_thai'] ?? 'off') ?>" placeholder="on or off" style="width:120px;padding:8px;border:1px solid #eee;border-radius:6px" />
                <div style="margin-top:6px;color:#666;font-size:13px">Nhập 'on' để hiển thị banner (mặc định 'off').</div>
            </div>

            <div style="margin-top:12px">
                <button type="submit" class="btn btn-role"><?= $isEdit ? 'Lưu thay đổi' : 'Tạo banner' ?></button>
                <a href="admin.php?action=banner" class="btn" style="margin-left:8px">Hủy</a>
            </div>
        </div>

        <aside>
            <label style="display:block;font-weight:600;margin-bottom:8px">Ảnh banner</label>
            <?php if ($isEdit && !empty($banner['hinh_banner'])): ?>
                <div style="margin-bottom:8px"><img src="../uploads/<?= htmlspecialchars($banner['hinh_banner']) ?>" style="max-width:100%;height:auto;border-radius:6px" alt=""></div>
            <?php endif; ?>
            <input type="file" name="hinh_banner" accept="image/*" />
            <div style="margin-top:10px;color:#666;font-size:13px">Kích thước đề xuất: 1200x400. Nếu không chọn ảnh khi sửa, ảnh cũ sẽ giữ nguyên.</div>
        </aside>
    </form>
</div>






