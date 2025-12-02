<?php
// Admin login page — full HTML similar to public login.php
$flash = $_SESSION['flash_login_error'] ?? null;
unset($_SESSION['flash_login_error']);
// success flash (shown briefly then redirect)
$flash_success = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_success']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #007bff, #00c6ff);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-box {
            background: rgba(255,255,255,0.95);
            width: 420px;
            padding: 45px 40px;
            border-radius: 26px;
            border: 1px solid rgba(255,255,255,0.4);
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12), 0 8px 16px rgba(0,0,0,0.05);
            transition: 0.3s;
        }

        h2 {
            margin-bottom: 18px;
            font-size: 26px;
            font-weight: 700;
            color: #222;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            transform: translateY(-6px);
        }

        label { display:block; text-align:left; width:100%; font-weight:500; color:#444; margin-bottom:6px; font-size:14px }
        .input-group{position:relative;width:100%;margin-bottom:10px}
        input { width:100%; padding:14px 40px 14px 15px; border:1px solid #dcdcdc; border-radius:12px; font-size:15px; box-sizing:border-box }
        input:focus{border-color:#0d6efd; box-shadow:0 0 8px rgba(0,123,255,0.35); outline:none}
        .toggle-password{position:absolute; top:50%; right:12px; transform:translateY(-50%); cursor:pointer; font-size:18px; color:#666; background:transparent; border:none; padding:0; display:inline-flex; align-items:center; justify-content:center}
        .toggle-password:hover{color:#007bff}
        .forgot-password{ text-align:right; margin-bottom:20px }
        button.login-btn{ width:100%; padding:14px; font-size:17px; background:linear-gradient(90deg,#22c55e,#16a34a); border:none; border-radius:12px; color:#fff; cursor:pointer; font-weight:600 }
        button.login-btn:hover{ transform:translateY(-2px); background:linear-gradient(90deg,#16a34a,#15803d) }
        .flash{ background:#fff0f0;padding:6px 8px;border:none;color:#b91c1c;border-radius:6px;margin:8px auto 12px;max-width:100%;text-align:center;font-size:14px }
        .flash-line{ color:#b91c1c; font-weight:600; margin:1px 0; line-height:1.0; text-align:center; font-size:15px }
        .flash-success{ background:#ecfdf5;padding:6px 8px;border-radius:6px;margin:8px auto 12px;max-width:100%;text-align:center;color:#065f46;font-weight:700 }
    </style>
</head>
<body>
<div class="login-box">
    <h2>Đăng nhập quản trị</h2>
    <?php if ($flash_success): ?>
        <div class="flash-success" role="status"><?= htmlspecialchars($flash_success) ?></div>
    <?php endif; ?>
    <?php if ($flash): ?>
        <?php if (is_array($flash)): ?>
            <div class="flash" role="alert">
                <?php foreach ($flash as $line): ?>
                    <div class="flash-line"><?= htmlspecialchars($line) ?></div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="flash" role="alert"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>
    <?php endif; ?>
    <form method="post" action="admin.php?action=login_submit">
        <label for="email">Email:</label>
        <input id="email" name="email" type="email" required>

        <label for="password">Mật khẩu:</label>
        <div class="input-group">
            <input id="password" name="password" type="password" required>
            <button type="button" class="toggle-password" aria-label="Hiển thị mật khẩu" onclick="togglePassword('password', this)">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2.05 12.55C3.88 16.32 7.61 19 12 19s8.12-2.68 9.95-6.45C20.12 7.68 16.39 5 12 5S3.88 7.68 2.05 12.55z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
        </div>

        <div class="forgot-password">
            <!-- optional link -->
        </div>

        <button type="submit" class="login-btn">Đăng nhập</button>
    </form>
</div>

<script>
    <?php if (!empty($flash_success)): ?>
    // redirect to admin index shortly after showing success
    setTimeout(function(){ window.location.href = 'admin.php?action=index'; }, 1500);
    <?php endif; ?>
    function togglePassword(id, btn) {
        const input = document.getElementById(id);
        const eyeSVG = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2.05 12.55C3.88 16.32 7.61 19 12 19s8.12-2.68 9.95-6.45C20.12 7.68 16.39 5 12 5S3.88 7.68 2.05 12.55z"/><circle cx="12" cy="12" r="3"/></svg>';
        const eyeSlashSVG = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.97 10.97 0 0 1 12 19c-6 0-10-7-10-7a20.6 20.6 0 0 1 4.2-5.2"/><path d="M1 1l22 22"/></svg>';
        if (input.type === 'password') {
            input.type = 'text';
            btn.innerHTML = eyeSlashSVG;
            btn.setAttribute('aria-label', 'Ẩn mật khẩu');
        } else {
            input.type = 'password';
            btn.innerHTML = eyeSVG;
            btn.setAttribute('aria-label', 'Hiển thị mật khẩu');
        }
    }
</script>
</body>
</html>
