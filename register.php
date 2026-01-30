<?php
session_start();

$error = "";
$success = "";

if (isset($_POST['signin'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    if ($password !== $confirm) {
        $error = "Password dan konfirmasi password tidak sama!";
    } else {
        // cek username sudah ada atau belum
        $cek = mysqli_query($conn, "SELECT * FROM pegawai WHERE username='$username'");
        if (mysqli_num_rows($cek) > 0) {
            $error = "Username sudah digunakan!";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($conn, "
                INSERT INTO pegawai (username, password, role, status)
                VALUES ('$username', '$hash', 'pegawai', 'aktif')
            ");
            $success = "Akun berhasil dibuat! Silakan login.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Sign In Kominfo</title>

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
        padding: 15px;
        text-align: center;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }

    .logo img {
        width: 120px;
        margin-bottom: 10px;
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
        padding: 10px 40px 10px 10px;
        border-radius: 25px;
        border: 1px solid #777;
        outline: none;
        margin-top: 6px;
        box-sizing: border-box;
        font-size: 14px;
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
    }

    .success {
        background: #d1e7dd;
        color: #0f5132;
        padding: 8px;
        border-radius: 10px;
        font-size: 13px;
        margin-bottom: 15px;
    }
</style>
</head>
<body>

<div class="login-box">
    <div class="logo">
        <img src="aset/logo-kominfo.jpeg" alt="Kominfo">
        <p>Kementerian Komunikasi dan Informatika</p>
    </div>

    <?php if ($error != "") { ?>
        <div class="error"><?= $error ?></div>
    <?php } ?>

    <?php if ($success != "") { ?>
        <div class="success"><?= $success ?></div>
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

        <div class="form-group">
            <label>Konfirmasi Password</label>
            <input type="password" name="confirm" required>
        </div>

        <button type="submit" name="signin" class="btn-login">Sign In</button>
    </form>

    <div class="register">
        Sudah Punya Akun? <a href="index.php">Login</a>
    </div>
</div>

</body>
</html>
