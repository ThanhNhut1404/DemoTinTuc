<div class="card">
    <h2 style="margin-top:0;margin-bottom:14px">Sửa bài viết</h2>

    <form method="POST" action="admin.php?action=update" enctype="multipart/form-data" style="display:grid;grid-template-columns:1fr 420px;gap:20px;align-items:start">
        <div>
            <input type="hidden" name="id" value="<?= htmlspecialchars($baiviet['id']) ?>">

            <div class="form-group" style="margin-bottom:12px">
                <label style="display:block;font-weight:600;margin-bottom:6px">Tiêu đề</label>
                <input type="text" name="tieu_de" value="<?= htmlspecialchars($baiviet['tieu_de'] ?? '') ?>" required style="width:100%;padding:10px;border:1px solid #e6eef8;border-radius:8px" />
            </div>

            <div class="form-group" style="margin-bottom:12px">
                <label style="display:block;font-weight:600;margin-bottom:6px">Mô tả ngắn</label>
                <textarea name="mo_ta_ngan" rows="3" style="width:100%;padding:10px;border:1px solid #e6eef8;border-radius:8px"><?= htmlspecialchars($baiviet['mo_ta_ngan'] ?? '') ?></textarea>
            </div>

            <div class="form-group" style="margin-bottom:12px">
                <label style="display:block;font-weight:600;margin-bottom:6px">Nội dung</label>
                <textarea name="noi_dung" rows="6" style="width:100%;padding:10px;border:1px solid #e6eef8;border-radius:8px"><?= htmlspecialchars($baiviet['noi_dung'] ?? '') ?></textarea>
            </div>

            <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;margin-top:8px">
                <div style="flex:1;min-width:160px">
                    <label style="display:block;font-weight:600;margin-bottom:6px">Chuyên mục (ID)</label>
                    <input type="text" name="id_chuyen_muc" value="<?= htmlspecialchars($baiviet['id_chuyen_muc'] ?? '') ?>" style="width:100%;padding:8px;border:1px solid #e6eef8;border-radius:8px" />
                </div>
                <div style="flex:1;min-width:160px">
                    <label style="display:block;font-weight:600;margin-bottom:6px">Thẻ tag</label>
                    <input type="text" name="tag" value="<?= htmlspecialchars($baiviet['tag'] ?? '') ?>" style="width:100%;padding:8px;border:1px solid #e6eef8;border-radius:8px" />
                </div>
            </div>

            <div style="display:flex;gap:12px;align-items:center;margin-top:12px">
                <label style="display:flex;align-items:center;gap:8px"><input type="checkbox" name="la_noi_bat" <?= !empty($baiviet['la_noi_bat']) ? 'checked' : '' ?>> Tin nổi bật</label>
                <label style="display:flex;align-items:center;gap:8px">Trạng thái
                    <?php $currentStatus = trim(strval($baiviet['trang_thai'] ?? '')) ?>
                    <select name="trang_thai" style="margin-left:6px;padding:6px;border:1px solid #e6eef8;border-radius:8px">
                        <option value="nhap" <?= ($currentStatus === 'nhap') ? 'selected' : '' ?>>Nháp</option>
                        <option value="cho_duyet" <?= ($currentStatus === 'cho_duyet') ? 'selected' : '' ?>>Chờ duyệt</option>
                        <option value="da_dang" <?= ($currentStatus === 'da_dang') ? 'selected' : '' ?>>Đã đăng</option>
                    </select>
                </label>
                <label style="display:flex;align-items:center;gap:8px">Ngày đăng
                    <input type="datetime-local" name="ngay_dang" style="margin-left:6px;padding:6px;border:1px solid #e6eef8;border-radius:8px" value="<?= !empty($baiviet['ngay_dang']) ? date('Y-m-d\\TH:i', strtotime($baiviet['ngay_dang'])) : '' ?>" />
                </label>
            </div>

            <div style="margin-top:16px">
                <button type="submit" class="btn btn-role">Cập nhật</button>
            </div>
        </div>

        <aside style="min-width:240px">
            <div style="border:1px dashed #e6eef8;padding:12px;border-radius:8px;background:#fafcff;text-align:center">
                <label style="display:block;font-weight:600;margin-bottom:8px">Ảnh đại diện hiện tại</label>
                <?php if (!empty($baiviet['anh_dai_dien'])): ?>
                    <img src="uploads/<?= htmlspecialchars($baiviet['anh_dai_dien']) ?>" style="max-width:100%;border-radius:6px;display:block;margin:0 auto 10px" />
                <?php else: ?>
                    <div style="width:100%;height:120px;border-radius:6px;background:#f8fafc;display:flex;align-items:center;justify-content:center;color:#cbd5e1">Chưa có ảnh</div>
                <?php endif; ?>
                <input type="file" name="anh_dai_dien" accept="image/*" />
            </div>

            <input type="hidden" name="existing_anh" value="<?= htmlspecialchars($baiviet['anh_dai_dien'] ?? '') ?>">
        </aside>
    </form>
</div>