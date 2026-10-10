<!DOCTYPE html>
<html>

<head>
    <title>Laporan Kinerja Karyawan - <?= $perf->nama_lengkap ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        @page {
            size: A4;
            margin: 1cm 2cm;
        }

        body {
            font-family: 'Lato', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
            background: #fff;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            position: relative;
            z-index: 1;
            margin: 0 auto;
            padding: 1cm 2.5cm;
            /* Preview padding - mimics print margins */
        }

        /* Watermark Styles */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            opacity: 0.05;
            z-index: -1;
            width: 500px;
            pointer-events: none;
        }

        .header-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .header-logo {
            width: 130px;
            margin-bottom: 10px;
        }

        .report-title {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 5px 0;
            border-bottom: none;
        }

        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 1px 0;
            vertical-align: top;
        }

        .info-table td:first-child {
            width: 140px;
        }

        .info-table td:nth-child(2) {
            width: 20px;
            text-align: center;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
            table-layout: fixed;
        }

        .main-table th {
            background-color: #f2f2f2;
            border: 1px solid #999;
            padding: 4px;
            text-align: center;
            font-weight: bold;
        }

        .main-table td {
            border: 1px solid #999;
            padding: 3px 10px;
        }

        .text-center {
            text-align: center;
        }

        .row-hadir {
            font-weight: bold;
            color: #000;
        }

        .calc-wrapper {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            page-break-inside: avoid;
        }

        .calc-left {
            width: 60%;
        }

        .calc-right {
            width: 35%;
        }

        .score-section {
            margin: 10px 0;
            font-size: 16px;
        }

        .score-value {
            font-size: 22px;
            font-weight: bold;
            border-bottom: 2px solid #333;
            padding: 0 10px;
            display: inline-block;
            min-width: 40px;
            text-align: center;
        }

        .conclusion-section {
            margin-top: 10px;
        }

        .checkbox-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin-top: 10px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            font-size: 13px;
        }

        .square {
            width: 14px;
            height: 14px;
            border: 1px solid #333;
            display: inline-block;
            margin-right: 8px;
            position: relative;
        }

        .square.checked::after {
            content: '✓';
            position: absolute;
            top: -6px;
            left: 1px;
            font-size: 14px;
            font-weight: bold;
        }

        .footer-note {
            margin-top: 15px;
            font-size: 12px;
        }

        .catatan-box {
            font-style: italic;
            margin-top: 5px;
            line-height: 1.6;
        }

        .signature-section {
            margin-top: 25px;
            text-align: right;
            display: flex;
            justify-content: flex-end;
        }

        .sig-block {
            width: 300px;
            text-align: left;
        }

        @media print {
            .container {
                width: 100%;
                padding: 0 !important;
            }
        }
    </style>
</head>

<body>

    <!-- Background Watermark -->
    <?php if (!empty($perusahaan->logo) && file_exists(FCPATH . 'assets/images/' . $perusahaan->logo)): ?>
        <img src="<?= base_url('assets/images/' . $perusahaan->logo) ?>" class="watermark" alt="Watermark">
    <?php endif; ?>

    <div class="container">
        <!-- Header Section -->
        <div class="header-section">
            <?php if (!empty($perusahaan->logo) && file_exists(FCPATH . 'assets/images/' . $perusahaan->logo)): ?>
                <img src="<?= base_url('assets/images/' . $perusahaan->logo) ?>" class="header-logo" alt="Logo">
            <?php else: ?>
                <h1 style="margin:0;">TGMMERCH™</h1>
            <?php endif; ?>
            <div class="report-title">RAPOR ABSENSI KARYAWAN (BULANAN)</div>
        </div>

        <!-- Employee Info -->
        <table class="info-table">
            <tr>
                <td>Nama Karyawan</td>
                <td>:</td>
                <td><strong><?= strtoupper($perf->nama_lengkap) ?></strong></td>
            </tr>
            <tr>
                <td>Nip</td>
                <td>:</td>
                <td><?= $perf->nik ?></td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td><?= $perf->nama_jabatan ?></td>
            </tr>
            <tr>
                <td>Divisi</td>
                <td>:</td>
                <td><?= $perf->divisi_nama ?></td>
            </tr>
            <tr>
                <td>Periode</td>
                <td>:</td>
                <td><?= date('F Y', strtotime($bulan . '-01')) ?></td>
            </tr>
        </table>

        <!-- Main Table -->
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 40%;">Keterangan</th>
                    <th style="width: 25%;">Jumlah Hari</th>
                    <th style="width: 35%;">Catatan</th>
                </tr>
            </thead>
            <tbody>
                <tr class="row-hadir">
                    <td>Hadir</td>
                    <td class="text-center"><?= $perf->total_hadir ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Telat</td>
                    <td class="text-center"><?= $perf->total_telat ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Sakit</td>
                    <td class="text-center"><?= $perf->total_sakit ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Cuti</td>
                    <td class="text-center"><?= $perf->total_cuti ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Alpha</td>
                    <td class="text-center"><?= $perf->total_alpha ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Izin</td>
                    <td class="text-center"><?= $perf->total_izin ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Pulang Cepat</td>
                    <td class="text-center"><?= isset($perf->total_pulang_cepat) ? $perf->total_pulang_cepat : 0 ?></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <div class="calc-wrapper">
            <div class="calc-left">
                <p><strong>Persentase Kehadiran :</strong></p>
                <div style="margin-bottom: 20px; font-style: italic;">
                    (Hadir / Total Hari Kerja) x 100%
                </div>

                <div class="score-section">
                    Total Nilai : <span class="score-value"><?= number_format($perf->skor_kinerja) ?></span> / 100
                </div>

                <div class="conclusion-section">
                    <p><strong>Kesimpulan :</strong></p>
                    <div class="checkbox-grid">
                        <?php
                        $score = $perf->skor_kinerja;
                        $categories = [
                            ['label' => 'Sangat Disiplin', 'min' => 95, 'max' => 100],
                            ['label' => 'Disiplin', 'min' => 85, 'max' => 94],
                            ['label' => 'Cukup Disiplin', 'min' => 75, 'max' => 84],
                            ['label' => 'Kurang Disiplin', 'min' => 65, 'max' => 74],
                            ['label' => 'Tidak Disiplin', 'min' => 0, 'max' => 64],
                        ];
                        foreach ($categories as $cat):
                            $isChecked = ($score >= $cat['min'] && $score <= $cat['max']);
                            ?>
                            <div class="checkbox-item">
                                <span class="square <?= $isChecked ? 'checked' : '' ?>"></span>
                                <?= $cat['label'] ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="calc-right">
                <p><strong>Interpretasi Nilai Absensi :</strong></p>
                <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                    <tr>
                        <td style="width: 80px;">• 95 - 100</td>
                        <td>= Sangat Disiplin</td>
                    </tr>
                    <tr>
                        <td>• 85 - 94</td>
                        <td>= Disiplin</td>
                    </tr>
                    <tr>
                        <td>• 75 - 84</td>
                        <td>= Cukup Disiplin</td>
                    </tr>
                    <tr>
                        <td>• 65 - 74</td>
                        <td>= Kurang Disiplin</td>
                    </tr>
                    <tr>
                        <td>• ≤ 64</td>
                        <td>= Tidak Disiplin</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="footer-note">
            <p><strong>Keterangan Penilaian :</strong></p>
            <div style="font-size: 11px; color: #555;">
                <strong>Alpha</strong> = Tidak hadir tanpa keterangan <strong>(-5 poin)</strong> |
                <strong>Izin</strong> = Tidak hadir dengan pemberitahuan <strong>(-1 poin)</strong> |
                <strong>Sakit</strong> = Dengan / tanpa surat dokter <strong>(-1 poin)</strong> |
                <strong>Telat</strong> = Melewati jam kerja <strong>(-1 poin)</strong> |
                <strong>Pulang Cepat</strong> = Sebelum jam kerja berakhir <strong>(-3 poin)</strong>
            </div>

            <p style="margin-top: 10px;"><strong>Catatan Penilaian :</strong></p>
            <div class="catatan-box">
                Karyawan menunjukkan tingkat kedisiplinan yang sangat baik dengan pelanggaran absensi yang minimal dan
                tidak terdapat ketidakhadiran tanpa keterangan.
            </div>
        </div>

        <div class="signature-section">
            <div class="sig-block">
                <p style="margin-bottom: 5px;"><strong>Pengesahan :</strong></p>
                <p style="margin: 0;">Bekasi, <?= date('d F Y') ?></p>
                <p style="margin-top: 10px; font-weight: bold;">Pimpinan Perusahaan</p>

                <div style="height: 80px; position: relative; margin: 5px 0;">
                    <?php if (!empty($perusahaan->logo) && file_exists(FCPATH . 'assets/images/' . $perusahaan->logo)): ?>
                        <img src="<?= base_url('assets/images/' . $perusahaan->logo) ?>"
                            style="width: 80px; opacity: 0.15; position: absolute; top:0; left:0;">
                    <?php endif; ?>

                </div>

                <p
                    style="margin: 0; font-weight: bold; border-bottom: 1px solid #000; display: inline-block; min-width: 220px;">
                    Mohamad Fachmi Irwan</p>
                <p style="margin: 0; font-size: 11px; color: #666;"><?= $perusahaan->nama_perusahaan ?></p>
            </div>
        </div>

    </div>
</body>

</html>