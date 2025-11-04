<?php
// Giả định các biến có sẵn từ controller:
// $user, $yeuThich, $daLuu, $binhLuan
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
body {font-family: Arial, sans-serif; background-color:#f4f6f8; margin:0;}
nav {
  display:flex; justify-content:center; background:#fff;
  border-bottom:1px solid #ccc; padding:15px;
}
nav a {
  margin:0 25px; text-decoration:none; color:#333; font-weight:bold;
  cursor:pointer; transition: all 0.2s;
}
nav a.active {color:#007bff; border-bottom:3px solid #007bff; padding-bottom:4px;}
.container {
  max-width:900px; margin:40px auto; background:#fff; padding:30px;
  border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.1);
}
.tab-content {display:none;}
.tab-content.active {display:block;}
button {
  background:#007bff; color:white; border:none;
  padding:8px 14px; border-radius:6px; cursor:pointer; font-weight:bold;
}
button:hover {background:#0056b3;}
input, textarea {
  width:100%; padding:8px; margin:6px 0; border:1px solid #ccc; border-radius:6px;
}
.flash {
  background:#d4edda; color:#155724; padding:10px 12px;
  border-radius:6px; margin-bottom:15px; font-weight:bold;
  border:1px solid #c3e6cb;
}
.avatar {
  display:block; margin:0 auto 10px auto; width:100px; height:100px;
  border-radius:50%; object-fit:cover; border:2px solid #007bff;
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

  <!-- 🔹 THÔNG TIN CÁ NHÂN -->
  <div id="contentProfile" class="tab-content active">
    <h2>Cập nhật thông tin cá nhân</h2>

    <?php if (!empty($_SESSION['flash_message'])): ?>
      <div class="flash"><?= $_SESSION['flash_message']; ?></div>
      <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <form method="post" action="admin.php?action=updateProfile" enctype="multipart/form-data">
      <img id="avatarPreview" class="avatar" src="<?= $avatar ?>" alt="Ảnh đại diện">
      
      <label>Họ tên:</label>
      <input type="text" name="ho_ten" value="<?= htmlspecialchars($user['ho_ten'] ?? '') ?>" required>

      <label>Email:</label>
      <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>

      <label>Ảnh đại diện mới:</label>
      <input type="file" name="anh_dai_dien" accept="image/*" onchange="previewImage(event)">

      <button type="submit">💾 Lưu thay đổi</button>
    </form>
  </div>

  <!-- 🔹 BÀI VIẾT ĐÃ THÍCH / ĐÃ LƯU -->
  <div id="contentLiked" class="tab-content">
    <h2>Bài viết bạn đã thích</h2>
    <?php if (!empty($yeuThich)): ?>
      <ul>
        <?php foreach ($yeuThich as $b): ?>
          <li><b><?= htmlspecialchars($b['tieu_de']) ?></b> - <?= htmlspecialchars($b['ngay_dang']) ?></li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p>Chưa có bài viết nào được thích.</p>
    <?php endif; ?>

    <h2>Bài viết bạn đã lưu</h2>
    <?php if (!empty($daLuu)): ?>
      <ul>
        <?php foreach ($daLuu as $b): ?>
          <li><b><?= htmlspecialchars($b['tieu_de']) ?></b> - <?= htmlspecialchars($b['ngay_dang']) ?></li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p>Chưa có bài viết nào được lưu.</p>
    <?php endif; ?>
  </div>

  <!-- 🔹 BÌNH LUẬN -->
  <div id="contentComments" class="tab-content">
    <h2>Bình luận của tôi</h2>
    <?php if (!empty($binhLuan)): ?>
      <?php foreach ($binhLuan as $b): ?>
        <div style="border:1px solid #ddd; border-radius:8px; padding:10px; margin-bottom:10px;">
          <b><?= htmlspecialchars($b['tieu_de']) ?></b><br>
          <?= htmlspecialchars($b['noi_dung']) ?><br>
          <small>Ngày: <?= htmlspecialchars($b['ngay_binh_luan']) ?></small><br>
          <form method="post" action="admin.php?action=deleteComment" style="margin-top:5px;">
            <input type="hidden" name="id_binh_luan" value="<?= $b['id'] ?>">
            <button type="submit" style="background:#dc3545;">🗑 Xóa</button>
          </form>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Bạn chưa có bình luận nào.</p>
    <?php endif; ?>
  </div>
</div>

<script>
const tabs = document.querySelectorAll('nav a');
const contents = document.querySelectorAll('.tab-content');

tabs.forEach(tab => {
  tab.addEventListener('click', () => {
    tabs.forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    contents.forEach(c => c.classList.remove('active'));
    document.querySelector('#content' + tab.id.replace('tab', '')).classList.add('active');
  });
});

function previewImage(event) {
  const reader = new FileReader();
  reader.onload = () => document.getElementById('avatarPreview').src = reader.result;
  reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>
