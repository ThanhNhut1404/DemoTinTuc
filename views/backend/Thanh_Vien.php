<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý thành viên</title>
<style>
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
    th { background-color: #f2f2f2; }
    a, button { padding: 6px 10px; text-decoration: none; border: none; border-radius: 4px; }
    .btn-lock { background-color: #ff6666; color: white; }
    .btn-unlock { background-color: #66cc66; color: white; }
    .btn-role { background-color: #4f8ef7; color: white; }
    /* Filter panel (overlay over table, small) */
    #searchForm { position: relative; display: block; }
    .filter-toggle { display: inline-block; margin-left: 8px; cursor: pointer; background:#f5f5f5; border:1px solid #ddd; border-radius:4px; /* padding inherited from button rule */ }
    /* Panel is absolutely positioned inside #searchForm. We'll position it under the toggle button via JS */
    .filter-panel { position: absolute; left: 0; top: 100%; width: 340px; max-width: calc(100% - 40px); background: #fff; border: 1px solid #ddd; box-shadow: 0 10px 30px rgba(0,0,0,.12); padding: 10px; border-radius: 6px; display: none; z-index: 2000; }
    .filter-panel.open { display: block; }
    .filter-panel fieldset { border: none; margin: 6px 0; }
    .filter-panel legend { font-weight:600; margin-bottom:6px; }
</style>
</head>
<body>

<h2>Quản lý thành viên</h2>

<?php
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
    // fallback: nếu rỗng thì coi là Hoạt động
    if ($s === '') return 'Đang Hoạt Động';
    // otherwise return original
    return $raw;
}
?>

<form id="searchForm" method="get" action="admin.php">
    <input type="hidden" name="action" value="search">
    <input type="text" name="keyword" placeholder="Tìm theo tên hoặc email" value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
    <button type="button" id="filterToggle" class="filter-toggle" aria-expanded="false">Bộ lọc</button>
    <button type="submit">Tìm kiếm</button>

    <div id="filterPanel" class="filter-panel" role="region" aria-hidden="true">
        <button type="button" id="filterClose" style="float:right;background:none;border:none;font-size:16px;cursor:pointer;">✕</button>
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
            <a href="admin.php?action=index" style="margin-left:8px;">Xóa bộ lọc</a>
        </div>
    </div>
    <?php
        // build base clear URL preserving filters other than keyword
        $baseFilters = [];
        if (!empty($_GET['role'])) $baseFilters[] = 'role=' . urlencode($_GET['role']);
        if (!empty($_GET['status'])) $baseFilters[] = 'status=' . urlencode($_GET['status']);
        if (!empty($_GET['gender'])) $baseFilters[] = 'gender=' . urlencode($_GET['gender']);
        if (!empty($_GET['gioi_tinh']) && empty($_GET['gender'])) $baseFilters[] = 'gender=' . urlencode($_GET['gioi_tinh']);
        $baseQuery = count($baseFilters) ? '&' . implode('&', $baseFilters) : '';
    ?>

    <?php if (!empty($_GET['keyword'])): ?>
        <?php $clearUrl = 'admin.php?action=index' . $baseQuery; ?>
        <a href="<?= $clearUrl ?>">Bỏ tìm kiếm</a>
    <?php endif; ?>
    <?php if (empty($_GET['keyword']) && ($baseQuery !== '')): ?>
        <a href="admin.php?action=index">Bỏ lọc</a>
    <?php endif; ?>
</form>

<script>
    // Toggle filter drawer
    (function(){
        var toggle = document.getElementById('filterToggle');
        var panel = document.getElementById('filterPanel');
        var closeBtn = document.getElementById('filterClose');
        if (!toggle || !panel) return;
        function positionPanelUnderToggle(){
            // Position the panel directly under the toggle button, relative to #searchForm (which is positioned)
            var rect = toggle.getBoundingClientRect();
            var parentRect = toggle.offsetParent && toggle.offsetParent.getBoundingClientRect ? toggle.offsetParent.getBoundingClientRect() : { left:0, top:0 };
            var left = toggle.offsetLeft; // offset relative to positioned parent (#searchForm)
            var top = toggle.offsetTop + toggle.offsetHeight + 6; // small gap
            // ensure panel doesn't overflow the form width
            panel.style.left = left + 'px';
            panel.style.top = top + 'px';
            // if panel would overflow right edge of parent, clamp it
            var parentWidth = toggle.offsetParent ? toggle.offsetParent.clientWidth : window.innerWidth;
            var panelWidth = Math.min(340, parentWidth - 20);
            panel.style.width = panelWidth + 'px';
            var overflow = left + panelWidth - parentWidth;
            if (overflow > 0) {
                panel.style.left = Math.max(8, left - overflow) + 'px';
            }
        }

        function open(){ positionPanelUnderToggle(); panel.classList.add('open'); panel.setAttribute('aria-hidden','false'); toggle.setAttribute('aria-expanded','true'); }
        function close(){ panel.classList.remove('open'); panel.setAttribute('aria-hidden','true'); toggle.setAttribute('aria-expanded','false'); }
        toggle.addEventListener('click', function(){ if (panel.classList.contains('open')) close(); else open(); });
        // reposition on resize so it stays under the button
        window.addEventListener('resize', function(){ if (panel.classList.contains('open')) positionPanelUnderToggle(); });
        if (closeBtn) closeBtn.addEventListener('click', close);
        // close on ESC
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') close(); });
    })();
</script>

<table>
    <tr>
        <th>ID</th>
        <?php $showGender = isset($dsThanhVien[0]['gioi_tinh']); ?>
        <?php $showDob = isset($dsThanhVien[0]['ngay_sinh']); ?>
        <th>Họ & tên</th>
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

    <?php foreach ($dsThanhVien as $tv): ?>
    <tr>
        <td><?= $tv['id'] ?></td>
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
                <input type="hidden" name="id" value="<?= $tv['id'] ?>">
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
    <td><?= htmlspecialchars(displayStatus($tv['trang_thai'])) ?></td>
        <td>
                <?php
                    $raw = mb_strtolower(trim((string)($tv['trang_thai'] ?? '')),'UTF-8');
                    $isActive = in_array($raw, ['hoat_dong', 'hoạt_động', 'active', 'hoạt động', 'hoat dong', '']);
                ?>
                <?php if ($isActive): ?>
                <a class="btn-lock" href="admin.php?action=lock&id=<?= $tv['id'] ?>&hanhDong=khoa<?= !empty($_GET['role']) ? '&role=' . urlencode($_GET['role']) : '' ?><?= !empty($_GET['status']) ? '&status=' . urlencode($_GET['status']) : '' ?><?= !empty($_GET['gender']) ? '&gender=' . urlencode($_GET['gender']) : ( !empty($_GET['gioi_tinh']) ? '&gender=' . urlencode($_GET['gioi_tinh']) : '' ) ?>">Khóa</a>
            <?php else: ?>
                <a class="btn-unlock" href="admin.php?action=unlock&id=<?= $tv['id'] ?>&hanhDong=mo<?= !empty($_GET['role']) ? '&role=' . urlencode($_GET['role']) : '' ?><?= !empty($_GET['status']) ? '&status=' . urlencode($_GET['status']) : '' ?><?= !empty($_GET['gender']) ? '&gender=' . urlencode($_GET['gender']) : ( !empty($_GET['gioi_tinh']) ? '&gender=' . urlencode($_GET['gioi_tinh']) : '' ) ?>">Mở khóa</a>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
