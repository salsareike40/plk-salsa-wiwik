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
            <?= $data['nama_pegawai'] ?>
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
        <div style="background:#fff;padding:10px 14px;border-radius:12px;margin-bottom:12px">
            <?= $data['alasan'] ?: 'Tidak ada alasan' ?>
        </div>
    </div>
       
    

</div>

<hr style="margin:24px 0;border:1px solid #e0e6ef">

<div style="
font-size:15px;
font-weight:600;
color:#0b57a4;
margin-bottom:8px;
display:flex;
align-items:center;
gap:6px;
">
📝 Catatan Admin
</div>

<textarea
rows="4"
readonly
style="
width:100%;
padding:14px 16px;
border-radius:14px;
border:1px solid #dbe3f0;
background:#f8fafc;
resize:none;
margin-bottom:16px;
font-size:14px;
line-height:1.6;
color:#333;
">
<?php
echo 'Belum ada catatan dari admin';
?>
</textarea>

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

<div style="display:flex;justify-content:flex-end;margin-top:20px">
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

<script>
function closeDetail(){
    document.getElementById('modalDetail').style.display = 'none';
}
</script>

</form>