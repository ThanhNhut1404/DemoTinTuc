<?php
session_start();
require_once __DIR__ . '/../models/ThanhVienModel.php';

use Website\TinTuc\Models\ThanhVienModel;

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$model = new ThanhVienModel();
$userId = $_SESSION['user']['id'];
$user = $model->layThongTinNguoiDung($userId);

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ho_ten = $_POST['ho_ten'] ?? '';
    $email = $_POST['email'] ?? '';
    $so_dien_thoai = $_POST['so_dien_thoai'] ?? '';
    $dia_chi = $_POST['dia_chi'] ?? '';
    $avatar = $_FILES['avatar'] ?? null;

    try {
        $model->updateProfile($userId, $ho_ten, $so_dien_thoai, $dia_chi, $avatar);
        $message = "✅ Cập nhật thông tin thành công!";
        $user = $model->layThongTinNguoiDung($userId);
    } catch (Exception $e) {
        $message = "❌ Lỗi: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Cập nhật hồ sơ cá nhân</title>
<style>
body { font-family: Arial, sans-serif; padding: 20px; }
form { max-width: 500px; margin: auto; }
label { display: block; margin-top: 10px; font-weight: bold; }
input[type="text"], input[type="email"], input[type="file"] { width: 100%; padding: 8px; margin-top: 5px; }
button { margin-top: 15px; padding: 10px 15px; cursor: pointer; }
.avatar-preview { display: block; width: 120px; height: 120px; object-fit: cover; margin-top: 10px; border-radius: 50%; }
.message { margin: 10px 0; font-weight: bold; }

/* Dropdown menu */
.dropdown { position: relative; display: inline-block; margin-right: 20px; }
.dropdown button { padding: 10px 15px; cursor: pointer; }
.dropdown-content { display: none; position: absolute; background-color: #f9f9f9; min-width: 180px; box-shadow: 0px 8px 16px rgba(0,0,0,0.2); z-index: 1; }
.dropdown-content a { color: black; padding: 10px 15px; text-decoration: none; display: block; }
.dropdown-content a:hover { background-color: #f1f1f1; }
.dropdown:hover .dropdown-content { display: block; }
.menu-bar { margin-bottom: 30px; }
</style>
</head>
<body>

<div class="menu-bar">
    <div class="dropdown">
        <button>Tài khoản ▼</button>
        <div class="dropdown-content">
            <a href="profile.php">Hồ sơ cá nhân</a>
            <a href="edit_profile.php">Cập nhật thông tin</a>
            <a href="change_password.php">Đổi mật khẩu</a>
            <a href="logout.php">Đăng xuất</a>
        </div>
    </div>

    <div class="dropdown">
        <button>Chuyên mục ▼</button>
        <div class="dropdown-content">
            <a href="#">Tin tức</a>
            <a href="#">Thể thao</a>
            <a href="#">Giải trí</a>
        </div>
    </div>
</div>

<h2>Cập nhật thông tin cá nhân</h2>

<?php if($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <label>Họ và tên</label>
    <input type="text" name="ho_ten" value="<?= htmlspecialchars($user['ho_ten'] ?? '') ?>" required>

    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>

    <label>Số điện thoại</label>
    <input type="text" name="so_dien_thoai" value="<?= htmlspecialchars($user['so_dien_thoai'] ?? '') ?>">

    <label>Địa chỉ</label>
    <input type="text" name="dia_chi" value="<?= htmlspecialchars($user['dia_chi'] ?? '') ?>">

    <label>Ảnh đại diện</label>
    <input type="file" name="avatar" accept="image/*">
    <?php if(!empty($user['avatar'])): ?>
        <img src="/public/uploads/avatars/<?= htmlspecialchars($user['avatar']) ?>" class="avatar-preview" id="avatarPreview">
    <?php else: ?>
        <img src="/public/uploads/avatars/default.png" class="avatar-preview" id="avatarPreview">
    <?php endif; ?>

    <button type="submit">Cập nhật thông tin</button>
</form>

<script>
const avatarInput = document.querySelector('input[name="avatar"]');
const avatarPreview = document.getElementById('avatarPreview');

avatarInput.addEventListener('change', function() {
    const file = this.files[0];
    if(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            avatarPreview.src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>

</body>
</html>

