<?php
session_start();
include "conn.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
    header("Location: data-pegawai.php");
    exit;
}

$nip   = mysqli_real_escape_string($conn, $_POST['nip']);
$nama  = mysqli_real_escape_string($conn, $_POST['nama']);
$jab   = mysqli_real_escape_string($conn, $_POST['jabatan']);
$unit  = mysqli_real_escape_string($conn, $_POST['unit_kerja']);

if($nip == '' || $nama == '' || $jab == '' || $unit == ''){
    $_SESSION['error'] = "Semua field wajib diisi!";
    header("Location: data-pegawai.php");
    exit;
}

$username = $nip; 
$password = ""; // password belum dibuat
$role = "user";
$reset = 1; // wajib buat password pertama kali

// CEK NIP SUDAH ADA ATAU BELUM
$cek = mysqli_query($conn,"SELECT nip FROM pegawai WHERE nip='$nip'");

if(mysqli_num_rows($cek) > 0){

    $_SESSION['error'] = "NIP sudah terdaftar!";
    header("Location: data-pegawai.php");
    exit;

}

// INSERT DATA + AKUN LOGIN
$insert = mysqli_query($conn,"
INSERT INTO pegawai (
nip,
nama_pegawai,
jabatan,
unit_kerja,
username,
password,
role,
reset_pass,
status
) VALUES (
'$nip',
'$nama',
'$jab',
'$unit',
'$username',
'$password',
'$role',
'$reset',
'aktif'
)
");

if($insert){
    $_SESSION['success'] = "Pegawai berhasil ditambahkan!";
}else{
    $_SESSION['error'] = "Gagal menambahkan pegawai!";
}

header("Location: data-pegawai.php");
exit;
?>