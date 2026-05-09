<?php
session_start();
include "conn.php"; 
if(!isset($_SESSION['username'])){
    header("Location: index.php");
    exit;
}
$username = $_SESSION['username'];
$nip = $_SESSION['nip']; // 🔥 ambil dari session

$qUser = mysqli_query($conn, "
    SELECT nama_pegawai, jabatan, unit_kerja
    FROM pegawai
    WHERE nip='$nip'
");

$user = mysqli_fetch_assoc($qUser);

$nama = $user['nama_pegawai'];
$jabatan = $user['jabatan'];
$unit_kerja = $user['unit_kerja'];

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $jabatan    = $_POST['jabatan'];
    $unit_kerja = $_POST['unit_kerja'];

    // ✅ UPDATE DATA PEGAWAI
    mysqli_query($conn,"
        UPDATE pegawai SET
            jabatan='$jabatan',
            unit_kerja='$unit_kerja'
        WHERE nip='$nip'
    ");

    // ⬇️ DATA CUTI
    $jenis_cuti   = $_POST['jenis_cuti'];
    $alasan       = $_POST['alasan'];
    $tgl_mulai    = $_POST['tgl_mulai'];
    $tgl_selesai  = $_POST['tgl_selesai'];
    $jumlah_hari  = (int)$_POST['jumlah_hari'];
    $alamat       = $_POST['alamat'];
    $no_telp      = $_POST['no_telp'];
    if(empty($tgl_mulai) || empty($tgl_selesai)){
    $_SESSION['error_cuti'] = "Tanggal harus diisi";
    header("Location: cuti.php");
    exit;
}
$start = new DateTime($tgl_mulai);
$end = new DateTime($tgl_selesai);
$jumlah_hari = $start->diff($end)->days + 1;
// VALIDASI TANGGAL
if($tgl_selesai < $tgl_mulai){
    $_SESSION['error_cuti'] = "Tanggal selesai tidak boleh sebelum tanggal mulai";
    header("Location: cuti.php");
    exit;
}
// VALIDASI CUTI MELAHIRKAN
// VALIDASI CUTI MELAHIRKAN
if($jenis_cuti == 'Cuti Melahirkan'){

    if($jumlah_hari > 90){
        $_SESSION['error_cuti'] = "Cuti melahirkan maksimal 90 hari";
        header("Location: cuti.php");
        exit;
    }

    // 🔥 TAMBAHAN WAJIB (CEK TOTAL YANG SUDAH DIPAKAI)
    $qTotalMelahirkan = mysqli_query($conn,"
    SELECT SUM(jumlah_hari) AS total
    FROM cuti
    WHERE nip='$nip'
    AND jenis_cuti='Cuti Melahirkan'
    AND status='Disetujui'
    ");

    $dataTotalMelahirkan = mysqli_fetch_assoc($qTotalMelahirkan);
    $totalMelahirkan = $dataTotalMelahirkan['total'] ?? 0;

    // ❌ kalau sudah habis 90 hari
    if($totalMelahirkan >= 90){
        $_SESSION['error_cuti'] = "Jatah cuti melahirkan sudah habis (90 hari)";
        header("Location: cuti.php");
        exit;
    }

    // ❌ kalau melebihi sisa
    if($totalMelahirkan + $jumlah_hari > 90){
        $_SESSION['error_cuti'] = "Pengajuan melebihi sisa cuti melahirkan";
        header("Location: cuti.php");
        exit;
    }

}
// cek jumlah hari tidak kosong
if(empty($jumlah_hari) || $jumlah_hari <= 0){
    echo "<script>alert('Jumlah hari cuti tidak valid');history.back();</script>";
    exit;
}
if($jenis_cuti != 'Cuti Melahirkan' && $jumlah_hari > 12){
    $_SESSION['error_cuti'] = "Tidak bisa mengajukan cuti, batas maksimal 12 hari";
    header("Location: cuti.php");
    exit;
}

// cek apakah sedang cuti melahirkan
$qCekMelahirkan = mysqli_query($conn,"
SELECT COUNT(*) AS total
FROM cuti
WHERE nip='$nip'
AND jenis_cuti='Cuti Melahirkan'
AND status='Disetujui'
AND CURDATE() BETWEEN tgl_mulai AND tgl_selesai
");

$dataCekMelahirkan = mysqli_fetch_assoc($qCekMelahirkan);

if($dataCekMelahirkan['total'] > 0){
    $_SESSION['error_cuti'] = "Anda sedang dalam masa cuti melahirkan, tidak dapat mengajukan cuti lain.";
    header("Location: cuti.php");
    exit;
}

$qPakai = mysqli_query($conn,"
    SELECT SUM(jumlah_hari) AS total
    FROM cuti
    WHERE nip='$nip'
    AND jenis_cuti='Cuti Tahunan'
    AND status='Disetujui'
    AND YEAR(tgl_mulai) = YEAR(CURDATE())
");

$data = mysqli_fetch_assoc($qPakai);
$terpakai = $data['total'] ?? 0;
$sisa = 12 - $terpakai;

if($jenis_cuti == 'Cuti Tahunan' && $jumlah_hari > $sisa){
    $_SESSION['error_cuti'] = "Sisa cuti tahunan tidak mencukupi. Sisa cuti Anda: $sisa hari";
    header("Location: cuti.php");
    exit;
}
// CEK apakah ada cuti yang tanggalnya bentrok
$qBentrok = mysqli_query($conn,"
SELECT COUNT(*) AS total
FROM cuti
WHERE nip='$nip'
AND status IN ('Menunggu','Disetujui')
AND tgl_selesai >= CURDATE()
AND (
    ('$tgl_mulai' BETWEEN tgl_mulai AND tgl_selesai)
    OR
    ('$tgl_selesai' BETWEEN tgl_mulai AND tgl_selesai)
    OR
    (tgl_mulai BETWEEN '$tgl_mulai' AND '$tgl_selesai')
)
");

$dataBentrok = mysqli_fetch_assoc($qBentrok);

if($dataBentrok['total'] > 0){
    $_SESSION['error_cuti'] = "Anda masih memiliki cuti pada tanggal tersebut. Tunggu cuti selesai terlebih dahulu.";
    header("Location: cuti.php");
    exit;
}


    mysqli_query($conn,"
    INSERT INTO cuti
    (
        nama,
        nip,
        jenis_cuti,
        alasan,
        tgl_mulai,
        tgl_selesai,
        jumlah_hari,
        alamat,
        no_telp,
        status
    )
    VALUES
    (
        '$nama',
        '$nip',
        '$jenis_cuti',
        '$alasan',
        '$tgl_mulai',
        '$tgl_selesai',
        '$jumlah_hari',
        '$alamat',
        '$no_telp',
        'Menunggu'
    )
");

        $_SESSION['success'] = true;
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
.form-row select{
    padding:10px 14px;
    border-radius:8px;
    border:1px solid #d8e1ef;
    background:#eef3fb;
    width:100%;
}
.info-melahirkan{
    display:none;              /* default: tidak tampil */
    margin-top:12px;
    padding:12px 16px;
    background:#dfe7f5;
    border-radius:10px;
    color:#2c5aa0;
    font-size:14px;
}
</style>
</head>
<script>
function closeModal(){
    document.getElementById('successModal').style.display = 'none';
}
function toggleInfoMelahirkan(jenis){
    const box = document.getElementById('infoMelahirkan');
    box.style.display = (jenis === 'Cuti Melahirkan') ? 'block' : 'none';
}
</script>

<body>

<div class="sidebar">
    <div class="logo">
        <img src="aset/kominfo.png" alt="">
        <h2>SICUTI</h2>
    </div>
<?php $page = basename($_SERVER['PHP_SELF']); ?>
    <div class="menu">
        <a href="dashboard.php" class="<?= $page=='dashboard.php'?'active':'' ?>">📊 Dashboard</a>
        <a href="cuti.php" class="<?= $page=='cuti.php'?'active':'' ?>">🗓️ Cuti</a>
        <a href="status-pengajuan.php" class="<?= $page=='status-pengajuan.php'?'active':'' ?>">📋 Status Pengajuan</a>
        <a href="riwayat-cuti.php" class="<?= $page=='riwayat-cuti.php'?'active':'' ?>">🕘 Riwayat Cuti</a>
    </div>
</div>


<div class="main">

    <!-- HEADER FULL PUTIH -->
    <div class="page-header">
        <h1>Permintaan & Pengajuan Cuti</h1>

        <div class="header-user">
            <span class="user-icon">👤</span>
            <span class="nama-pegawai"><?= $nama ?> (<?= $nip ?>)</span>
            <span class="divider">|</span>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    
<?php if(isset($_SESSION['error_cuti'])): ?>
<div class="modal-overlay" id="errorModal">
    <div class="modal-box">
        <h3><?= $_SESSION['error_cuti'] ?></h3>
        <button onclick="closeError()">OK</button>
    </div>
</div>
<?php unset($_SESSION['error_cuti']); endif; ?>


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
            <input type="text" name="nama" value="<?= $nama ?>" readonly>
        </div>

        <div class="form-row">
            <label>NIP</label>
            <input type="text" name="nip" value="<?= $nip ?>" readonly>
        </div>

        <div class="form-row">
            <label>Jabatan</label>
            <select name="jabatan">

    <option value="Kepala Dinas" <?= $jabatan=='Kepala Dinas'?'selected':'' ?>>
        Kepala Dinas
    </option>

    <option value="Sekretaris" <?= $jabatan=='Sekretaris'?'selected':'' ?>>
        Sekretaris
    </option>

    <option value="Kepala Bidang Persandian dan Keamanan Informasi" <?= $jabatan=='Kepala Bidang Persandian dan Keamanan Informasi'?'selected':'' ?>>
        Kepala Bidang Persandian dan Keamanan Informasi
    </option>

    <option value="Kepala Bidang Penyelenggaraan e-Government" <?= $jabatan=='Kepala Bidang Penyelenggaraan e-Government'?'selected':'' ?>>
        Kepala Bidang Penyelenggaraan e-Government
    </option>

    <option value="Arsiparis Ahli Madya" <?= $jabatan=='Arsiparis Ahli Madya'?'selected':'' ?>>
        Arsiparis Ahli Madya
    </option>

    <option value="Kepala Bidang Pengelolaan Informasi dan Komunikasi Publik" <?= $jabatan=='Kepala Bidang Pengelolaan Informasi dan Komunikasi Publik'?'selected':'' ?>>
        Kepala Bidang Pengelolaan Informasi dan Komunikasi Publik
    </option>

    <option value="Kepala Bidang Statistik" <?= $jabatan=='Kepala Bidang Statistik'?'selected':'' ?>>
        Kepala Bidang Statistik
    </option>

    <option value="Sandiman Ahli Muda" <?= $jabatan=='Sandiman Ahli Muda'?'selected':'' ?>>
        Sandiman Ahli Muda
    </option>

    <option value="Pranata Humas Ahli Muda" <?= $jabatan=='Pranata Humas Ahli Muda'?'selected':'' ?>>
        Pranata Humas Ahli Muda
    </option>

    <option value="Pranata Komputer Ahli Muda" <?= $jabatan=='Pranata Komputer Ahli Muda'?'selected':'' ?>>
        Pranata Komputer Ahli Muda
    </option>

    <option value="Kepala Sub Bagian Umum dan Kepegawaian" <?= $jabatan=='Kepala Sub Bagian Umum dan Kepegawaian'?'selected':'' ?>>
        Kepala Sub Bagian Umum dan Kepegawaian
    </option>

    <option value="Statistisi Ahli Muda" <?= $jabatan=='Statistisi Ahli Muda'?'selected':'' ?>>
        Statistisi Ahli Muda
    </option>

    <option value="Kepala Sub Bagian Keuangan" <?= $jabatan=='Kepala Sub Bagian Keuangan'?'selected':'' ?>>
        Kepala Sub Bagian Keuangan
    </option>

    <option value="Arsiparis Mahir" <?= $jabatan=='Arsiparis Mahir'?'selected':'' ?>>
        Arsiparis Mahir
    </option>

    <option value="Penelaah Teknis Kebijakan" <?= $jabatan=='Penelaah Teknis Kebijakan'?'selected':'' ?>>
        Penelaah Teknis Kebijakan
    </option>

    <option value="Penata Layanan Operasional" <?= $jabatan=='Penata Layanan Operasional'?'selected':'' ?>>
        Penata Layanan Operasional
    </option>

    <option value="Pranata Hubungan Masyarakat Ahli Pertama" <?= $jabatan=='Pranata Hubungan Masyarakat Ahli Pertama'?'selected':'' ?>>
        Pranata Hubungan Masyarakat Ahli Pertama
    </option>

    <option value="Pranata Komputer Ahli Pertama" <?= $jabatan=='Pranata Komputer Ahli Pertama'?'selected':'' ?>>
        Pranata Komputer Ahli Pertama
    </option>

    <option value="Statistisi Ahli Pertama" <?= $jabatan=='Statistisi Ahli Pertama'?'selected':'' ?>>
        Statistisi Ahli Pertama
    </option>

    <option value="Pengadministrasi Perkantoran" <?= $jabatan=='Pengadministrasi Perkantoran'?'selected':'' ?>>
        Pengadministrasi Perkantoran
    </option>

</select>
        </div>

        <div class="form-row">
            <label>Unit Kerja</label>
            <select name="unit_kerja">

            <option value="Bidang Persandian dan Keamanan Informasi"
            <?= $unit_kerja=='Bidang Persandian dan Keamanan Informasi'?'selected':'' ?>>
                Bidang Persandian dan Keamanan Informasi
            </option>

            <option value="Bidang Penyelenggaraan e-Government"
            <?= $unit_kerja=='Bidang Penyelenggaraan e-Government'?'selected':'' ?>>
                Bidang Penyelenggaraan e-Government
            </option>

            <option value="Bidang Pengelolaan Informasi dan Komunikasi Publik"
            <?= $unit_kerja=='Bidang Pengelolaan Informasi dan Komunikasi Publik'?'selected':'' ?>>
                Bidang Pengelolaan Informasi dan Komunikasi Publik
            </option>

            <option value="Bidang Statistik"
            <?= $unit_kerja=='Bidang Statistik'?'selected':'' ?>>
                Bidang Statistik
            </option>

            <option value="Sub Bagian Umum dan Kepegawaian"
            <?= $unit_kerja=='Sub Bagian Umum dan Kepegawaian'?'selected':'' ?>>
                Sub Bagian Umum dan Kepegawaian
            </option>

            <option value="Sub Bagian Keuangan"
            <?= $unit_kerja=='Sub Bagian Keuangan'?'selected':'' ?>>
                Sub Bagian Keuangan
            </option>

        </select>
        </div>
    </div>
    <!-- GRID -->
    <div class="grid-2">

        <!-- JENIS CUTI -->
        <div class="box">
            <h3>Jenis Cuti Yang Diambil</h3>
         <div class="radio-group">

<label>
<input type="radio" name="jenis_cuti" value="Cuti Tahunan"
       onchange="toggleInfoMelahirkan(this.value)" required>
Cuti Tahunan
</label>

<label>
<input type="radio" name="jenis_cuti" value="Cuti Besar"
       onchange="toggleInfoMelahirkan(this.value)">
Cuti Besar
</label>

<label>
<input type="radio" name="jenis_cuti" value="Cuti Sakit"
       onchange="toggleInfoMelahirkan(this.value)">
Cuti Sakit
</label>

<label>
<input type="radio" name="jenis_cuti" value="Cuti Melahirkan"
       onchange="toggleInfoMelahirkan(this.value)">
Cuti Melahirkan
</label>

<label>
<input type="radio" name="jenis_cuti" value="Cuti Alasan Penting"
       onchange="toggleInfoMelahirkan(this.value)">
Cuti Karena Alasan Penting
</label>

<!-- Info khusus melahirkan -->
<div id="infoMelahirkan" class="info-melahirkan">
    Maksimal cuti melahirkan adalah 3 bulan <b>(90 hari)</b>
</div>

</div>
        </div>

        <!-- KANAN -->
        <div>
            <div class="box">
                    <h3>Alasan Cuti</h3>
                    <textarea 
                        name="alasan" 
                        placeholder="Masukkan alasan cuti"
                        rows="4"
                        style="
                            width:100%;
                            padding:10px 14px;
                            border-radius:8px;
                            border:1px solid #d8e1ef;
                            background:#eef3fb;
                            resize:none;
                        "
                    ></textarea>
                </div>

                <div class="box">
                <h3>Lama Cuti</h3>

                <div class="form-row">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="tgl_mulai" id="tgl_mulai" required>
                </div>

                <div class="form-row">
                    <label>Tanggal Selesai</label>
                    <input type="date" name="tgl_selesai" id="tgl_selesai" required>
                </div>

                <div class="form-row">
                    <label>Jumlah Hari</label>
                    <input type="text" name="jumlah_hari" id="jumlah_hari" placeholder="Jumlah hari" readonly>
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
<script>
function closeError(){
    document.getElementById('errorModal').style.display='none';
}
function hitungHari(){
    let mulai = document.getElementById("tgl_mulai").value;
    let selesai = document.getElementById("tgl_selesai").value;

    if(mulai && selesai){
        let tglMulai = new Date(mulai);
        let tglSelesai = new Date(selesai);
        let selisih = (tglSelesai - tglMulai) / (1000 * 60 * 60 * 24) + 1;

        if(selisih >= 0){
            document.getElementById("jumlah_hari").value = selisih;
        }
    }
}

document.getElementById("tgl_mulai").addEventListener("change", hitungHari);
document.getElementById("tgl_selesai").addEventListener("change", hitungHari);
</script>
</body>
</html>