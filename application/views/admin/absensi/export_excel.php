<?php
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="absensi_' . $tanggal . '.xls"');
?>
<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>NIK</th>
            <th>Nama Karyawan</th>
            <th>Jabatan</th>
            <th>Tanggal</th>
            <th>Jam Masuk</th>
            <th>Jam Pulang</th>
            <th>Status</th>
            <th>Keterangan</th>
            <th>Device</th>
            <th>User Agent</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($absensi)): ?>
            <?php $no = 1;
            foreach ($absensi as $absen): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $absen->nik ?></td>
                    <td><?= $absen->nama_lengkap ?></td>
                    <td><?= $absen->jabatan ?></td>
                    <td><?= date('d/m/Y', strtotime($absen->tanggal)) ?></td>
                    <td><?= $absen->jam_masuk ? $absen->jam_masuk : '-' ?></td>
                    <td><?= $absen->jam_pulang ? $absen->jam_pulang : '-' ?></td>
                    <td><?= $absen->status ?></td>
                    <td><?= $absen->keterangan ? $absen->keterangan : '-' ?></td>
                    <td><?= $absen->device_type ? $absen->device_type : '-' ?></td>
                    <td><?= $absen->user_agent ? $absen->user_agent : '-' ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="9" style="text-align: center;">Tidak ada data absensi</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>