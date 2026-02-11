<?php
session_start();
include "conn.php";

// CEK LOGIN & ROLE
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// SEARCH
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$query = mysqli_query($conn, "
    SELECT id_pegawai, nip, nama_pegawai, jabatan, unit_kerja
    FROM pegawai
    WHERE status = 'aktif'
    ORDER BY nama_pegawai ASC
");

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Pegawai</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Inter',sans-serif;
}

body{
    display:flex;
    height:100vh;
    background:#f2f6fb;
}

/* ===== SIDEBAR ===== */
.sidebar{
    width:260px;
    background:#0b5aa6;
    color:#fff;
    padding:30px 20px;
}

.menu a{
    display:flex;
    align-items:center;
    gap:12px;
    padding:14px 18px;
    margin-bottom:12px;
    border-radius:10px;
    color:#fff;
    text-decoration:none;
    font-weight:500;
}

.menu a.active,
.menu a:hover{
    background:#0a4c8c;
}

/* ===== MAIN ===== */
.main{
    flex:1;
    padding:30px;
    overflow:auto;
}

/* HEADER */
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.header h1{
    color:#0b5aa6;
}

/* TOOLBAR */
.toolbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.btn-add{
    background:#4f7ecb;
    color:#fff;
    padding:10px 18px;
    border-radius:10px;
    text-decoration:none;
    font-weight:500;
}

.search input{
    padding:10px 14px;
    width:260px;
    border-radius:10px;
    border:1px solid #ccc;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    background:#fff;
    border-radius:14px;
    overflow:hidden;
    box-shadow:0 10px 20px rgba(0,0,0,.08);
}

thead{
    background:#cfdbee;
}

th, td{
    padding:14px;
    font-size:14px;
}

tbody tr{
    border-bottom:1px solid #eee;
}

tbody tr:hover{
    background:#f7faff;
}

/* DETAIL BTN */
.btn-detail{
    background:#4f7ecb;
    color:#fff;
    padding:6px 14px;
    border-radius:8px;
    text-decoration:none;
    font-size:13px;
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Sistem Dinas<br>Kominfo Kota</h2>

    <div class="menu">
        <a href="ad-dashboard.php">📊 Dashboard</a>
        <a href="pegawai.php" class="active">👥 Data Pegawai</a>
        <a href="pengajuan.php">📑 Pengajuan Cuti</a>
    </div>
</div>

<!-- MAIN -->
<div class="main">

    <div class="header">
        <h1>Data Pegawai</h1>
        <div>👤 <?= $_SESSION['username']; ?> | <a href="logout.php">Logout</a></div>
    </div>

    <div class="toolbar">
        <a href="tambah-pegawai.php" class="btn-add">+ Tambah Pegawai</a>

        <form method="get" class="search">
            <input type="text" name="search" placeholder="Cari Nama/NIP..."
                   value="<?= htmlspecialchars($search); ?>">
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIP</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Unit Kerja</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;

            if (mysqli_num_rows($query) == 0) {
                // TIDAK RENDER APA-APA (TABEL KOSONG TOTAL)
            } else {
                while ($row = mysqli_fetch_assoc($query)) {
            ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $row['nip']; ?></td>
                    <td><?= $row['nama_pegawai']; ?></td>
                    <td><?= $row['jabatan']; ?></td>
                    <td><?= $row['unit_kerja']; ?></td>
                    <td>
                        
                    </td>
                </tr>
            <?php
                }
            }

            ?>
        </tbody>

    </table>


</div>

</body>
</html>
