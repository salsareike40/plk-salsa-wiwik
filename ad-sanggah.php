<?php
session_start();
include "conn.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
    header("Location: ad-dashboard.php");
    exit;
}


$username = $_SESSION['username'];

$query = mysqli_query($conn,"
    SELECT * FROM cuti
    WHERE status='Menunggu'
    ORDER BY tgl_pengajuan DESC
");

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Status Pengajuan</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

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
    height:100vh;
}

/* ================= SIDEBAR ================= */
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


/* MAIN */
.main{
    flex:1;
    padding:30px;
    overflow:auto;
}

/* TOPBAR */
.topbar{
    display:flex;
    justify-content:flex-end;
    align-items:center;
    margin-bottom:25px;
}
.user{
    font-weight:500;
}



/* BOX */
.box{
    background:#fff;
    padding:25px;
    border-radius:18px;
    box-shadow:0 10px 20px rgba(0,0,0,.08);
}

/* TABLE */
table{
    width:100%;
    border-collapse:separate;
    border-spacing:0 10px; /* ⬅️ HANYA BODY YANG BERJARAK */
    table-layout:fixed;
}
/* ===== FIX JARAK KOLOM TABEL ===== */
.box th:nth-child(1),
.box td:nth-child(1){
    width:60px;
    text-align:center;
}

.box th:nth-child(2),
.box td:nth-child(2){
    width:160px;
}

.box th:nth-child(3),
.box td:nth-child(3){
    width:120px;
}

.box th:nth-child(4),
.box td:nth-child(4){
    width:180px;
}

.box th:nth-child(5),
.box td:nth-child(5){
    width:260px;
}

.box th:nth-child(6),
.box td:nth-child(6){
    width:140px;
    text-align:center;
}

.box th:nth-child(7),
.box td:nth-child(7){
    width:140px;
    text-align:center;
}

th{
    text-align:center; /* ✅ */
    font-size:13px;
    color:#777;
    padding-bottom:12px;
}
td{
    padding:12px 12px;
    font-size:14px;
    border-top:1px solid #eef2f7;
    text-align:center; /* ✅ */
}

/* BADGE */
.badge{
    padding:6px 16px;
    border-radius:999px;
    font-size:12px;
    font-weight:600;
    display:inline-block;
}

.wait{
    background:#fff1cc;
    color:#b26a00;
}

.ok{
    background:#d8f5e3;
    color:#0b7a3e;
}

.no{
    background:#ffe0e0;
    color:#b00000;
}
/* ===== HEADER PAGE FULL WIDTH ===== */
.page-header{
    background:#ffffff;
    padding:18px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;

    /* tarik keluar dari padding .main */
    margin:-30px -30px 30px -30px;

    box-shadow:0 2px 6px rgba(0,0,0,0.08);
}

.page-header h1{
    font-size:22px;
    font-weight:600;
    color:#0b5aa6;
}

/* USER AREA */
.header-user{
    display:flex;
    align-items:center;
    gap:8px;
    font-size:14px;
    color:#333;
}

.header-user a{
    color:#0b5aa6;
    text-decoration:none;
    font-weight:500;
}

.divider{
    color:#aaa;
}
.btn-detail{
    background:#5678b8;
    color:#fff;
    padding:10px 22px;
    border-radius:10px;
    text-decoration:none;
    font-size:14px;
    font-weight:600;
    display:inline-flex;
    align-items:center;
    gap:6px;
}

.btn-detail:hover{
    background:#1e8f8b;
}
.modal-overlay{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.45);
    display:none;
    align-items:center;
    justify-content:center;
    z-index:9999;
}

.modal-box{
    background:#f2f4f8;
    width:720px;
    border-radius:18px;
    box-shadow:0 20px 40px rgba(0,0,0,.25);
    animation:pop .25s ease;
}

@keyframes pop{
    from{transform:scale(.9);opacity:0}
    to{transform:scale(1);opacity:1}
}

.modal-header{
    padding:18px 22px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-bottom:1px solid #e0e6ef;
}

.modal-header h3{
    font-size:18px;
    color:#1f3b6d;
}

.modal-header .close{
    cursor:pointer;
    font-size:20px;
}

.modal-body{
    padding:22px;
}

.modal-footer{
    padding:16px;
    text-align:right;
}

.btn-ok{
    background:#0b5aa6;
    color:#fff;
    border:none;
    padding:8px 26px;
    border-radius:10px;
    font-weight:600;
    cursor:pointer;
}
.box thead th{
    background:transparent;
    color:#fff;
    text-align:center;
    font-weight:600;
    padding:16px 12px;
    border-radius:0; /* ⬅️ penting */
}
.box thead th:first-child{
    border-radius:14px 0 0 14px;
}

.box thead th:last-child{
    border-radius:0 14px 14px 0;
}
.box thead{
    border-spacing:0;
}
.box thead tr{
    background:#0b5aa6;
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo">
        <img src="aset/kominfo.png" alt="Kominfo">
        <h2>Sistem Cuti<br>Dinas Kominfo Kota</h2>
    </div>

    <div class="menu">

        <?php if($_SESSION['role'] === 'admin'): ?>

            <a href="ad-dashboard.php">📊 Dashboard</a>
            <a href="data-pegawai.php">🧑‍💼 Data Pegawai</a>
            <a href="pengajuan.php">📑 Pengajuan Cuti</a>
            <a href="ad-sanggah.php" class="active">⚠️ Status Pengajuan</a>

        <?php else: ?>

            <a href="dashboard.php">📊 Dashboard</a>
            <a href="cuti.php">🗓️ Cuti</a>
            <a href="sanggahan.php">⚠️ Status Pengajuan</a>

        <?php endif; ?>

    </div>

</div>


<!-- MAIN -->
<div class="main">
<form id="formCuti">
    <!-- HEADER LIST PUTIH FULL -->
    <div class="page-header">
        <h1>Daftar Pengajuan Cuti</h1>

        <div class="header-user">
            <span class="user-icon">👤</span>
            <span class="user-name"><?= $username ?></span>
            <span class="divider">|</span>
            <a href="logout.php">Logout</a>
        </div>
    </div>


    <div class="box">
        <table>
            <thead>
                <tr>
                    <th style="width:40px">No</th>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Jenis Cuti</th>
                    <th>Tanggal Cuti</th>
                    <th>Status</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php if(mysqli_num_rows($query)==0): ?>
                <tr>
                    <td colspan="5" style="text-align:center;color:#888">
                        Belum ada pengajuan cuti
                    </td>
                </tr>
            <?php endif; ?>

            <?php $no = 1; ?>
            <?php while($row=mysqli_fetch_assoc($query)): ?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= $row['username'] ?></td>
    <td><?= $row['nip'] ?></td>
    <td><?= $row['jenis_cuti'] ?></td>
    <td>
        <?= date('d M Y',strtotime($row['tgl_mulai'])) ?>
        s/d
        <?= date('d M Y',strtotime($row['tgl_selesai'])) ?>
    </td>
    <td>
        <?php
        if($row['status']=='Menunggu'){
            echo '<span class="badge wait">Menunggu</span>';
        }elseif($row['status']=='Disetujui'){
            echo '<span class="badge ok">Disetujui</span>';
        }else{
            echo '<span class="badge no">Ditolak</span>';
        }
        ?>
    </td>

    <!-- 🔽 KOLOM AKSI -->
    <td style="text-align:center">
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
</form>
</div>

<!-- MODAL DETAIL CUTI -->
<div class="modal-overlay" id="modalDetail">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Detail Pengajuan Cuti</h3>
            <span class="close" onclick="closeDetail()">✕</span>
        </div>

        <div class="modal-body" id="detailContent">
            <p>Loading...</p>
        </div>

        <div class="modal-footer">
            
        </div>
    </div>
</div>
<script>
function openDetail(id){
    document.getElementById('modalDetail').style.display = 'flex';
    document.getElementById('detailContent').innerHTML = '<p>Loading...</p>';

    fetch('ad-detail-cuti.php?id=' + id)
        .then(res => res.text())
        .then(data => {
            document.getElementById('detailContent').innerHTML = data;
        })
        .catch(() => {
            document.getElementById('detailContent').innerHTML = '<p>Gagal memuat data</p>';
        });
}

function simpanCatatan(){
    const catatan = document.getElementById('catatan').value;
    const id = document.getElementById('id').value;

    fetch('update-catatan.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + encodeURIComponent(id) +
              '&catatan=' + encodeURIComponent(catatan)
    })
    .then(() => {
        // 🔁 LOAD ULANG DETAIL DARI DB
        openDetail(id);
    })
    .catch(() => {
        alert('Gagal menyimpan catatan');
    });
}

function closeDetail(){
    document.getElementById('modalDetail').style.display = 'none';
}
</script>


</body>
</html>
