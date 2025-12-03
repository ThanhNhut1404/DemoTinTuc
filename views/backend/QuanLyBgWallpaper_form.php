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
            /* slightly wider content area with smaller side gaps */
        box-sizing: border-box;
            max-width: 1280px; /* increase maximum width for larger screens */
            width: calc(100% - 2px); /* leave ~16px gap on each side on small viewports */
        margin: 0 auto;
    }

    .form-container h2 {
        margin-top: 0;
    }
    /* When the form heading uses .member-title, match the list heading style */
    .form-container h2.member-title {
        font-size: 24px;
        color: #007bff;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    /* two-column layout for form: left = fields, right = image preview */
    .form-grid {
        display: flex;
        gap: 12px; /* increased gap so right column sits a bit further away */
        align-items: flex-start;
    }

    .form-grid .left {
        flex: 1 1 auto;
        min-width: 0; /* allow shrinking */
        max-width: 520px; /* constrain left column so right column sits closer */
    }

    .form-grid .right {
        width: 360px; /* overall right column width */
        flex: 0 0 360px;
        box-sizing: border-box;
        margin-left: 0px; /* no offset */
    }

    @media (max-width: 900px) {
        .form-grid {
            flex-direction: column;
        }
        .form-grid .right {
            width: 100%;
            flex-direction: column;
        }
        .preview-container {
            width: 100%;
        }
        .current-info {
            flex: 0 0 auto;
            max-width: 100%;
            margin-left: 0;
        }
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #374151;
        font-size: 0.95rem;
    }

    /* Make the right-column image label match the left-column labels */
    .form-grid .right > label {
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
        min-height: 160px; /* increased slightly per user request */
        width: 100%; /* match the text input width in the left column */
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
        margin-bottom: 18px;
        border: 2px dashed #d1d5db;
        border-radius: 10px;
        padding: 16px; /* increased padding for a larger feel */
        background: white;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        flex-wrap: nowrap; /* keep preview and current-info on one line */
    }

    .preview-img {
        max-width: 100%;
        max-height: 520px; /* increased so selected preview image displays larger */
        width: 100%;
        height: auto;
        object-fit: contain;
        border-radius: 8px;
        display: none;
    }

    .preview-img.show {
        display: block;
    }

    .preview-placeholder {
    color: #9ca3af;
    font-size: 0.95rem;
    width: 100%;     /* fit the preview-main area */
    height: 209px;    /* lowered so the preview area is less tall */
    box-sizing: border-box;
    min-width: 0;
}

    .preview-img.show {
        display: block;
        width: 100%;
        height: auto;
        object-fit: contain;
    }

    /* layout inside preview container */
    .preview-main {
        flex: 1 1 auto;
        min-width: 0;
        max-width: calc(100% - 140px); /* leave room for .current-info on the right */
    }

    .current-info {
        flex: 0 0 120px;
        max-width: 140px;
        text-align: left;
        color: #6b7280;
        font-size: 0.9rem;
    }

    .current-info .current-thumb {
        width: 100%;
        height: auto;
        max-height: 110px;
        object-fit: cover;
        border-radius: 6px;
        display: block;
        margin-top: 8px;
        margin-bottom: 6px;
    }

    .current-info .current-filename {
        word-break: break-all;
        font-size: 0.85rem;
        color: #374151;
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
        /* Match the search button color/appearance */
        background: #22c55e; /* same as .btn.btn-search */
        color: #fff;
        border: none;
        flex: 1;
    }

    .form-actions .btn-submit:hover {
        /* Give submit button a darker-green hover like the cancel button has a darker-red hover */
        transform: none;
        box-shadow: none;
        background: #16a34a; /* darker green hover (matches .btn.btn-search hover) */
        border-color: #16a34a;
    }

    .form-actions .btn-cancel {
        /* Match delete button styles from global admin.css */
        background-color: #ef4444;
        border-color: #ef4444;
        color: #fff;
        flex: 0;
    }

    .form-actions .btn-cancel:hover {
        background-color: #dc2626;
        border-color: #dc2626;
    }

    .file-input-label {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-top: 18px !important; /* keep alignment tweak until confirmed */
        background-color: #007bff; /* match .member-title text color */
        color: #fff !important; /* white text, force in case other rules override */
        padding: 10px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: background-color 0.15s ease, transform 0.12s ease;
        border: none;
        text-align: center;
    }

    .file-input-label:hover {
        background-color: #0b5ed7; /* hover blue similar to heading */
        transform: none;
        box-shadow: none;
    }

    .file-input-label input {
        display: none;
    }

    .file-input-label .file-icon {
        display: inline-flex;
        width: 18px;
        height: 18px;
        margin-right: 8px;
        align-items: center;
        justify-content: center;
        flex: 0 0 18px;
        vertical-align: middle;
    }

    .file-input-label .file-icon svg {
        width: 16px;
        height: 16px;
        fill: currentColor; /* inherit white */
        display: block;
        vertical-align: middle;
    }

    /* Ensure the text sibling is vertically centered with the icon */
    .file-input-label > span:not(.file-icon) {
        display: inline-flex;
        align-items: center;
        vertical-align: middle;
    }

    .file-input-label {
        line-height: 1; /* prevent text baseline shifting */
    }

    /* Ensure any child nodes inherit white color */
    .file-input-label, .file-input-label * {
        color: #fff !important;
    }

    .file-name {
        display: inline-block;
        margin-left: 10px;
        color: #6b7280;
        font-size: 0.95rem;
    }

    /* Temporary visual aid to confirm this CSS file is loaded — remove after verification */
    .preview-container, .file-input-label {
        outline: 2px dashed rgba(255,99,71,0.06);
    }
</style>

<div class="form-container">
    <h2 class="member-title"><?= $isEdit ? 'Sửa Background Website' : 'Thêm Background' ?></h2>

    <form method="POST" action="admin.php?action=<?= $isEdit ? 'bg_wallpaper_update' : 'bg_wallpaper_store' ?>" enctype="multipart/form-data">
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= $formData['id'] ?>">
        <?php endif; ?>

        <div class="form-grid">
            <div class="left">
                <div class="form-group">
                    <label for="ten_wallpaper">Tên Background</label>
                    <input type="text" id="ten_wallpaper" name="ten_wallpaper" required placeholder="Ví dụ: background mùa hè" value="<?= htmlspecialchars($formData['ten_wallpaper'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="mo_ta">Mô Tả</label>
                    <textarea id="mo_ta" name="mo_ta" placeholder="Mô tả ngắn về background này"><?= htmlspecialchars($formData['mo_ta'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="trang_thai" name="trang_thai" value="on" <?= ($formData['trang_thai'] ?? '') === 'on' ? 'checked' : '' ?>>
                        <label for="trang_thai" style="margin:0;">Kích hoạt Background này ngay</label>
                    </div>
                </div>
            </div>

            <div class="right">
                <label for="imageInput">Hình ảnh Background</label>
                <div class="preview-container">
                    <div class="preview-main">
                        <img id="previewImg" class="preview-img" alt="Xem trước">
                        <div class="preview-placeholder" id="placeholder">Chọn hoặc kéo thả hình ảnh vào đây</div>
                    </div>
                </div>

                    <!-- current image removed per user request -->

                <label for="imageInput" class="file-input-label">
                    <input type="file" id="imageInput" name="anh_nen" accept="image/*">
                    <span class="file-icon" aria-hidden="true">
                        <!-- folder SVG similar to sidebar icons; uses currentColor to match text -->
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                            <path d="M10 4H4a2 2 0 0 0-2 2v2h20V8a2 2 0 0 0-2-2h-8l-2-2zM2 10v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6H2z" />
                        </svg>
                    </span>
                    <span>Chọn Hình ảnh</span>
                </label>
                <span class="file-name" id="fileName"></span>
                
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-search btn-submit"><?= $isEdit ? 'Cập Nhật' : 'Thêm Background' ?></button>
            <button type="button" class="btn btn-delete btn-cancel" onclick="history.back()">Hủy</button>
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
