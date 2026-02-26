<?php
session_start();
include "conn.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
    header("Location: data-pegawai.php");
    exit;
}

$nip   = $_POST['nip'];
$nama  = $_POST['nama'];
$jab   = $_POST['jabatan'];
$unit  = $_POST['unit_kerja'];

mysqli_query($conn,"
    INSERT INTO pegawai (nip, nama_pegawai, jabatan, unit_kerja, status)
    VALUES ('$nip','$nama','$jab','$unit','aktif')
");

header("Location: data-pegawai.php");
exit;
