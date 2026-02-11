<?php
session_start();
include "conn.php";
if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit;
}

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
$qTotal = mysqli_query($conn, "SELECT COUNT(*) AS total FROM cuti");
$total = mysqli_fetch_assoc($qTotal)['total'];

// Menunggu persetujuan
$qMenunggu = mysqli_query($conn, "SELECT COUNT(*) AS total FROM cuti WHERE status='Menunggu'");
$menunggu = mysqli_fetch_assoc($qMenunggu)['total'];

// Disetujui
$qSetuju = mysqli_query($conn, "SELECT COUNT(*) AS total FROM cuti WHERE status='Disetujui'");
$setuju = mysqli_fetch_assoc($qSetuju)['total'];

// Ditolak
$qTolak = mysqli_query($conn, "SELECT COUNT(*) AS total FROM cuti WHERE status='Ditolak'");
$tolak = mysqli_fetch_assoc($qTolak)['total'];


$query = mysqli_query($conn, "
    SELECT * FROM cuti
    ORDER BY tgl_pengajuan DESC
    LIMIT 5
");

$querySanggahan = mysqli_query($conn,"
    SELECT * FROM cuti
    WHERE status='Menunggu'
    ORDER BY tgl_pengajuan DESC
    LIMIT 5
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
/* ===== DASHBOARD HEADER ===== */
/* ===== HEADER DASHBOARD (SIMPLE) ===== */
/* ===== SINGLE HEADER DASHBOARD ===== */
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

table{
    width:100%;
    border-collapse:collapse;
}

th{
    text-align:left;
    font-size:13px;
    color:#777;
    padding-bottom:10px;
}

td{
    padding:10px 0;
    font-size:14px;
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

</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo">
        <img src="aset/kominfo.png" alt="">
        <h2>Sistem Dinas<br>Kominfo Kota</h2>
    </div>

    <div class="menu">

        <?php if ($_SESSION['role'] === 'admin') { ?>

            <a href="ad-dashboard.php" class="active">📊 Dashboard</a>
            <a href="data-pegawai.php">🧑‍💼 Data Pegawai</a>
            <a href="pengajuan.php">📑 Pengajuan Cuti</a>

        <?php } elseif ($_SESSION['role'] === 'pegawai') { ?>

            <a href="dashboard.php" class="active">📊 Dashboard</a>
            <a href="cuti.php">🗓️ Cuti</a>
            <a href="sanggahan.php">⚠️ Sanggahan</a>

        <?php } ?>

    </div>

</div>

<!-- MAIN -->
<div class="main">
<!-- HEADER DASHBOARD (HIASAN) -->
<!-- HEADER DASHBOARD (ADMIN - SINGLE) -->
<div class="dashboard-header">
    <h1>Dashboard</h1>

    <div class="header-user">
        <span class="user-icon">👤</span>
        <span class="user-name"><?= $username ?></span>
        <span class="divider">|</span>
        <a href="logout.php">Logout</a>
    </div>
</div>

    <!-- STAT -->
    <div class="stats">

    <!-- TOTAL PEGAWAI -->
    <div class="card total">
        <div class="icon blue">👥</div>
        <div class="card-text">
            <div class="number">125</div>
            <div class="label">Total Pegawai</div>
        </div>
    </div>

    <!-- KANAN -->
    <div class="small-cards">

        <div class="card">
            <div class="icon purple">📝</div>
            <div class="card-text">
                <div class="number"><?= $total ?></div>
                <div class="label">Pengajuan</div>
            </div>
        </div>

        <div class="card">
            <div class="icon yellow">⏳</div>
            <div class="card-text">
                <div class="number"><?= $menunggu ?></div>
                <div class="label">Menunggu Persetujuan</div>
            </div>
        </div>

        <div class="card">
            <div class="icon green">✔</div>
            <div class="card-text">
                <div class="number"><?= $setuju ?></div>
                <div class="label">Disetujui</div>
            </div>
        </div>

        <div class="card">
            <div class="icon red">✖</div>
            <div class="card-text">
                <div class="number"><?= $tolak ?></div>
                <div class="label">Ditolak</div>
            </div>
        </div>

    </div>
</div>



<div class="content">
    <div class="box jenis-cuti">
        <h3>Jenis Cuti</h3>

        <div class="cuti-wrap">
            <!-- DONUT -->
            <div class="donut-wrapper">
                <canvas id="cutiChart"></canvas>
                <div class="donut-center">100%</div>
            </div>

            <!-- LEGEND -->
            <div class="cuti-legend">
                <div><span class="dot blue"></span> Cuti Tahunan <b><?= $dataPersen['Cuti Tahunan'] ?>%</b></div>
                <div><span class="dot green"></span> Cuti Sakit <b><?= $dataPersen['Cuti Sakit'] ?>%</b></div>
                <div><span class="dot purple"></span> Cuti Besar <b><?= $dataPersen['Cuti Besar'] ?>%</b></div>
                <div><span class="dot yellow"></span> Cuti Melahirkan <b><?= $dataPersen['Cuti Melahirkan'] ?>%</b></div>
                <div><span class="dot red"></span> Alasan Penting <b><?= $dataPersen['Alasan Penting'] ?>%</b></div>

            </div>
        </div>
    </div>

     <!-- BAWAH : CUTI TERBARU -->
      <div class="content-dua-box">
        <div class="box cuti-terbaru">
            <div class="box-header">
                <h3>Status Cuti Terbaru</h3>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Jenis Cuti</th>
                        <th>Tgl Pengajuan</th>
                        <th>Hari</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(mysqli_num_rows($query) == 0): ?>
<tr>
    <td colspan="5" style="text-align:center;color:#888">
        Belum ada pengajuan cuti
    </td>
</tr>
<?php endif; ?>

<?php while($row = mysqli_fetch_assoc($query)): ?>
<tr>
    <td><?= $row['nama'] ?></td>
    <td><?= $row['jenis_cuti'] ?></td>
    <td><?= date('d M Y', strtotime($row['tgl_pengajuan'])) ?></td>
    <td><?= $row['jumlah_hari'] ?></td>
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
</tr>
<?php endwhile; ?>
</tbody>

            </table>
        </div>

    </div>

    <div class="box cuti-bulanan">
        <div class="box-header">
        <h3>Pengajuan Cuti Bulanan</h3>

        <select class="year-select">
            <option>2026</option>
            <option>2025</option>
        </select>
    </div>

    <canvas id="cutiBulananChart"></canvas>
    
</div>
<div class="box sanggahan-terbaru">
    <div class="box-header">
        <h3>Sanggahan Harian Terbaru</h3>
        <a href="sanggahan.php" class="link">Lihat Semua ›</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Jenis Cuti</th>
                <th>Tgl Pengajuan</th>
                <th>Hari</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>

        <?php if(mysqli_num_rows($querySanggahan) == 0): ?>
            <tr>
                <td colspan="5" style="text-align:center;color:#888">
                    Tidak ada sanggahan
                </td>
            </tr>
        <?php endif; ?>

        <?php while($row = mysqli_fetch_assoc($querySanggahan)): ?>
        <tr>
            <td><?= $row['nama'] ?></td>
            <td><?= $row['jenis_cuti'] ?></td>
            <td><?= date('d M Y', strtotime($row['tgl_pengajuan'])) ?></td>
            <td><?= $row['jumlah_hari'] ?></td>
            <td><span class="badge wait">Menunggu</span></td>
        </tr>
        <?php endwhile; ?>

        </tbody>
    </table>
</div>



<script>

const cutiLabels = <?= json_encode(array_keys($dataPersen)) ?>;
const cutiData   = <?= json_encode(array_values($dataPersen)) ?>;


const donutCtx = document.getElementById('cutiChart');

if(donutCtx){
    new Chart(donutCtx,{
        type:'doughnut',
        data:{
            labels:[
                'Cuti Tahunan',
                'Cuti Sakit',
                'Cuti Besar',
                'Cuti Melahirkan',
                'Alasan Penting'
            ],
            datasets:[{
                data: cutiData,
                backgroundColor:[
                    '#1e88e5',
                    '#43a047',
                    '#7e57c2',
                    '#fbc02d',
                    '#e53935'
                ],
                borderWidth:0
            }]
        },
        options:{
            responsive:true,
            maintainAspectRatio:false,
            cutout:'70%',
            plugins:{
                legend:{ display:false },
                tooltip:{
                    position:'nearest',   // 🔑 NEMPEL KE SLICE YANG DI-HOVER
            intersect:true,       // hanya aktif kalau benar-benar di slice
            yAlign:'bottom',
            caretPadding:12
}

            }
        }
    });
}

new Chart(document.getElementById('cutiBulananChart'),{
    type:'bar',
    data:{
        labels:['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Des'],
        datasets:[
            {
                data:[17,13,8,6,3,3,2,2,2,2,2],
                backgroundColor:[
                    '#9ec5f8',
                    '#1e88e5',
                    '#d6e1f2',
                    '#d6e1f2',
                    '#e4ebf7',
                    '#e4ebf7',
                    '#e4ebf7',
                    '#e4ebf7',
                    '#e4ebf7',
                    '#e4ebf7',
                    '#e4ebf7'
                ],
                borderRadius:8,
                barThickness:26
            }
        ]
    },
    options:{
        plugins:{
            legend:{ display:false }
        },
        scales:{
            x:{
                grid:{ display:false }
            },
            y:{
                beginAtZero:true,
                ticks:{
                    stepSize:5
                },
                grid:{
                    color:'#e5ebf3'
                }
            }
        }
    }
});

</script>

</body>
</html>
