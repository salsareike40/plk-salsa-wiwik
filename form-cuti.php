<?php
date_default_timezone_set("Asia/Jakarta");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Pengajuan Cuti Pegawai</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border: 1px solid #ccc;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        h4 {
            text-align: center;
            margin-top: 0;
            font-weight: normal;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table, th, td {
            border: 1px solid #000;
        }

        td {
            padding: 8px;
            vertical-align: top;
        }

        input[type=text],
        input[type=date],
        input[type=number],
        textarea {
            width: 100%;
            padding: 5px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
        }

        .checkbox-group label {
            display: block;
        }

        .btn {
            margin-top: 20px;
            padding: 10px 25px;
            border: none;
            background: #3c8dbc;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        .btn:hover {
            background: #367fa9;
        }

        .right {
            text-align: right;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>FORMULIR PERMINTAAN DAN PEMBERIAN CUTI</h2>
    <h4>Pegawai Negeri Sipil</h4>

    <form method="post" action="simpan_cuti.php">

        <!-- A. DATA PEGAWAI -->
        <table>
            <tr>
                <td colspan="4"><b>I. DATA PEGAWAI</b></td>
            </tr>
            <tr>
                <td width="20%">Nama</td>
                <td width="30%"><input type="text" name="nama"></td>
                <td width="20%">NIP</td>
                <td width="30%"><input type="text" name="nip"></td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td><input type="text" name="jabatan"></td>
                <td>Unit Kerja</td>
                <td><input type="text" name="unit_kerja"></td>
            </tr>
        </table>

        <!-- B. JENIS CUTI -->
        <table>
            <tr>
                <td colspan="4"><b>II. JENIS CUTI YANG DIAMBIL</b></td>
            </tr>
            <tr>
                <td colspan="4" class="checkbox-group">
                    <label><input type="radio" name="jenis_cuti" value="Cuti Tahunan"> Cuti Tahunan</label>
                    <label><input type="radio" name="jenis_cuti" value="Cuti Besar"> Cuti Besar</label>
                    <label><input type="radio" name="jenis_cuti" value="Cuti Sakit"> Cuti Sakit</label>
                    <label><input type="radio" name="jenis_cuti" value="Cuti Melahirkan"> Cuti Melahirkan</label>
                    <label><input type="radio" name="jenis_cuti" value="Cuti Alasan Penting"> Cuti Karena Alasan Penting</label>
                    <label><input type="radio" name="jenis_cuti" value="Cuti Diluar Tanggungan"> Cuti di Luar Tanggungan Negara</label>
                </td>
            </tr>
        </table>

        <!-- C. ALASAN CUTI -->
        <table>
            <tr>
                <td><b>III. ALASAN CUTI</b></td>
            </tr>
            <tr>
                <td><textarea name="alasan_cuti" rows="3"></textarea></td>
            </tr>
        </table>

        <!-- D. LAMA CUTI -->
        <table>
            <tr>
                <td colspan="4"><b>IV. LAMA CUTI</b></td>
            </tr>
            <tr>
                <td width="20%">Selama</td>
                <td width="30%"><input type="number" name="lama_cuti"> Hari</td>
                <td width="20%">Mulai Tanggal</td>
                <td width="30%"><input type="date" name="tgl_mulai"></td>
            </tr>
            <tr>
                <td>Sampai Tanggal</td>
                <td colspan="3"><input type="date" name="tgl_selesai"></td>
            </tr>
        </table>

        <!-- E. ALAMAT CUTI -->
        <table>
            <tr>
                <td colspan="4"><b>V. ALAMAT SELAMA MENJALANKAN CUTI</b></td>
            </tr>
            <tr>
                <td colspan="3"><textarea name="alamat_cuti" rows="2"></textarea></td>
                <td>No. Telp<br><input type="text" name="no_telp"></td>
            </tr>
        </table>

        <!-- F. CATATAN CUTI -->
        <table>
            <tr>
                <td colspan="4"><b>VI. CATATAN CUTI</b></td>
            </tr>
            <tr>
                <td>N-2</td>
                <td><input type="number" name="sisa_n2"></td>
                <td>N-1</td>
                <td><input type="number" name="sisa_n1"></td>
            </tr>
            <tr>
                <td>N</td>
                <td colspan="3"><input type="number" name="sisa_n"></td>
            </tr>
        </table>

        <div class="right">
            <button class="btn" type="submit">Ajukan Cuti</button>
        </div>

    </form>
</div>

</body>
</html>
