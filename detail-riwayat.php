<?php
include "conn.php";

$id = $_GET['id'] ?? $_POST['id'];

$q = mysqli_query($conn,"
    SELECT 
        cuti.*,
        pegawai.nama_pegawai,
        pegawai.jabatan,
        pegawai.unit_kerja
    FROM cuti
    JOIN pegawai ON cuti.nip = pegawai.nip
    WHERE cuti.id='$id'
");

$data = mysqli_fetch_assoc($q);
?>

<form method="post" action="proses-cuti.php">

<div id="printArea">

<!-- HEADER PDF -->
<div class="print-header">

    <img src="aset/kominfo.png" class="logo-print">

    <div class="header-text">
        <h1>SISTEM INFORMASI CUTI PEGAWAI</h1>
        <p>Pemerintah Kota Mataram</p>
        <p>Jl. Contoh No. 123 Mataram</p>
    </div>

</div>

<hr class="garis-print">

<h2 class="judul-print">
    DETAIL PENGAJUAN CUTI
</h2>

<input type="hidden" name="id" value="<?= $data['id'] ?>">

<table class="table-print">

<tr>
    <td><b>Nama</b></td>
    <td><?= $data['nama_pegawai'] ?></td>

    <td><b>Jenis Cuti</b></td>
    <td><?= $data['jenis_cuti'] ?></td>
</tr>

<tr>
    <td><b>NIP</b></td>
    <td><?= $data['nip'] ?></td>

    <td><b>Tanggal</b></td>
    <td>
        <?= date('d M Y', strtotime($data['tgl_mulai'])) ?>
        -
        <?= date('d M Y', strtotime($data['tgl_selesai'])) ?>
    </td>
</tr>

<tr>
    <td><b>Jabatan</b></td>
    <td><?= $data['jabatan'] ?></td>

    <td><b>Lama Cuti</b></td>
    <td><?= $data['jumlah_hari'] ?> Hari</td>
</tr>

<tr>
    <td><b>Unit Kerja</b></td>
    <td><?= $data['unit_kerja'] ?></td>

    <td><b>Status</b></td>
    <td>
        <span class="status-print">
            <?= $data['status'] ?>
        </span>
    </td>
</tr>

<tr>
    <td><b>Alasan</b></td>
    <td colspan="3">
        <?= $data['alasan'] ?: 'Tidak ada alasan' ?>
    </td>
</tr>

<tr>
    <td><b>Catatan Admin</b></td>
    <td colspan="3">
        <?= nl2br(htmlspecialchars($data['catatan'] ?? '-')) ?>
    </td>
</tr>

</table>




<style>
.btn{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:12px 22px;
    border-radius:14px;
    font-weight:600;
    font-size:14px;
    border:none;
    cursor:pointer;
    transition:.25s;
    box-shadow:0 8px 20px rgba(0,0,0,.12);
}

.btn-reject{
    background:linear-gradient(135deg,#ff6b6b,#e74c3c);
    color:#fff;
}
.btn-reject:hover{
    transform:translateY(-2px);
    box-shadow:0 12px 26px rgba(231,76,60,.35);
}

.btn-approve{
    background:linear-gradient(135deg,#2ecc71,#27ae60);
    color:#fff;
}
.btn-approve:hover{
    transform:translateY(-2px);
    box-shadow:0 12px 26px rgba(46,204,113,.35);
}

.btn:active{
    transform:scale(.96);
}

@media print {

    body *{
        visibility: hidden;
    }

    #printArea,
    #printArea *{
        visibility: visible;
    }

    #printArea{
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        background: white;
        padding: 40px;
    }

    .no-print{
        display: none !important;
    }
}

.print-header{
    display:flex;
    align-items:center;
    gap:20px;
    margin-bottom:20px;
}

.logo-print{
    width:90px;
}

.header-text h1{
    font-size:30px;
    color:#0b3b75;
    margin-bottom:6px;
}

.header-text p{
    margin:2px 0;
    color:#444;
    font-size:14px;
}

.garis-print{
    border:2px solid #0b3b75;
    margin:25px 0;
}

.judul-print{
    text-align:center;
    color:#0b3b75;
    margin-bottom:30px;
    font-size:32px;
    font-weight:700;
}
.table-print{
    width:100%;
    border-collapse:separate;
    border-spacing:0;
    font-size:15px;
    box-shadow:0 4px 14px rgba(0,0,0,.05);
    border:1px solid #d6dce5;
}

.table-print td{
    border-right:1px solid #d6dce5;
    border-bottom:1px solid #d6dce5;
    padding:14px;
    vertical-align:top;
    width:25%;
}
.table-print tr td:last-child{
    border-right:none;
}
.table-print tr:nth-child(even){
    background:#f8fbff;
}

.status-print{
    background:#d1fae5;
    color:#047857;
    padding:6px 14px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}
.table-print tr:last-child td{
    border-bottom:none
}
</style>



<div class="no-print"
style="
display:flex;
justify-content:flex-end;
gap:12px;
margin-top:20px;
">

    <button
        type="button"
        onclick="window.print()"
        style="
            background:#0b57a4;
            color:#fff;
            border:none;
            padding:12px 24px;
            border-radius:12px;
            font-size:14px;
            font-weight:600;
            cursor:pointer;
        "
    >
        🖨 Cetak PDF
    </button>

    <button
        type="button"
        onclick="closeDetail()"
        style="
            background:#2b7cff;
            color:#fff;
            border:none;
            padding:12px 28px;
            border-radius:12px;
            font-size:14px;
            font-weight:600;
            cursor:pointer;
        "
    >
        OK
    </button>

</div>

</div> <!-- penutup printArea -->

<script>
function closeDetail(){
    document.getElementById('modalDetail').style.display = 'none';
}
</script>

</form>