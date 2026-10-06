<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Export Data Karyawan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 20px; }
        .company-name { font-size: 16px; font-weight: bold; text-align: center; margin-bottom: 5px; }
        .report-title { font-size: 14px; font-weight: bold; text-align: center; margin-bottom: 20px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f0f0f0; }
        @media print {
            @page { size: landscape; margin: 1cm; }
            button { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <header>
        <div class="company-name"><?= $this->session->userdata('nama_perusahaan') ?? 'TGM ERP' ?></div>
        <div class="report-title">DATA KARYAWAN</div>
        <div style="margin-bottom: 10px;">Tanggal Export: <?= date('d/m/Y H:i:s') ?></div>
    </header>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama Lengkap</th>
                <th>L/P</th>
                <th>Jabatan</th>
                <th>Divisi</th>
                <th>Cabang</th>
                <th>Status</th>
                <th>Tanggal Masuk</th>
                <th>No. HP</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($karyawan as $row): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row->nik ?></td>
                <td><?= $row->nama_lengkap ?></td>
                <td><?= $row->jenis_kelamin == 'Laki-Laki' ? 'L' : 'P' ?></td>
                <td><?= $row->nama_jabatan ?></td>
                <td><?= $row->divisi_nama ?></td>
                <td><?= $row->nama_perusahaan ?></td>
                <td><?= $row->status_karyawan ?></td>
                <td><?= $row->tanggal_masuk ?></td>
                <td><?= $row->telepon ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
