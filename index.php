<?php
session_start();
$error = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // LOGIN CONTOH (nanti ganti database)
    if ($username === "pegawai" && $password === "12345") {
        $_SESSION['username'] = $username;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login Kominfo</title>

<style>
    body {
        margin: 0;
        height: 100vh;
        font-family: Arial, sans-serif;
        background: #8fb6de;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .login-box {
        width: 420px;
        background: #E5EBFA;
        border-radius: 25px;
        padding: 45px 40px;
        text-align: center;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }

    .logo img {
        width: 130px;
        margin-bottom: 10px;
    }

    .logo h3 {
        margin: 0;
        font-size: 18px;
        font-weight: bold;
        color: #1b1f3b;
    }

    .logo p {
        margin: 3px 0 25px;
        font-size: 12px;
        color: #444;
    }

    .form-group {
        text-align: left;
        margin-bottom: 18px;
    }

    .form-group label {
        font-size: 13px;
        color: #333;
    }

    .form-group input {
        width: 100%;
        padding: 12px 16px;
        border-radius: 25px;
        border: 1px solid #777;
        outline: none;
        margin-top: 6px;
        box-sizing: border-box;
        font-size: 14px;
    }

    .form-group input:focus {
        border-color: #0b5ed7;
    }

    .btn-login {
        margin-top: 15px;
        width: 160px;
        padding: 10px;
        background: #0b5ed7;
        border: none;
        border-radius: 25px;
        color: #fff;
        font-size: 14px;
        cursor: pointer;
    }

    .btn-login:hover {
        background: #084298;
    }

    .register {
        margin-top: 18px;
        font-size: 12px;
    }

    .register a {
        color: #0b5ed7;
        text-decoration: none;
        font-weight: bold;
    }

    .error {
    background: #f8d7da;
    color: #842029;
    padding: 8px;
    border-radius: 10px;
    font-size: 13px;
    margin-bottom: 15px;
    transition: opacity 0.5s ease;
    }
</style>
</head>

<script>
    const errorMsg = document.getElementById('error-msg');
    if (errorMsg) {
        setTimeout(() => {
            errorMsg.style.opacity = '0';
            setTimeout(() => {
                errorMsg.style.display = 'none';
            }, 500);
        }, 5000); // 5 detik
    }
</script>

<body>

<div class="login-box">
    <div class="logo">
        <!-- GANTI LOGO SESUAI FILE KAMU -->
        <img src="aset/logo-kominfo.jpeg" alt="Kominfo">
        <p>Kementerian Komunikasi dan Informatika</p>
    </div>

    <?php if ($error != "") { ?>
        <div class="error" id="error-msg"><?= $error ?></div>
    <?php } ?>


    <form method="post">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit" name="login" class="btn-login">Log In</button>
    </form>

    <div class="register">
    Belum Punya Akun? <a href="register.php">Sign In</a>
    </div>

</div>

</body>
</html>
