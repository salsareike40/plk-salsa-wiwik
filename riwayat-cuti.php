<?php
session_start();
include "conn.php";

if(!isset($_SESSION['username'])){
    header("Location: index.php");
    exit;
}

$username = $_SESSION['username'];

/* ambil data pegawai */
$qUser = mysqli_query($conn,"
SELECT nip,nama_pegawai
FROM pegawai
WHERE username='$username'
");

$user = mysqli_fetch_assoc($qUser);
$nipLogin = $user['nip'];
$status = $_GET['status'] ?? '';
$cari = $_GET['cari'] ?? '';

$where = "WHERE nip='$nipLogin' AND status IN ('Disetujui','Ditolak')";

if($status != ''){
    $where .= " AND status='$status'";
}

if($cari != ''){
    $where .= " AND jenis_cuti LIKE '%$cari%'";
}

$qCuti = mysqli_query($conn,"
SELECT id,jenis_cuti,tgl_pengajuan,jumlah_hari,status
FROM cuti
$where
ORDER BY tgl_pengajuan DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Riwayat Cuti</title>

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Inter',sans-serif;
}

body{
background:#eef4fb;
display:flex;
min-height:100vh;
}

/* HEADER */
.header{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:30px;
}

.header h1{
font-size:24px;
color:#0b5aa6;
}

.user{
font-size:14px;
}

.user a{
color:#0b5aa6;
text-decoration:none;
margin-left:10px;
}

/* BOX */
.riwayat-box{
background:#fff;
padding:30px;
border-radius:18px;
box-shadow:0 10px 25px rgba(0,0,0,0.07);
}

.riwayat-box h3{
margin-bottom:20px;
font-size:18px;
}



/* HOVER */
.riwayat-table tbody tr:hover{
background:#f8fafc;
}

/* STATUS BADGE */
.badge{
padding:6px 14px;
border-radius:20px;
font-size:12px;
font-weight:600;
}

.ok{
background:#d1fae5;
color:#047857;
}

.no{
background:#fee2e2;
color:#b91c1c;
}

.wait{
background:#fef3c7;
color:#92400e;
}

/* EMPTY */
.empty{
    text-align:center;
    padding:30px;
    color:#94a3b8;
}
.sidebar{
width:260px;
background:#0b5aa6;
color:#fff;
padding:30px 20px;
}

.logo{
text-align:center;
margin-bottom:40px;
}

.logo img{
width:150px;
margin-bottom:15px;
}

.logo h2{
font-size:20px;
font-weight:600;
line-height:1.4;
}

.menu a{
display:flex;
align-items:center;
gap:12px;
padding:14px 18px;
margin-bottom:10px;
border-radius:10px;
color:#fff;
text-decoration:none;
font-weight:500;
}

.menu a.active,
.menu a:hover{
background:#0a4c8c;
}

.main{
flex:1;
padding:30px;
}
/* HEADER ATAS */
.top-header{
    background:#ffffff;
    padding:18px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;

    margin:-30px -30px 30px -30px;

    box-shadow:0 2px 6px rgba(0,0,0,0.08);
}

.title{
    font-size:22px;
    font-weight:600;
    color:#0b5aa6;
}

.user-area{
    display:flex;
    align-items:center;
    gap:8px;
    font-size:14px;
}

.user-area a{
    color:#0b5aa6;
    text-decoration:none;
    font-weight:500;
}

.divider{
    color:#aaa;
}
/* TABLE */
table{
width:100%;
border-collapse:collapse;
background:#fff;
border-radius:16px;
overflow:hidden;
}

/* HEADER */
thead{
background:#0b5aa6;
}

thead th{
color:#fff;
padding:16px 14px;
font-size:14px;
font-weight:600;
text-align:center;
}

/* BODY */
tbody td{
padding:16px 14px;
font-size:14px;
color:#333;
text-align:center;
border-bottom:1px solid #eef2f7;
}

tbody tr:last-child td{
border-bottom:none;
}

tbody tr:hover{
background:#f6f9ff;
}

/* BOX RIWAYAT */
.riwayat-box{
background:#fff;
padding:30px;
border-radius:20px;
box-shadow:0 12px 30px rgba(0,0,0,0.08);
margin-top:10px;
}

/* TABLE */
.riwayat-table{
width:100%;
border-collapse:separate;
border-spacing:0;
table-layout:fixed;
}

/* HEADER */
.riwayat-table thead{
background:#f8fafc;
}

.riwayat-table th{
text-align:left;
padding:18px 24px;
font-size:14px;
font-weight:600;
color:#64748b;
border-bottom:2px solid #e5e7eb;
}

/* BODY */
.riwayat-table td{
padding:18px 24px;
font-size:14px;
border-bottom:1px solid #f1f5f9;
color:#334155;
}

/* HOVER EFFECT */
.riwayat-table tbody tr{
transition:0.2s;
}

.riwayat-table tbody tr:hover{
background:#f8fafc;
transform:scale(1.002);
}

/* KOLOM */
.riwayat-table th:nth-child(1),
.riwayat-table td:nth-child(1){
width:40%;
}

.riwayat-table th:nth-child(2),
.riwayat-table td:nth-child(2){
width:25%;
}

.riwayat-table th:nth-child(3),
.riwayat-table td:nth-child(3){
width:15%;
text-align:center;
}

.riwayat-table th:nth-child(4),
.riwayat-table td:nth-child(4){
width:20%;
text-align:center;
}

/* STATUS BADGE */
.badge{
padding:6px 16px;
border-radius:20px;
font-size:12px;
font-weight:600;
display:inline-block;
}

/* STATUS WARNA */
.ok{
background:#d1fae5;
color:#047857;
}

.no{
background:#fee2e2;
color:#b91c1c;
}

.wait{
background:#fef3c7;
color:#92400e;
}

/* EMPTY */
.empty{
text-align:center;
padding:30px;
color:#94a3b8;
}
/* FILTER BAR */
.filter-bar{
display:flex;
align-items:center;
gap:10px;
margin-bottom:18px;
}

.filter-select{
padding:10px 14px;
border-radius:10px;
border:1px solid #dce3f1;
background:#ffffff;
font-size:14px;
font-weight:500;
color:#333;
cursor:pointer;
}

.search-input{
flex:1;
padding:12px 16px;
border-radius:14px;
border:1px solid #dce3f1;
background:#eef2f7;
font-size:14px;
}

.search-input:focus{
outline:none;
border-color:#0b5aa6;
background:#fff;
box-shadow:0 0 0 3px rgba(11,90,166,.15);
}
</style>
</head>

<body>
<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo">
        <img src="aset/kominfo.png" alt="">
        <h2>SICUTI</h2>
    </div>

    <div class="menu">
        <a href="dashboard.php" class="active">📊 Dashboard</a>
        <a href="cuti.php">🗓️ Cuti</a>
        <a href="status-pengajuan.php">⚠️ Status Pengajuan</a>
        <a href="riwayat-cuti.php">⚠️ Riwayat Cuti</a>

    </div>
</div>

<!-- MAIN -->
<div class="main">
<div class="top-header">

    <div class="title">
        Riwayat Cuti
    </div>

    <div class="user-area">
        <span class="icon">👤</span>
        <span class="name"><?= $user['nama_pegawai'] ?> (<?= $user['nip'] ?>)</span>
        <span class="divider">|</span>
        <a href="logout.php">Logout</a>
    </div>

</div>



<div class="riwayat-box">

<div class="filter-bar">

<form method="GET" style="display:flex;align-items:center;gap:12px;width:100%">

<select name="status" onchange="this.form.submit()" class="filter-select">
<option value="">Semua</option>
<option value="Disetujui" <?= ($_GET['status'] ?? '')=='Disetujui'?'selected':'' ?>>Disetujui</option>
<option value="Ditolak" <?= ($_GET['status'] ?? '')=='Ditolak'?'selected':'' ?>>Ditolak</option>
</select>

<input
type="text"
name="cari"
placeholder="Cari jenis cuti..."
value="<?= $_GET['cari'] ?? '' ?>"
class="search-input"
>

</form>

</div>

<table>
<thead>
<tr>
<th>No</th>
<th>Jenis Cuti</th>
<th>Tanggal Pengajuan</th>
<th>Hari</th>
<th>Status</th>
<th>Detail</th>
</tr>
</thead>

<tbody>

<?php if(mysqli_num_rows($qCuti)==0): ?>
<tr>
<td colspan="6" class="empty">Belum ada riwayat cuti</td>
</tr>
<?php endif; ?>

<?php $no=1; while($row=mysqli_fetch_assoc($qCuti)): ?>

<tr>

<td><?= $no++ ?></td>

<td><?= $row['jenis_cuti'] ?></td>

<td><?= date('d M Y',strtotime($row['tgl_pengajuan'])) ?></td>

<td><?= $row['jumlah_hari'] ?> Hari</td>

<td>

<?php
if($row['status']=="Disetujui"){
echo '<span class="badge ok">Disetujui</span>';
}
elseif($row['status']=="Ditolak"){
echo '<span class="badge no">Ditolak</span>';
}
else{
echo '<span class="badge wait">Menunggu</span>';
}
?>
</td>


<td>
<a href="javascript:void(0)"
style="background:#22a6a1;color:white;padding:6px 14px;border-radius:6px;text-decoration:none"
onclick="openDetail(<?= $row['id'] ?>)">
👁 Detail
</a>
</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

<div class="modal-overlay" id="modalDetail"
style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);
align-items:center;justify-content:center;z-index:9999">

<div style="background:#f2f4f8;width:650px;border-radius:16px;
box-shadow:0 20px 40px rgba(0,0,0,.25)">

<div style="padding:16px 20px;display:flex;
justify-content:space-between;align-items:center">

<h3>Detail Pengajuan Cuti</h3>

<span style="cursor:pointer" onclick="closeDetail()">✕</span>

</div>

<div style="padding:20px" id="detailContent">
Loading...
</div>

</div>
</div>

<script>

function openDetail(id){

document.getElementById('modalDetail').style.display = 'flex';

document.getElementById('detailContent').innerHTML = 'Loading...';

fetch('detail-riwayat.php?id=' + id)
.then(res => res.text())
.then(data => {
document.getElementById('detailContent').innerHTML = data;
});

}

function closeDetail(){
document.getElementById('modalDetail').style.display = 'none';
}

</script>

</body>
</html>