<?php
session_start();
include "conn.php"; 
if(!isset($_SESSION['username'])){
    header("Location: index.php");
    exit;
}
$username = $_SESSION['username'];
$page = basename($_SERVER['PHP_SELF']);
$success = false;

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $nama         = $_POST['nama'];
    $nip          = $_POST['nip'];
    $jabatan      = $_POST['jabatan'];
    $masa_kerja   = $_POST['masa_kerja'];
    $unit_kerja   = $_POST['unit_kerja'];
    $jenis_cuti   = $_POST['jenis_cuti'];
    $alasan       = $_POST['alasan'];
    $tgl_mulai    = $_POST['tgl_mulai'];
    $tgl_selesai  = $_POST['tgl_selesai'];
    $jumlah_hari  = $_POST['jumlah_hari'];
    $alamat       = $_POST['alamat'];
    $no_telp      = $_POST['no_telp'];

    mysqli_query($conn,"
        INSERT INTO cuti
        (nama,nip,jabatan,masa_kerja,unit_kerja,jenis_cuti,alasan,
         tgl_mulai,tgl_selesai,jumlah_hari,alamat,no_telp,status)
        VALUES
        ('$nama','$nip','$jabatan','$masa_kerja','$unit_kerja','$jenis_cuti','$alasan',
         '$tgl_mulai','$tgl_selesai','$jumlah_hari','$alamat','$no_telp','Menunggu')
    ");

      $_SESSION['success'] = "Pengajuan cuti berhasil dikirim";

    header("Location: cuti.php");
    exit;
}


?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Permintaan & Pengajuan Cuti</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* ===== MODAL SUCCESS ===== */
.modal-overlay{
    position:fixed;
    top:0; left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,.45);
    display:flex;
    align-items:center;
    justify-content:center;
    z-index:9999;
}

.modal-box{
    background:#fff;
    padding:30px 36px;
    border-radius:14px;
    text-align:center;
    width:360px;
    box-shadow:0 20px 40px rgba(0,0,0,.2);
    animation:pop .25s ease;
}

.modal-box h3{
    margin-bottom:20px;
    font-size:18px;
    color:#1f2937;
}

.modal-box button{
    background:#2b7cff;
    border:none;
    color:#fff;
    padding:10px 36px;
    border-radius:8px;
    font-size:15px;
    font-weight:600;
    cursor:pointer;
}

@keyframes pop{
    from{
        transform:scale(.9);
        opacity:0;
    }
    to{
        transform:scale(1);
        opacity:1;
    }
}

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
.menu a{
    display:flex;
    align-items:center;
    gap:12px;

    width:100%;              /* 🔑 KUNCI */
    box-sizing:border-box;   /* 🔑 KUNCI */

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


.menu a:not(.active):hover{
    background:#0a4c8c;
}


/* MAIN */
.main{
    flex:1;
    padding:30px 40px;
}
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}
.header h1{
    font-size:26px;
    color:#0b5aa6;
}
.user{
    font-weight:500;
}

/* BOX */
.box{
    background:#fff;
    border-radius:18px;
    padding:24px 28px;
    margin-bottom:22px;
    box-shadow:0 8px 16px rgba(0,0,0,.08);
}
.box h3{
    margin-bottom:18px;
}

/* FORM */
.form-row{
    display:grid;
    grid-template-columns:180px 1fr;
    gap:16px;
    margin-bottom:14px;
}
.form-row input{
    padding:10px 14px;
    border-radius:8px;
    border:1px solid #d8e1ef;
    background:#eef3fb;
    width:100%;
}

/* GRID */
.grid-2{
    display:grid;
    grid-template-columns:1.3fr 1fr;
    gap:22px;
}

/* RADIO */
.radio-group label{
    display:flex;
    align-items:center;
    gap:10px;
    margin-bottom:10px;
}

/* DATE */
.date-row{
    display:flex;
    gap:12px;
}
.date-row input{
    width:100%;
}

/* BUTTON */
.btn{
    display:block;
    margin:30px auto 0;
    background:#1e6fd9;
    color:#fff;
    border:none;
    padding:12px 34px;
    border-radius:10px;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
}
.alert-success{
    background:#d4f6df;
    color:#0a7a32;
    padding:14px 18px;
    border-radius:10px;
    margin-bottom:20px;
    font-weight:600;
    box-shadow:0 6px 12px rgba(0,0,0,.08);
}

/* ===== HEADER PAGE FULL WIDTH ===== */
.page-header{
    background:#ffffff;
    padding:18px 40px;
    display:flex;
    justify-content:space-between;
    align-items:center;

    /* bikin nempel ke atas & kiri kanan */
    margin:-30px -40px 30px -40px;

    box-shadow:0 2px 6px rgba(0,0,0,0.08);
}

.page-header h1{
    font-size:22px;
    font-weight:600;
    color:#0b5aa6;
}

/* USER DI HEADER */
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
<script>
function closeModal(){
    document.getElementById('successModal').style.display = 'none';
}
</script>

<body>

<div class="sidebar">
    <div class="logo">
        <img src="aset/kominfo.png" alt="">
        <h2>Sistem Dinas<br>Kominfo Kota</h2>
    </div>

    <div class="menu">
        <a href="dashboard.php" class="<?= $page=='dashboard.php'?'active':'' ?>">📊 Dashboard</a>
        <a href="cuti.php" class="<?= $page=='cuti.php'?'active':'' ?>">🗓️ Cuti</a>
        <a href="sanggahan.php" class="<?= $page=='sanggahan.php'?'active':'' ?>">📑 Sanggahan</a>
    </div>
</div>


<div class="main">

    <!-- HEADER FULL PUTIH -->
    <div class="page-header">
        <h1>Permintaan & Pengajuan Cuti</h1>

        <div class="header-user">
            <span class="user-icon">👤</span>
            <span class="user-name"><?= $username ?></span>
            <span class="divider">|</span>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    
<?php if(isset($_SESSION['success'])): ?>
<div class="modal-overlay" id="successModal">
    <div class="modal-box">
        <h3>Pengajuan Cuti Berhasil</h3>
        <button onclick="closeModal()">OK</button>
    </div>
</div>
<?php unset($_SESSION['success']); endif; ?>


<form method="post" action="">



    <div class="box">
        <h3>Data Pegawai</h3>

        <div class="form-row">
            <label>Nama</label>
            <input type="text" name="nama" placeholder="Masukkan nama pegawai" required>
        </div>

        <div class="form-row">
            <label>NIP</label>
            <input type="text" name="nip" placeholder="Masukkan NIP" required>
        </div>

        <div class="form-row">
            <label>Jabatan</label>
            <input type="text" name="jabatan" placeholder="Masukkan jabatan">
        </div>

        <div class="form-row">
            <label>Masa Kerja</label>
            <input type="text" name="masa_kerja" placeholder="Contoh: 10 Tahun">
        </div>

        <div class="form-row">
            <label>Unit Kerja</label>
            <input type="text" name="unit_kerja" placeholder="Masukkan unit kerja">
        </div>
    </div>

    <!-- GRID -->
    <div class="grid-2">

        <!-- JENIS CUTI -->
        <div class="box">
            <h3>Jenis Cuti Yang Diambil</h3>
            <div class="radio-group">
                <label><input type="radio" name="jenis_cuti" value="Cuti Tahunan" required> Cuti Tahunan</label>
                <label><input type="radio" name="jenis_cuti" value="Cuti Besar"> Cuti Besar</label>
                <label><input type="radio" name="jenis_cuti" value="Cuti Sakit"> Cuti Sakit</label>
                <label><input type="radio" name="jenis_cuti" value="Cuti Melahirkan"> Cuti Melahirkan</label>
                <label><input type="radio" name="jenis_cuti" value="Cuti Alasan Penting"> Cuti Karena Alasan Penting</label>
            </div>
        </div>

        <!-- KANAN -->
        <div>
            <div class="box">
                <h3>Alasan Cuti</h3>
                <input type="text" name="alasan" placeholder="Masukkan alasan cuti" style="width:100%">
            </div>

            <div class="box">
                <h3>Lama Cuti</h3>
                <div class="date-row">
                    <input type="date" name="tgl_mulai" required>
                    <input type="date" name="tgl_selesai" required>
                </div>

                <div class="form-row" style="margin-top:12px">
                    <label>Jumlah Hari</label>
                    <input type="text" name="jumlah_hari" placeholder="Jumlah hari">
                </div>
            </div>
        </div>

    </div>

    <!-- ALAMAT -->
    <div class="box">
        <h3>Alamat Cuti</h3>
        <div class="form-row">
            <label>Alamat</label>
            <input type="text" name="alamat">
        </div>
        <div class="form-row">
            <label>No Telp</label>
            <input type="text" name="no_telp">
        </div>
    </div>

    <button type="submit" class="btn">Ajukan</button>

</form>

</div>

</body>
</html>
