<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Transaction Log - <?= date('F Y', strtotime($bulan_ini)) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/plugins/fontawesome/css/all.min.css') ?>" rel="stylesheet">
    <style>
        @page {
            size: A4 landscape;
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
            font-size: 9pt;
            line-height: 1.3;
            color: #1a1a1a;
            -webkit-print-color-adjust: exact;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
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

        .report-title {
            font-size: 14pt;
            font-weight: 700;
            color: #000;
            margin: 5px 0 2px 0;
        }

        .report-period {
            font-size: 10pt;
            color: #666;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th {
            background-color: #f8f9fa;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 8px 6px;
            text-align: left;
            font-size: 8pt;
            font-weight: 700;
            color: #666;
            text-transform: uppercase;
        }

        td {
            padding: 6px 6px;
            border-bottom: 1px dashed #ddd;
            vertical-align: middle;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: 700;
        }

        .text-success {
            color: #00b09b;
        }

        .btn-print {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #000;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 30px;
            font-family: inherit;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 999;
            text-decoration: none;
        }

        .btn-print:hover {
            background: #222;
        }
    </style>
</head>

<!-- skrng<body onload="window.print()"> -->

<button onclick="window.print()" class="btn-print no-print">
    <i class="fas fa-print"></i> PRINT / SAVE PDF
</button>

<div class="header">
    <div class="company-logo">
        <?= strtoupper($perusahaan) ?><br>
        <span>ERP System</span>
    </div>
    <h1 class="report-title">AUDIT TRANSACTION LOG</h1>
    <p class="report-period">PERIODE: <?= strtoupper(date('F Y', strtotime($bulan_ini))) ?></p>
</div>

<table>
    <thead>
        <tr>
            <th style="width: 12%;">TANGGAL & WAKTU</th>
            <th style="width: 25%;">KONSUMEN / SPK</th>
            <th style="width: 30%;">PRODUK</th>
            <th style="width: 13%;">JENIS TRANSAKSI</th>
            <th style="width: 10%;">KASIR</th>
            <th class="text-right" style="width: 10%;">DIBAYAR</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $grand_total = 0;
        foreach ($pemasukan_list as $trx):
            $grand_total += $trx['total'];
            ?>
            <tr>
                <td>
                    <div class="fw-bold"><?= date('d M Y', strtotime($trx['tanggal'])) ?></div>
                    <div style="font-size: 8pt; color: #666;"><?= date('H:i', strtotime($trx['jam'])) ?> WIB</div>
                </td>
                <td>
                    <div class="fw-bold"><?= $trx['nospk'] ?></div>
                    <div style="font-size: 8pt; color: #666; font-weight: 500; margin-bottom: 2px;">
                        <i class="fas fa-user" style="font-size: 7pt; width: 12px;"></i>
                        <?= $trx['konsumen'] ?: 'Walk-in Customer' ?>
                    </div>
                </td>
                <td>
                    <?php if (!empty($trx['produk'])): ?>
                        <div style="font-size: 7.5pt; color: #333;">
                            <?= $trx['produk'] ?>
                        </div>
                    <?php else: ?>
                        <div style="font-size: 7.5pt; color: #aaa; font-style: italic;">
                            -
                        </div>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($trx['jenis'] == 'Pelunasan Piutang'): ?>
                        <span
                            style="background: #fff3cd; color: #856404; padding: 2px 6px; border-radius: 4px; font-size: 7.5pt; font-weight: 600;">PELUNASAN
                            PIUTANG</span>
                    <?php else: ?>
                        <span
                            style="background: #e2e3e5; color: #383d41; padding: 2px 6px; border-radius: 4px; font-size: 7.5pt; font-weight: 600;">TRANSAKSI</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div style="font-size: 8.5pt; text-transform: uppercase;"><?= $trx['kasir'] ?: '-' ?></div>
                </td>
                <td class="text-right fw-bold text-success">
                    Rp <?= number_format($trx['total'], 0, ',', '.') ?>
                </td>
            </tr>
        <?php endforeach; ?>

        <?php if (empty($pemasukan_list)): ?>
            <tr>
                <td colspan="6" class="text-center" style="padding: 20px;">Belum ada data transaksi di periode ini.</td>
            </tr>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-right fw-bold"
                    style="font-size: 10pt; border-top: 1px solid #000; padding-top: 15px;">TOTAL KESELURUHAN DIBAYAR:</td>
                <td class="text-right fw-bold text-success"
                    style="font-size: 11pt; border-top: 1px solid #000; padding-top: 15px;">Rp
                    <?= number_format($grand_total, 0, ',', '.') ?>
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>

</html>