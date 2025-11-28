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

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            display: block;
            text-align: left;
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
            padding: 12px 40px 12px 15px; /* để icon con mắt không chồng lên */
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

        .toggle-password:hover {
            color: #007bff;
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

        p {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="register-box">
    <h2>Đăng ký</h2>
    <form action="index.php?action=do_register" method="POST">

        <!-- Họ và tên -->
        <label for="ho_ten">Họ và tên:</label>
        <input type="text" id="ho_ten" name="ho_ten" required>

        <!-- Ngày sinh -->
        <label for="ngay_sinh">Ngày sinh:</label>
        <input type="date" id="ngay_sinh" name="ngay_sinh" required>

        <!-- Giới tính -->
        <label for="gioi_tinh">Giới tính:</label>
        <select id="gioi_tinh" name="gioi_tinh" required>
            <option value="">-- Chọn giới tính --</option>
            <option value="Nam">Nam</option>
            <option value="Nữ">Nữ</option>
            
        </select>

        <!-- Email -->
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <!-- Mật khẩu -->
        <label for="password">Mật khẩu:</label>
        <div class="input-group">
            <input type="password" id="password" name="mat_khau" required>
            <span class="toggle-password" onclick="togglePassword('password', this)">👁️</span>
        </div>

        <!-- Xác nhận mật khẩu -->
        <label for="confirm_password">Xác nhận mật khẩu:</label>
        <div class="input-group">
            <input type="password" id="confirm_password" name="confirm_password" required>
            <span class="toggle-password" onclick="togglePassword('confirm_password', this)">👁️</span>
        </div>

        <button type="submit">Đăng ký</button>
    </form>

    <p>
        Đã có tài khoản? <a href="index.php?action=login">Đăng nhập</a>
    </p>
</div>

<script>
    function togglePassword(id, icon) {
        const input = document.getElementById(id);
        if (input.type === "password") {
            input.type = "text";
            icon.textContent = "🙈"; // đổi icon khi hiện mật khẩu
        } else {
            input.type = "password";
            icon.textContent = "👁️"; // đổi icon khi ẩn mật khẩu
        }
    }
</script>
</body>
</html>
