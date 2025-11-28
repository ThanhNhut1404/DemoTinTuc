<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php?action=login");
    exit;
}

require_once 'models/ThanhVienModel.php';
$model = new \Website\TinTuc\Models\ThanhVienModel();
$user = $model->findById($_SESSION['user']['id']);
?>

<?php include 'header.php'; ?>   <!-- đặt đúng vị trí ở đây -->

<h2>Hồ sơ cá nhân</h2>

<form method="post" action="index.php?action=update_profile" enctype="multipart/form-data">
    <label>Họ tên:</label><br>
    <input type="text" name="ho_ten" value="<?= htmlspecialchars($user['ho_ten']) ?>"><br><br>

    <label>Số điện thoại:</label><br>
    <input type="text" name="so_dien_thoai" value="<?= htmlspecialchars($user['so_dien_thoai']) ?>"><br><br>

    <label>Địa chỉ:</label><br>
    <input type="text" name="dia_chi" value="<?= htmlspecialchars($user['dia_chi']) ?>"><br><br>

    <label>Avatar:</label><br>
    <input type="file" name="avatar"><br><br>

    <?php if (!empty($user['avatar'])): ?>
        <img src="<?= $user['avatar'] ?>" width="120">
    <?php endif; ?>

    <br><br>
    <button type="submit">Cập nhật</button>
</form>

<br>
<a href="index.php?action=home">Quay lại Trang chủ</a>
