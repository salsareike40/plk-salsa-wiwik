<?php
session_start();
include "conn.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

mysqli_query($conn,"
    UPDATE pegawai
    SET status='nonaktif'
    WHERE id_pegawai=$id
");

header("Location: data-pegawai.php");
exit;
