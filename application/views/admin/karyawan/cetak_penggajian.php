<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Penggajian Karyawan - <?= date('F Y', strtotime($bulan . '-01')) ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=JetBrains+Mono:wght@400;700&display=swap');

        :root {
            --primary: #2c3e50;
            --border: #e2e8f0;
            --bg-light: #f8fafc;
        }

        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body {
            font-family: 'Inter', sans-serif;
            font-size: 9px;
            color: #1e293b;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }

        header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary);
        }

        .company-name {
            font-size: 16px;
            font-weight: 900;
            text-transform: uppercase;
            color: var(--primary);
            margin: 0;
        }

        .report-title {
            font-size: 12px;
            font-weight: 700;
            margin: 5px 0;
            color: #64748b;
        }

        .report-period {
            font-size: 10px;
            background: #f1f5f9;
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-block;
            font-weight: 700;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 6px;
            border: 1px solid var(--border);
            text-align: center;
            vertical-align: middle;
        }

        .table td {
            padding: 6px;
            border: 1px solid var(--border);
            vertical-align: middle;
        }

        .font-mono {
            font-family: 'JetBrains+Mono', monospace;
            font-size: 8.5px;
            font-weight: 600;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .fw-bold {
            font-weight: 700;
        }

        .text-primary {
            color: #2563eb;
        }

        .text-rose {
            color: #e11d48;
        }

        .text-slate {
            color: #64748b;
        }

        .karyawan-box {
            line-height: 1.2;
        }

        .nik-text {
            font-size: 7px;
            color: #94a3b8;
            margin-top: 2px;
        }

        .bg-light {
            background-color: #f1f5f9;
        }

        tfoot td {
            background-color: #f8fafc;
            font-weight: 900;
            font-size: 9px;
            padding: 10px 6px;
            border-top: 2px solid var(--primary);
        }

        .footer-sign {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }

        .sign-box {
            text-align: center;
            width: 200px;
        }

        .sign-space {
            height: 60px;
        }

        .sign-name {
            font-weight: 700;
            text-decoration: underline;
        }
    </style>
</head>

<body onload="window.print()">
    <header>
        <h1 class="company-name"><?= $perusahaan->nama_perusahaan ?? 'PT. TRIGEMILANG MANDIRI' ?></h1>
        <p class="report-title">REKAPITULASI PENGGAJIAN KARYAWAN</p>
        <span class="report-period">PERIODE: <?= date('F Y', strtotime($bulan . '-01')) ?></span>
    </header>

    <table class="table">
        <thead>
            <tr>
                <th width="3%">NO</th>
                <th width="15%">KARYAWAN</th>
                <th width="5%">HADIR</th>
                <th width="10%">GAJI POKOK</th>
                <th width="9%">TUNJ. JABATAN</th>
                <th width="9%">TUNJ. TRANS</th>
                <th width="9%">LBR WAJIB</th>
                <th width="9%">LBR KERJA</th>
                <th width="9%">REWARD/BONUS</th>
                <th width="9%">POTONGAN</th>
                <th width="13%" class="bg-light">TOTAL TERIMA</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $totals = [
                'gp' => 0,
                'tj' => 0,
                'tt' => 0,
                'lw' => 0,
                'lk' => 0,
                'rb' => 0,
                'pt' => 0,
                'gt' => 0
            ];
            foreach ($salary_data as $row):
                $reward_total = $row['reward'] + $row['bpjs'] + $row['tunjangan_extra'] + $row['tunjangan_kinerja_manual'] + $row['bonus_kinerja'] + $row['bonus_kehadiran'] + ($row['biaya_makan_lembur'] ?? 0) + ($row['custom_in1_nom']??0) + ($row['custom_in2_nom']??0) + ($row['custom_in3_nom']??0);
                $potongan_total = $row['potongan_alpha'] + $row['potongan_terlambat'] + $row['kasbon'] + $row['pinjaman'] + ($row['custom_out1_nom']??0) + ($row['custom_out2_nom']??0) + ($row['custom_out3_nom']??0);

                $totals['gp'] += $row['gaji_pokok'];
                $totals['tj'] += $row['tunjangan_jabatan'];
                $totals['tt'] += $row['tunjangan_transportasi'];
                $totals['lw'] += ($row['detail_lembur']['wajib']['total'] ?? 0);
                $totals['lk'] += ($row['detail_lembur']['kerjaan']['total'] ?? 0);
                $totals['rb'] += $reward_total;
                $totals['pt'] += $potongan_total;
                $totals['gt'] += $row['total_gaji'];
                ?>
                <tr>
                    <td class="text-center text-slate"><?= $no++ ?></td>
                    <td>
                        <div class="karyawan-box text-primary fw-bold">
                            <?= strtoupper($row['nama_lengkap']) ?>
                        </div>
                        <div class="nik-text"><?= $row['nik'] ?> | <?= $row['nama_jabatan'] ?></div>
                    </td>
                    <td class="text-center fw-bold"><?= $row['total_hadir'] ?> H</td>
                    <td class="text-end font-mono"><?= number_format($row['gaji_pokok']) ?></td>
                    <td class="text-end font-mono"><?= number_format($row['tunjangan_jabatan']) ?></td>
                    <td class="text-end font-mono"><?= number_format($row['tunjangan_transportasi']) ?></td>
                    <td class="text-end font-mono"><?= number_format($row['detail_lembur']['wajib']['total'] ?? 0) ?></td>
                    <td class="text-end font-mono"><?= number_format($row['detail_lembur']['kerjaan']['total'] ?? 0) ?></td>
                    <td class="text-end font-mono"><?= $reward_total > 0 ? number_format($reward_total) : '-' ?></td>
                    <td class="text-end font-mono text-rose">
                        <?= $potongan_total > 0 ? '(' . number_format($potongan_total) . ')' : '-' ?>
                    </td>
                    <td class="text-end font-mono fw-bold bg-light" style="font-size: 10px;">
                        <?= number_format($row['total_gaji']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-center fw-bold">GRAND TOTAL</td>
                <td class="text-end font-mono"><?= number_format($totals['gp']) ?></td>
                <td class="text-end font-mono"><?= number_format($totals['tj']) ?></td>
                <td class="text-end font-mono"><?= number_format($totals['tt']) ?></td>
                <td class="text-end font-mono"><?= number_format($totals['lw']) ?></td>
                <td class="text-end font-mono"><?= number_format($totals['lk']) ?></td>
                <td class="text-end font-mono"><?= number_format($totals['rb']) ?></td>
                <td class="text-end font-mono text-rose">
                    <?= $totals['pt'] > 0 ? '(' . number_format($totals['pt']) . ')' : '-' ?>
                </td>
                <td class="text-end font-mono fw-bold bg-light" style="font-size: 11px;">
                    <?= number_format($totals['gt']) ?>
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="footer-sign">
        <div class="sign-box">
            <p>Bekasi, <?= date('d F Y') ?></p>
            <p>Dibuat oleh,</p>
            <div class="sign-space"></div>
            <p class="sign-name">Bagian Administrasi & Keuangan</p>
            <p>Rahmi Irwan</p>
        </div>
        <div class="sign-box">
            <p>Bekasi,
                <?= date('d F Y') ?>
            </p>
            <p>Disetujui oleh,</p>
            <div class="sign-space"></div>
            <p class="sign-name">Direktur Utama</p>
            <p>H. Muhammad Irwan </p>
        </div>
    </div>
</body>

</html>