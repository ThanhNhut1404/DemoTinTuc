<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa bài viết</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"], textarea, select {
            width: 100%; padding: 8px; margin-top: 4px;
        }
        button {
            margin-top: 15px; padding: 8px 15px; background: #28a745; color: white;
            border: none; border-radius: 5px; cursor: pointer;
        }
        button:hover { background: #1e7e34; }
    </style>
</head>
<body>

<h2>Sửa bài viết</h2>

<form method="POST" action="index.php?action=update" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $baiviet['id'] ?>">

    <label>Tiêu đề</label>
    <input type="text" name="tieu_de" value="<?= htmlspecialchars($baiviet['tieu_de']) ?>" required>

    <label>Mô tả ngắn</label>
    <textarea name="mo_ta" rows="3"><?= htmlspecialchars($baiviet['mo_ta']) ?></textarea>

    <label>Nội dung</label>
    <textarea name="noi_dung" rows="6"><?= htmlspecialchars($baiviet['noi_dung']) ?></textarea>

    <label>Ảnh đại diện hiện tại</label><br>
    <?php if (!empty($baiviet['anh_dai_dien'])): ?>
        <img src="uploads/<?= $baiviet['anh_dai_dien'] ?>" width="150"><br>
    <?php endif; ?>
    <input type="file" name="anh_dai_dien">

    <label>Chuyên mục (ID)</label>
    <input type="text" name="id_chuyen_muc" value="<?= htmlspecialchars($baiviet['id_chuyen_muc']) ?>">

    <label>Thẻ tag</label>
    <input type="text" name="tag" value="<?= htmlspecialchars($baiviet['tag']) ?>">

    <label><input type="checkbox" name="la_noi_bat" <?= $baiviet['la_noi_bat'] ? 'checked' : '' ?>> Tin nổi bật</label>

    <label>Trạng thái</label>
    <select name="trang_thai">
        <option value="nhap" <?= $baiviet['trang_thai']=='nhap'?'selected':'' ?>>Nháp</option>
        <option value="cho_duyet" <?= $baiviet['trang_thai']=='cho_duyet'?'selected':'' ?>>Chờ duyệt</option>
        <option value="da_dang" <?= $baiviet['trang_thai']=='da_dang'?'selected':'' ?>>Đã đăng</option>
    </select>

    <label>Ngày đăng</label>
    <input type="datetime-local" name="ngay_dang" value="<?= date('Y-m-d\TH:i', strtotime($baiviet['ngay_dang'])) ?>">

    <button type="submit">Cập nhật</button>
</form>

</body>
</html>
