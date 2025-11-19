<?php
// Fragment: Danh sách thành viên (rendered inside admin layout)
// This file assumes layout provides CSS/JS assets.

// Helper để hiển thị trạng thái bằng tiếng Việt dễ đọc
function displayStatus($raw)
{
    $s = mb_strtolower(trim((string)($raw ?? '')),'UTF-8');
    if (in_array($s, ['hoat_dong', 'hoạt_động', 'active', 'hoạt động', 'hoat dong'])) {
        return 'Đang Hoạt Động';
    }
    if (in_array($s, ['khoa', 'bi_khoa', 'locked'])) {
        return 'Đang Khóa';
    }
    if ($s === '') return 'Đang Hoạt Động';
    return $raw;
}

?>

<div class="card">
    <h2 style="margin-top:0">Danh sách thành viên</h2>

    <?php if (!empty($filterWarning)): ?>
        <div style="margin:8px 0; padding:8px; background:#fff3cd; color:#856404; border:1px solid #ffeeba; border-radius:4px;">
            <?= htmlspecialchars($filterWarning) ?>
        </div>
    <?php endif; ?>

    <form id="searchForm" class="form-inline" method="get" action="admin.php" style="margin-bottom:12px;position:relative;">
        <input type="hidden" name="action" value="search">
        <input class="search-input" type="text" name="keyword" placeholder="Tìm theo tên hoặc email" value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
        <button type="button" id="filterToggle" class="btn" aria-expanded="false">Bộ lọc</button>
        <button type="submit" class="btn">Tìm kiếm</button>

        <?php if (!empty($_GET['keyword'])): ?>
            <?php
            $qs = ['action' => 'index'];
            if (!empty($_GET['role'])) $qs['role'] = $_GET['role'];
            if (!empty($_GET['status'])) $qs['status'] = $_GET['status'];
            if (!empty($_GET['gender']) || !empty($_GET['gioi_tinh'])) {
                $qs['gender'] = $_GET['gender'] ?? $_GET['gioi_tinh'];
            }
            $clearUrl = 'admin.php?' . http_build_query($qs);
            ?>
            <a href="<?= htmlspecialchars($clearUrl) ?>" class="btn" style="margin-left:8px;">Bỏ tìm kiếm</a>
        <?php endif; ?>

        <div id="filterPanel" class="filter-panel" role="region" aria-hidden="true">
            <button type="button" id="filterClose" class="filter-close" aria-label="Đóng">✕</button>
            <h4 style="margin:0 0 8px 0;">Bộ lọc</h4>
            <fieldset>
                <legend>Quyền</legend>
                <select name="role">
                    <option value="">-- Tất cả quyền --</option>
                    <option value="User" <?= (($_GET['role'] ?? '') === 'User') ? 'selected' : '' ?>>User</option>
                    <option value="Editor" <?= (($_GET['role'] ?? '') === 'Editor') ? 'selected' : '' ?>>Editor</option>
                    <option value="Admin" <?= (($_GET['role'] ?? '') === 'Admin') ? 'selected' : '' ?>>Admin</option>
                </select>
            </fieldset>
            <fieldset>
                <legend>Giới tính</legend>
                <select name="gender">
                    <option value="">-- Tất cả giới tính --</option>
                    <option value="Nam" <?= (($_GET['gender'] ?? $_GET['gioi_tinh'] ?? '') === 'Nam') ? 'selected' : '' ?>>Nam</option>
                    <option value="Nu" <?= (($_GET['gender'] ?? $_GET['gioi_tinh'] ?? '') === 'Nu') ? 'selected' : '' ?>>Nữ</option>
                    <option value="Male" <?= (($_GET['gender'] ?? $_GET['gioi_tinh'] ?? '') === 'Male') ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= (($_GET['gender'] ?? $_GET['gioi_tinh'] ?? '') === 'Female') ? 'selected' : '' ?>>Female</option>
                </select>
            </fieldset>
            <fieldset>
                <legend>Trạng thái</legend>
                <select name="status">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="Hoat_dong" <?= (($_GET['status'] ?? '') === 'Hoat_dong') ? 'selected' : '' ?>>Đang Hoạt Động</option>
                    <option value="Khoa" <?= (($_GET['status'] ?? '') === 'Khoa') ? 'selected' : '' ?>>Đang Khóa</option>
                </select>
            </fieldset>
            <div style="margin-top:8px;text-align:right;">
                <button type="submit" class="btn">Áp dụng</button>
                <a href="admin.php?action=index" style="margin-left:8px;" class="btn">Xóa bộ lọc</a>
            </div>
        </div>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th style="width:25px">ID</th>
                <?php $showGender = isset($dsThanhVien[0]['gioi_tinh']); ?>
                <?php $showDob = isset($dsThanhVien[0]['ngay_sinh']); ?>
                <th>Họ & tên</th>
                <?php if ($showGender): ?><th class="col-gender">Giới tính</th><?php endif; ?>
                <?php if ($showDob): ?><th>Ngày sinh</th><?php endif; ?>
                <th>Email</th>
                <th class="col-role">Quyền</th>
                <th class="col-status">Trạng thái</th>
                <th class="col-actions">Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($dsThanhVien as $tv): ?>
            <tr>
                <td><?= htmlspecialchars($tv['id']) ?></td>
                <td><?= htmlspecialchars($tv['ho_ten']) ?></td>
                <?php if ($showGender): ?><td class="col-gender"><?= htmlspecialchars($tv['gioi_tinh'] ?? '') ?></td><?php endif; ?>
                <?php if ($showDob): ?>
                    <td><?php $d = $tv['ngay_sinh'] ?? null; if (!empty($d) && $d !== '0000-00-00') echo htmlspecialchars(date('d/m/Y', strtotime($d))); else echo ''; ?></td>
                <?php endif; ?>
                <td><?= htmlspecialchars($tv['email']) ?></td>
                <td class="col-role">
                    <form method="post" action="admin.php?action=updateRole" class="form-inline">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($tv['id']) ?>">
                        <input type="hidden" name="role" value="<?= htmlspecialchars($_GET['role'] ?? '') ?>">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($_GET['status'] ?? '') ?>">
                        <input type="hidden" name="gender" value="<?= htmlspecialchars($_GET['gender'] ?? $_GET['gioi_tinh'] ?? '') ?>">
                        <select name="quyen">
                            <option value="User" <?= ($tv['quyen'] ?? '') == 'User' ? 'selected' : '' ?>>User</option>
                            <option value="Editor" <?= ($tv['quyen'] ?? '') == 'Editor' ? 'selected' : '' ?>>Editor</option>
                            <option value="Admin" <?= ($tv['quyen'] ?? '') == 'Admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                        <button class="btn btn-role" type="submit">Cập nhật</button>
                    </form>
                </td>
                <td class="col-status">
                    <?php $statusLabel = displayStatus($tv['trang_thai'] ?? ''); $low = mb_strtolower(trim((string)($tv['trang_thai'] ?? '')), 'UTF-8'); $isLocked = in_array($low, ['khoa','bi_khoa','locked']); ?>
                    <span class="badge <?= $isLocked ? 'badge--locked' : 'badge--active' ?>"><?= htmlspecialchars($statusLabel) ?></span>
                </td>
                <td class="col-actions">
                    <?php if (!$isLocked): ?>
                        <a class="btn btn-lock" href="admin.php?action=lock&id=<?= urlencode($tv['id']) ?>&hanhDong=khoa<?= !empty($_GET['role']) ? '&role=' . urlencode($_GET['role']) : '' ?><?= !empty($_GET['status']) ? '&status=' . urlencode($_GET['status']) : '' ?><?= !empty($_GET['gender']) ? '&gender=' . urlencode($_GET['gender']) : ( !empty($_GET['gioi_tinh']) ? '&gender=' . urlencode($_GET['gioi_tinh']) : '' ) ?>">Khóa</a>
                    <?php else: ?>
                        <a class="btn btn-unlock" href="admin.php?action=unlock&id=<?= urlencode($tv['id']) ?>&hanhDong=mo<?= !empty($_GET['role']) ? '&role=' . urlencode($_GET['role']) : '' ?><?= !empty($_GET['status']) ? '&status=' . urlencode($_GET['status']) : '' ?><?= !empty($_GET['gender']) ? '&gender=' . urlencode($_GET['gender']) : ( !empty($_GET['gioi_tinh']) ? '&gender=' . urlencode($_GET['gioi_tinh']) : '' ) ?>">Mở khóa</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    (function(){
        var toggle = document.getElementById('filterToggle');
        var panel = document.getElementById('filterPanel');
        var closeBtn = document.getElementById('filterClose');
        if (!toggle || !panel) return;
        toggle.addEventListener('click', function(){ panel.classList.toggle('open'); });
        if (closeBtn) closeBtn.addEventListener('click', function(){ panel.classList.remove('open'); });
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') panel.classList.remove('open'); });
    })();
</script>

