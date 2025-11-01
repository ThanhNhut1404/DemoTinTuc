<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm bài viết</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"], textarea, select {
            width: 100%; padding: 8px; margin-top: 4px;
        }
        button {
            margin-top: 15px; padding: 8px 15px; background: #007bff; color: white;
            border: none; border-radius: 5px; cursor: pointer;
        }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>

<h2>Thêm mới bài viết</h2>

<form method="POST" action="index.php?action=store" enctype="multipart/form-data">
    <label>Tiêu đề</label>
    <input type="text" name="tieu_de" required>

    <label>Mô tả ngắn</label>
    <textarea name="mo_ta" rows="3"></textarea>

    <label>Nội dung</label>
    <textarea name="noi_dung" rows="6"></textarea>

    <label>Ảnh đại diện</label>
    <input type="file" name="anh_dai_dien">

    <label>Chuyên mục (ID)</label>
    <input type="text" name="id_chuyen_muc">

    <label>Thẻ tag</label>
    <input type="text" name="tag">

    <label><input type="checkbox" name="la_noi_bat"> Tin nổi bật</label>

    <label>Trạng thái</label>
    <select name="trang_thai">
        <option value="nhap">Nháp</option>
        <option value="cho_duyet">Chờ duyệt</option>
        <option value="da_dang">Đã đăng</option>
    </select>

    <label>Ngày đăng</label>
    <input type="datetime-local" name="ngay_dang">

    <button type="submit">Lưu bài viết</button>
</form>

</body>
</html>
