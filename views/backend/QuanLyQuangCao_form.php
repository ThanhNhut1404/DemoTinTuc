<?php
// Fragment: form thêm/sửa quảng cáo
// Controller đặt $_GET['sub'] = 'create' hoặc 'edit'
// Nếu edit, có $quangcao (record)
$isEdit = (isset($_GET['sub']) && $_GET['sub'] === 'edit');
$qc = $isEdit ? ($quangcao ?? null) : null;
?>

<style>
    /* 🎨 Backend Form Styling - Đồng bộ style với Banner */
    .backend-form-card-qc {
        padding: 24px;
        background: #f7f9fc;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        max-width: 1280px; /* increased to give more horizontal room */
        width: calc(100% - 2px);
        margin: 0 auto;
    }

    .backend-form-card-qc h2 {
        margin-top: 0;
        font-size: 1.5rem;
        margin-bottom: 24px;
        color: #1f2937;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 15px;
    }

    /* 📐 Grid Layout */
    .form-layout-qc {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 24px;
    }

    @media (max-width: 850px) {
        .form-layout-qc { grid-template-columns: 1fr; }
        .preview-area-qc { order: -1; }
    }

    /* 📝 Form Controls */
    .form-group-qc { margin-bottom: 20px; }
    
    .form-label-qc {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #374151;
        font-size: 0.95rem;
    }

    .form-control-qc {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.2s;
        box-sizing: border-box;
    }

    .form-control-qc:focus {
        border-color: #0d6efd;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    }

    /* 🖼️ Image Preview Section */
    .image-preview-box-qc {
        background: #fff;
        padding: 15px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        text-align: center;
    }

    .current-img-qc {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        border: 1px solid #f3f4f6;
        display: block;
        margin-bottom: 12px;
    }

    .upload-hint-qc {
        font-size: 0.8rem;
        color: #6b7280;
        margin-top: 8px;
        background: #f9fafb;
        padding: 8px;
        border-radius: 6px;
    }

    /* 🔘 Buttons */
    .form-actions-qc {
        margin-top: 30px;
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .btn-submit-qc {
        background: linear-gradient(90deg, #0d6efd, #0b5ed7);
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.1s, opacity 0.2s;
        font-size: 0.95rem;
    }
    
    .btn-submit-qc:hover { opacity: 0.9; transform: translateY(-1px); }

    .btn-cancel-qc {
        background: #fff;
        color: #4b5563;
        border: 1px solid #d1d5db;
        padding: 9px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.95rem;
        transition: background 0.2s;
        display: inline-block;
    }

    .btn-cancel-qc:hover { background: #f3f4f6; color: #1f2937; }

    /* 💡 Status Select Styling */
    select.form-control-qc {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px 12px;
        appearance: none;
    }
</style>

<div class="backend-form-card-qc">
    <h2><?= $isEdit ? 'Sửa Quảng Cáo' : 'Thêm Quảng Cáo' ?></h2>

    <form method="post" action="admin.php?action=<?= $isEdit ? 'qc_update' : 'qc_store' ?><?= $isEdit ? '&id=' . intval($qc['id']) : '' ?>" enctype="multipart/form-data" class="form-layout-qc">
        
        <div class="info-area-qc">
            <div class="form-group-qc">
                <label class="form-label-qc">Tiêu đề</label>
                <input type="text" class="form-control-qc" name="tieu_de" value="<?= htmlspecialchars($qc['tieu_de'] ?? '') ?>" placeholder="Nhập tiêu đề quảng cáo..." required />
            </div>

            <div class="form-group-qc">
                <label class="form-label-qc">Liên kết (URL)</label>
                <input type="text" class="form-control-qc" name="lien_ket" value="<?= htmlspecialchars($qc['lien_ket'] ?? '') ?>" placeholder="https://..." />
            </div>

            <div class="form-group-qc">
                <label class="form-label-qc">Vị trí</label>
                <select name="vi_tri" class="form-control-qc">
                    <option value="Trang_chu" <?= (!isset($qc['vi_tri']) || $qc['vi_tri'] === 'Trang_chu') ? 'selected' : '' ?>> Trang chủ</option>
                    <option value="Sidebar" <?= (isset($qc['vi_tri']) && $qc['vi_tri'] === 'Sidebar') ? 'selected' : '' ?>> Chuyên mục</option>
                </select>
            </div>

            <div class="form-group-qc">
                <label class="form-label-qc">Trạng thái</label>
                <select name="trang_thai" class="form-control-qc">
                    <option value="off" <?= (isset($qc['trang_thai']) && $qc['trang_thai'] === 'off') ? 'selected' : '' ?>>🔴 Tắt (Ẩn)</option>
                    <option value="on" <?= (!isset($qc['trang_thai']) || $qc['trang_thai'] === 'on') ? 'selected' : '' ?>>🟢 Bật (Hiển thị)</option>
                </select>
                <div style="margin-top:6px;color:#6b7280;font-size:0.85rem">Chọn 'Bật' để quảng cáo xuất hiện.</div>
            </div>

            <div class="form-actions-qc">
                <button type="submit" class="btn-submit-qc">
                    <?= $isEdit ? 'Lưu Thay Đổi' : 'Tạo Quảng Cáo' ?>
                </button>
                <a href="admin.php?action=quang_cao" class="btn-cancel-qc">Quay lại</a>
            </div>
        </div>

        <aside class="preview-area-qc">
            <label class="form-label-qc">Hình ảnh Quảng Cáo</label>
            <div class="image-preview-box-qc">
                <?php
                    $img = '';
                    if ($isEdit) {
                        $img = $qc['hinh_anh'] ?? '';
                    }
                ?>
                <?php if ($isEdit && !empty($img)): ?>
                    <img src="uploads/<?= htmlspecialchars($img) ?>" class="current-img-qc" alt="Quảng Cáo Preview" id="previewImgQC">
                <?php else: ?>
                    <img id="previewImgQC" class="current-img-qc" style="display:none" alt="Preview ảnh mới">
                    <div id="noImagePlaceholderQC" style="padding: 30px 0; background:#f9fafb; border-radius:8px; margin-bottom:10px; color:#9ca3af;">
                        Chưa có ảnh
                    </div>
                <?php endif; ?>
                
                <input type="file" name="hinh_anh" accept="image/*" class="form-control-qc" style="padding:8px" id="imageInputQC" />
                
                <div class="upload-hint-qc">
                    💡 <b>Mẹo:</b> Kích thước chuẩn 800x600px.<br>
                    Nếu đang sửa, bỏ trống để giữ nguyên ảnh cũ.
                </div>
            </div>
        </aside>

    </form>
</div>

<script>
    document.getElementById('imageInputQC').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const previewImg = document.getElementById('previewImgQC');
                const noImagePlaceholder = document.getElementById('noImagePlaceholderQC');
                
                previewImg.src = event.target.result;
                previewImg.style.display = 'block';
                
                if (noImagePlaceholder) {
                    noImagePlaceholder.style.display = 'none';
                }
            };
            reader.readAsDataURL(file);
        }
    });
</script>
