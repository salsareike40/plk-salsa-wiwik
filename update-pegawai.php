<?php
include "conn.php";

mysqli_query($conn,"
    UPDATE pegawai SET
        nip='$_POST[nip]',
        nama_pegawai='$_POST[nama_pegawai]',
        jabatan='$_POST[jabatan]',
        unit_kerja='$_POST[unit_kerja]',
        sisa_cuti='$_POST[sisa_cuti]'
    WHERE id_pegawai='$_POST[id_pegawai]'
");

header("Location: data-pegawai.php");
