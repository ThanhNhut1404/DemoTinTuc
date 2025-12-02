<?php
// Fragment: form thêm/sửa banner
$isEdit = (isset($_GET['sub']) && $_GET['sub'] === 'edit');
$banner = $isEdit ? ($banner ?? null) : null;
?>
<style>
    /* 🎨 Backend Form Styling - Đồng bộ style */
    .backend-form-card {
        padding: 24px;
        background: #f7f9fc;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        max-width: 1000px;
        margin: 0 auto;
    }

    .backend-form-card h2 {
        margin-top: 0;
        font-size: 1.5rem;
        margin-bottom: 24px;
        color: #1f2937;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 15px;
    }

    /* 📐 Grid Layout */
    .form-layout {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 24px;
    }

    @media (max-width: 850px) {
        .form-layout { grid-template-columns: 1fr; } /* Mobile: xếp chồng */
        .preview-area { order: -1; } /* Đẩy ảnh lên đầu ở mobile cho dễ nhìn */
    }

    /* 📝 Form Controls */
    .form-group { margin-bottom: 20px; }
    
    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #374151;
        font-size: 0.95rem;
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.2s;
        box-sizing: border-box; /* Quan trọng để không bị vỡ layout */
    }

    .form-control:focus {
        border-color: #0d6efd;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    }

    /* 🖼️ Image Preview Section */
    .image-preview-box {
        background: #fff;
        padding: 15px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        text-align: center;
    }

    .current-img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        border: 1px solid #f3f4f6;
        display: block;
        margin-bottom: 12px;
    }

    .upload-hint {
        font-size: 0.8rem;
        color: #6b7280;
        margin-top: 8px;
        background: #f9fafb;
        padding: 8px;
        border-radius: 6px;
    }

    /* 🔘 Buttons */
    .form-actions {
        margin-top: 30px;
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .btn-submit {
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
    
    .btn-submit:hover { opacity: 0.9; transform: translateY(-1px); }

    .btn-cancel {
        background: #fff;
        color: #4b5563;
        border: 1px solid #d1d5db;
        padding: 9px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.95rem;
        transition: background 0.2s;
    }

    .btn-cancel:hover { background: #f3f4f6; color: #1f2937; }

    /* 💡 Status Select Styling */
    select.form-control {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px 12px;
        appearance: none; /* Xóa mũi tên mặc định của trình duyệt */
    }
</style>

<div class="backend-form-card">
    <h2><?= $isEdit ? '✏️ Sửa Banner' : '✨ Thêm Banner Mới' ?></h2>

    <form method="post" action="admin.php?action=<?= $isEdit ? 'banner_update' : 'banner_store' ?><?= $isEdit ? '&id=' . intval($banner['id']) : '' ?>" enctype="multipart/form-data" class="form-layout">
        
        <div class="info-area">
            <div class="form-group">
                <label class="form-label">Liên kết (Link)</label>
                <input type="text" class="form-control" name="lien_ket" value="<?= htmlspecialchars($banner['lien_ket'] ?? '') ?>" placeholder="https://..." />
            </div>

            <div class="form-group">
                <label class="form-label">Mô tả ngắn</label>
                <textarea class="form-control" name="mo_ta" rows="4" placeholder="Nhập mô tả cho banner..."><?= htmlspecialchars($banner['mo_ta'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Trạng thái hiển thị</label>
                <select name="trang_thai" class="form-control" style="width: 200px;">
                    <option value="off" <?= (isset($banner['trang_thai']) && $banner['trang_thai'] === 'off') ? 'selected' : '' ?>>🔴 Tắt (Ẩn)</option>
                    <option value="on" <?= (isset($banner['trang_thai']) && $banner['trang_thai'] === 'on') ? 'selected' : '' ?>>🟢 Bật (Hiển thị)</option>
                </select>
                <div style="margin-top:6px;color:#6b7280;font-size:0.85rem">Chọn 'Bật' để banner xuất hiện ngay trên trang chủ.</div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <?= $isEdit ? 'Lưu Thay Đổi' : 'Tạo Banner' ?>
                </button>
                <a href="admin.php?action=banner" class="btn-cancel">Quay lại</a>
            </div>
        </div>

        <aside class="preview-area">
            <label class="form-label">Hình ảnh Banner</label>
            <div class="image-preview-box">
                <?php
                    $img = '';
                    if ($isEdit) {
                        $img = $banner['hinh_banner'] ?? $banner['hinh_anh'] ?? $banner['hinh'] ?? '';
                    }
                ?>
                <?php if ($isEdit && !empty($img)): ?>
                    <img src="uploads/<?= htmlspecialchars($img) ?>" class="current-img" alt="Banner Preview" id="previewImg">
                <?php else: ?>
                    <img id="previewImg" class="current-img" style="display:none" alt="Preview ảnh mới">
                    <div id="noImagePlaceholder" style="padding: 30px 0; background:#f9fafb; border-radius:8px; margin-bottom:10px; color:#9ca3af;">
                        Chưa có ảnh
                    </div>
                <?php endif; ?>
                
                <input type="file" name="hinh_banner" accept="image/*" class="form-control" style="padding:8px" id="imageInput" />
                
                <div class="upload-hint">
                    💡 <b>Mẹo:</b> Kích thước chuẩn 1200x400px.<br>
                    Nếu đang sửa, bỏ trống để giữ nguyên ảnh cũ.
                </div>
            </div>
        </aside>

    </form>
</div>

<script>
    document.getElementById('imageInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const previewImg = document.getElementById('previewImg');
                const noImagePlaceholder = document.getElementById('noImagePlaceholder');
                
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