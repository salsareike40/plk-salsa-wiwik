session_start();
include "conn.php";

$nama = $_SESSION['username'];
$jenis = $_POST['jenis_cuti'];
$tgl  = date('Y-m-d');
$hari = $_POST['jumlah_hari'];

mysqli_query($conn, "
    INSERT INTO cuti (nama, jenis_cuti, tgl_pengajuan, jumlah_hari, status)
    VALUES ('$nama','$jenis','$tgl','$hari','Menunggu')
");
