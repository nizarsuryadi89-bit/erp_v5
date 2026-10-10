<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi - <?= $karyawan->nama_lengkap ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Ultra-Compact Premium A4 Styling */
        @page {
            size: A4;
            margin: 0.7cm 1cm;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 8.5pt;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
            line-height: 1.2;
            background-color: #fff;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #1a1a1a;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }

        .header h1 {
            margin: 0;
            font-size: 13pt;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 800;
        }

        .header p {
            margin: 2px 0;
            font-size: 9.5pt;
            font-weight: 600;
        }

        .header .period-label {
            display: inline-block;
            margin-top: 2px;
            padding: 2px 10px;
            background-color: #f0f0f0 !important;
            border-radius: 15px;
            font-size: 8pt;
            -webkit-print-color-adjust: exact;
        }

        .info-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding: 6px 10px;
            background-color: #fafafa !important;
            border-radius: 6px;
            -webkit-print-color-adjust: exact;
        }

        .employee-info {
            width: 80%;
            border-collapse: collapse;
        }

        .employee-info td {
            padding: 1px 0;
        }

        .employee-info .label {
            width: 80px;
            font-weight: 700;
            font-size: 8pt;
            color: #555;
            text-transform: uppercase;
        }

        .employee-info .value {
            font-weight: 600;
        }

        .employee-photo {
            width: 60px;
            text-align: right;
        }

        .employee-photo img {
            width: 50px;
            height: 65px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #000;
        }

        .report-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #000;
            border-radius: 4px;
            overflow: hidden;
        }

        .report-table th {
            background-color: #1a1a1a !important;
            color: #fff !important;
            padding: 5px 2px;
            font-size: 7.5pt;
            text-transform: uppercase;
            font-weight: 700;
            -webkit-print-color-adjust: exact;
        }

        .report-table td {
            border-bottom: 0.1px solid #eee;
            border-right: 0.1px solid #eee;
            padding: 3.5px 2px;
            font-size: 8pt;
            text-align: center;
        }

        .report-table td:last-child {
            border-right: none;
        }

        .report-table tr:last-child td {
            border-bottom: none;
        }

        .report-table .font-mono {
            font-family: 'JetBrains Mono', monospace;
            font-size: 7.5pt;
        }

        .bg-sunday-holiday {
            background-color: #fff9f9 !important;
            -webkit-print-color-adjust: exact;
        }

        .text-danger {
            color: #d9534f !important;
            font-weight: 700;
            -webkit-print-color-adjust: exact;
        }

        .total-row {
            background-color: #f8f9fa !important;
            font-weight: 800;
            -webkit-print-color-adjust: exact;
        }

        .total-row td {
            border-top: 1.5px solid #000;
            padding: 6px 4px;
            font-size: 8.5pt;
        }

        .footer-sig {
            margin-top: 15px;
            width: 100%;
        }

        .footer-sig td {
            width: 50%;
            text-align: center;
        }

        .sig-space {
            height: 40px;
        }

        .sig-name {
            font-weight: 800;
            font-size: 9pt;
            border-bottom: 1.2px solid #000;
            display: inline-block;
            min-width: 150px;
            text-transform: uppercase;
        }

        .sig-role {
            font-size: 7.5pt;
            color: #555;
            margin-top: 2px;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
            }

            tr {
                page-break-inside: avoid;
            }
        }

        /* Watermark Styling */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60pt;
            color: rgba(0, 0, 0, 0.04);
            z-index: -1000;
            white-space: nowrap;
            font-weight: 900;
            text-transform: uppercase;
            pointer-events: none;
            -webkit-print-color-adjust: exact;
        }
    </style>
</head>

<body onload="window.print()">
    <div class="watermark">
        <?= $perusahaan ?>
    </div>
    <div class="header">
        <h1>Raport Kehadiran Karyawan</h1>
        <p><?= $perusahaan ?></p>
        <div class="period-label">
            Periode: <?= date('d/m/Y', strtotime($start_date)) ?> — <?= date('d/m/Y', strtotime($end_date)) ?>
        </div>
    </div>

    <div class="info-container">
        <table class="employee-info">
            <tr>
                <td class="label">Nama</td>
                <td class="value">: <?= $karyawan->nama_lengkap ?></td>
            </tr>
            <tr>
                <td class="label">NIK</td>
                <td class="value">: <?= $karyawan->nik ?></td>
            </tr>
            <tr>
                <td class="label">Jabatan</td>
                <td class="value">: <?= $karyawan->nama_jabatan ?></td>
            </tr>
            <tr>
                <td class="label">Divisi</td>
                <td class="value">: <?= $karyawan->divisi_nama ?></td>
            </tr>
        </table>
        <div class="employee-photo">
            <img src="<?= base_url('assets/images/karyawan/' . ($karyawan->foto ? $karyawan->foto : 'default.png')) ?>"
                alt="Foto">
        </div>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 10%;">Hari</th>
                <th style="width: 18%;">Absensi</th>
                <th style="width: 8%;">Pagi</th>
                <th style="width: 12%;">Wajib+Tmb</th>
                <th style="width: 10%;">Total</th>
                <th style="width: 20%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_pagi = 0;
            $total_wajib_tmb = 0;
            $total_all = 0;

            foreach ($dates_range as $date):
                $att = isset($att_map[$date]) ? $att_map[$date] : null;
                $dayNum = date('w', strtotime($date));
                $dayIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][$dayNum];
                $isSaturday = ($dayNum == 6);
                $isSunday = ($dayNum == 0);
                $isHoliday = isset($holiday_map[$date]);
                
                $calc = isset($lembur_calc[$date]) ? $lembur_calc[$date] : ['pagi' => 0, 'wajib' => 0, 'kerjaan' => 0, 'total' => 0];
                $p_hours = $calc['pagi'];
                $wt_hours = $calc['wajib'] + $calc['kerjaan'];
                $t_hours = $calc['total'];

                $total_pagi += $p_hours;
                $total_wajib_tmb += $wt_hours;
                $total_all += $t_hours;
                ?>
                <tr class="<?= ($isSunday || $isHoliday) ? 'bg-sunday-holiday' : '' ?>">
                    <td class="font-mono"><?= date('d/m/y', strtotime($date)) ?></td>
                    <td class="<?= ($isSunday || $isHoliday) ? 'text-danger' : '' ?>"><?= $dayIndo ?></td>
                    <?php if ($isHoliday): ?>
                        <td class="text-danger" style="font-size: 7.5pt; font-style: italic;">
                            <?= $holiday_map[$date] ?>
                        </td>
                    <?php elseif ($isSunday): ?>
                        <td class="text-danger" style="font-size: 7.5pt; font-style: italic;">LIBUR MINGGU</td>
                    <?php else: ?>
                        <td class="font-mono">
                            <?php if ($att && $att->status && $att->status != 'Hadir'): ?>
                                <span class="text-danger fw-700"><?= strtoupper($att->status) ?></span>
                            <?php else: ?>
                                <?php
                                $check_jk = isset($jk_map[$dayIndo]) ? $jk_map[$dayIndo]->jam_masuk : '08:00:00';
                                $is_late = $att && $att->jam_masuk && strtotime($att->jam_masuk) > strtotime($check_jk . ' +1 minute');
                                ?>
                                <span class="<?= $is_late ? 'text-danger' : '' ?>">
                                    <?= ($att && $att->jam_masuk && $att->jam_masuk != '00:00:00') ? substr($att->jam_masuk, 0, 5) : '-' ?>
                                </span>
                                —
                                <?= ($att && $att->jam_pulang && $att->jam_pulang != '00:00:00') ? substr($att->jam_pulang, 0, 5) : '-' ?>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                    <td class="font-mono"><?= $p_hours > 0 ? str_replace('.', ',', (string) $p_hours) : '-' ?></td>
                    <td class="font-mono"><?= $wt_hours > 0 ? str_replace('.', ',', (string) $wt_hours) : '-' ?></td>
                    <td class="font-mono fw-800" style="background: #fcfcfc;"><?= $t_hours > 0 ? str_replace('.', ',', (string) $t_hours) : '-' ?></td>
                    <td style="font-size: 7pt; text-align: left; padding: 2px 4px; font-style: italic; color: #555;">
                        <?= $att && $att->keterangan ? $att->keterangan : '-' ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" style="text-align: right; padding-right: 15px;">TOTAL AKUMULASI JAM LEMBUR</td>
                <td class="font-mono"><?= str_replace('.', ',', (string) $total_pagi) ?></td>
                <td class="font-mono"><?= str_replace('.', ',', (string) $total_wajib_tmb) ?></td>
                <td class="font-mono" style="background: #eee;"><?= str_replace('.', ',', (string) $total_all) ?> Jam</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <table class="footer-sig">
        <tr>
            <td>
                <div class="sig-container">
                    <p>Dibuat Oleh,</p>
                    <div class="sig-space"></div>
                    <div class="sig-name">Admin Absensi</div>
                    <div class="sig-role">Staf HRD & Payroll</div>
                </div>
            </td>
            <td>
                <div class="sig-container">
                    <p>Diketahui Oleh,</p>
                    <div class="sig-space"></div>
                    <div class="sig-name">Manager / Owner</div>
                    <div class="sig-role">Pimpinan Perusahaan</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="no-print" style="margin-top: 25px; text-align: center;">
        <button onclick="window.print()"
            style="padding: 8px 20px; cursor: pointer; border-radius: 4px; border: 1px solid #ccc; background: #fff; font-weight: 700;">
            <i class="bi bi-printer"></i> CETAK LAPORAN
        </button>
        <button onclick="window.close()"
            style="padding: 8px 20px; margin-left: 10px; cursor: pointer; border-radius: 4px; border: 1px solid #ccc; background: #fff;">
            TUTUP
        </button>
    </div>
</body>

</html>