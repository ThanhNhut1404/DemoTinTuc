<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>
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

        .register-box {
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
            margin-bottom: 25px;
            color: #333;
            font-size: 24px;
            letter-spacing: 0.5px;
        }

        label {
            display: block;
            width: 100%;
            font-weight: 500;
            color: #444;
            margin-bottom: 6px;
            font-size: 14px;
        }

        .input-group {
            position: relative;
            width: 100%;
            margin-bottom: 18px;
        }

        input, select {
            width: 100%;
            padding: 12px 40px 12px 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        input:focus, select:focus {
            border-color: #007bff;
            box-shadow: 0 0 6px rgba(0, 123, 255, 0.3);
            outline: none;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 18px;
            color: #666;
        }

        button {
            width: 100%;
            background: #007bff;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #0056b3;
            transform: translateY(-1px);
        }

        #password-strength-text {
            font-size: 13px;
            margin-top: -10px;
            margin-bottom: 5px;
            font-weight: 600;
        }
        #password-strength-bar {
            width: 100%;
            height: 6px;
            background: #ddd;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 10px;
        }
        #password-strength-fill {
            height: 100%;
            width: 0%;
            background: red;
            transition: width 0.3s ease, background 0.3s ease;
        }

        /* ⭐ STYLE GIỐNG LOGIN */
        .auth-switch {
            text-align: center;
            margin-top: 15px;
            font-size: 15px;
            color: #333;
        }

        .auth-switch a {
            color: #007bff;
            font-weight: 600;
            text-decoration: none;
            transition: 0.2s;
        }

        .auth-switch a:hover {
            text-decoration: underline;
            color: #0056d2;
        }
    </style>
</head>

<body>
<div class="register-box">
    <h2>Đăng ký</h2>

    <form action="index.php?action=do_register" method="POST">

        <label for="ho_ten">Họ và tên:</label>
        <input type="text" id="ho_ten" name="ho_ten" required>

        <label for="ngay_sinh">Ngày sinh:</label>
        <input type="date" id="ngay_sinh" name="ngay_sinh" required>

        <label for="gioi_tinh">Giới tính:</label>
        <select id="gioi_tinh" name="gioi_tinh" required>
            <option value="">-- Chọn giới tính --</option>
            <option value="Nam">Nam</option>
            <option value="Nữ">Nữ</option>
        </select>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Mật khẩu:</label>
        <div class="input-group">
            <input type="password" id="password" name="mat_khau" required>
            <span class="toggle-password" onclick="togglePassword('password', this)">👁️</span>
        </div>

        <div id="password-strength-text"></div>
        <div id="password-strength-bar">
            <div id="password-strength-fill"></div>
        </div>

        <label for="confirm_password">Xác nhận mật khẩu:</label>
        <div class="input-group">
            <input type="password" id="confirm_password" name="confirm_password" required>
            <span class="toggle-password" onclick="togglePassword('confirm_password', this)">👁️</span>
        </div>

        <button type="submit">Đăng ký</button>
    </form>

    <!-- ⭐ PHẦN MỚI CHUẨN UI -->
    <div class="auth-switch">
        Đã có tài khoản? <a href="index.php?action=login">Đăng nhập</a>
    </div>

</div>


<script>
function togglePassword(id, icon) {
    const input = document.getElementById(id);
    input.type = input.type === "password" ? "text" : "password";
    icon.textContent = input.type === "text" ? "🙈" : "👁️";
}

function checkPasswordStrength(password) {
    let score = 0;
    if (password.length >= 6) score++;
    if (password.length >= 8) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[a-z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[\W]/.test(password)) score++;
    return score;
}

document.getElementById("password").addEventListener("input", function () {
    const value = this.value;
    const score = checkPasswordStrength(value);

    const text = document.getElementById("password-strength-text");
    const bar = document.getElementById("password-strength-fill");

    if (!value) {
        text.innerText = "";
        bar.style.width = "0%";
        return;
    }

    if (score <= 2) {
        text.innerText = "Mật khẩu yếu";
        text.style.color = "red";
        bar.style.width = "33%";
        bar.style.background = "red";
    } 
    else if (score <= 4) {
        text.innerText = "Mật khẩu trung bình";
        text.style.color = "orange";
        bar.style.width = "66%";
        bar.style.background = "orange";
    } 
    else {
        text.innerText = "Mật khẩu mạnh";
        text.style.color = "green";
        bar.style.width = "100%";
        bar.style.background = "green";
    }
});
</script>

</body>
</html>
