<?php
include "conn.php";

$id        = $_POST['id'];
$catatan   = $_POST['catatan'];
$keputusan = $_POST['keputusan'];

if($keputusan == 'Setuju'){
    $status = 'Disetujui';
}else{
    $status = 'Ditolak';
}

mysqli_query($conn,"
    UPDATE cuti SET
        status='$status',
        catatan='$catatan'
    WHERE id='$id'
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Proses Cuti</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
<meta http-equiv="refresh" content="2;url=ad-sanggah.php">
<style>
body{
    background:#eef4fb;
    display:flex;
    align-items:center;
    justify-content:center;
    height:100vh;
    font-family:'Inter',sans-serif;
}
.card{
    background:#fff;
    padding:40px 46px;
    border-radius:18px;
    box-shadow:0 20px 40px rgba(0,0,0,.15);
    text-align:center;
    animation:pop .3s ease;
}
@keyframes pop{
    from{transform:scale(.9);opacity:0}
    to{transform:scale(1);opacity:1}
}
.icon{
    font-size:46px;
    margin-bottom:12px;
}
h2{
    color:#0b5aa6;
    margin-bottom:10px;
}
p{
    color:#555;
    font-size:14px;
}
</style>
</head>

<body>
<div class="card">
    <div class="icon"><?= ($status=='Disetujui') ? '✅' : '❌' ?></div>
    <h2><?= $status ?></h2>
    <p>Pengajuan cuti berhasil diproses.</p>
    <p>Anda akan diarahkan kembali...</p>
</div>
</body>
</html>