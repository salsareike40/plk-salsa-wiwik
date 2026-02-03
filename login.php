<?php
session_start();

$error = "";
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Contoh login statis
    if ($username == "admin" && $password == "12345") {
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
<title>Sign In</title>

<style>
body {
    margin: 0;
    height: 100vh;
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #6c6c6c, #9e9e9e);
    display: flex;
    justify-content: center;
    align-items: center;
}

.login-box {
    width: 360px;
    background: #fff;
    padding: 30px;
    border-radius: 4px;
    box-shadow: 0 0 12px rgba(0,0,0,0.35);
    text-align: center;
}

.logo {
    margin-bottom: 20px;
}

.logo-circle {
    display: inline-block;
    background: red;
    color: white;
    font-weight: bold;
    width: 40px;
    height: 40px;
    line-height: 40px;
    border-radius: 50%;
    font-size: 20px;
}

.logo-text {
    font-size: 26px;
    font-weight: bold;
    margin-left: 6px;
}

.subtitle {
    font-size: 12px;
    color: #666;
    margin-top: 4px;
}

.input-group {
    position: relative;
    margin: 15px 0;
}

.input-group input {
    width: 100%;
    padding: 10px 40px 10px 10px;
    border: 1px solid #ccc;
    border-radius: 2px;
    font-size: 14px;
}

.input-group span {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
}

button {
    width: 100%;
    padding: 10px;
    background: #3c8dbc;
    border: none;
    color: white;
    font-size: 16px;
    cursor: pointer;
    border-radius: 2px;
}

button:hover {
    background: #367fa9;
}

.footer {
    margin-top: 15px;
    font-size: 12px;
    color: #337ab7;
}

.error {
    background: #f2dede;
    color: #a94442;
    padding: 8px;
    margin-bottom: 10px;
    border-radius: 3px;
    font-size: 13px;
}
</style>
</head>

<body>

<div class="login-box">
    <div class="logo">
        <span class="logo-circle">n</span>
        <span class="logo-text">nigoweb</span>
        <div class="subtitle">Website & Software Developer</div>
    </div>

    <?php if ($error != "") { ?>
        <div class="error"><?= $error ?></div>
    <?php } ?>

    <form method="post">
        <div class="input-group">
            <input type="text" name="username" placeholder="Username" required>
            <span>👤</span>
        </div>

        <div class="input-group">
            <input type="password" name="password" placeholder="Password" required>
            <span>🔒</span>
        </div>

        <button type="submit" name="login">Sign In</button>
    </form>

</div>

</body>
</html>
