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

$cek = mysqli_query($conn,"
    SELECT * FROM pegawai 
    WHERE nip='$nip'
");

$data = mysqli_fetch_assoc($cek);

if($data){

    // ❌ kalau masih aktif → tolak
    if($data['status'] == 'aktif'){
        $_SESSION['error'] = "NIP sudah terdaftar!";
        header("Location: data-pegawai.php");
        exit;
    }

    // 🔄 kalau nonaktif → aktifkan kembali
    mysqli_query($conn,"
        UPDATE pegawai SET
            nama_pegawai='$nama',
            jabatan='$jab',
            unit_kerja='$unit',
            status='aktif'
        WHERE nip='$nip'
    ");

    $_SESSION['success'] = "Pegawai berhasil diaktifkan kembali!";
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