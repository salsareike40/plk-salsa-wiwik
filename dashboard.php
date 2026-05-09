<?php
session_start();
include "conn.php";
if ($_SESSION['role'] === 'admin') {
    header("Location: ad-dashboard.php");
    exit;
}
// ================= DATA USER LOGIN =================
$username = $_SESSION['username'];

$qUser = mysqli_query($conn,"
    SELECT nip, nama_pegawai
    FROM pegawai
    WHERE username='$username'
");

$user = mysqli_fetch_assoc($qUser);

$nip       = $user['nip'];
$nama      = $user['nama_pegawai'];
// Jatah default cuti tahunan
$jatahCuti = 12;


// hitung cuti tahunan (TIDAK TERKAIT cuti melahirkan)
$qPakai = mysqli_query($conn,"
SELECT SUM(jumlah_hari) AS total
FROM cuti
WHERE nip='$nip'
AND jenis_cuti!='Cuti Melahirkan'
AND status='Disetujui'
AND YEAR(tgl_mulai) = YEAR(CURDATE())
");

$dataPakai = mysqli_fetch_assoc($qPakai);
$terpakai = $dataPakai['total'] ?? 0;

$sisa = $jatahCuti - $terpakai;

if($sisa < 0){
    $sisa = 0;
}
$statusTahunan = $terpakai > 0 ? "Sudah Digunakan" : "Belum Ada";



$qPegawai = mysqli_query($conn,"
    SELECT COUNT(*) AS total
    FROM pegawai
    WHERE status='aktif'
      AND nama_pegawai IS NOT NULL
      AND nama_pegawai != ''
");

$totalPegawai = mysqli_fetch_assoc($qPegawai)['total'];

$jenisList = [
    'Cuti Tahunan',
    'Cuti Sakit',
    'Cuti Besar',
    'Cuti Melahirkan',
    'Alasan Penting'
];

$dataJumlah = array_fill_keys($jenisList, 0);
$totalCuti = 0;

$qJenis = mysqli_query($conn,"
    SELECT jenis_cuti, COUNT(*) AS total
    FROM cuti
    GROUP BY jenis_cuti
");

while($row = mysqli_fetch_assoc($qJenis)){
    if(isset($dataJumlah[$row['jenis_cuti']])){
        $dataJumlah[$row['jenis_cuti']] = (int)$row['total'];
        $totalCuti += (int)$row['total'];
    }
}
// ===== HITUNG PERSEN & PAKSA TOTAL = 100 =====
$dataPersen = [];
$totalPersen = 0;

foreach($dataJumlah as $jenis => $jumlah){
    $dataPersen[$jenis] = $totalCuti > 0
        ? round(($jumlah / $totalCuti) * 100)
        : 0;
    $totalPersen += $dataPersen[$jenis];
}

// koreksi supaya total = 100
$selisih = 100 - $totalPersen;

if($selisih != 0){
    // tambahkan selisih ke kategori terbesar
    $maxKey = array_keys($dataPersen, max($dataPersen))[0];
    $dataPersen[$maxKey] += $selisih;
}


// TOTAL pengajuan cuti
$qTotal = mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM cuti 
    WHERE nip='$nip'
");
$total = mysqli_fetch_assoc($qTotal)['total'];

// Menunggu persetujuan
$qMenunggu = mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM cuti 
    WHERE nip='$nip' 
    AND status='Menunggu'
");
$menunggu = mysqli_fetch_assoc($qMenunggu)['total'];

// Disetujui
$qSetuju = mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM cuti 
    WHERE nip='$nip' 
    AND status='Disetujui'
");
$setuju = mysqli_fetch_assoc($qSetuju)['total'];

// Ditolak
$qTolak = mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM cuti 
    WHERE nip='$nip' 
    AND status='Ditolak'
");
$tolak = mysqli_fetch_assoc($qTolak)['total'];

$jatahMelahirkan = 90;

$qMelahirkan = mysqli_query($conn,"
SELECT tgl_mulai, tgl_selesai
FROM cuti
WHERE nip='$nip'
AND jenis_cuti='Cuti Melahirkan'
AND status='Disetujui'
ORDER BY tgl_mulai DESC
LIMIT 1
");

$dataMelahirkan = mysqli_fetch_assoc($qMelahirkan);

$hariTerpakai = 0;
$sisaMelahirkan = $jatahMelahirkan;
$statusMelahirkan = "Belum Ada";

if($dataMelahirkan){

    $tglMulai = new DateTime($dataMelahirkan['tgl_mulai']);
    $tglSelesai = new DateTime($dataMelahirkan['tgl_selesai']);
    $today = new DateTime();

    // samakan waktu
    $today->setTime(0,0,0);
    $tglMulai->setTime(0,0,0);
    $tglSelesai->setTime(0,0,0);

    // 🔵 BELUM MULAI
    if($today < $tglMulai){
        $statusMelahirkan = "Belum Mulai";
        $hariTerpakai = 0;
        $sisaMelahirkan = $jatahMelahirkan;
    }

    // 🟡 SEDANG BERJALAN
    elseif($today >= $tglMulai && $today <= $tglSelesai){
        $statusMelahirkan = "Sedang Berjalan";

        $interval = $tglMulai->diff($today);
        $hariTerpakai = $interval->days + 1;

        if($hariTerpakai > $jatahMelahirkan){
            $hariTerpakai = $jatahMelahirkan;
        }

        $sisaMelahirkan = $jatahMelahirkan - $hariTerpakai;
    }

    // 🟢 SUDAH SELESAI
    else{
        $statusMelahirkan = "Selesai";
        $hariTerpakai = $jatahMelahirkan;
        $sisaMelahirkan = 0;
    }
}

$query = mysqli_query($conn, "
    SELECT * FROM cuti
    WHERE status IN ('Disetujui','Ditolak')
    ORDER BY tgl_pengajuan DESC
    LIMIT 8
");

$querySanggahan = mysqli_query($conn,"
    SELECT * FROM cuti
    WHERE status='Menunggu'
    ORDER BY tgl_pengajuan DESC
    LIMIT 8
");


if(!isset($_SESSION['username'])){
    header("Location: index.php");
    exit;
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Kominfo</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

/* ================= MAIN ================= */
.main{
    flex:1;
    padding:30px;
    overflow:auto;
}

/* TOP BAR */
.topbar{
    display:flex;
    justify-content:flex-end;
    align-items:center;
    margin-bottom:25px;
}

.user{
    font-weight:500;
}

/* ================= STAT CARDS ================= */
.stats{
    display:grid;
    grid-template-columns:2.4fr 2fr;
    gap:22px;
    margin-bottom:25px;
}

/* BOX UMUM */
.card{
    background:#c6d9ee;
    border-radius:20px;
    padding:20px 26px;
    display:flex;
    align-items:center;
    gap:16px;
    box-shadow:0 8px 16px rgba(0,0,0,0.08);
}

/* TOTAL PEGAWAI (BESAR) */
.card.total{
    padding:28px 32px;
}

/* ICON */
.icon{
    display:flex;
    align-items:center;
    justify-content:center;
}

.icon.blue{ color:#0b5aa6; }
.icon.purple{ color:#9c27b0; }
.icon.yellow{ color:#f4b400; }
.icon.green{ color:#2e7d32; }
.icon.red{ color:#d32f2f; }

/* ICON SIZE */
.total .icon{
    font-size:38px;
}

.small-cards .icon{
    font-size:26px;
}

/* TEXT */
.card-text{
    display:flex;
    flex-direction:column;
}

.card-text .number{
    font-size:22px;
    font-weight:700;
}

.total .number{
    font-size:44px;
}

.card-text .label{
    font-size:14px;
    color:#333;
    margin-top:2px;
}

/* KANAN (4 BOX KECIL) */
.small-cards{
    display:grid;
    grid-template-columns:1.5fr 1.5fr;
    gap:22px;
}
.small-cards .card{
    padding:20px 40px;
}
/* ===== HEADER DASHBOARD FULL WIDTH ===== */
.dashboard-header{
    background:#ffffff;
    padding:18px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;

    /* KUNCI FULL WIDTH */
    margin: -30px -30px 30px -30px; /* tarik keluar padding .main */
    border-radius:0;

    box-shadow:0 2px 6px rgba(0,0,0,0.08);
}

.dashboard-header h1{
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
}

.header-user a{
    color:#0b5aa6;
    text-decoration:none;
    font-weight:500;
}

.divider{
    color:#aaa;
}



/* ================= CHARTS ================= */
.content{
    display:grid;
    grid-template-columns:1fr 2fr;
    gap:25px;
    margin-bottom:25px;
}

.box{
    background:#fff;
    border-radius:18px;
    padding:25px;
    box-shadow:0 10px 20px rgba(0,0,0,.08);
}

.box h3{
    font-size:18px;
    margin-bottom:15px;
}

/* ================= TABLE ================= */
.tables{
    display:grid;
    grid-template-columns:1.5fr 1fr;
    gap:25px;
}
.user-table table{
    width:100%;
    border-collapse:collapse;
    table-layout:fixed;
}

.user-table th{
    text-align:left;
    font-size:14px;
    color:#6b7280;
    padding:14px 18px;
    border-bottom:1px solid #e5e7eb;
}

.user-table td{
    padding:14px 18px;
    font-size:14px;
    border-bottom:1px solid #f1f5f9;
}

.user-table tr:last-child td{
    border-bottom:none;
}

/* Atur lebar kolom */
.user-table th:nth-child(1),
.user-table td:nth-child(1){
    width:40%;
}

.user-table th:nth-child(2),
.user-table td:nth-child(2){
    width:25%;
}

.user-table th:nth-child(3),
.user-table td:nth-child(3){
    width:10%;
    text-align:center;
}

.user-table th:nth-child(4),
.user-table td:nth-child(4){
    width:15%;
    text-align:center;
}

/* Hover effect */
.user-table tbody tr:hover{
    background:#f9fafb;
}

.badge{
    padding:5px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

.wait{ background:#ffe1b3; color:#b56a00; }
.ok{ background:#c8f0d3; color:#0a7a32; }
.no{ background:#ffd1d1; color:#b00000; }

.link{
    float:right;
    font-size:13px;
    color:#2b7cff;
    text-decoration:none;
}
/* ===== JENIS CUTI ===== */
.jenis-cuti{
    padding:30px 34px;      /* lebih lega */
    display:flex;
    flex-direction:column;
    justify-content:center;
    min-height:320px;   
}

.cuti-wrap{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:36px;       /* jarak donut ↔ legend */
    margin-top:12px;
}

/* DONUT */
.donut-wrapper{
    position:relative;
    width:220px;
    height:220px;
    min-height:220px;
}


.donut-center{
    position:absolute;
    top:50%;
    left:50%;
    transform:translate(-50%,-50%);
    font-size:22px;
    font-weight:700;
}

/* LEGEND */
.cuti-legend{
    display:flex;
    flex-direction:column;
    gap:10px;
    font-size:14px;
}

.cuti-legend div{
    display:flex;
    align-items:center;
    gap:10px;
    white-space:nowrap; 
}

.cuti-legend b{
    margin-left:auto;
}

/* DOT */
.dot{
    width:12px;
    height:12px;
    border-radius:50%;
}

.dot.blue{ background:#1e88e5; }
.dot.green{ background:#43a047; }
.dot.purple{ background:#7e57c2; }
.dot.yellow{ background:#fbc02d; }
.dot.red{ background:#e53935; }

/* ===== CUTI BULANAN ===== */
.cuti-bulanan{
    padding:24px 28px;
    max-height:360px;        /* <<< INI YANG MENGECILKAN BOX */
    display:flex;
    flex-direction:column;
}
.cuti-bulanan canvas{
    max-height:260px;        /* <<< BIAR CHART NGGAK MAKSA TINGGI */
}


.box-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:15px;
}

.year-select{
    padding:6px 12px;
    border-radius:8px;
    border:1px solid #cfd8e3;
    background:#f7f9fc;
    font-size:13px;
    cursor:pointer;
}
.dashboard-header{
    background:#ffffff;
    padding:18px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;

    /* KUNCI FULL WIDTH */
    margin: -30px -30px 30px -30px; /* tarik keluar padding .main */
    border-radius:0;

    box-shadow:0 2px 6px rgba(0,0,0,0.08);
}

.dashboard-header h1{
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
}

.header-user a{
    color:#0b5aa6;
    text-decoration:none;
    font-weight:500;
}

.divider{
    color:#aaa;
}
/* ===== USER DASHBOARD ===== */

.user-welcome{
    display:flex;
    align-items:center;
    gap:15px;
    margin-bottom:25px;
}

.avatar{
    width:55px;
    height:55px;
    border-radius:50%;
    background:#ccc;
}

.user-welcome h2{
    font-size:20px;
    font-weight:600;
}
.user-cards{
    display:grid;
    grid-template-columns: repeat(3, 1fr);
    gap:24px; /* jarak lebih lega */
}
.u-card{
    display:flex;
    align-items:center;
    gap:18px;
    padding:28px; /* lebih besar */
    border-radius:20px;
    min-height:110px; /* bikin tinggi */
    box-shadow:0 12px 28px rgba(0,0,0,0.08);
    transition:.25s;
}

.u-card:hover{
    transform:translateY(-5px);
}

/* icon */
.u-card .icon{
    width:55px;
    height:55px;
    font-size:24px;
}

/* text */
.u-card p{
    font-size:15px;
}

.u-card h3{
    font-size:26px;
    font-weight:700;
}
.blue{ background:linear-gradient(135deg,#c7f0f3,#dff7f9); }
.purple{ background:linear-gradient(135deg,#e6e0ff,#f2efff); }
.yellow{ background:linear-gradient(135deg,#fff1c9,#fff7e3); }
.green{ background:linear-gradient(135deg,#d6f5df,#e9fff0); }
.red{ background:linear-gradient(135deg,#ffd6d6,#ffecec); }
.pink{ background:linear-gradient(135deg,#ffd6e0,#ffe9ef); }

.btn-ajukan{
    display:inline-block;
    background:#2b7cff;
    color:#fff;
    padding:10px 20px;
    border-radius:20px;
    margin:10px 0 20px 0; /* geser ke kiri */
    text-decoration:none;
    font-weight:600;
}

.user-table{
    background:#fff;
    padding:25px;
    border-radius:18px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
}

.user-table h3{
    margin-bottom:15px;
}
.red{
    background:#fecaca;
}
.purple{
    background:#e9d5ff;
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
        <a href="status-pengajuan.php">📋 Status Pengajuan</a>
        <a href="riwayat-cuti.php">🕘 Riwayat Cuti</a>

    </div>
</div>

<!-- MAIN -->
<div class="main">
   <!-- HEADER DASHBOARD FULL -->
<div class="dashboard-header">
    <h1>Dashboard</h1>

    <div class="header-user">
        <span class="user-icon">👤</span>
        <span class="nama-pegawai"><?= $nama ?> (<?= $nip ?>)</span>
        <span class="divider">|</span>
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- ===== USER DASHBOARD STYLE ===== -->

<div class="user-welcome">
    <img src="aset/avatar.jpeg" class="avatar">
    <h2>Selamat Datang, <?= $nama ?> (<?= $nip ?>)</h2>
</div>
<a href="cuti.php" class="btn-ajukan">+ Ajukan Cuti</a>
    <div class="user-cards">

    <div class="u-card blue">
        <div class="icon">📅</div>
        <div>
            <p>Sisa Cuti Tahunan</p>
            <h3><?= $sisa ?> Hari</h3>
            <small>
                Terpakai: <?= $terpakai ?> / 12 <br>
                <?= $statusTahunan ?>
            </small>
        </div>
    </div>

    <div class="u-card purple">
        <div class="icon">📄</div>
        <div>
            <p>Pengajuan Saya</p>
            <h3><?= $total ?></h3>
        </div>
    </div>

    <div class="u-card yellow">
        <div class="icon">⏳</div>
        <div>
            <p>Menunggu</p>
            <h3><?= $menunggu ?></h3>
        </div>
    </div>

    <div class="u-card pink">
        <div class="icon">🎀</div>
        <div>
            <p>Cuti Melahirkan</p>
            <h3><?= $sisaMelahirkan ?> Hari</h3>
            <small>
                Terpakai: <?= $hariTerpakai ?> / 90 <br>
                <?= $statusMelahirkan ?>
            </small>
        </div>
    </div>

    <div class="u-card green">
        <div class="icon">✔</div>
        <div>
            <p>Disetujui</p>
            <h3><?= $setuju ?></h3>
        </div>
    </div>

    <div class="u-card red">
        <div class="icon">✖</div>
        <div>
            <p>Ditolak</p>
            <h3><?= $tolak ?></h3>
        </div>
    </div>

</div>

</div>






</body>
</html>
