<?php
session_start();
include "conn.php";

if(!isset($_SESSION['username'])){
    header("Location: index.php");
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
<title>Sanggahan</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
body{
    margin:0;
    font-family:'Inter',sans-serif;
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
    border-collapse:collapse;
}
th{
    text-align:left;
    font-size:13px;
    color:#777;
    padding-bottom:12px;
}
td{
    padding:12px 0;
    font-size:14px;
    border-top:1px solid #eef2f7;
}

/* BADGE */
.badge{
    padding:6px 14px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}
.wait{ background:#ffe1b3; color:#b56a00; }
.ok{ background:#c8f0d3; color:#0a7a32; }
.no{ background:#ffd1d1; color:#b00000; }
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

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo">
        <img src="aset/kominfo.png" alt="Kominfo">
        <h2>Sistem Dinas<br>Kominfo Kota</h2>
    </div>

    <div class="menu">
        <a href="dashboard.php">📊 Dashboard</a>
        <a href="cuti.php">🗓️ Cuti</a>
        <a href="sanggahan.php" class="active">📑 Sanggahan</a>
    </div>
</div>


<!-- MAIN -->
<div class="main">

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
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Jenis Cuti</th>
                    <th>Tanggal Cuti</th>
                    <th>Status</th>
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

            <?php while($row=mysqli_fetch_assoc($query)): ?>
                <tr>
                    <td><?= $row['nama'] ?></td>
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
                </tr>
            <?php endwhile; ?>

            </tbody>
        </table>
    </div>

</div>
</body>
</html>
