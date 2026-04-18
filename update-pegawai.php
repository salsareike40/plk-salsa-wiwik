<?php
include "conn.php";
session_start();

$id         = $_POST['id_pegawai'];
$nip        = $_POST['nip'];
$nama       = $_POST['nama_pegawai'];
$jabatan    = $_POST['jabatan'];
$unit_kerja = $_POST['unit_kerja'];

// 🔴 CEK DUPLIKAT NIP
$cek = mysqli_query($conn, "
    SELECT * FROM pegawai 
    WHERE nip='$nip' AND id_pegawai != '$id'
");

if(mysqli_num_rows($cek) > 0){
    $_SESSION['error'] = "❌ NIP sudah ada dalam daftar pegawai!";
    header("Location: data-pegawai.php");
    exit;
}

// ✅ UPDATE DATA
mysqli_query($conn, "
    UPDATE pegawai SET
        nip='$nip',
        nama_pegawai='$nama',
        jabatan='$jabatan',
        unit_kerja='$unit_kerja'
    WHERE id_pegawai='$id'
");

$_SESSION['success'] = "✅ Data pegawai berhasil diperbarui!";
header("Location: data-pegawai.php");
exit;
?>