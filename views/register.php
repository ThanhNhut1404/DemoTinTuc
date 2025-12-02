<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>
    <style>

/* ===== ALERT: chỉnh để NẰM ĐÚNG HÀNG với các input ===== */
.alert {
    /* chiếm toàn bộ chiều ngang bên trong .register-box */
    display: block;
    width: 100%;
    box-sizing: border-box;   /* rất quan trọng để padding không làm tràn */
    padding: 12px 14px;       /* giống kiểu padding input */
    margin: 12px 0 15px 0;    /* cách trên/dưới hợp lý */
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    text-align: center;       /* căn giữa chữ bên trong */
    animation: fadeIn 0.2s ease-in-out;
    box-shadow: 0 2px 6px rgba(0,0,0,0.03);
}

.alert.error {
    background: #ffe3e3;
    color: #d20000;
    border: 1px solid #ffb7b7;
}

.alert.success {
    background: #f7f7f7ff;
    color: #007c2e;
    border: 1px solid #ffffffff;
}
/* ======================================================= */

@keyframes fadeIn {
    from {opacity: 0;}
    to {opacity: 1;}
}

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
    padding: 45px 40px; /* <-- inputs căn theo vùng nội dung này */
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

    text-align: center;     /* căn giữa */
    font-weight: 700;       /* in đậm */
    text-transform: uppercase; /* in hoa */
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
        <div id="password-strength"></div>

        <div class="input-group">
            <input type="password" id="password" name="mat_khau" required>
            <span class="toggle-password" onclick="togglePassword('password', this)" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-eye">
                    <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z" stroke="#666" stroke-width="1.4" fill="#ecf6ff"/>
                    <circle cx="12" cy="12" r="3" fill="#007bff"/>
                </svg>
            </span>
        </div>

        <div id="password-strength-text"></div>
        <div id="password-strength-bar">
            <div id="password-strength-fill"></div>
        </div>

        <label for="confirm_password">Xác nhận mật khẩu:</label>
        <div class="input-group">
            <input type="password" id="confirm_password" name="confirm_password" required>
            <span class="toggle-password" onclick="togglePassword('confirm_password', this)" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-eye">
                    <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z" stroke="#666" stroke-width="1.4" fill="#ecf6ff"/>
                    <circle cx="12" cy="12" r="3" fill="#007bff"/>
                </svg>
            </span>
        </div>

        <!-- THÔNG BÁO HIỆN Ở ĐÂY (SẼ NẰM THẲNG HÀNG VỚI INPUTS) -->
        <?php if(isset($errorMessage)): ?>
            <div class="alert error">
                <?= $errorMessage ?>
            </div>
        <?php endif; ?>

        <?php if(isset($successMessage)): ?>
            <div class="alert success">
                <?= $successMessage ?>
            </div>
        <?php endif; ?>
        <!-- END -->

        <button type="submit">Đăng ký</button>
    </form>

    <div class="auth-switch">
        Đã có tài khoản? <a href="index.php?action=login">Đăng nhập</a>
    </div>

</div>


<script>
function togglePassword(id, icon) {
    const input = document.getElementById(id);
    const openSvg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z" stroke="#666" stroke-width="1.4" fill="#ecf6ff"/><circle cx="12" cy="12" r="3" fill="#007bff"/></svg>';
    const closeSvg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-6 0-10-7-10-7 .9-1.53 2.12-2.95 3.58-4.16m2.42-1.77A9.99 9.99 0 0 1 12 5c6 0 10 7 10 7 0 1.12-.23 2.19-.65 3.17M3 3l18 18" stroke="#666" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = closeSvg;
    } else {
        input.type = 'password';
        icon.innerHTML = openSvg;
    }
}

// Password strength: mirror server rules
function passwordStrengthLevel(pw) {
    if (!pw) return 'empty';
    const hasUpper = /[A-Z]/.test(pw);
    const hasLower = /[a-z]/.test(pw);
    const hasDigit = /[0-9]/.test(pw);
    const hasSymbol = /[\W]/.test(pw);
    if (pw.length >= 10 && hasUpper && hasLower && hasDigit && hasSymbol) return 'strong';
    if (pw.length >= 8 && /[a-zA-Z]/.test(pw) && hasDigit) return 'medium';
    return 'weak';
}

document.getElementById('password').addEventListener('input', function () {
    const value = this.value;
    const lvl = passwordStrengthLevel(value);
    const text = document.getElementById('password-strength-text');
    const bar = document.getElementById('password-strength-fill');

    if (lvl === 'empty') {
        text.innerText = '';
        bar.style.width = '0%';
        return;
    }
    if (lvl === 'weak') {
        text.innerText = 'Mật khẩu yếu (ít nhất 8 ký tự, gồm chữ và số)';
        text.style.color = 'red';
        bar.style.width = '33%';
        bar.style.background = 'red';
    } else if (lvl === 'medium') {
        text.innerText = 'Mật khẩu trung bình (cố gắng dùng ký tự đặc biệt để mạnh hơn)';
        text.style.color = 'orange';
        bar.style.width = '66%';
        bar.style.background = 'orange';
    } else {
        text.innerText = 'Mật khẩu mạnh';
        text.style.color = 'green';
        bar.style.width = '100%';
        bar.style.background = 'green';
    }
});

// Set max date for ngay_sinh to today to prevent future dates
(function setMaxDob() {
    const dob = document.getElementById('ngay_sinh');
    if (!dob) return;
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    dob.max = `${yyyy}-${mm}-${dd}`;
})();

// Form submit validation: match passwords and require at least medium strength
document.querySelector('form').addEventListener('submit', function (e) {
    const pw = document.getElementById('password').value;
    const cpw = document.getElementById('confirm_password').value;
    const lvl = passwordStrengthLevel(pw);
    if (pw !== cpw) {
        e.preventDefault();
        alert('Mật khẩu và xác nhận mật khẩu không khớp.');
        return false;
    }
    if (lvl === 'weak' || lvl === 'empty') {
        e.preventDefault();
        alert('Mật khẩu quá yếu. Vui lòng đặt mật khẩu ít nhất 8 ký tự, gồm chữ và số.');
        return false;
    }
    // DOB checked by 'max' attribute and server-side as well
});
</script>

</body>
</html>
