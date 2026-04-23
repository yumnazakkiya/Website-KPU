<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: "Segoe UI", Tahoma, sans-serif;
        }

        body {
            margin: 0;
            height: 100vh;
            background: radial-gradient(circle at center, #8b0000, #3b0000);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: #ffffff;
            width: 360px;
            padding: 30px 35px 35px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            color: #7a0000;
        }

        .login-card h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1.5px solid #b07a7a;
            font-size: 14px;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: #7a0000;
            border: none;
            border-radius: 6px;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-login:hover {
            background: #5e0000;
        }

        .error {
            color: red;
            font-size: 13px;
            margin-top: 5px;
        }

        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Log In</h2>

    <!-- ERROR GLOBAL -->
    <?php if(isset($_GET['error']) && $_GET['error']=='kosong'): ?>
        <div class="error">Username dan password wajib diisi</div>
    <?php endif; ?>

    <form action="proses_login.php" method="POST">

        <!-- USERNAME -->
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required autofocus>

            <?php if(isset($_GET['error_user']) && $_GET['error_user']==1): ?>
                <div class="error">Username tidak ditemukan</div>
            <?php endif; ?>
        </div>

        <!-- PASSWORD -->
        <div class="form-group">
            <label>Password</label>

            <div class="password-wrapper">
                <input type="password" name="password" id="password" required>
                <span class="toggle-password" onclick="togglePassword()">👁</span>
            </div>

            <?php if(isset($_GET['error_pass']) && $_GET['error_pass']==1): ?>
                <div class="error">Password salah</div>
            <?php endif; ?>

            <?php if(isset($_GET['error_nonaktif']) && $_GET['error_nonaktif']==1): ?>
                <div class="error">Akun Anda sudah tidak aktif</div>
            <?php endif; ?>
        </div>

        <!-- BUTTON -->
        <button type="submit" name="login" class="btn-login">Log In</button>

    </form>
</div>

<script>
function togglePassword() {
    var password = document.getElementById("password");
    var icon = document.querySelector(".toggle-password");

    if (password.type === "password") {
        password.type = "text";
        icon.style.color = "#e88c9a"; 
    } else {
        password.type = "password";
        icon.style.color = "#7a0000"; 
    }
}
</script>

</body>
</html>