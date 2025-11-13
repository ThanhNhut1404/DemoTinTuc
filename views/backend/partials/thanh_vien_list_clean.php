<?php
// Clean member list partial. If $dsThanhVien is not provided by caller, fetch it here.
use Website\TinTuc\Models\ThanhVienModel;

if (!isset($dsThanhVien)) {
    $model = new ThanhVienModel();
    $action = $_GET['action'] ?? 'index';
    if (($action === 'search' && isset($_GET['keyword'])) || !empty($_GET['keyword'])) {
        $keyword = trim($_GET['keyword'] ?? '');
        $role = $_GET['role'] ?? null;
        $status = $_GET['status'] ?? null;
        $gender = $_GET['gender'] ?? $_GET['gioi_tinh'] ?? null;
        if ($keyword !== '') {
            $dsThanhVien = $model->search($keyword, $role, $status, $gender);
        } else {
            $dsThanhVien = $model->getAll($role, $status, $gender);
        }
    } else {
        $role = $_GET['role'] ?? null;
        $status = $_GET['status'] ?? null;
        $gender = $_GET['gender'] ?? $_GET['gioi_tinh'] ?? null;
        $dsThanhVien = $model->getAll($role, $status, $gender);
    }
}

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

<div>
        <form id="searchFormInner" method="get" action="admin.php" style="margin-bottom:12px;">
            <input type="hidden" name="action" value="search">
            <input type="text" name="keyword" placeholder="Tìm theo tên hoặc email" value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
            <button type="button" id="filterToggleInner" class="filter-toggle" aria-expanded="false">Bộ lọc</button>
            <button type="submit">Tìm kiếm</button>
            <div id="filterPanelInner" class="filter-panel" role="region" aria-hidden="true">
                <button type="button" id="filterCloseInner" class="filter-close" aria-label="Đóng">✕</button>
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
                    <button type="submit">Áp dụng</button>
                    <a href="admin.php?action=thanh_vien_roles" style="margin-left:8px;">Xóa bộ lọc</a>
                </div>
            </div>
        </form>
        <table class="member-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <?php $showGender = isset($dsThanhVien[0]['gioi_tinh']); ?>
                    <?php $showDob = isset($dsThanhVien[0]['ngay_sinh']); ?>
                    <th>Họ &amp; tên</th>
                    <?php if ($showGender): ?>
                        <th>Giới tính</th>
                    <?php endif; ?>
                    <?php if ($showDob): ?>
                        <th>Ngày sinh</th>
                    <?php endif; ?>
                    <th>Email</th>
                    <th>Quyền</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($dsThanhVien as $tv): ?>
            <tr>
                <td><?= htmlspecialchars($tv['id']) ?></td>
                <td><?= htmlspecialchars($tv['ho_ten']) ?></td>
                <?php if ($showGender): ?>
                    <td><?= htmlspecialchars($tv['gioi_tinh'] ?? '') ?></td>
                <?php endif; ?>
                <?php if ($showDob): ?>
                    <td><?php
                        $d = $tv['ngay_sinh'] ?? null;
                        if (!empty($d) && $d !== '0000-00-00') echo htmlspecialchars(date('d/m/Y', strtotime($d)));
                        else echo '';
                    ?></td>
                <?php endif; ?>
                <td><?= htmlspecialchars($tv['email']) ?></td>
                <td>
                    <form method="post" action="admin.php?action=updateRole">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($tv['id']) ?>">
                        <input type="hidden" name="role" value="<?= htmlspecialchars($_GET['role'] ?? '') ?>">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($_GET['status'] ?? '') ?>">
                        <input type="hidden" name="gender" value="<?= htmlspecialchars($_GET['gender'] ?? $_GET['gioi_tinh'] ?? '') ?>">
                        <select name="quyen">
                            <option value="User" <?= ($tv['quyen'] ?? '') == 'User' ? 'selected' : '' ?>>User</option>
                            <option value="Editor" <?= ($tv['quyen'] ?? '') == 'Editor' ? 'selected' : '' ?>>Editor</option>
                            <option value="Admin" <?= ($tv['quyen'] ?? '') == 'Admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                        <button class="btn-role" type="submit">Cập nhật</button>
                    </form>
                </td>
                <td>
                    <?php $statusLabel = displayStatus($tv['trang_thai'] ?? '');
                          $low = mb_strtolower(trim((string)($tv['trang_thai'] ?? '')), 'UTF-8');
                          $isLocked = in_array($low, ['khoa','bi_khoa','locked']);
                    ?>
                    <span class="badge <?= $isLocked ? 'badge--locked' : 'badge--active' ?>"><?= htmlspecialchars($statusLabel) ?></span>
                </td>
                <td>
                    <?php if (!$isLocked): ?>
                        <a class="btn-lock" href="admin.php?action=lock&id=<?= urlencode($tv['id']) ?>&hanhDong=khoa<?= !empty($_GET['role']) ? '&role=' . urlencode($_GET['role']) : '' ?><?= !empty($_GET['status']) ? '&status=' . urlencode($_GET['status']) : '' ?><?= !empty($_GET['gender']) ? '&gender=' . urlencode($_GET['gender']) : ( !empty($_GET['gioi_tinh']) ? '&gender=' . urlencode($_GET['gioi_tinh']) : '' ) ?>">Khóa</a>
                    <?php else: ?>
                        <a class="btn-unlock" href="admin.php?action=unlock&id=<?= urlencode($tv['id']) ?>&hanhDong=mo<?= !empty($_GET['role']) ? '&role=' . urlencode($_GET['role']) : '' ?><?= !empty($_GET['status']) ? '&status=' . urlencode($_GET['status']) : '' ?><?= !empty($_GET['gender']) ? '&gender=' . urlencode($_GET['gender']) : ( !empty($_GET['gioi_tinh']) ? '&gender=' . urlencode($_GET['gioi_tinh']) : '' ) ?>">Mở khóa</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <style>
        /* small badge styles used by the member list */
        .badge{display:inline-block;padding:6px 10px;border-radius:999px;font-size:13px}
        .badge--active{background:#eefbf3;color:#065f46}
        .badge--locked{background:#fff5f5;color:#b91c1c;border:1px solid rgba(239,68,68,0.08)}
        .filter-close{float:right;background:none;border:none;font-size:16px;cursor:pointer}
    </style>

    <script>
        (function(){
            var toggle = document.getElementById('filterToggleInner');
            var panel = document.getElementById('filterPanelInner');
            var closeBtn = document.getElementById('filterCloseInner');
            if (!toggle || !panel) return;
            toggle.addEventListener('click', function(){ panel.classList.toggle('open'); });
            if (closeBtn) closeBtn.addEventListener('click', function(){ panel.classList.remove('open'); });
            document.addEventListener('keydown', function(e){ if (e.key === 'Escape') panel.classList.remove('open'); });
        })();
    </script>