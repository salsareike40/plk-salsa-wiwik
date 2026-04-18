<?php
session_start();
include "conn.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

// 🔴 1. Ambil NIP
$data = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT nip FROM pegawai WHERE id_pegawai=$id
"));

if(!$data){
    $_SESSION['error'] = "Data pegawai tidak ditemukan!";
    header("Location: data-pegawai.php");
    exit;
}

$nip = $data['nip'];

// 🔴 2. Hapus semua cuti
mysqli_query($conn,"DELETE FROM cuti WHERE nip='$nip'");

// 🔴 3. Nonaktifkan pegawai
mysqli_query($conn,"DELETE FROM pegawai WHERE id_pegawai=$id");

$_SESSION['success'] = "Pegawai dan data cuti berhasil dihapus!";
header("Location: data-pegawai.php");
exit;
?>