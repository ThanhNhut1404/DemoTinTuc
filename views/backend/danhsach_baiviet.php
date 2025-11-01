<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách bài viết</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2 { color: #333; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px 10px; text-align: left; }
        th { background: #f2f2f2; }
        a.button {
            display: inline-block; padding: 6px 10px; background: #007bff; color: white;
            border-radius: 5px; text-decoration: none; margin-bottom: 10px;
        }
        a.button:hover { background: #0056b3; }
    </style>
</head>
<body>

<h2>Danh sách bài viết</h2>
<a href="index.php?action=create" class="button">+ Thêm bài viết mới</a>

<table>
    <tr>
        <th>ID</th>
        <th>Tiêu đề</th>
        <th>Chuyên mục</th>
        <th>Trạng thái</th>
        <th>Ngày đăng</th>
        <th>Tin nổi bật</th>
        <th>Hành động</th>
    </tr>

    <?php if (!empty($baiviets)): ?>
        <?php foreach ($baiviets as $b): ?>
        <tr>
            <td><?= $b['id'] ?></td>
            <td><?= htmlspecialchars($b['tieu_de']) ?></td>
            <td><?= htmlspecialchars($b['id_chuyen_muc']) ?></td>
            <td><?= htmlspecialchars($b['trang_thai']) ?></td>
            <td><?= $b['ngay_dang'] ?></td>
            <td><?= $b['la_noi_bat'] ? '✔️' : '' ?></td>
            <td>
                <a href="index.php?action=edit&id=<?= $b['id'] ?>">✏️ Sửa</a> |
                <a href="index.php?action=delete&id=<?= $b['id'] ?>" onclick="return confirm('Xóa bài viết này?')">🗑️ Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="7">Chưa có bài viết nào.</td></tr>
    <?php endif; ?>
</table>

</body>
</html>
