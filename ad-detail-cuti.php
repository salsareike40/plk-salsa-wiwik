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

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">

    <!-- KOLOM KIRI -->
    <div>
        <label style="font-size:13px;color:#777">Nama</label>
        <div style="background:#fff;padding:10px 14px;border-radius:12px;margin-bottom:12px">
            <?= $data['username'] ?>
        </div>

        <label style="font-size:13px;color:#777">NIP</label>
        <div style="background:#fff;padding:10px 14px;border-radius:12px;margin-bottom:12px">
            <?= $data['nip'] ?>
        </div>

        <label style="font-size:13px;color:#777">Jabatan</label>
        <div style="background:#fff;padding:10px 14px;border-radius:12px;margin-bottom:12px">
            <?= $data['jabatan'] ?>
        </div>

        <label style="font-size:13px;color:#777">Unit Kerja</label>
        <div style="background:#fff;padding:10px 14px;border-radius:12px">
            <?= $data['unit_kerja'] ?>
        </div>
    </div>

    <!-- KOLOM KANAN -->
    <div>
        <label style="font-size:13px;color:#777">Jenis Cuti</label>
        <div style="background:#fff;padding:10px 14px;border-radius:12px;margin-bottom:12px">
            <?= $data['jenis_cuti'] ?>
        </div>

        <label style="font-size:13px;color:#777">Tanggal</label>
        <div style="background:#fff;padding:10px 14px;border-radius:12px;margin-bottom:12px">
            <?= date('d M Y', strtotime($data['tgl_mulai'])) ?>
            -
            <?= date('d M Y', strtotime($data['tgl_selesai'])) ?>
        </div>

        <label style="font-size:13px;color:#777">Lama Cuti</label>
        <div style="background:#fff;padding:10px 14px;border-radius:12px;margin-bottom:12px">
            <?= $data['jumlah_hari'] ?> Hari
        </div>

        <label style="font-size:13px;color:#777">Alasan</label>
        <div style="background:#fff;padding:10px 14px;border-radius:12px">
            <?= nl2br($data['alasan']) ?>
        </div>
    </div>

</div>

<hr style="margin:24px 0;border:1px solid #e0e6ef">

<input type="hidden" name="id" value="<?= $data['id'] ?>">

<textarea
    id="catatan"
    name="catatan"
    rows="4"
    placeholder="Tulis catatan untuk pegawai..."
    style="
        width:100%;
        padding:10px 14px;
        border-radius:12px;
        border:1px solid #ddd;
        resize:none;
        margin-bottom:16px
    "
><?= htmlspecialchars($data['catatan'] ?? '') ?></textarea>

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
</style>

<div style="display:flex;gap:16px;justify-content:flex-end">

    <!-- TIDAK SETUJU -->
    <button 
        type="submit"
        name="keputusan"
        value="Tidak Setuju"
        class="btn btn-reject"
    >
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
            <path d="M18 6L6 18M6 6l12 12"
                  stroke="white"
                  stroke-width="2"
                  stroke-linecap="round"/>
        </svg>
        Tidak Setuju
    </button>

    <!-- SETUJU -->
    <button 
        type="submit"
        name="keputusan"
        value="Setuju"
        class="btn btn-approve"
    >
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
            <path d="M5 13l4 4L19 7"
                  stroke="white"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"/>
        </svg>
        Setuju
    </button>

</div>



</form>