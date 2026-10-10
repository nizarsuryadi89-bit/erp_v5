<?php
/**
 * SURAT JALAN - Premium Warehouse Theme
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>
        <?= $title ?>
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary: #1a1c23;
            --slate: #64748b;
        }

        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 40px;
            color: var(--primary);
            line-height: 1.5;
            background: #fff;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header-left h1 {
            margin: 0;
            font-weight: 800;
            letter-spacing: -1px;
            font-size: 28px;
            text-transform: uppercase;
        }

        .header-left p {
            margin: 5px 0 0 0;
            color: var(--slate);
            font-weight: 600;
            font-size: 14px;
        }

        .header-right {
            text-align: right;
        }

        .doc-id {
            background: var(--primary);
            color: white;
            padding: 8px 15px;
            font-weight: 800;
            border-radius: 4px;
            font-size: 14px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .info-box h4 {
            margin: 0 0 10px 0;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--slate);
        }

        .info-content {
            font-weight: 700;
            font-size: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        th {
            background: #f8fafc;
            text-align: left;
            padding: 12px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid var(--primary);
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
        }

        .fw-bold {
            font-weight: 700;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 60px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }

        .sign-box {
            text-align: center;
        }

        .sign-line {
            margin-top: 60px;
            border-top: 1px solid var(--primary);
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }

        .sign-label {
            margin-top: 10px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .no-print {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.9);
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 700;
            text-decoration: none;
            font-size: 13px;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 40px;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button class="btn" onclick="window.print()">CETAK SEKARANG</button>
        <button class="btn" style="background: #64748b;" onclick="window.close()">TUTUP</button>
    </div>

    <div class="header">
        <div class="header-left">
            <h1>SURAT JALAN</h1>
            <p>INTERNAL MATERIAL TRANSFER NOTE</p>
        </div>
        <div class="header-right">
            <div class="doc-id">DOC#
                <?= str_pad($header['pengambilan_id'], 5, '0', STR_PAD_LEFT) ?>
            </div>
            <p style="margin: 10px 0 0 0; font-weight: 700; font-size: 13px;">
                Tanggal:
                <?= date('d/m/Y', strtotime($header['pengambilan_tgl'])) ?>
            </p>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <h4>DIKIRIM DARI</h4>
            <div class="info-content">Gudang Utama (Main Warehouse)</div>
        </div>
        <div class="info-box">
            <h4>TUJUAN PENGIRIMAN</h4>
            <div class="info-content">
                <?= $header['gudang_nama'] ?>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th>NAMA MATERIAL / PRODUK</th>
                <th width="15%" class="text-center">QTY</th>
                <th width="15%" class="text-center">SATUAN</th>
                <th>CATATAN</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            foreach ($details as $d): ?>
                <tr>
                    <td class="text-center">
                        <?= $no++ ?>
                    </td>
                    <td>
                        <div class="fw-bold">
                            <?= $d->bahan_nama ?>
                        </div>
                        <div style="font-size: 11px; color: var(--slate);">
                            <?= $d->varian ?>
                        </div>
                    </td>
                    <td class="text-center fw-bold">
                        <?= number_format($d->qty) ?>
                    </td>
                    <td class="text-center">
                        <?= $d->satuan_nama ?>
                    </td>
                    <td style="font-size: 11px; color: var(--slate); font-style: italic;">
                        <?= $d->keterangan ?: '-' ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        <div class="sign-box">
            <div class="sign-label">PENGELUAR GUDANG</div>
            <div class="sign-line"></div>
            <div style="font-size: 12px; font-weight: 700; margin-top: 5px;">
                <?= $header['creator'] ?>
            </div>
        </div>
        <div class="sign-box">
            <div class="sign-label">DRIVER / KURIR</div>
            <div class="sign-line"></div>
        </div>
        <div class="sign-box">
            <div class="sign-label">PENERIMA PRODUKSI</div>
            <div class="sign-line"></div>
        </div>
    </div>

    <div
        style="margin-top: 60px; font-size: 9px; color: var(--slate); text-align: center; border-top: 1px dashed #ddd; padding-top: 20px;">
        Dokumen ini merupakan bukti sah pengeluaran barang dari Gudang Utama ke unit terkait.<br>
        Dicetak otomatis oleh Sistem ERP pada
        <?= date('d/m/Y H:i:s') ?>
    </div>

</body>

</html>