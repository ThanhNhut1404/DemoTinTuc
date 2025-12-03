<?php
// Khởi động session
if (session_status() === PHP_SESSION_NONE) session_start();

// Nếu chưa đăng nhập → đá về trang login
if (!isset($_SESSION['user'])) {
    header("Location: index.php?action=login");
    exit;
}

// Lấy thông tin người dùng đang đăng nhập
$user = $_SESSION['user'];
$_SESSION['user_id'] = $user['id'];

// Avatar
$avatar = !empty($user['anh_dai_dien'])
    ? 'uploads/' . htmlspecialchars($user['anh_dai_dien'])
    : 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thông tin tài khoản</title>

<style>
body {
  font-family: "Segoe UI", sans-serif;
  background: #f6f7f9;
  margin: 0;
  padding: 0;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  min-height: 100vh;
}

/* Căn giữa nội dung form */
.wrapper {
  max-width: 650px;
  width: 100%;
  margin: 40px auto;
  background: #fff;
  padding: 40px;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}

/* Tiêu đề */
.content h2 {
  color: #0066cc;
  margin-bottom: 25px;
  font-size: 26px;
  text-align: center;
  font-weight: bold;
}

/* Form elements */
label {
  display: block;
  margin-top: 10px;
  margin-bottom: 6px;
  font-weight: 600;
}

input, textarea, select {
  width: 100%;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 8px;
  margin-bottom: 18px;
  font-size: 14px;
}

input:focus, textarea:focus {
  border-color: #4c9fff;
  outline: none;
}

/* Avatar chỉnh giữa */
.avatar-edit {
  text-align: center;
  margin-bottom: 30px;
}

.avatar-edit img {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #4c9fff;
}

.change-photo {
  display: inline-block;
  margin-top: 10px;
  padding: 6px 14px;
  background: #0066cc;
  color: white;
  border-radius: 6px;
  cursor: pointer;
  font-size: 13px;
}

/* Buttons area */
.buttons {
  display: flex;
  justify-content: space-between;
  margin-top: 25px;
  align-items: center;
}

/* Nút home */
.btn-home {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 18px;
  background: #e8f0ff;
  color: #1a56db;
  border-radius: 8px;
  border: 1px solid #bcd2ff;
  text-decoration: none;
  font-size: 15px;
  transition: 0.25s;
}

.btn-home:hover {
  background: #d5e4ff;
  border-color: #8cb3ff;
}

/* Button save / cancel group */
.right-buttons {
  display: flex;
  gap: 12px;
}

.btn-save {
  background: #2855a7ff;
  padding: 12px 25px;
  border-radius: 8px;
  color: white;
  font-size: 15px;
  border: none;
  cursor: pointer;
}

.btn-cancel {
  background: #e0e0e0;
  padding: 12px 25px;
  border-radius: 8px;
  color: black;
  border: none;
  font-size: 15px;
  cursor: pointer;
}

.btn-save:hover { background: #0066cc; }
.btn-cancel:hover { background: #c9c9c9; }
</style>

</head>
<body>

<div class="wrapper">

  <div class="content">
      <h2>Thông tin tài khoản</h2>

      <?php if (!empty($_SESSION['flash_message'])): ?>
        <div style="background:#d4edda;color:#0066cc;padding:12px;border-radius:8px;margin-bottom:20px;">
          <?= $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?>
        </div>
      <?php endif; ?>

      <form action="index.php?action=updateProfile" method="POST" enctype="multipart/form-data">

        <div class="avatar-edit">
            <img id="preview" src="<?= $avatar ?>">
            <br>
            <label class="change-photo" for="avatarFile">Đổi ảnh</label>
            <input type="file" id="avatarFile" name="anh_dai_dien" accept="image/*" style="display:none" onchange="previewImg(event)">
        </div>

        <label>Họ và tên</label>
        <input type="text" name="ho_ten" required value="<?= htmlspecialchars($user['ho_ten']) ?>">

        

        <label>Ngày sinh</label>
        <input type="date" name="ngay_sinh" value="<?= htmlspecialchars($user['ngay_sinh'] ?? '') ?>">

        <label>Giới tính</label>
        <select name="gioi_tinh">
            <option value="">Chọn giới tính</option>
            <option value="Nam" <?= ($user['gioi_tinh'] == "Nam" ? "selected" : "") ?>>Nam</option>
            <option value="Nữ" <?= ($user['gioi_tinh'] == "Nữ" ? "selected" : "") ?>>Nữ</option>
        </select>

        <!-- Buttons row -->
        <div class="buttons">
          
          <!-- Button Home -->
          <a class="btn-home" href="index.php">
            <span style="font-size:18px;">🏠</span> Trang chủ
          </a>

          <!-- Right button group -->
          <div class="right-buttons">
            <button class="btn-save" type="submit">Lưu thay đổi</button>
            <button type="button" class="btn-cancel" onclick="location.reload()">Hủy bỏ</button>
          </div>

        </div>

      </form>
  </div>

</div>

<script>
function previewImg(event) {
    const reader = new FileReader();
    reader.onload = () => document.getElementById('preview').src = reader.result;
    reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>
