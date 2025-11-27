<?php
// Fragment: form thêm/sửa quảng cáo
// Controller đặt $_GET['sub'] = 'create' hoặc 'edit'
// Nếu edit, có $quangcao (record)
$isEdit = (isset($_GET['sub']) && $_GET['sub'] === 'edit');
$qc = $isEdit ? ($quangcao ?? null) : null;
?>
<div class="card">
    <h2 style="margin-top:0"><?= $isEdit ? 'Sửa quảng cáo' : 'Thêm quảng cáo' ?></h2>

    <form method="post" action="admin.php?action=<?= $isEdit ? 'qc_update' : 'qc_store' ?><?= $isEdit ? '&id=' . intval($qc['id']) : '' ?>" enctype="multipart/form-data" style="display:grid;grid-template-columns:1fr 320px;gap:18px">
        <div>
            <div class="form-group">
                <label>Tiêu đề</label>
                <input type="text" name="tieu_de" value="<?= htmlspecialchars($qc['tieu_de'] ?? '') ?>" required style="width:100%;padding:8px;border:1px solid #eee;border-radius:6px" />
            </div>

            <div class="form-group">
                <label>Liên kết</label>
                <input type="text" name="lien_ket" value="<?= htmlspecialchars($qc['lien_ket'] ?? '') ?>" style="width:100%;padding:8px;border:1px solid #eee;border-radius:6px" />
            </div>

            <div class="form-group">
                <label>Vị trí</label>
                <select name="vi_tri" style="padding:8px;border:1px solid #eee;border-radius:6px">
                    <option value="Trang_chu" <?= (!isset($qc['vi_tri']) || $qc['vi_tri'] === 'Trang_chu') ? 'selected' : '' ?>>Trang_chu</option>
                    <option value="Sidebar" <?= (isset($qc['vi_tri']) && $qc['vi_tri'] === 'Sidebar') ? 'selected' : '' ?>>Sidebar</option>
                </select>
            </div>

            <div class="form-group">
                <label>Trạng thái</label>
                <select name="trang_thai" style="padding:8px;border:1px solid #eee;border-radius:6px">
                    <option value="active" <?= (!isset($qc['trang_thai']) || $qc['trang_thai'] === 'active') ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= (isset($qc['trang_thai']) && $qc['trang_thai'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <div style="margin-top:12px">
                <button type="submit" class="btn btn-role"><?= $isEdit ? 'Lưu thay đổi' : 'Tạo quảng cáo' ?></button>
                <a href="admin.php?action=quang_cao" class="btn" style="margin-left:8px">Hủy</a>
            </div>
        </div>

        <aside>
            <label style="display:block;font-weight:600;margin-bottom:8px">Ảnh quảng cáo</label>
            <?php if ($isEdit && !empty($qc['hinh_anh'])): ?>
                <div style="margin-bottom:8px"><img src="../uploads/<?= htmlspecialchars($qc['hinh_anh']) ?>" style="max-width:100%;height:auto;border-radius:6px" alt=""></div>
            <?php endif; ?>
            <input type="file" name="hinh_anh" accept="image/*" />
            <div style="margin-top:10px;color:#666;font-size:13px">Kích thước đề xuất: 800x600. Nếu không chọn ảnh khi sửa, ảnh cũ sẽ giữ nguyên.</div>
        </aside>
    </form>
</div>
