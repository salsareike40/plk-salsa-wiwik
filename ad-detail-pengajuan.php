<?php
include "conn.php";

$id = $_GET['id'];

$q = mysqli_query($conn,"
    SELECT 
        cuti.*,
        pegawai.nama_pegawai,
        pegawai.jabatan,
        pegawai.unit_kerja
    FROM cuti
    LEFT JOIN pegawai 
        ON cuti.nip = pegawai.nip
    WHERE cuti.id = '$id'
");

$d = mysqli_fetch_assoc($q);
// ===== HITUNG SISA CUTI =====
$jatah = 12;

// total cuti yang sudah disetujui (selain melahirkan)
$qPakai = mysqli_query($conn,"
    SELECT SUM(jumlah_hari) AS total
    FROM cuti
    WHERE nip='".$d['nip']."'
    AND status='Disetujui'
");

$dataPakai = mysqli_fetch_assoc($qPakai);
$terpakai = $dataPakai['total'] ?? 0;

$sisaCuti = $jatah - $terpakai;

if($sisaCuti < 0){
    $sisaCuti = 0;
}
?>

<style>
.detail-wrapper{
    font-family:'Inter',sans-serif;
    color:#334155;
}

.section{
    margin-bottom:20px;
}

.section-title{
    font-size:13px;
    font-weight:600;
    color:#64748b;
    margin-bottom:8px;
    text-transform:uppercase;
    letter-spacing:.5px;
}

.grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:16px;
    align-items:start; /* 🔥 ini penting */
}

.card{
    background:#ffffff;
    border:1px solid #e5e7eb;
    border-radius:14px;
    padding:16px 18px;
    font-size:14px;

    display:flex;
    flex-direction:column;
    gap:10px; /* 🔥 pengganti margin-top */
}

.label{
    font-size:12px;
    color:#64748b;
    margin-bottom:4px;
}

.value{
    font-weight:600;
    color:#0f172a;
}

.box{
    background:#ffffff;
    border:1px solid #e5e7eb;
    border-radius:14px;
    padding:14px 16px;
    font-size:14px;
    line-height:1.6;
}

.badge{
    display:inline-block;
    padding:6px 14px;
    border-radius:999px;
    font-size:12px;
    font-weight:600;
}

.wait{ background:#fde68a; color:#7a5200; }
.ok{ background:#bbf7d0; color:#166534; }
.no{ background:#fecaca; color:#7f1d1d; }
</style>

<div class="detail-wrapper">

    <!-- DATA PEGAWAI & CUTI -->
    <div class="grid section">
        <div class="card">
            <div class="label">Nama</div>
            <div class="value"><?= $d['nama_pegawai'] ?></div>

            <div class="label">NIP</div>
            <div class="value"><?= $d['nip'] ?></div>

            <div class="label">Jabatan</div>
            <div class="value"><?= $d['jabatan'] ?></div>

            <div class="label">Unit Kerja</div>
            <div class="value"><?= $d['unit_kerja'] ?></div>
            <div class="label">Sisa Cuti</div>
            <div class="value"><?= $sisaCuti ?> Hari</div>
        </div>

        <div class="card">
            <div class="label">Jenis Cuti</div>
            <div class="value"><?= $d['jenis_cuti'] ?></div>

            <div class="label">Tanggal</div>
            <div class="value">
                <?= date('d M Y',strtotime($d['tgl_mulai'])) ?> –
                <?= date('d M Y',strtotime($d['tgl_selesai'])) ?>
            </div>

            <div class="label">Lama Cuti</div>
            <div class="value"><?= $d['jumlah_hari'] ?> Hari</div>

            <div class="label">Status</div>
            <?php if($d['status']=='Menunggu'): ?>
                <span class="badge wait">Menunggu</span>
            <?php elseif($d['status']=='Disetujui'): ?>
                <span class="badge ok">Disetujui</span>
            <?php else: ?>
                <span class="badge no">Ditolak</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- ALASAN -->
    <div class="section">
        <div class="section-title">Alasan Pegawai</div>
        <div class="box">
            <?= nl2br($d['alasan']) ?>
        </div>
    </div>

    <!-- CATATAN ADMIN -->
    <div class="section">
        <div class="section-title">Catatan Admin</div>
        <div class="box">
            <?= $d['catatan'] ? nl2br($d['catatan']) : '<em style="color:#94a3b8">Belum ada catatan</em>' ?>
        </div>
    </div>

</div>