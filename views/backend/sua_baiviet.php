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
                    <label style="display:block;font-weight:600;margin-bottom:6px">Chuyên mục</label>
                    <select name="id_chuyen_muc" style="width:100%;padding:8px;border:1px solid #e6eef8;border-radius:8px">
                        <option value="">-- Chọn chuyên mục --</option>
                        <?php if (!empty($chuyenMucList)): ?>
                            <?php foreach ($chuyenMucList as $cm): ?>
                                <option value="<?= htmlspecialchars($cm['id'] ?? '') ?>" <?= ($baiviet['id_chuyen_muc'] == $cm['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cm['ten_chuyen_muc'] ?? $cm['name'] ?? '') ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div style="flex:1;min-width:160px">
                    <label style="display:block;font-weight:600;margin-bottom:6px">Thẻ tag</label>
                    <select name="tag" style="width:100%;padding:8px;border:1px solid #e6eef8;border-radius:8px">
                        <option value="">-- Chọn thẻ tag --</option>
                        <?php if (!empty($tagList)): ?>
                            <?php foreach ($tagList as $t): ?>
                                <?php
                                    // Support multiple possible field names returned by TagModel/database
                                    $tagId = $t['id'] ?? $t['the_tag'] ?? $t['tag_id'] ?? '';
                                    $tagLabel = $t['ten_tag'] ?? $t['the_tag'] ?? $t['name'] ?? '';
                                    $currentTag = $baiviet['tag'] ?? $baiviet['the_tag'] ?? $baiviet['id_the_tag'] ?? '';
                                ?>
                                <option value="<?= htmlspecialchars($tagId) ?>" <?= ((string)$currentTag === (string)$tagId && $tagId !== '') ? 'selected' : '' ?>><?= htmlspecialchars($tagLabel) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div style="display:flex;gap:12px;align-items:center;margin-top:12px">
                <label style="display:flex;align-items:center;gap:8px"><input type="checkbox" name="la_noi_bat" <?= !empty($baiviet['la_noi_bat']) ? 'checked' : '' ?>> Tin nổi bật</label>
                <label style="display:flex;align-items:center;gap:8px">Trạng thái
                    <?php $currentStatus = trim(strval($baiviet['trang_thai'] ?? '')) ?>
                    <select name="trang_thai" style="margin-left:6px;padding:6px;border:1px solid #e6eef8;border-radius:8px">
                        <option value="Nhap" <?= ($currentStatus === 'Nhap') ? 'selected' : '' ?>>Nháp</option>
                        <option value="Cho_duyet" <?= ($currentStatus === 'Cho_duyet') ? 'selected' : '' ?>>Chờ duyệt</option>
                        <option value="Da_dang" <?= ($currentStatus === 'Da_dang') ? 'selected' : '' ?>>Đã đăng</option>
                        <option value="Tu_choi" <?= ($currentStatus === 'Tu_choi') ? 'selected' : '' ?>>Từ chối</option>
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

<!-- Rich editor (CKEditor 5) with simple upload -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
ClassicEditor
    .create(document.querySelector('textarea[name="noi_dung"]'), {
        simpleUpload: {
            // The URL that the images are uploaded to.
            uploadUrl: 'admin.php?action=upload_image'
        }
    })
    .catch(error => { console.error(error); });
</script>