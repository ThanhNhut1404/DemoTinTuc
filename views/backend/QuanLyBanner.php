<?php
// Fragment: danh sách banner
// Controller sẽ cung cấp $banners

$sub = $_GET['sub'] ?? '';
if ($sub === 'create' || $sub === 'edit') {
    include __DIR__ . '/QuanLyBanner_form.php';
    return;
}
?>
<style>
    /* Minimal banner-specific styles — keep table and card styling consistent with Thanh_Vien */
    /* Larger thumbnail for better visibility */
    .banner-thumb { width:160px; height:90px; object-fit:cover; border-radius:6px; border:1px solid #eee; display:block }
    .link-shorten { display:inline-block; max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:#0d6efd }
    /* Center header titles for the banner table */
    .card .table thead th { text-align: center; }
    /* Make the date column shrink-to-fit its content */
    .card .table th.col-date, .card .table td.col-date { white-space: nowrap; width: 1%; }
    /* Narrow the status column and keep its content centered */
    .card .table th.col-status, .card .table td.col-status { width: 90px; white-space: nowrap; text-align: center; }
    /* Ensure the small 'Thêm Banner' button looks like the form's green submit button
       (the form's CSS isn't loaded on this page, so provide a minimal local copy) */
    .btn-submit {
        background: #22c55e;
        color: #ffffff;
        border: none;
        padding: 8px 14px;
        min-width: 0;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: opacity 0.2s, background-color 0.12s ease;
        box-sizing: border-box;
    }
    .btn-submit:hover { background-color: #16a34a; }
</style>

<div class="card">
    <h2 class="member-title">Danh sách Banner</h2>
    
    <?php if (isset($_GET['updated'])): ?>
        <div style="padding:10px;background:#e6ffee;border:1px solid #90ee90;margin-bottom:15px;border-radius:8px; color:#0a7a2a;">Cập nhật thành công.</div>
    <?php endif; ?>
    
    <p>
        <a href="admin.php?action=banner_create" class="btn-submit" style="display:inline-block;text-decoration:none;padding:8px 14px;min-width:0;vertical-align:middle;">Thêm Banner</a>
    </p>

    <?php if (empty($banners)): ?>
        <div style="text-align:center; padding:40px; background:#fff; border-radius:12px; color:#6b7280;">Chưa có dữ liệu banner.</div>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 15px">ID</th>
                    <th style="width:180px">Hình ảnh</th>
                    <th>Mô tả / Link</th>
                    <th class="col-date">Ngày tạo</th>
                    <th class="col-status">Trạng thái</th>
                    <th class="col-actions" style="width:110px">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($banners as $b): ?>
                    <?php $img = $b['hinh_banner'] ?? $b['hinh_anh'] ?? $b['hinh'] ?? ''; ?>
                    <tr>
                        <td><?= htmlspecialchars($b['id']) ?></td>
                        <td>
                            <?php
                                // Robust normalization for stored image values. Handles:
                                // - raw filename (e.g. banner1.jpg)
                                // - 'uploads/...' paths
                                // - absolute filesystem paths containing '/public/' or backslashes
                                // - full URLs (http/https)
                                // Fallback: try basename lookup in public/uploads/ if initial candidate missing.
                                $img = is_null($img) ? '' : trim((string)$img);
                                $imgSrc = '';
                                if ($img !== '') {
                                    $norm = str_replace('\\', '/', $img);
                                    $lower = strtolower($norm);

                                    // If it's already a full URL, use directly
                                    if (strpos($lower, 'http://') === 0 || strpos($lower, 'https://') === 0) {
                                        $imgSrc = $norm;
                                    } else {
                                        // If the stored value contains an 'uploads/' segment, use that part
                                        $pos = stripos($norm, 'uploads/');
                                        if ($pos !== false) {
                                            $imgSrc = substr($norm, $pos);
                                        } else {
                                            // If it's an absolute path that contains '/public/', take what's after public/
                                            $pos2 = stripos($norm, '/public/');
                                            if ($pos2 !== false) {
                                                $after = substr($norm, $pos2 + strlen('/public/'));
                                                if (stripos($after, 'uploads/') === 0) {
                                                    $imgSrc = $after;
                                                } else {
                                                    $imgSrc = 'uploads/' . ltrim($after, '/');
                                                }
                                            } else {
                                                // If it contains directory separators, fall back to basename
                                                if (strpos($norm, '/') !== false) {
                                                    $imgSrc = 'uploads/' . ltrim(basename($norm), '/');
                                                } else {
                                                    // Plain filename
                                                    $imgSrc = 'uploads/' . ltrim($norm, '/');
                                                }
                                            }
                                        }
                                    }

                                    // If the candidate does not exist on disk, try basename lookup in uploads/
                                    $publicPath = __DIR__ . '/../../public/';
                                    $candidateFs = $publicPath . $imgSrc;
                                    if (!is_file($candidateFs)) {
                                        // Remove query string if present
                                        $imgSrcNoQuery = explode('?', $imgSrc)[0];
                                        if ($imgSrcNoQuery !== $imgSrc && is_file($publicPath . $imgSrcNoQuery)) {
                                            $imgSrc = $imgSrcNoQuery;
                                        } else {
                                            // try by basename
                                            $base = basename($norm);
                                            if ($base !== '' && is_file($publicPath . 'uploads/' . $base)) {
                                                $imgSrc = 'uploads/' . $base;
                                            }
                                        }
                                    }
                                }
                            ?>
                            <?php
                                // Simple robust strategy:
                                // 1) If stored value is a full URL => use it
                                // 2) If a file exists at public/uploads/<basename(stored)> => use that absolute URL
                                // 3) If a file exists at public/<stored> (for stored 'uploads/..' or other paths) => use that
                                // 4) Otherwise show 'No img'
                                $imgUrl = '';
                                $publicWebBase = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); // e.g. '/DemoTinTuc/public'
                                $publicPath = __DIR__ . '/../../public/';
                                $stored = trim((string)$img);

                                if ($stored !== '') {
                                    $lower = strtolower($stored);
                                    if (strpos($lower, 'http://') === 0 || strpos($lower, 'https://') === 0) {
                                        $imgUrl = $stored;
                                    } else {
                                        // If the file exists in public/uploads, use a relative 'uploads/...' URL
                                        $baseName = basename(str_replace('\\','/',$stored));
                                        $uploadsFs = $publicPath . 'uploads/' . $baseName;
                                        if (is_file($uploadsFs)) {
                                            $imgUrl = 'uploads/' . $baseName;
                                        } else {
                                            // try public/<stored> (handles stored 'uploads/...' or other relative paths)
                                            $candidate = ltrim(str_replace('\\','/',$stored), '/');
                                            if (is_file($publicPath . $candidate)) {
                                                $imgUrl = $candidate;
                                            }
                                        }
                                    }
                                }
                            ?>
                            <?php if (!empty($imgUrl)): ?>
                                <img src="<?= htmlspecialchars($imgUrl) ?>" class="banner-thumb" alt="Banner">
                            <?php else: ?>
                                <span style="font-size:0.85rem;color:#999;font-style:italic">Không có ảnh</span>
                            <?php endif; ?>

                            <?php // DEBUG: show raw stored value, computed src, filesystem path and existence when ?debug_images=1 is present (temporary)
                            if (isset($_GET['debug_images']) && $_GET['debug_images'] == '1'):
                                $fsPath = '';
                                $exists = false;
                                if (!empty($imgSrc)) {
                                    $fsPath = realpath(__DIR__ . '/../../public/' . $imgSrc) ?: (__DIR__ . '/../../public/' . $imgSrc);
                                    $exists = is_file(__DIR__ . '/../../public/' . $imgSrc);
                                }
                            ?>
                                <div style="margin-top:6px;font-size:0.75rem;color:#444;background:#f8f9fa;padding:6px;border-radius:6px;border:1px dashed #e1e1e1;">
                                    <div><strong>stored:</strong> <code><?= htmlspecialchars($img) ?></code></div>
                                    <div><strong>src:</strong> <code><?= htmlspecialchars($imgSrc) ?></code></div>
                                    <div><strong>imgUrl:</strong> <code><?= htmlspecialchars($imgUrl ?? '') ?></code></div>
                                    <div><strong>fs:</strong> <code><?= htmlspecialchars($fsPath) ?></code> — <strong><?= $exists ? 'FOUND' : 'MISSING' ?></strong></div>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="font-weight:600;margin-bottom:4px"><?= htmlspecialchars($b['mo_ta'] ?? 'Không có mô tả') ?></div>
                            <div class="link-shorten" title="<?= htmlspecialchars($b['lien_ket'] ?? '') ?>"><?= htmlspecialchars($b['lien_ket'] ?? '#') ?></div>
                        </td>
                        <td class="col-date" style="font-size:0.85rem;color:#666">
                            <?php
                                $dt = $b['ngay_tao'] ?? null;
                                if (!empty($dt) && $dt !== '0000-00-00' && strtotime($dt) !== false) {
                                    echo htmlspecialchars(date('H:i:s d/m/Y', strtotime($dt)));
                                } else {
                                    echo '-';
                                }
                            ?>
                        </td>
                        <td class="col-status">
                            <?php $isOn = (isset($b['trang_thai']) && ($b['trang_thai'] === 'on' || $b['trang_thai'] === '1' || $b['trang_thai'] === 1)); ?>
                            <span class="badge <?= $isOn ? 'badge--active' : 'badge--locked' ?>"><?= $isOn ? 'Đang bật' : 'Đang tắt' ?></span>
                        </td>
                        <td class="col-actions" style="text-align:right">
                            <a class="btn btn-edit" href="admin.php?action=banner_edit&id=<?= urlencode($b['id']) ?>">Sửa</a>
                            <a class="btn btn-delete" href="admin.php?action=banner_delete&id=<?= urlencode($b['id']) ?>" onclick="return confirm('Xóa banner này?')">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <?php
    // Temporary debug block to log image paths and their existence status
    if (isset($_GET['debug_images']) && $_GET['debug_images'] == '1') {
        echo "<div style='background:#f9f9f9;padding:10px;border:1px solid #ccc;margin-bottom:10px;'>";
        echo "<strong>Debug Info:</strong><br>";
        echo "Stored value: <code>" . htmlspecialchars($img) . "</code><br>";
        echo "Computed src: <code>" . htmlspecialchars($imgSrc) . "</code><br>";
        echo "Filesystem path: <code>" . htmlspecialchars($fsPath ?? 'N/A') . "</code><br>";
        echo "File exists: <strong>" . (isset($fsPath) && file_exists($fsPath) ? 'Yes' : 'No') . "</strong><br>";
        echo "</div>";
    }
    ?>
</div>