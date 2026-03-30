<?php
include "conn.php";

$message = "";
$error = "";

if(isset($_POST['reset'])){

$nip = $_POST['nip'];
$password = $_POST['password'];
$confirm = $_POST['confirm'];

if($password != $confirm){

$error = "Password dan konfirmasi password tidak sama!";

}else{

$hash = password_hash($password,PASSWORD_DEFAULT);

mysqli_query($conn,"
UPDATE pegawai
SET password='$hash'
WHERE nip='$nip'
");

$message = "Password berhasil diganti. Silakan login.";
}

}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Reset Password</title>

<style>

body{
margin:0;
height:100vh;
font-family:Arial, sans-serif;
background:#8fb6de;
display:flex;
justify-content:center;
align-items:center;
}

.box{
width:420px;
background:#E5EBFA;
border-radius:25px;
padding:40px;
text-align:center;
box-shadow:0 10px 25px rgba(0,0,0,0.2);
}

.logo img{
width:120px;
margin-bottom:10px;
}

.logo p{
font-size:12px;
margin-bottom:25px;
color:#444;
}

.form-group{
text-align:left;
margin-bottom:20px;
}

.form-group label{
font-size:13px;
}

.form-group input{
width:100%;
padding:12px;
border-radius:25px;
border:1px solid #777;
margin-top:6px;
box-sizing:border-box;
font-size:14px;
}

.form-group input:focus{
border-color:#0b5ed7;
}

.btn{
margin-top:15px;
width:160px;
padding:10px;
background:#0b5ed7;
border:none;
border-radius:25px;
color:white;
font-size:14px;
cursor:pointer;
}

.btn:hover{
background:#084298;
}

.message{
background:#d1e7dd;
color:#0f5132;
padding:10px;
border-radius:10px;
margin-bottom:15px;
font-size:13px;
}

.error{
background:#f8d7da;
color:#842029;
padding:10px;
border-radius:10px;
margin-bottom:15px;
font-size:13px;
}

.back{
margin-top:20px;
font-size:12px;
}

.back a{
color:#0b5ed7;
text-decoration:none;
font-weight:bold;
}

</style>
</head>

<body>

<div class="box">

<div class="logo">
<img src="aset/logo-kominfo.jpeg">
<p>Kementerian Komunikasi dan Informatika</p>
</div>

<?php if($message!=""){ ?>
<div class="message"><?= $message ?></div>
<?php } ?>

<?php if($error!=""){ ?>
<div class="error"><?= $error ?></div>
<?php } ?>

<form method="post">

<div class="form-group">
<label>Masukkan NIP</label>
<input type="text" name="nip" required>
</div>

<div class="form-group">
<label>Password Baru</label>
<input type="password" name="password" required>
</div>

<div class="form-group">
<label>Konfirmasi Password</label>
<input type="password" name="confirm" required>
</div>

<button class="btn" name="reset">Proses</button>

</form>

<div class="back">
Kembali ke <a href="index.php">Login</a>
</div>

</div>

</body>
</html>