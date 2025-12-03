<?php
// Ensure composer autoload is loaded from project root
require_once __DIR__ . '/../vendor/autoload.php';
use Website\TinTuc\Models\ThanhVienModel;

$model = new ThanhVienModel();
$token = $_GET['token'] ?? '';

$message = '';
$success = false;

// ✅ Bảo vệ token XSS
$token_safe = htmlspecialchars($token, ENT_QUOTES, 'UTF-8');

if (!$token) die("Link không hợp lệ");

// ✅ Kiểm tra token hợp lệ (display page only if valid)
$user = $model->validateResetToken($token);
if (!$user) die("Link đã hết hạn hoặc không hợp lệ");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt mật khẩu mới</title>
    <style>
        body { font-family: Arial; display:flex; justify-content:center; align-items:center; height:100vh; background:#f0f2f5; }
        .form-container { background:#fff; padding:30px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.1); width:350px; text-align:center; }
        input { width:100%; padding:12px; margin:10px 0; border-radius:5px; border:1px solid #ccc; }
        button { width:100%; padding:12px; border:none; border-radius:5px; background:#007bff; color:#fff; cursor:pointer; }
        button:hover { background:#0056b3; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Đặt mật khẩu mới</h2>
        <form method="post" action="index.php?action=submit_reset">
                    <input type="hidden" name="token" value="<?= $token_safe ?>">
                    <input type="password" name="password" placeholder="Nhập mật khẩu mới" required>
                    <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu" required>
                    <button type="submit">Đặt mật khẩu mới</button>
        </form>
                <?php if ($message): ?>
                    <p style="color: <?= $success ? 'green' : 'red' ?>; margin-top:12px;"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
    </div>
</body>
</html>
        
