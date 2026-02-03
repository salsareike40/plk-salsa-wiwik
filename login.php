<?php
session_start();
include "conn.php";

$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM pegawai WHERE username='$username'");

    if ($query && mysqli_num_rows($query) == 1) {
        $user = mysqli_fetch_assoc($query);
        echo "<pre>";
        var_dump($user);
        echo "</pre>";
        exit;


        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
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

</html>
