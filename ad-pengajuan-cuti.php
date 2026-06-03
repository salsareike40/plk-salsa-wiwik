<?php
session_start();
include "conn.php";

if(!isset($_SESSION['role'])){
    header("Location: index.php");
    exit;
}

// CONTOH QUERY (sesuaikan dengan tabel cuti kamu)
$username = $_SESSION['username'];

$q = $_GET['q'] ?? '';
$status = $_GET['status'] ?? '';

$where = "WHERE cuti.status IN ('Disetujui','Ditolak')";

if($status != ''){
    $status = mysqli_real_escape_string($conn,$status);
    $where .= " AND cuti.status='$status'";
}

if ($q != '') {
    $q = mysqli_real_escape_string($conn, $q);
    $where .= " AND (
        pegawai.nama_pegawai LIKE '%$q%' 
        OR cuti.jenis_cuti LIKE '%$q%'
    )";
}


$query = mysqli_query($conn,"
SELECT 
cuti.id,
cuti.nip,
cuti.jenis_cuti,
cuti.tgl_mulai,
cuti.tgl_selesai,
cuti.status,
pegawai.nama_pegawai,
pegawai.jabatan,
pegawai.unit_kerja
FROM cuti
LEFT JOIN pegawai ON cuti.nip = pegawai.nip
$where
ORDER BY cuti.id DESC
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
    gap:26px; /* 🔥 ini bikin jarak renggang */
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
    transition:0.2s;
}

/* 🔥 ACTIVE (PUTIH) */
.menu a.active{
    background:#eaf2ff;
    color:#0b57a4;
    font-weight:600;
}

/* 🔥 HOVER (JANGAN TIMPA ACTIVE) */
.menu a:hover:not(.active){
    background:#0a4c8c;
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

.table-card{
    background:#f7f9fd;
    border-radius:18px;
    padding:18px; /* 🔥 INI KUNCINYA */
}

table{
    width:100%;
    border-radius:14px;
    border-collapse:collapse;
    background:#ffffff;
    overflow:hidden;
    box-shadow:0 10px 20px rgba(0,0,0,.08);
}
thead{
    background:#0b5aa6;
}

thead th{
    color:#ffffff;
    font-weight:600;
    padding:18px 16px;
}

/* rounded ujung */
thead th:first-child{
    border-top-left-radius:12px;
}
thead th:last-child{
    border-top-right-radius:12px;
}

tbody tr{
    border-bottom:1px solid #edf1f7;
}

tbody tr:last-child{
    border-bottom:none;
}

tbody td{
    padding:16px;
    text-align:center;
    color:#444;
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
    padding:4px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

.approved{
    background:#5cb85c;
    color:#fff;
}

.rejected{
    background:#d9534f;
    color:#fff;
}

/* BUTTON DETAIL */
.btn-detail{
    background:#4f79bd;
    color:#fff;
    padding:8px 16px;
    border-radius:10px;
    text-decoration:none;
    font-weight:600;
    font-size:14px;
    display:inline-flex;
    align-items:center;
    gap:6px;
}
.pending{
    background:#facc15;
    color:#7a5200;
}
.table-body{
    padding:18px;
}
.table-card{
    background:#f7f9fd;
    border-radius:14px;
    overflow:hidden; /* penting */
}

.table-body{
    padding:18px;
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo">
        <img src="aset/kominfo.png">
        <h2>SICUTI</h2>
    </div>
<?php $page = basename($_SERVER['PHP_SELF']); ?>
<div class="menu">
    <a href="ad-dashboard.php" class="<?= $page=='ad-dashboard.php'?'active':'' ?>">
        📊 Dashboard
    </a>

    <a href="data-pegawai.php" class="<?= $page=='data-pegawai.php'?'active':'' ?>">
        🧑‍💼 Data Pegawai
    </a>

    <a href="ad-pengajuan-cuti.php" class="<?= $page=='ad-pengajuan-cuti.php'?'active':'' ?>">
        📑 Pengajuan Cuti
    </a>

    <a href="ad-statusajuan.php" class="<?= $page=='ad-statusajuan.php'?'active':'' ?>">
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

      <div style="display:flex;gap:12px;margin-bottom:20px">

<form method="GET" style="display:flex;gap:12px">

<select 
name="status" 
onchange="this.form.submit()" 

style="
padding:10px 14px;
border-radius:10px;
border:1px solid #ccc;
background:#fff;
color:#000;
">

<option value="">Semua Status</option>

<option value="Disetujui" <?= ($_GET['status'] ?? '')=='Disetujui'?'selected':'' ?>>
Disetujui
</option>

<option value="Ditolak" <?= ($_GET['status'] ?? '')=='Ditolak'?'selected':'' ?>>
Ditolak
</option>

</select>

<input
type="text"
name="q"
placeholder="Cari nama / jenis cuti..."
value="<?= $_GET['q'] ?? '' ?>"
style="padding:10px 16px;border-radius:20px;border:1px solid #ccc;width:260px"
>

<button type="submit" style="padding:10px 16px;border:none;background:#0b57a4;color:white;border-radius:10px">
Cari
</button>

</form>

</div>

<script>
let typingTimer;

function doSearch(val){
    clearTimeout(typingTimer);
    typingTimer = setTimeout(function(){
        window.location = 'ad-pengajuan-cuti.php?q=' + encodeURIComponent(val);
    }, 600); // tunggu user selesai ngetik
}
</script>

        <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Jenis Cuti</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                <tbody>
                <?php if(mysqli_num_rows($query) > 0): ?>
                    <?php $no=1; while($row=mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['nip'] ?></td>
                        <td><?= $row['nama_pegawai'] ?></td>
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
                <?php else: ?>

                <tr>
                    <td colspan="7" style="
                        padding:40px;
                        text-align:center;
                        color:#777;
                        font-size:15px;
                    ">
                        Belum ada data pengajuan cuti
                    </td>
                </tr>

                <?php endif; ?>
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

    fetch('ad-detail-pengajuan.php?id=' + id)
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