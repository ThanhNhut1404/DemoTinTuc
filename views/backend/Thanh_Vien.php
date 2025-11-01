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
</style>
</head>
<body>

<h2>Quản lý thành viên</h2>

<form method="get" action="admin.php">
    <input type="hidden" name="action" value="search">
    <input type="text" name="keyword" placeholder="Tìm theo tên hoặc email" value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
    <select name="role">
        <option value="">-- Tất cả quyền --</option>
        <option value="User" <?= (($_GET['role'] ?? '') === 'User') ? 'selected' : '' ?>>User</option>
        <option value="Editor" <?= (($_GET['role'] ?? '') === 'Editor') ? 'selected' : '' ?>>Editor</option>
        <option value="Admin" <?= (($_GET['role'] ?? '') === 'Admin') ? 'selected' : '' ?>>Admin</option>
    </select>
    <button type="submit">Tìm kiếm</button>
    <?php if (!empty($_GET['role'])): ?>
        <a href="admin.php?action=index">Bỏ lọc</a>
    <?php endif; ?>
</form>

<table>
    <tr>
        <th>ID</th>
        <th>Họ tên</th>
        <th>Email</th>
        <th>Quyền</th>
        <th>Trạng thái</th>
        <th>Thao tác</th>
    </tr>

    <?php foreach ($dsThanhVien as $tv): ?>
    <tr>
        <td><?= $tv['id'] ?></td>
        <td><?= htmlspecialchars($tv['ho_ten']) ?></td>
        <td><?= htmlspecialchars($tv['email']) ?></td>
        <td>
            <form method="post" action="admin.php?action=updateRole">
                <input type="hidden" name="id" value="<?= $tv['id'] ?>">
                <input type="hidden" name="role" value="<?= htmlspecialchars($_GET['role'] ?? '') ?>">
                <select name="quyen">
                    <option value="User" <?= ($tv['quyen'] ?? '') == 'User' ? 'selected' : '' ?>>User</option>
                    <option value="Editor" <?= ($tv['quyen'] ?? '') == 'Editor' ? 'selected' : '' ?>>Editor</option>
                    <option value="Admin" <?= ($tv['quyen'] ?? '') == 'Admin' ? 'selected' : '' ?>>Admin</option>
                </select>
                <button class="btn-role" type="submit">Cập nhật</button>
            </form>
        </td>
        <td><?= $tv['trang_thai'] ?></td>
        <td>
                <?php if ((strtolower($tv['trang_thai'] ?? '') === 'hoat_dong') || strtolower($tv['trang_thai'] ?? '') === 'hoạt_động'): ?>
                <a class="btn-lock" href="admin.php?action=lock&id=<?= $tv['id'] ?>&hanhDong=khoa<?= !empty($_GET['role']) ? '&role=' . urlencode($_GET['role']) : '' ?>">Khóa</a>
            <?php else: ?>
                <a class="btn-unlock" href="admin.php?action=unlock&id=<?= $tv['id'] ?>&hanhDong=mo<?= !empty($_GET['role']) ? '&role=' . urlencode($_GET['role']) : '' ?>">Mở khóa</a>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
