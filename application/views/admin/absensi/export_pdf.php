<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>ATT_REPORT_<?= date('Ymd', strtotime($tanggal)) ?></title>
    <style>
        @page {
            size: A4;
            margin: 0.8cm 1cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8pt;
            color: #1a1a1a;
            line-height: 1.1;
            margin: 0;
            padding: 0;
        }

        /* Header Compact */
        .header {
            width: 100%;
            border-bottom: 2px solid #000;
            margin-bottom: 10px;
            padding-bottom: 5px;
        }

        .title {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
        }

        .subtitle {
            font-size: 9pt;
            color: #444;
            margin: 2px 0;
        }

        /* Stats Bar - Single Line */
        .stats-bar {
            width: 100%;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            background: #f9f9f9;
        }

        .stats-bar td {
            padding: 4px 10px;
            border: none;
            text-align: center;
            font-size: 7.5pt;
        }

        .stats-bar b {
            font-size: 9pt;
        }

        /* Ultra Compact Table */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #eee;
            border: 1px solid #ccc;
            padding: 4px;
            font-weight: bold;
            font-size: 7pt;
            text-align: left;
        }

        td {
            border: 1px solid #ddd;
            padding: 3px 4px;
            vertical-align: middle;
        }

        .no {
            text-align: center;
            width: 15px;
            color: #777;
            font-size: 7pt;
        }

        .time {
            text-align: center;
            width: 40px;
            font-weight: bold;
        }

        .status {
            text-align: center;
            width: 60px;
        }

        .geo {
            text-align: center;
            width: 35px;
            font-size: 6.5pt;
        }

        /* Status Colors */
        .st-ok {
            color: #008000;
        }

        .st-late {
            color: #d00000;
        }

        .badge {
            font-weight: bold;
            padding: 1px 4px;
            border-radius: 3px;
            text-transform: uppercase;
            font-size: 6.5pt;
        }

        .bg-h {
            background: #e6ffed;
            color: #1e4620;
        }

        .bg-i {
            background: #fff8e1;
            color: #795548;
        }

        .bg-a {
            background: #ffeef0;
            color: #b71c1c;
        }

        .footer {
            margin-top: 10px;
            width: 100%;
        }

        .signature {
            float: right;
            width: 160px;
            text-align: center;
        }

        .sig-space {
            height: 40px;
        }
    </style>
</head>

<body onload="window.print()">
    <div class="header">
        <table width="100%" border="0">
            <tr>
                <td>
                    <div class="title">LAPORAN ABSENSI HARIAN</div>
                    <div class="subtitle">TGM Group - Daily Monitoring Presence</div>
                </td>
                <td align="right" valign="bottom">
                    <b>Tanggal: <?= date('d/m/Y', strtotime($tanggal)) ?></b><br>
                    <span style="font-size: 7pt;">Printed: <?= date('H:i') ?></span>
                </td>
            </tr>
        </table>
    </div>

    <?php
    $total = 0;
    $hadir = 0;
    $izin = 0;
    $sakit = 0;
    $alpha = 0;
    $terlambat = 0;
    $filtered_data = [];
    foreach ($absensi as $a) {
        if (stripos($a->nama_lengkap, 'terminal') !== false)
            continue;
        $total++;
        if ($a->status == 'Hadir') {
            $hadir++;
            if ($a->jam_masuk && strtotime($a->jam_masuk) > strtotime('08:15:00'))
                $terlambat++;
        } else if ($a->status == 'Izin')
            $izin++;
        else if ($a->status == 'Sakit')
            $sakit++;
        else if ($a->status == 'Alpha')
            $alpha++;
        $filtered_data[] = $a;
    }
    ?>

    <table class="stats-bar">
        <tr>
            <td>TOTAL: <b><?= $total ?></b></td>
            <td>HADIR: <b class="st-ok"><?= $hadir ?></b></td>
            <td>LATE: <b class="st-late"><?= $terlambat ?></b></td>
            <td>IZIN/SAKIT: <b><?= $izin + $sakit ?></b></td>
            <td>ALPHA: <b class="st-late"><?= $alpha ?></b></td>
            <td>RATIO: <b><?= $total > 0 ? round(($hadir / $total) * 100) : 0 ?>%</b></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="15">#</th>
                <th width="50">NIK</th>
                <th>NAMA KARYAWAN</th>
                <th width="35">MASUK</th>
                <th width="35">PULANG</th>
                <th width="50">STATUS</th>
                <th width="40">DEVICE</th>
                <th width="30">GEO</th>
                <th>CATATAN</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($filtered_data)): ?>
                <?php $no = 1;
                foreach ($filtered_data as $absen): ?>
                    <tr>
                        <td class="no"><?= $no++ ?></td>
                        <td style="font-size: 7.5pt;"><?= $absen->nik ?></td>
                        <td><b><?= strtoupper($absen->nama_lengkap) ?></b></td>
                        <td class="time">
                            <?php if ($absen->jam_masuk): ?>
                                <span class="<?= strtotime($absen->jam_masuk) > strtotime('08:15:00') ? 'st-late' : 'st-ok' ?>">
                                    <?= date('H:i', strtotime($absen->jam_masuk)) ?>
                                </span>
                            <?php else: ?>-<?php endif; ?>
                        </td>
                        <td class="time"><?= $absen->jam_pulang ? date('H:i', strtotime($absen->jam_pulang)) : '-' ?></td>
                        <td class="status">
                            <?php
                            $st = $absen->status;
                            $cls = "";
                            if ($st == 'Hadir')
                                $cls = "bg-h";
                            else if ($st == 'Izin' || $st == 'Sakit' || strpos($st, 'Cuti') !== false)
                                $cls = "bg-i";
                            else if ($st == 'Alpha')
                                $cls = "bg-a";
                            ?>
                            <span class="badge <?= $cls ?>"><?= $st ?></span>
                        </td>
                        <td style="font-size: 6.5pt; text-align: center;"><?= $absen->device_type ?: '-' ?></td>
                        <td class="geo"><?= !empty($absen->lokasi_absen) ? 'VALID' : '-' ?></td>
                        <td style="font-size: 7pt; color: #555;"><?= $absen->keterangan ? $absen->keterangan : '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" align="center" style="padding:20px;">No Data</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            Manajemen TGM<br>
            <div class="sig-space"></div>
            ( ____________________ )
        </div>
        <div style="font-size: 6.5pt; color: #888; margin-top: 50px;">
            * Dokumen resmi ERP System. Generated on <?= date('d/m/Y H:i') ?>
        </div>
    </div>
</body>

</html>