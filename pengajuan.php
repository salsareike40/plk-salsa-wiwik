<?php
session_start();
include "conn.php";

if(!isset($_SESSION['role'])){
    header("Location: index.php");
    exit;
}

$username = $_SESSION['username'];

// CONTOH QUERY (sesuaikan dengan tabel cuti kamu)
$username = $_SESSION['username'];

$q = $_GET['q'] ?? '';

$where = "";
if ($q != '') {
    $q = mysqli_real_escape_string($conn, $q);
    $where = "WHERE 
        cuti.username LIKE '%$q%' 
        OR cuti.jenis_cuti LIKE '%$q%'
        OR cuti.status LIKE '%$q%'
        OR cuti.tgl_mulai LIKE '%$q%'
        OR cuti.tgl_selesai LIKE '%$q%'";
}


$query = mysqli_query($conn,"
    SELECT 
        cuti.id,
        cuti.username,
        cuti.jenis_cuti,
        cuti.tgl_mulai,
        cuti.tgl_selesai,
        cuti.status,
        pegawai.jabatan,
        pegawai.unit_kerja
    FROM cuti
    JOIN pegawai ON cuti.username = pegawai.username
    $where
    ORDER BY cuti.tgl_pengajuan ASC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pengajuan Cuti</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Inter',sans-serif;
}

body{
    display:flex;
    min-height:100vh;
    background:#eef3fb;
}

/* ===== SIDEBAR ===== */
.sidebar{
    width:260px;
    background:#0b57a4;
    color:#fff;
    padding:30px 24px;
}
.logo{
    text-align:center;
    margin-bottom:40px;
}
.logo img{
    width:120px;
    margin-bottom:16px;
}
.logo h2{
    font-size:20px;
    font-weight:700;
    line-height:1.4;
}

.menu{
    display:flex;
    flex-direction:column;
    gap:18px;
}
.menu a{
    display:flex;
    align-items:center;
    gap:12px;
    padding:12px 18px;
    border-radius:10px;
    color:#fff;
    text-decoration:none;
    font-weight:500;
}
.menu a.active{
    background:#eaf2ff;
    color:#0b57a4;
}

/* ===== MAIN ===== */
.main{
    flex:1;
}

/* ===== HEADER ===== */
.header{
    background:#ffffff;
    padding:20px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 3px 6px rgba(0,0,0,.08);
}
.header h1{
    font-size:26px;
    font-weight:700;
    color:#0b57a4;
}
.user{
    display:flex;
    align-items:center;
    gap:10px;
    font-weight:500;
}
.user a{
    color:#0b57a4;
    text-decoration:none;
    font-weight:600;
}

/* ===== CONTENT ===== */
.content{
    padding:30px;
}

/* SEARCH */
.search{
    margin-bottom:20px;
}
.search input{
    width:380px;
    padding:12px 18px;
    border-radius:20px;
    border:none;
    background:#e2e6ef;
    font-size:14px;
}

/* ===== TABLE ===== */
.table-wrapper{
    background:linear-gradient(145deg,#f1f4fb,#ffffff);
    border-radius:18px;
    padding:18px;
}

table{
    width:100%;
    border-collapse:separate;
    border-spacing:0 8px;
}

thead{
    background:#1f5fa5;
}

thead th{
    padding:16px;
    color:white;
    font-weight:600;
    text-align:left;
}

/* supaya header seperti kapsul */
thead th:first-child{
    border-top-left-radius:14px;
    border-bottom-left-radius:14px;
}

thead th:last-child{
    border-top-right-radius:14px;
    border-bottom-right-radius:14px;
}

tbody td{
    padding:14px;
    color:#444;
}

tbody tr{
    border-bottom:1px solid #e4e8f1;
}

tbody tr:last-child{
    border-bottom:none;
}

/* CENTER KOLOM */
table th,
table td{
    vertical-align:middle;
}
table th:nth-child(1),
table th:nth-child(6),
table td:nth-child(1),
table td:nth-child(6){
    text-align:center;
}

/* STATUS BADGE */
.status{
    padding:6px 14px;
    border-radius:8px;
    font-size:13px;
    font-weight:600;
    display:inline-block;
}
.approved{
    background:#5aa469;
    color:#fff;
}
.rejected{
    background:#d16a6a;
    color:#fff;
}

/* BUTTON DETAIL */
.btn-detail{
    background:#4f79bd;
    color:#fff;
    padding:8px 18px;
    border-radius:10px;
    text-decoration:none;
    font-size:14px;
    font-weight:600;
}
.pending{
    background:#facc15;
    color:#7a5200;
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo">
        <img src="aset/kominfo.png">
        <h2>Sistem Cuti<br>Dinas Kominfo Kota</h2>
    </div>

   <?php $page = basename($_SERVER['PHP_SELF']); ?>

<div class="menu">
    <a href="dashboard.php" class="<?= $page=='dashboard.php'?'active':'' ?>">
        📊 Dashboard
    </a>

    <a href="data-pegawai.php" class="<?= $page=='data-pegawai.php'?'active':'' ?>">
        🧑‍💼 Data Pegawai
    </a>

    <a href="pengajuan.php" class="<?= $page=='pengajuan.php'?'active':'' ?>">
        📑 Pengajuan Cuti
    </a>

    <a href="ad-sanggah.php" class="<?= $page=='ad-sanggah.php'?'active':'' ?>">
        ⚠️ Status Sanggahan
    </a>
</div>

</div>

<!-- MAIN -->
<div class="main">

    <!-- HEADER -->
    <div class="header">
        <h1>Pengajuan Cuti</h1>
        <div class="user">
            👤 <?= $username ?> | <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <div class="search">
          <input
            type="text"
            name="q"
            placeholder="Cari..."
            value="<?= $_GET['q'] ?? '' ?>"
            onkeyup="doSearch(this.value)">
        </div>

<script>
let typingTimer;

function doSearch(val){
    clearTimeout(typingTimer);
    typingTimer = setTimeout(function(){
        window.location = 'pengajuan.php?q=' + encodeURIComponent(val);
    }, 600); // tunggu user selesai ngetik
}
</script>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Jenis Cuti</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php $no=1; while($row=mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['username'] ?></td>
                        <td><?= $row['jenis_cuti'] ?></td>
                        <td>
                            <?= date('d M Y',strtotime($row['tgl_mulai'])) ?>
                            –
                            <?= date('d M Y',strtotime($row['tgl_selesai'])) ?>
                        </td>
                        <td>
                            <?php if($row['status'] == 'Menunggu'): ?>
                                <span class="status pending">Menunggu</span>

                            <?php elseif($row['status'] == 'Disetujui'): ?>
                                <span class="status approved">Disetujui</span>

                            <?php else: ?>
                                <span class="status rejected">Ditolak</span>
                            <?php endif; ?>
                        </td>
                        <td>
                           <a href="javascript:void(0)"
                                class="btn-detail"
                                onclick="openDetail(<?= $row['id'] ?>)">
                                👁 Detail
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<div class="modal-overlay" id="modalDetail"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);
            align-items:center;justify-content:center;z-index:9999">

    <div style="background:#f2f4f8;width:720px;border-radius:18px;
                box-shadow:0 20px 40px rgba(0,0,0,.25)">
        
        <div style="padding:18px 22px;display:flex;
                    justify-content:space-between;align-items:center">
            <h3>Detail Pengajuan Cuti</h3>
            <span style="cursor:pointer" onclick="closeDetail()">✕</span>
        </div>

        <div style="padding:22px" id="detailContent">
            Loading...
        </div>
    </div>
</div>

<script>
function openDetail(id){
    document.getElementById('modalDetail').style.display = 'flex';
    document.getElementById('detailContent').innerHTML = 'Loading...';

    fetch('ad-readonly.php?id=' + id)
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
