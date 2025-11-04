<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$avatar = !empty($user['anh_dai_dien'])
    ? 'uploads/' . htmlspecialchars($user['anh_dai_dien'])
    : 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Trang người dùng</title>
<style>
:root {
  --main-color: #007bff;
  --bg-light: #f4f6f8;
  --text-dark: #333;
  --radius: 10px;
}
body {
  font-family: "Segoe UI", sans-serif;
  background-color: var(--bg-light);
  margin: 0;
  animation: fadeIn 0.5s ease;
}
@keyframes fadeIn { from {opacity:0;} to {opacity:1;} }

nav {
  display: flex;
  justify-content: center;
  background: white;
  border-bottom: 1px solid #ddd;
  padding: 18px;
  box-shadow: 0 1px 5px rgba(0,0,0,0.05);
  position: sticky;
  top: 0;
  z-index: 10;
}
nav a {
  margin: 0 30px;
  text-decoration: none;
  color: var(--text-dark);
  font-weight: 600;
  padding-bottom: 4px;
  transition: all 0.3s ease;
  cursor: pointer;
}
nav a:hover { color: var(--main-color); }
nav a.active {
  color: var(--main-color);
  border-bottom: 3px solid var(--main-color);
}

.container {
  max-width: 850px;
  margin: 40px auto;
  background: #fff;
  padding: 35px;
  border-radius: var(--radius);
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  transition: all 0.3s ease;
}

h2 {
  color: var(--main-color);
  text-align: center;
  margin-top: 0;
}

.tab-content { display: none; animation: fadeIn 0.5s ease; }
.tab-content.active { display: block; }

.avatar-wrapper {
  position: relative;
  width: 120px;
  height: 120px;
  margin: 0 auto 20px auto;
}
.avatar-wrapper img {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid var(--main-color);
  transition: 0.3s;
}
.avatar-wrapper:hover img { filter: brightness(0.8); }
.avatar-wrapper .change-link {
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  background: rgba(0,0,0,0.6);
  color: white;
  font-size: 13px;
  padding: 4px 8px;
  border-radius: 20px;
  opacity: 0;
  transition: opacity 0.3s;
  cursor: pointer;
}
.avatar-wrapper:hover .change-link { opacity: 1; }

input, select, textarea {
  width: 100%;
  padding: 10px;
  margin: 8px 0 15px 0;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
  transition: border-color 0.3s;
}
input:focus, textarea:focus, select:focus {
  border-color: var(--main-color);
  outline: none;
}

button {
  background: var(--main-color);
  color: white;
  border: none;
  padding: 10px 18px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: bold;
  transition: 0.3s;
}
button:hover { background: #0056b3; }

.flash {
  background: #d4edda;
  color: #155724;
  padding: 10px 14px;
  border-radius: 6px;
  margin-bottom: 15px;
  border: 1px solid #c3e6cb;
  animation: fadeOut 4s forwards;
}
@keyframes fadeOut {
  0%,80%{opacity:1;}
  100%{opacity:0; height:0; margin:0; padding:0;}
}

.comment-box {
  border:1px solid #ddd;
  border-radius:8px;
  padding:12px;
  margin-bottom:10px;
  transition: 0.3s;
}
.comment-box:hover { background:#f9f9f9; }
.comment-box small { color:#666; }

/* 🔹 Giao diện chọn giới tính đẹp */
.gender-group {
  display: flex;
  justify-content: center;
  gap: 15px;
  margin: 10px 0 25px;
}
.gender-option {
  position: relative;
  display: flex;
  align-items: center;
  background: #f1f5fb;
  border: 2px solid transparent;
  border-radius: 25px;
  padding: 10px 18px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s ease;
  user-select: none;
}
.gender-option:hover {
  background: #e7f1ff;
  border-color: var(--main-color);
}
.gender-option.active {
  background: var(--main-color);
  color: #fff;
  border-color: var(--main-color);
  box-shadow: 0 0 8px rgba(0,123,255,0.3);
}
.gender-option input { display: none; }
.gender-option span {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 14px;
}

/* ✅ Căn giữa 2 nút */
.button-group {
  display: flex;
  justify-content: center;
  gap: 15px;
  margin-top: 10px;
}
.btn-cancel {
  background: #e0e0e0;
  color: #333;
}
.btn-cancel:hover {
  background: #c9c9c9;
}
</style>
</head>
<body>

<nav>
  <a id="tabProfile" class="active">Cập nhật thông tin</a>
  <a id="tabLiked">Đã thích / Đã lưu</a>
  <a id="tabComments">Bình luận của tôi</a>
</nav>

<div class="container">

  <div id="contentProfile" class="tab-content active">
    <h2>Cập nhật thông tin cá nhân</h2>

    <?php if (!empty($_SESSION['flash_message'])): ?>
      <div class="flash"><?= $_SESSION['flash_message']; ?></div>
      <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <form method="post" action="admin.php?action=updateProfile" enctype="multipart/form-data">
      <div class="avatar-wrapper">
        <img id="avatarPreview" src="<?= $avatar ?>" alt="Ảnh đại diện">
        <label for="fileInput" class="change-link">Thay đổi ảnh</label>
        <input id="fileInput" type="file" name="anh_dai_dien" accept="image/*" style="display:none" onchange="previewImage(event)">
      </div>

      <label>Họ tên</label>
      <input type="text" name="ho_ten" value="<?= htmlspecialchars($user['ho_ten'] ?? '') ?>" required>

      <label>Email</label>
      <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>

      <label>Ngày sinh</label>
      <input type="date" name="ngay_sinh" value="<?= htmlspecialchars($user['ngay_sinh'] ?? '') ?>" min="1980-01-01" max="<?= date('Y-m-d') ?>">

      <label>Giới tính:</label>
      <div class="gender-group">
        <?php $gioiTinh = $user['gioi_tinh'] ?? ''; ?>
        <label class="gender-option <?= ($gioiTinh == 'Nam') ? 'active' : '' ?>">
          <input type="radio" name="gioi_tinh" value="Nam" <?= ($gioiTinh == 'Nam') ? 'checked' : '' ?>>
          <span>👨 Nam</span>
        </label>
        <label class="gender-option <?= ($gioiTinh == 'Nữ') ? 'active' : '' ?>">
          <input type="radio" name="gioi_tinh" value="Nữ" <?= ($gioiTinh == 'Nữ') ? 'checked' : '' ?>>
          <span>👩 Nữ</span>
        </label>
      </div>

      <div class="button-group">
        <button type="submit">💾 Lưu thay đổi</button>
        <button type="button" class="btn-cancel" onclick="window.location.reload()"> Hủy bỏ</button>
      </div>
    </form>
  </div>

</div>

<script>
function previewImage(event) {
  const reader = new FileReader();
  reader.onload = () => document.getElementById('avatarPreview').src = reader.result;
  reader.readAsDataURL(event.target.files[0]);
}

document.querySelectorAll('.gender-option input').forEach(radio => {
  radio.addEventListener('change', () => {
    document.querySelectorAll('.gender-option').forEach(opt => opt.classList.remove('active'));
    radio.parentElement.classList.add('active');
  });
});
</script>

</body>
</html>
