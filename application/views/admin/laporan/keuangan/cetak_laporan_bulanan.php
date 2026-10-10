<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan Bulanan - <?= date('F Y', strtotime($bulan_ini)) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/plugins/fontawesome/css/all.min.css') ?>" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 0.8cm 1cm;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            tr {
                page-break-inside: avoid;
            }
        }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 9.5pt;
            line-height: 1.3;
            color: #1a1a1a;
            -webkit-print-color-adjust: exact;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
        }

        .company-logo {
            font-size: 20pt;
            font-weight: 800;
            letter-spacing: -1px;
            color: #000;
            margin-bottom: -5px;
        }

        .company-logo span {
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-weight: 600;
            color: #666;
        }

        .company-info {
            font-size: 8.5pt;
            color: #444;
            margin-top: 5px;
        }

        .divider-heavy {
            border-top: 1.5px solid #000;
            margin: 10px 0;
        }

        .report-title {
            font-size: 13pt;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            margin-top: 10px;
        }

        .report-date {
            font-size: 9pt;
            text-align: center;
            margin-bottom: 15px;
            color: #555;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-top: 10px;
        }

        .summary-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .label-col {
            width: 450px;
        }

        .sep-col {
            width: 20px;
            text-align: center;
        }

        .val-col {
            font-weight: 600;
            text-align: right;
            width: 150px;
        }

        .indent {
            padding-left: 20px !important;
            color: #555;
            font-size: 8.5pt;
        }

        .fw-bold {
            font-weight: 700 !important;
        }

        /* Expense Table */
        .expense-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 8.5pt;
        }

        .expense-table th {
            text-align: left;
            padding: 6px 8px;
            background-color: #f5f5f5 !important;
            border: 0.5px solid #ddd;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 8pt;
        }

        .expense-table td {
            padding: 5px 8px;
            border: 0.5px solid #ddd;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        /* Rekap Box */
        .rekap-box {
            background-color: #fcfcfc;
            border: 1px solid #eee;
            padding: 10px 15px;
            margin-top: 15px;
        }

        .rekap-title {
            font-weight: 700;
            font-size: 10pt;
            margin-bottom: 8px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 4px;
        }

        .rekap-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }

        .grand-result {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1.5px solid #000;
            font-weight: 800;
            font-size: 10.5pt;
        }

        /* Footer */
        .footer-container {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 200px;
            text-align: center;
        }

        .signature-space {
            height: 60px;
        }

        .signature-name {
            border-bottom: 1px solid #000;
            font-weight: 700;
            display: inline-block;
            min-width: 150px;
        }

        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: #fff;
            padding: 8px 16px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            border-radius: 6px;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<?php
$nama_perusahaan_header = "SEMUA CABANG";
if (isset($perusahaan_id) && $perusahaan_id > 1) {
    $perusahaan_data = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row();
    $nama_perusahaan_header = $perusahaan_data ? strtoupper($perusahaan_data->nama_perusahaan) : "TGM PRINT";
    $alamat_perusahaan = $perusahaan_data ? $perusahaan_data->alamat_perusahaan : "Jl. Letnan Marsaid No.46, Marga Jaya, Bekasi Selatan, Kota Bekasi";
    $email_perusahaan = $perusahaan_data ? $perusahaan_data->email : "tgmprint.bekasi@gmail.com";
    $logo_perusahaan = $perusahaan_data ? $perusahaan_data->logo : "";
} else {
    $perusahaan_data = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => 1])->row();
    $alamat_perusahaan = $perusahaan_data ? $perusahaan_data->alamat_perusahaan : "Jl. Letnan Marsaid No.46, Marga Jaya, Bekasi Selatan, Kota Bekasi";
    $email_perusahaan = $perusahaan_data ? $perusahaan_data->email : "tgmprint.bekasi@gmail.com";
    $logo_perusahaan = $perusahaan_data ? $perusahaan_data->logo : "";
}

// Check if file exists on disk
$logo_path = 'assets/images/' . $logo_perusahaan;
$logo_exists = (!empty($logo_perusahaan) && file_exists(FCPATH . $logo_path));

// Fallback to default logo if specific one not found
if (!$logo_exists && file_exists(FCPATH . 'assets/images/logo.png')) {
    $logo_path = 'assets/images/logo.png';
    $logo_exists = true;
}
?>

<body onload="window.print()">
    <button class="print-btn no-print" onclick="window.print()">
        <i class="fas fa-print"></i> CETAK LAPORAN
    </button>

    <!-- HEADER -->
    <div class="header">
        <?php if ($logo_exists): ?>
                <img src="<?= base_url($logo_path) ?>" style="height: 50px; width: auto; max-height: 50px; margin-bottom: 5px;">
        <?php else: ?>
                <div class="company-logo">tgmprint <span>OFFICIAL</span></div>
        <?php endif; ?>
        <div class="company-info">
            <?= $alamat_perusahaan ?><br>
            Email: <?= $email_perusahaan ?>
        </div>
    </div>

    <div class="divider-heavy"></div>

    <div class="report-title">Laporan Keuangan Bulanan</div>
    <div class="report-date"><?= $nama_perusahaan_header ?> • Periode: <?= date('F Y', strtotime($bulan_ini)) ?></div>

    <table class="summary-table">
        <tr class="fw-bold">
            <td class="label-col">A. TOTAL PENDAPATAN (Cash + Transfer + EDC + QRIS)</td>
            <td class="sep-col">:</td>
            <td class="val-col">Rp <?= number_format($stat_keuangan['total_uang_masuk'], 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td class="label-col indent">1. Saldo Awal</td>
            <td class="sep-col">:</td>
            <td class="val-col">Rp 0</td>
        </tr>
        <tr>
            <td class="label-col indent">2. Kas Tunai (Cash)</td>
            <td class="sep-col">:</td>
            <td class="val-col">Rp <?= number_format($summary['cash']['total'], 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td class="label-col indent">3. Transfer Bank</td>
            <td class="sep-col">:</td>
            <td class="val-col">Rp <?= number_format($summary['transfer']['total'], 0, ',', '.') ?></td>
        </tr>

        <!-- Transfer Breakdown -->
        <?php if (!empty($breakdown_transfer)): ?>
                <?php foreach ($breakdown_transfer as $bt): ?>
                        <tr>
                            <td class="label-col indent" style="padding-left: 40px !important; font-size: 8pt; color: #777;">
                                └─ <?= $bt['nama_bank'] ?> (<?= $bt['no_rekening'] ?>)
                            </td>
                            <td class="sep-col" style="font-size: 8pt; color: #777;">:</td>
                            <td class="val-col" style="font-size: 8pt; color: #777; font-weight: normal;">Rp
                                <?= number_format($bt['total'], 0, ',', '.') ?>
                            </td>
                        </tr>
                <?php endforeach; ?>
        <?php endif; ?>

        <tr>
            <td class="label-col indent">4. Mesin EDC</td>
            <td class="sep-col">:</td>
            <td class="val-col">Rp <?= number_format($summary['debit']['total'], 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td class="label-col indent">5. E-Wallet / QRIS</td>
            <td class="sep-col">:</td>
            <td class="val-col">Rp <?= number_format($summary['ewallet']['total'], 0, ',', '.') ?></td>
        </tr>

        <tr class="fw-bold" style="border-top: 0.5px solid #eee;">
            <td class="label-col">B. TOTAL PIUTANG BERJALAN BULAN INI</td>
            <td class="sep-col">:</td>
            <td class="val-col">Rp
                <?= number_format($stat_keuangan['piutang_baru_terbentuk'], 0, ',', '.') ?>
            </td>
        </tr>

        <tr class="fw-bold">
            <td class="label-col">C. TOTAL PENGELUARAN BULANAN</td>
            <td class="sep-col">:</td>
            <td class="val-col">Rp <?= number_format($stat_keuangan['total_pengeluaran'], 0, ',', '.') ?></td>
        </tr>
    </table>

    <!-- Expense Details -->
    <table class="expense-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 30%;">Rincian Pengeluaran</th>
                <th style="width: 30%;">Penerima</th>
                <th class="text-right" style="width: 20%;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($list_pengeluaran)): ?>
                    <tr>
                        <td colspan="5" class="text-center" style="color: #999;">Tidak ada pengeluaran bulanan tercatat</td>
                    </tr>
            <?php else: ?>
                    <?php $no = 1;
                    foreach ($list_pengeluaran as $exp): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="text-center"><?= date('d/m/Y', strtotime($exp['pengeluaran_tgl'])) ?></td>
                                <td style="text-transform: uppercase;"><?= $exp['pengeluaran_detail'] ?></td>
                                <td style="text-transform: uppercase;"><?= $exp['pengeluaran_penerima'] ?></td>
                                <td class="text-right fw-bold">Rp <?= number_format($exp['pengeluaran_jumlah'], 0, ',', '.') ?></td>
                            </tr>
                    <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="fw-bold" style="background-color: #fcfcfc;">
                <td colspan="4" class="text-right">TOTAL PENGELUARAN (C)</td>
                <td class="text-right">Rp <?= number_format($stat_keuangan['total_pengeluaran'], 0, ',', '.') ?></td>
            </tr>
        </tfoot>
    </table>

    <!-- Rekapitulasi Section -->
    <div class="rekap-box">
        <div class="rekap-title">Rekapitulasi Kas Tunai (Monthly Cash Balance)</div>
        <div class="rekap-row">
            <span>Total Penerimaan Cash (A2)</span>
            <span>Rp <?= number_format($summary['cash']['total'], 0, ',', '.') ?></span>
        </div>
        <div class="rekap-row">
            <span>Total Pengeluaran Bulan Ini (C)</span>
            <span style="color: #d32f2f;">- Rp
                <?= number_format($stat_keuangan['total_pengeluaran'], 0, ',', '.') ?></span>
        </div>
        <div class="rekap-row grand-result">
            <span>ESTIMASI SALDO KAS TUNAI (A2 - C)</span>
            <span>Rp
                <?= number_format(($summary['cash']['total']) - $stat_keuangan['total_pengeluaran'], 0, ',', '.') ?></span>
        </div>
    </div>

    <!-- Final Net Balance (Accrual Reference) -->
    <div class="rekap-box" style="margin-top: 10px; border-color: #3b82f6; background-color: #f0f9ff;">
        <div class="rekap-row fw-bold" style="font-size: 11pt; color: #1e3a8a;">
            <span>SALDO BERSIH BULAN INI (Total Kas Masuk - Pengeluaran)</span>
            <span>Rp <?= number_format($stat_keuangan['total_uang_masuk'] - $stat_keuangan['total_pengeluaran'], 0, ',', '.') ?></span>
        </div>
    </div>

    <div class="footer-container">
        <div class="signature-box">
            <div style="margin-bottom: 5px;">Admin Keuangan,</div>
            <div class="signature-space"></div>
            <div class="signature-name"></div>
        </div>
        <div class="signature-box">
            <div style="margin-bottom: 5px;">Manager / Owner,</div>
            <div class="signature-space"></div>
            <div class="signature-name"></div>
        </div>
    </div>

    <div style="margin-top: 10px; font-size: 8pt; color: #777; text-align: center;">
        Dicetak pada: <?= date('d/m/Y H:i:s') ?> oleh Sistem ERP
    </div>

</body>

</html>