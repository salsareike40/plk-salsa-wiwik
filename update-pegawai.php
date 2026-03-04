<?php
include "conn.php";

$id         = $_POST['id_pegawai'];
$nip        = $_POST['nip'];
$nama       = $_POST['nama_pegawai'];
$jabatan    = $_POST['jabatan'];
$unit_kerja = $_POST['unit_kerja'];

mysqli_query($conn, "
    UPDATE pegawai SET
        nip='$nip',
        username='$nama',
        jabatan='$jabatan',
        unit_kerja='$unit_kerja'
    WHERE id_pegawai='$id'
");

header("Location: data-pegawai.php");
exit;