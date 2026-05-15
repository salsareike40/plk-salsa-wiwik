<?php
session_start();
include "conn.php";
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
    header("Location: index.php");
    exit;
}

$username = $_SESSION['username'];
/* ====== AJAX DETAIL ====== */
if (isset($_GET['ajax']) && $_GET['ajax'] === 'detail') {

    $id   = $_GET['id'];
    $mode = $_GET['mode'] ?? 'view';

    $qJabatan = mysqli_query($conn,"SELECT DISTINCT jabatan FROM pegawai WHERE jabatan != ''");
$qUnit    = mysqli_query($conn,"SELECT DISTINCT unit_kerja FROM pegawai WHERE unit_kerja != ''");

    $q = mysqli_query($conn,"
        SELECT nip, nama_pegawai, jabatan, unit_kerja
        FROM pegawai
        WHERE id_pegawai='$id'
    ");

    $d = mysqli_fetch_assoc($q);

    if ($mode == 'view') {
        ?>
        <table style="width:100%;font-size:14px">
    <tr>
        <td width="35%">NIP</td>
        <td width="5%" style="text-align:center">:</td>
        <td><?= $d['nip'] ?></td>
    </tr>
    <tr>
        <td>Nama</td>
        <td style="text-align:center">:</td>
        <td><?= $d['nama_pegawai'] ?></td>
    </tr>
    <tr>
        <td>Jabatan</td>
        <td style="text-align:center">:</td>
        <td><?= $d['jabatan'] ?></td>
    </tr>
    <tr>
        <td>Unit Kerja</td>
        <td style="text-align:center">:</td>
        <td><?= $d['unit_kerja'] ?></td>
    </tr>
</table>

        <div style="margin-top:18px;display:flex;gap:10px;justify-content:center">
            <a href="javascript:void(0)"
               class="btn-detail"
               style="background:#3b6fc4"
               onclick="loadEdit(<?= $id ?>)">✏ Edit</a>

            <a href="javascript:void(0)"
                class="btn-detail"
                style="background:#e74c3c"
                onclick="confirmHapus(<?= $id ?>)">
                🗑 Hapus
            </a>
        </div>
        <?php
    } else {
        ?>
        <form method="post" action="update-pegawai.php">
            <input type="hidden" name="id_pegawai" value="<?= $id ?>">

            <table class="detail-table" style="width:100%;font-size:14px">
                <tr>
                    <td width="40%">NIP :</td>
                    <td><input type="text" name="nip" value="<?= $d['nip'] ?>" required></td>
                </tr>
                <tr>
                    <td>Nama :</td>
                    <td><input type="text" name="nama_pegawai" value="<?= $d['nama_pegawai'] ?>" required></td>
                </tr>
                <tr>
                    <td>Jabatan :</td>
                    <td>
                        <select name="jabatan" required>
                            <option value="">Pilih Jabatan</option>

                            <?php
                            $listJabatan = [
                                "Kepala Dinas",
                                "Sekretaris",
                                "Kepala Bidang Persandian dan Keamanan Informasi",
                                "Kepala Bidang Penyelenggaraan e-Government",
                                "Arsiparis Ahli Madya",
                                "Kepala Bidang Pengelolaan Informasi dan Komunikasi Publik",
                                "Kepala Bidang Statistik",
                                "Sandiman Ahli Muda",
                                "Pranata Humas Ahli Muda",
                                "Pranata Komputer Ahli Muda",
                                "Kepala Sub Bagian Umum dan Kepegawaian",
                                "Statistisi Ahli Muda",
                                "Kepala Sub Bagian Keuangan",
                                "Arsiparis Mahir",
                                "Penelaah Teknis Kebijakan",
                                "Penata Layanan Operasional",
                                "Pranata Hubungan Masyarakat Ahli Pertama",
                                "Pranata Komputer Ahli Pertama",
                                "Statistisi Ahli Pertama",
                                "Pengadministrasi Perkantoran"
                            ];

                            foreach($listJabatan as $jab){
                            ?>

                            <option value="<?= $jab ?>"
                                <?= ($jab == $d['jabatan']) ? 'selected' : '' ?>>
                                <?= $jab ?>
                            </option>

                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Unit Kerja :</td>
                    <td>
                        <select name="unit_kerja" required>
                            <option value="">Pilih Unit Kerja</option>

                            <?php
                            $listUnit = [
                                "Bidang Persandian dan Keamanan Informasi",
                                "Bidang Penyelenggaraan e-Government",
                                "Bidang Pengelolaan Informasi dan Komunikasi Publik",
                                "Bidang Statistik",
                                "Sub Bagian Umum dan Kepegawaian",
                                "Sub Bagian Keuangan"
                            ];

                            foreach($listUnit as $unit){
                            ?>

                            <option value="<?= $unit ?>"
                                <?= ($unit == $d['unit_kerja']) ? 'selected' : '' ?>>
                                <?= $unit ?>
                            </option>

                            <?php } ?>
                        </select>
                    </td>
                </tr>
            </table>

            <div style="margin-top:18px;display:flex;gap:10px;justify-content:center">
                <button type="submit" class="btn-detail" style="background:#27ae60">💾 Simpan</button>
                <a href="javascript:void(0)" class="btn-detail" style="background:#7f8c8d"
                   onclick="openDetail(<?= $id ?>)">❌ Batal</a>
            </div>
        </form>
        <?php
    }

    exit; 
}
$q = $_GET['q'] ?? '';

$where = "
WHERE status = 'aktif'
AND role != 'admin'
AND nama_pegawai IS NOT NULL
AND nama_pegawai != ''
";

if ($q != '') {
    $q = mysqli_real_escape_string($conn, $q);
    $where .= " AND (
        nama_pegawai LIKE '%$q%'
        OR nip LIKE '%$q%'
    )";
}

$query = mysqli_query($conn,"
    SELECT id_pegawai, nip, nama_pegawai, jabatan, unit_kerja
    FROM pegawai
    $where
    ORDER BY id_pegawai DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Pegawai</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Inter',sans-serif;
}

/* ===== BODY ===== */
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
    padding:30px 22px;
}

.logo{
    text-align:center;
    margin-bottom:40px;
}
.logo img{
    width:140px;
    margin-bottom:20px;
}
.logo h2{
    font-size:22px;
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
    padding:0;
}

/* ===== HEADER ===== */
.page-header{
    background:#ffffff;
    padding:20px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 3px 6px rgba(0,0,0,.08);
}

.page-header h1{
    font-size:26px;
    font-weight:700;
    color:#0b57a4;
}

.header-user{
    display:flex;
    align-items:center;
    gap:10px;
    font-size:15px;
}
.header-user a{
    color:#0b57a4;
    text-decoration:none;
    font-weight:600;
}

/* ===== CONTENT ===== */
.content{
    padding:30px;
}

/* TOOLBAR */
.toolbar{
    display:flex;
    align-items:center;
    gap:16px;
    margin-bottom:25px;
}
.btn-add{
    background:#0b5aa6;
    color:#fff;
    padding:12px 18px;
    border-radius:10px;
    text-decoration:none;
    font-weight:600;
    display:flex;
    align-items:center;
    gap:8px;
    border:none;
}
.search{
    flex:1;
}
.search input{
    width:100%;
    padding:12px 18px;
    border-radius:10px;
    border:none;
    background:#dde4ee;
    font-size:14px;
}

/* ===== TABLE CARD ===== */
.table-card{
    background:#f7f9fd;
    border-radius:18px;
    padding:18px;
}

table{
    width:100%;
    border-collapse:collapse;
    background:#ffffff;
    border-radius:14px;
    overflow:hidden;
    box-shadow:0 10px 20px rgba(0,0,0,.08);
}

thead{
    background:#0b5aa6;   /* biru Kominfo */
}

thead th{
    color:#ffffff;
    font-weight:600;
}

th, td{
    padding:16px;
    font-size:15px;
}
th{
    color:#555;
    font-weight:600;
}
tbody tr{
    border-bottom:1px solid #edf1f7;
}
tbody tr:last-child{
    border-bottom:none;
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
/* ===== MODAL ===== */
.modal-overlay{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.35);
    display:none;
    align-items:center;
    justify-content:center;
    z-index:999;
}

.modal{
    width:480px;
    background:#f4f6fb;
    border-radius:16px;
    box-shadow:0 20px 40px rgba(0,0,0,.25);

    transform: translateY(-80px);
}

.modal-header{
    padding:16px 20px;
    background:#e9edf5;
    border-radius:16px 16px 0 0;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.modal-header h3{
    font-size:18px;
    color:#1f3b6d;
}

.modal-header .close{
    cursor:pointer;
    font-size:18px;
}

.modal-body{
    padding:20px;
}

.modal-body label{
    display:block;
    font-size:14px;
    font-weight:500;
    margin-bottom:6px;
}

.modal-body label span{
    color:red;
}

.modal-body input,
.modal-body select{
    width:100%;
    padding:10px 12px;
    border-radius:8px;
    border:1px solid #cfd6e3;
    margin-bottom:14px;
}

.row{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:14px;
}

.modal-footer{
    padding:16px 20px;
    display:flex;
    justify-content:flex-end;
    gap:10px;
}

.btn-cancel{
    background:#e0e4ec;
    border:none;
    padding:8px 16px;
    border-radius:8px;
    cursor:pointer;
}

.btn-save{
    background:#3b6fc4;
    color:#fff;
    border:none;
    padding:8px 18px;
    border-radius:8px;
    cursor:pointer;
}

table th,
table td{
    text-align: center;
    vertical-align: middle;
}
button{
    cursor: pointer;
}
.detail-table td:nth-child(2){
    width: 12px;
    text-align: center;
    vertical-align: middle;
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
        📥 Pengajuan Cuti
    </a>

    <a href="ad-statusajuan.php" class="<?= $page=='ad-statusajuan.php'?'active':'' ?>">
        ⚠️ Status Pengajuan
    </a>
</div>

</div>

<!-- MAIN -->
<div class="main">

    <!-- HEADER -->
    <div class="page-header">
        <h1>Data Pegawai</h1>
        <div class="header-user">
            👤 <?= $username ?> | <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <!-- TOOLBAR -->
        <div class="toolbar">
            <button class="btn-add" onclick="openModal()">+ Tambah Pegawai</button>
        <form method="GET" style="display:flex;gap:12px">

    <input
    type="text"
    name="q"
    placeholder="Cari Nama / NIP..."
    value="<?= $_GET['q'] ?? '' ?>"
    style="
    padding:10px 16px;
    border-radius:20px;
    border:1px solid #ccc;
    width:260px
    ">

    <button
    type="submit"
    style="
    padding:10px 16px;
    border:none;
    background:#0b57a4;
    color:white;
    border-radius:10px
    ">
    Cari
    </button>

</form>


        </div>

        <!-- TABLE -->
        <div class="table-card">
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
                <?php $no=1; while($row=mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['nip'] ?></td>
                        <td><?= $row['nama_pegawai'] ?></td>
                        <td><?= $row['jabatan'] ?></td>
                        <td><?= $row['unit_kerja'] ?></td>
                        <td>
                            
                            <?php if($row['jabatan'] != '' && $row['unit_kerja'] != ''): ?>
                                <a href="javascript:void(0)"
                                    class="btn-detail"
                                    onclick="openDetail(<?= $row['id_pegawai'] ?>)">
                                    👁 Detail
                                    </a>

                            <?php endif; ?>
                            

                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
<!-- MODAL TAMBAH PEGAWAI -->
<div class="modal-overlay" id="modalTambah">
    <div class="modal">

        <div class="modal-header">
            <h3>Tambah Pegawai</h3>
            <span class="close" onclick="closeModal()">✕</span>
        </div>

        <form action="proses-pegawai.php" method="post">
            <div class="modal-body">

                <label>NIP <span>*</span></label>
                <input type="text" name="nip" placeholder="Masukkan NIP" required>

                <label>Nama <span>*</span></label>
                <input type="text" name="nama" placeholder="Masukkan Nama" required>

                <label>No Telpon <span>*</span></label>
                <input type="text" name="no_telp" placeholder="Masukkan No Telpon" required>

                <div class="row">
                    <div>
                        <label>Jabatan <span>*</span></label>
                        <select name="jabatan" required>
                            <option value="">Pilih Jabatan</option>
                            <option>Kepala Dinas</option>
                            <option>Sekretaris</option>
                            <option>Kepala Bidang Persandian dan Keamanan Informasi</option>
                            <option>Kepala Bidang Penyelenggaraan e-Government</option>
                            <option>Arsiparis Ahli Madya</option>
                            <option>Kepala Bidang Pengelolaan Informasi dan Komunikasi Publik</option>
                            <option>Kepala Bidang Statistik</option>
                            <option>Sandiman Ahli Muda</option>
                            <option>Pranata Humas Ahli Muda</option>
                            <option>Pranata Komputer Ahli Muda</option>
                            <option>Kepala Sub Bagian Umum dan Kepegawaian</option>
                            <option>Statistisi Ahli Muda</option>
                            <option>Kepala Sub Bagian Keuangan</option>
                            <option>Arsiparis Mahir</option>
                            <option>Penelaah Teknis Kebijakan</option>
                            <option>Penata Layanan Operasional</option>
                            <option>Pranata Hubungan Masyarakat Ahli Pertama</option>
                            <option>Pranata Komputer Ahli Pertama</option>
                            <option>Statistisi Ahli Pertama</option>
                            <option>Pengadministrasi Perkantoran</option>
                        </select>
                    </div>

                    <div>
                        <label>Unit Kerja <span>*</span></label>
                        <select name="unit_kerja" required>
                            <option value="">Pilih Unit Kerja</option>
                            <option>Bidang Persandian dan Keamanan Informasi</option>
                            <option>Bidang Penyelenggaraan e-Government</option>
                            <option>Bidang Pengelolaan Informasi dan Komunikasi Publik</option>
                            <option>Bidang Statistik</option>
                            <option>Sub Bagian Umum dan Kepegawaian</option>
                            <option>Sub Bagian Keuangan</option>
                        </select>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn-save">Simpan</button>
            </div>
        </form>

    </div>
</div>
<script>
function openModal(){
    document.getElementById('modalTambah').style.display = 'flex';
}

function closeModal(){
    document.getElementById('modalTambah').style.display = 'none';
}
function openDetail(id){
    document.getElementById('modalDetail').style.display = 'flex';

    fetch('data-pegawai.php?ajax=detail&id=' + id)
        .then(res => res.text())
        .then(html => {
            document.getElementById('detailContent').innerHTML = html;
        });
}

function closeDetail(){
    document.getElementById('modalDetail').style.display = 'none';
}
function loadEdit(id){
    fetch('data-pegawai.php?ajax=detail&id=' + id + '&mode=edit')
        .then(res => res.text())
        .then(html => {
            document.getElementById('detailContent').innerHTML = html;
        });
}
</script>
<!-- MODAL DETAIL PEGAWAI -->
<div class="modal-overlay" id="modalDetail">
    <div class="modal">

        <div class="modal-header">
            <h3>Detail Pegawai</h3>
            <span class="close" onclick="closeDetail()">✕</span>
        </div>

        <div class="modal-body" id="detailContent">
            <p>Loading...</p>
        </div>

        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeDetail()">Tutup</button>
        </div>

    </div>
</div>

<div id="modalHapus" style="
display:none;
position:fixed;
inset:0;
background:rgba(0,0,0,.4);
align-items:center;
justify-content:center;
z-index:9999">

    <div style="
    background:#fff;
    padding:30px;
    border-radius:16px;
    text-align:center;
    width:320px;
    box-shadow:0 20px 40px rgba(0,0,0,.25)">

        <h3 style="margin-bottom:10px">Yakin hapus pegawai?</h3>

        <div style="display:flex;gap:10px;justify-content:center">

            <button onclick="prosesHapus()"
                style="background:#e74c3c;color:#fff;
                border:none;padding:10px 18px;border-radius:10px">
                Hapus
            </button>

            <button onclick="closeHapus()"
                style="background:#ccc;border:none;
                padding:10px 18px;border-radius:10px">
                Batal
            </button>

        </div>

    </div>
</div>

<script>
let idHapus = null;

function confirmHapus(id){
    idHapus = id;
    document.getElementById('modalHapus').style.display = 'flex';
}

function closeHapus(){
    document.getElementById('modalHapus').style.display = 'none';
}

function prosesHapus(){
    window.location = 'hapus-pegawai.php?id=' + idHapus;
}
</script>

<?php if(isset($_SESSION['error'])): ?>
<div id="notifError" style="
position:fixed;
top:80px; /* biar turun dikit dari header */
right:30px; /* kasih jarak kanan */
background:#e74c3c;
color:#fff;
padding:12px 20px;
border-radius:10px;
box-shadow:0 10px 25px rgba(0,0,0,.2);
z-index:9999;
min-width:220px;
">

    <?= $_SESSION['error'] ?>

</div>

<script>
setTimeout(() => {
    document.getElementById('notifError').style.display='none';
}, 3000);
</script>

<?php unset($_SESSION['error']); ?>
<?php endif; ?>

</body>
</html>