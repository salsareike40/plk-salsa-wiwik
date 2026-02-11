<?php
session_start();
include "conn.php";

$error = "";

if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // AMBIL DATA DARI TABEL PEGAWAI
    $query = mysqli_query($conn, 
        "SELECT * FROM pegawai WHERE username='$username'"
    );

    $pegawai = mysqli_fetch_assoc($query);

    if ($pegawai) {

        // CEK PASSWORD
        if (password_verify($password, $pegawai['password'])) {

                        // login.php (SETELAH password_verify)
            $_SESSION['login']      = true;
            $_SESSION['id_pegawai'] = $pegawai['id_pegawai'];
            $_SESSION['username']   = $pegawai['username'];
            $_SESSION['role']       = $pegawai['role']; // admin / pegawai

            header("Location: dashboard.php");
            exit;


        } else {
            $error = "Password salah!";
        }

    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login Sistem Kominfo</title>

<style>
body{
    margin:0;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:#0A5CA8;
    font-family:Segoe UI,sans-serif;
}
.login-box{
    background:#fff;
    padding:40px;
    width:350px;
    border-radius:15px;
    box-shadow:0 10px 25px rgba(0,0,0,.2);
}
.login-box h2{
    text-align:center;
    margin-bottom:30px;
}
input{
    width:100%;
    padding:12px;
    margin-bottom:15px;
    border-radius:8px;
    border:1px solid #ccc;
}
button{
    width:100%;
    padding:12px;
    background:#0A5CA8;
    color:#fff;
    border:none;
    border-radius:8px;
    font-size:16px;
    cursor:pointer;
}
.error{
    background:#FFD6D6;
    color:#C40000;
    padding:10px;
    border-radius:8px;
    margin-bottom:15px;
    text-align:center;
}
</style>
</head>

<body>

<div class="login-box">
    <h2>Login Sistem</h2>

    <?php if($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>
</div>

</body>
</html>
