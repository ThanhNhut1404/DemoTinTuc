<?php
// Load composer autoload
require_once __DIR__ . '/../vendor/autoload.php';
use Website\TinTuc\Models\ThanhVienModel;

$model = new ThanhVienModel();
$token = $_GET['token'] ?? '';

$message = '';
$success = false;

// Protect token from XSS when rendering
$token_safe = htmlspecialchars($token, ENT_QUOTES, 'UTF-8');

if (!$token) {
    die("Link không hợp lệ");
}

// Validate token; show page only if token is valid
$user = $model->validateResetToken($token);
if (!$user) {
    die("Link đã hết hạn hoặc không hợp lệ");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt mật khẩu mới</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--bg:#f4f6f9;--card:#ffffff;--accent:#2563eb;--muted:#6b7280}
        *{box-sizing:border-box}
        body{font-family:Inter,system-ui,Segoe UI,Roboto,Arial;margin:0;background:linear-gradient(180deg,#eef2ff 0%,var(--bg) 100%);display:flex;align-items:center;justify-content:center;min-height:100vh;padding:24px}
        .card{width:100%;max-width:480px;background:var(--card);border-radius:12px;padding:28px;box-shadow:0 10px 30px rgba(2,6,23,0.08)}
        h2{margin:0 0 8px;font-size:22px;color:#0f172a}
        p.lead{margin:0 0 18px;color:var(--muted);font-size:14px}
        form .field{margin-bottom:12px}
        input[type=password]{width:100%;padding:12px 14px;border-radius:8px;border:1px solid #e6e9ef;background:#fff;font-size:14px}
        input[type=password]:focus{outline:none;box-shadow:0 6px 18px rgba(37,99,235,0.12);border-color:var(--accent)}
        .btn{display:inline-block;width:100%;padding:12px;border-radius:10px;background:var(--accent);color:#fff;border:0;font-weight:600;cursor:pointer}
        .btn:active{transform:translateY(1px)}
        .helper{font-size:13px;color:var(--muted);margin-top:12px}
        .msg{padding:10px;border-radius:8px;margin-top:12px;font-size:14px}
        .msg.success{background:#ecfdf5;color:#065f46;border:1px solid #bbf7d0}
        .msg.error{background:#fff1f2;color:#9f1239;border:1px solid #fecaca}
        .small{font-size:12px;color:var(--muted)}
        @media (max-width:520px){.card{padding:18px}}
    </style>
</head>
<body>
    <div class="card" role="main">
        <h2>Đặt mật khẩu mới</h2>
        <p class="lead">Nhập mật khẩu mới để hoàn tất đặt lại mật khẩu của bạn.</p>

        <form method="post" action="index.php?action=submit_reset" novalidate>
            <input type="hidden" name="token" value="<?= $token_safe ?>">
            <div class="field"><input type="password" name="password" placeholder="Mật khẩu mới" required aria-label="Mật khẩu mới"></div>
            <div class="field"><input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu" required aria-label="Xác nhận mật khẩu"></div>
            <button class="btn" type="submit">Đặt mật khẩu mới</button>
        </form>

        <?php if ($message): ?>
            <div class="msg <?= $success ? 'success' : 'error' ?>"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <p class="helper">Bạn nhớ mật khẩu? <a href="index.php?action=login">Đăng nhập</a></p>
        <p class="small">Mật khẩu nên có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường và số.</p>
    </div>
</body>
</html>

