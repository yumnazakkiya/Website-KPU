<?php
// index.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Homepage</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: "Times New Roman", serif;
        }

        body {
            margin: 0;
            height: 100vh;
            background: radial-gradient(circle at center, #8b0000, #3b0000);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: #ffffff;
            width: 360px;
            padding: 30px 35px 35px;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.35);
            text-align: center;
            color: #7a0000;
        }

        .card h1 {
            margin-bottom: 18px;
            font-weight: bold;
        }

        .logo {
            width: 120px;
            margin: 10px auto 20px;
            display: block;
        }

        .subtitle {
            font-size: 16px;
            margin-bottom: 22px;
        }

        .btn {
            display: inline-block;
            width: 120px;
            padding: 12px;
            background: #7a0000;
            border-radius: 6px;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .btn:hover {
            background: #5e0000;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h1>Selamat Datang!</h1>

        <img src="Logo.png" alt="Logo KPU" class="logo">

        <div class="subtitle">
            Sistem Profil Kepegawaian
        </div>

        <a href="Login.php" class="btn">MASUK</a>
    </div>
</div>

</body>
</html>