<?php
session_start();
include "conn.php";

if(isset($_POST['ganti'])){

$password1 = $_POST['password1'];
$password2 = $_POST['password2'];

if($password1 != $password2){

echo "Password tidak sama";

}else{

$hash = password_hash($password1,PASSWORD_DEFAULT);

mysqli_query($conn,"
UPDATE pegawai
SET password='$hash'
WHERE nip='".$_SESSION['nip']."'
");

header("location:dashboard.php");

}

}
?>

<form method="post">

Password Baru
<input type="password" name="password1">

Konfirmasi Password
<input type="password" name="password2">

<button name="ganti">Ganti Password</button>

</form>