<?php
// Fragment: form thêm/sửa nền website
$isEdit = isset($_GET['id']);
$formData = [];
if ($isEdit) {
    $bgId = (int)$_GET['id'];
    $model = new \Website\TinTuc\Models\BgWallpaperModel();
    $formData = $model->find($bgId);
    if (!$formData) {
        echo '<div style="color:red; padding:10px;">Nền không tồn tại.</div>';
        return;
    }
}
?>
<style>
    .form-container {
        padding: 24px;
        background: #f7f9fc;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        max-width: 600px;
    }

    .form-container h2 {
        margin-top: 0;
        font-size: 1.6rem;
        color: #1f2937;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 10px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #374151;
        font-size: 0.95rem;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 1rem;
        font-family: inherit;
        box-sizing: border-box;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 80px;
    }

    .form-group .checkbox-wrapper {
        display: flex;
        align-items: center;
    }

    .form-group input[type="checkbox"] {
        width: auto;
        margin-right: 8px;
    }

    .preview-container {
        margin-bottom: 16px;
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        padding: 16px;
        background: white;
        text-align: center;
    }

    .preview-img {
        max-width: 100%;
        max-height: 300px;
        border-radius: 8px;
        display: none;
    }

    .preview-img.show {
        display: block;
    }

    .preview-placeholder {
        color: #9ca3af;
        font-size: 0.95rem;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 24px;
        border-top: 1px solid #e5e7eb;
        padding-top: 16px;
    }

    .form-actions button {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 1rem;
    }

    .form-actions .btn-submit {
        background: linear-gradient(90deg, #0d6efd, #0b5ed7);
        color: white;
        flex: 1;
    }

    .form-actions .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }

    .form-actions .btn-cancel {
        background: #e5e7eb;
        color: #374151;
        flex: 0;
    }

    .form-actions .btn-cancel:hover {
        background: #d1d5db;
    }

    .file-input-label {
        position: relative;
        display: inline-block;
        background: linear-gradient(90deg, #0d6efd, #0b5ed7);
        color: white;
        padding: 10px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s;
    }

    .file-input-label:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }

    .file-input-label input {
        display: none;
    }

    .file-name {
        display: inline-block;
        margin-left: 10px;
        color: #6b7280;
        font-size: 0.95rem;
    }
</style>

<div class="form-container">
    <h2><?= $isEdit ? 'Sửa Nền Website' : 'Thêm Nền Website Mới' ?></h2>

    <form method="POST" action="admin.php?action=<?= $isEdit ? 'bg_wallpaper_update' : 'bg_wallpaper_store' ?>" enctype="multipart/form-data">
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= $formData['id'] ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="ten_wallpaper">Tên Nền</label>
            <input type="text" id="ten_wallpaper" name="ten_wallpaper" required placeholder="Ví dụ: Nền mùa hè" value="<?= htmlspecialchars($formData['ten_wallpaper'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="mo_ta">Mô Tả</label>
            <textarea id="mo_ta" name="mo_ta" placeholder="Mô tả chi tiết về nền này"><?= htmlspecialchars($formData['mo_ta'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="imageInput">Hình Ảnh Nền</label>
            
            <div class="preview-container">
                <img id="previewImg" class="preview-img" alt="Xem trước">
                <div class="preview-placeholder" id="placeholder">Chọn hoặc kéo thả hình ảnh vào đây</div>
            </div>

            <label for="imageInput" class="file-input-label">
                <input type="file" id="imageInput" name="anh_nen" accept="image/*">
                📁 Chọn Hình Ảnh
            </label>
            <span class="file-name" id="fileName"></span>
            
            <?php if ($isEdit && !empty($formData['duong_dan_file'])): ?>
                <div style="margin-top:10px; font-size:0.9rem; color:#6b7280;">
                    <strong>Hình hiện tại:</strong> <?= htmlspecialchars($formData['duong_dan_file']) ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <div class="checkbox-wrapper">
                <input type="checkbox" id="trang_thai" name="trang_thai" value="on" <?= ($formData['trang_thai'] ?? '') === 'on' ? 'checked' : '' ?>>
                <label for="trang_thai" style="margin:0;">Kích hoạt nền này ngay</label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit"><?= $isEdit ? 'Cập Nhật' : 'Thêm Nền' ?></button>
            <button type="button" class="btn-cancel" onclick="history.back()">Hủy</button>
        </div>
    </form>
</div>

<script>
document.getElementById('imageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    document.getElementById('fileName').textContent = '📄 ' + file.name;

    const reader = new FileReader();
    reader.onload = function(event) {
        document.getElementById('previewImg').src = event.target.result;
        document.getElementById('previewImg').classList.add('show');
        document.getElementById('placeholder').style.display = 'none';
    };
    reader.readAsDataURL(file);
});

// Preview hình hiện tại khi edit
<?php if ($isEdit && !empty($formData['duong_dan_file'])): ?>
document.addEventListener('DOMContentLoaded', function() {
    const img = document.getElementById('previewImg');
    img.src = 'uploads/wallpapers/<?= htmlspecialchars($formData['duong_dan_file']) ?>';
    img.classList.add('show');
    document.getElementById('placeholder').style.display = 'none';
});
<?php endif; ?>

// Drag-drop support
const previewContainer = document.querySelector('.preview-container');
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    previewContainer.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

previewContainer.addEventListener('drop', function(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    document.getElementById('imageInput').files = files;
    document.getElementById('imageInput').dispatchEvent(new Event('change'));
}, false);
</script>
