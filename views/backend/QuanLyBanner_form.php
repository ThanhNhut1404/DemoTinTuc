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
        max-width: 1280px; /* increased to match other form containers */
        width: calc(100% - 2px);
        box-sizing: border-box;
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

    /* when heading uses the shared member-title class, match site heading style */
    .backend-form-card h2.member-title {
        font-size: 24px;
        color: #007bff;
        font-weight: 700;
        margin-bottom: 12px;
        padding-bottom: 0;
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
    .form-group--compact { margin-top: -6px; margin-bottom: 14px; }
    
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
    /* Fixed-size preview box so the container never changes size with different images */
    .image-preview-box {
        background: #fff;
        padding: 15px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        text-align: center;
        height: 300px; /* fixed height so the box doesn't resize (increased slightly) */
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden; /* hide any overflow and keep fixed size */
    }

    /* Image should fit inside the fixed box without stretching it */
    .current-img {
        max-width: 100%;
        max-height: 100%; /* fill available area inside the box but never overflow */
        object-fit: contain; /* preserve aspect ratio */
        border-radius: 8px;
        border: 1px solid #f3f4f6;
        display: block;
        margin: 0 auto; /* center inside the flex container */
    }

    /* Placeholder fills the preview box and centers its text */
    #noImagePlaceholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0; /* spacing already handled by container padding */
        background: #f9fafb;
        border-radius: 8px;
        color: #9ca3af;
        font-size: 0.95rem;
    }

    .upload-hint {
        font-size: 0.9rem;
        color: #6b7280;
        margin-top: 10px;
        background: #f9fafb;
        padding: 8px;
        border-radius: 6px;
        text-align: center; /* center the note text */
        box-sizing: border-box;
        flex: 1 1 auto; /* allow hint to take remaining space so buttons can sit on same row */
        margin-right: 12px; /* small gap between hint and buttons */
        min-width: 0; /* allow text to truncate/wrap correctly in flex */
    }

    /* 🔘 Buttons */
    .form-actions {
        margin-top: 30px;
        display: flex;
        gap: 12px;
        align-items: center;
    }

    /* actions placed in the left info column underneath the status field */
    .info-actions { margin-top: 60px; }

    /* preview footer layout: note on the left, actions on the right (same horizontal row) */
    .preview-footer {
        display: flex;
        align-items: center;
        justify-content: center; /* center the hint under the preview area when actions are moved */
        gap: 12px;
        flex-wrap: nowrap; /* keep note and (if any) buttons on one line */
    }

    /* when buttons are placed in the left column, make sure preview hint is centered */
    .preview-actions { display: none; }
    .upload-hint { order: 0; margin-left: 0; }

    .btn-submit {
        background: #22c55e; /* green to match Thêm Background */
        color: white;
        border: none;
        /* Increased horizontal padding and min-width to make the button wider */
        padding: 12px 32px;
        min-width: 400px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.2s, background-color 0.12s ease;
        font-size: 0.95rem;
        line-height: 1;
    }

    .btn-submit:hover { background-color: #16a34a; transform: none; }

    .btn-cancel {
        background-color: #ef4444; /* red like delete/cancel */
        color: #fff;
        border: 1px solid #ef4444;
        padding: 9px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        transition: background 0.12s ease, color 0.12s ease;
    }

    .btn-cancel:hover { background-color: #dc2626; color: #fff; }

    /* Upload button (styled label for hidden file input) */
    .btn-upload {
        background: #0d6efd; /* blue */
        color: #ffffff; /* white text */
        border: none;
        padding: 10px 26px; /* wider horizontal padding */
        min-width: 300px; /* ensure larger visible width */
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.95rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        justify-content: center;
    }

    .btn-upload:hover { background: #0b5ed7; }

    /* 💡 Status Select Styling */
    select.form-control {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px 12px;
        appearance: none; /* Xóa mũi tên mặc định của trình duyệt */
    }
</style>

<div class="card backend-form-card">
    <h2 class="member-title"><?= $isEdit ? 'Sửa Banner' : 'Thêm Banner' ?></h2>

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

            <div class="form-group form-group--compact">
                <label class="form-label">Trạng thái hiển thị</label>
                <select name="trang_thai" class="form-control" style="width: 140px;">
                    <option value="off" <?= (isset($banner['trang_thai']) && $banner['trang_thai'] === 'off') ? 'selected' : '' ?>>🔴 Tắt (Ẩn)</option>
                    <option value="on" <?= (isset($banner['trang_thai']) && $banner['trang_thai'] === 'on') ? 'selected' : '' ?>>🟢 Bật (Hiển thị)</option>
                </select>
                <div style="margin-top:6px;color:#6b7280;font-size:0.85rem">Chọn 'Bật' để banner xuất hiện ngay trên trang chủ.</div>
            </div>

            <!-- action buttons moved here (left column) so they sit under the status field -->

            <div class="form-actions info-actions">
                <button type="submit" class="btn-submit">
                    <?= $isEdit ? 'Lưu Thay Đổi' : 'Tạo Banner' ?>
                </button>

                <!-- hidden file input + labeled button placed inline with actions -->
                <input type="file" name="hinh_banner" accept="image/*" id="imageInput" style="display:none" />
                <label for="imageInput" class="btn-upload">Chọn ảnh</label>

                <a href="admin.php?action=banner" class="btn-cancel">Hủy</a>
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
                    <div id="noImagePlaceholder">Chưa có ảnh</div>
                <?php endif; ?>
            </div>

            <!-- preview footer: note + actions placed on one horizontal row -->
            <div class="preview-footer">
                <div class="upload-hint">
                    <b>💡:</b> Kích thước chuẩn 1200x400px.<br>
                    Nếu đang sửa, bỏ trống để giữ nguyên ảnh.
                </div>

                <div class="form-actions preview-actions">
                    <button type="submit" class="btn-submit">
                        <?= $isEdit ? 'Lưu Thay Đổi' : 'Tạo Banner' ?>
                    </button>

                    <a href="admin.php?action=banner" class="btn-cancel">Hủy</a>
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