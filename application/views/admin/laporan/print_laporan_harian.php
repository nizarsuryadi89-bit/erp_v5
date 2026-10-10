<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan Harian - <?= $formatted_date ?></title>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 20px 50px;
            font-size: 13px;
            line-height: 1.4;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
        }

        .company-logo {
            font-size: 40px;
            font-weight: 800;
            letter-spacing: -2px;
            text-transform: lowercase;
            margin-bottom: -10px;
            display: inline-flex;
            align-items: center;
        }

        .company-logo span {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-left: 5px;
            font-weight: 600;
            padding-top: 10px;
        }

        .company-info {
            font-size: 11px;
            margin-top: 10px;
            line-height: 1.3;
        }

        .divider-heavy {
            border-top: 3px solid #666;
            margin: 15px 0;
        }

        .divider-light {
            border-top: 1px solid #000;
            margin: 15px 0;
        }

        .report-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }

        .report-date {
            font-size: 13px;
            text-align: center;
            margin-bottom: 10px;
        }

        /* Generic Row Alignment */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 1px 0;
            vertical-align: top;
        }

        .label-col {
            width: 600px;
        }

        .sep-col {
            width: 30px;
            text-align: center;
        }

        .val-col {
            text-align: left;
            font-weight: bold;
            white-space: nowrap;
        }

        .indent {
            padding-left: 30px !important;
        }

        /* Expense Table */
        .expense-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .expense-table th {
            text-align: left;
            padding: 5px 0;
            font-weight: bold;
        }

        .expense-table td {
            padding: 2px 0;
        }

        .text-right {
            text-align: right !important;
        }

        .fw-bold {
            font-weight: bold;
        }

        /* Rekapitulasi Section */
        .rekap-box {
            margin-top: 30px;
            width: 100%;
        }

        .rekap-row {
            display: flex;
            margin-bottom: 2px;
        }

        .rekap-label {
            width: 580px;
        }

        .rekap-val {
            font-weight: bold;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
        }

        .signature-line {
            margin-top: 60px;
            border-bottom: 1px solid #000;
            width: 250px;
        }

        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #000;
            color: #fff;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            border-radius: 4px;
            z-index: 1000;
        }
    </style>
</head>

<body>
    <!-- <button class="print-btn no-print" onclick="window.print()">PRINT REPORT</button> -->

    <div class="header">
        <div class="company-logo">tgmprint <span>OFFICIAL</span></div>
        <div class="company-info">
            Jl. Letnan Marsaid No.46, Marga Jaya, Kec. Bekasi Sel., Kota Bekasi, Jawa Barat 17141<br>
            Email : tgmprint.bekasi@gmail.com
        </div>
    </div>

    <div class="divider-heavy"></div>

    <div class="report-title">Laporan Keuangan Harian
        <?= $perusahaan ? $perusahaan->nama_perusahaan : 'TGM PUSAT' ?>
    </div>
    <div class="report-date">Tanggal : <?= $formatted_date ?></div>

    <div class="divider-light" style="border-top-width: 2px;"></div>

    <table class="summary-table">
        <tr>
            <td class="label-col">A. Grand All Transaksi (Saldo + Cash + Transfer + EDC + QRIS)</td>
            <td class="sep-col">:</td>
            <td class="val-col">Rp. <?= number_format($grand_total, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td class="label-col">1. Saldo</td>
            <td class="sep-col">:</td>
            <td class="val-col">Rp. <?= number_format($metode_pembayaran['Saldo'], 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td class="label-col">2. Cash</td>
            <td class="sep-col">:</td>
            <td class="val-col">Rp. <?= number_format($metode_pembayaran['Cash'], 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td class="label-col">3. Transfer ke</td>
            <td class="sep-col">:</td>
            <td></td>
        </tr>

        <!-- Transfer Rincian -->
        <?php if (!empty($transfer_details)): ?>
            <?php foreach ($transfer_details as $label => $val): ?>
                <tr>
                    <td class="label-col indent"><?= $label ?></td>
                    <td class="sep-col">:</td>
                    <td class="val-col">Rp. <?= number_format($val, 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td class="label-col indent">Tidak ada transferan</td>
                <td class="sep-col">:</td>
                <td class="val-col">Rp. 0</td>
            </tr>
        <?php endif; ?>

        <tr>
            <td class="label-col indent fw-bold">Total Transferan</td>
            <td class="sep-col">:</td>
            <td class="val-col">Rp. <?= number_format($total_transfer, 0, ',', '.') ?></td>
        </tr>

        <!-- More Section A -->
        <tr>
            <td class="label-col">4. EDC</td>
            <td class="sep-col">:</td>
            <td class="val-col">Rp. <?= number_format($metode_pembayaran['EDC'], 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td class="label-col">5. OVO</td>
            <td class="sep-col">:</td>
            <td class="val-col">Rp. <?= number_format(@$metode_pembayaran['OVO'] ?: 0, 0, ',', '.') ?></td>
        </tr>

        <!-- Section B & C Labels -->
        <tr>
            <td class="label-col">B. JUMLAH PIUTANG</td>
            <td class="sep-col">:</td>
            <td class="val-col">Rp. <?= number_format($total_piutang, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td class="label-col">C. PENGELUARAN HARIAN</td>
            <td class="sep-col">:</td>
            <td></td>
        </tr>
    </table>

    <!-- Table Pengeluaran -->
    <table class="expense-tabl">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 330px;">Nama Pengeluaran</th>
                <th style="width: 330px;">Keterangan</th>
                <th class="text-right">Jumlah Pengeluaran</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pengeluaran)): ?>
                <tr>
                    <td colspan="4" class="text-center" style="padding: 10px;">Tidak ada pengeluaran hari ini</td>
                </tr>
            <?php else: ?>
                <?php $no = 1;
                foreach ($pengeluaran as $p): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td style="text-transform: uppercase;"><?= $p->pengeluaran_detail ?></td>
                        <td style="text-transform: uppercase;"><?= $p->kategori_pengeluaran ?></td>
                        <td class="text-right">Rp. <?= number_format($p->pengeluaran_jumlah, 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            <tr class="fw-bold">
                <td colspan="3" class="text-right" style="padding-top: 5px;">Total Pengeluaran</td>
                <td class="text-right" style="padding-top: 5px;">Rp.
                    <?= number_format($total_pengeluaran, 0, ',', '.') ?>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Rekapitulasi Section -->
    <div class="rekap-box">
        <div class="fw-bold" style="margin-bottom: 5px;">Rekapitulasi Saldo Keuangan Harian</div>
        <div class="rekap-row">
            <div class="rekap-label">A. SALDO</div>
            <div class="rekap-val">Rp. <?= number_format($metode_pembayaran['Saldo'], 0, ',', '.') ?></div>
        </div>
        <div class="rekap-row">
            <div class="rekap-label">B. JUMLAH CASH</div>
            <div class="rekap-val">Rp. <?= number_format($metode_pembayaran['Cash'], 0, ',', '.') ?></div>
        </div>
        <div class="rekap-row">
            <div class="rekap-label">C. JUMLAH PENGELUARAN</div>
            <div class="rekap-val">Rp. <?= number_format($total_pengeluaran, 0, ',', '.') ?></div>
        </div>
        <div class="rekap-row fw-bold">
            <div class="rekap-label">Saldo Akhir Harian Cash (A + B) - C</div>
            <div class="rekap-val">Rp.
                <?= number_format(($metode_pembayaran['Saldo'] + $metode_pembayaran['Cash']) - $total_pengeluaran, 0, ',', '.') ?>
            </div>
        </div>
    </div>

    <div class="divider-heavy" style="margin-top: 30px;"></div>

    <div class="footer">
        <div>Penanggung Jawab Keuangan</div>
        <div>Bekasi, <?= $formatted_date ?></div>
        <div>TGM PUSAT</div>
        <div class="signature-line"></div>
    </div>

    <div class="divider-light" style="margin-top: 50px; border-top-color: #999;"></div>

</body>

</html>