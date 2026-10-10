<!DOCTYPE html>
<html>

<head>
    <title>Label Produksi - #<?= $items[0]->nospk ?></title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 20px;
        }

        .label-box {
            border: 2px solid #000;
            width: 10cm;
            min-height: 5cm;
            padding: 15px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .title {
            font-size: 14pt;
            font-weight: bold;
        }

        .spk {
            font-size: 18pt;
            font-weight: bold;
            float: right;
        }

        .meta {
            margin-bottom: 15px;
        }

        .meta-row {
            display: flex;
            margin-bottom: 5px;
            font-size: 11pt;
        }

        .label {
            width: 100px;
            font-weight: bold;
        }

        .value {
            flex: 1;
        }

        .items {
            border-top: 1px dashed #000;
            padding-top: 10px;
        }

        .item-row {
            font-size: 12pt;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            font-size: 9pt;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
            }
        }
    </style>
</head>
<!-- <body onload="window.print()"> -->
<div class="no-print" style="margin-bottom: 20px;">
    <button onclick="window.print()">Cetak</button>
    <button onclick="window.close()">Tutup</button>
</div>

<!-- Label Utama -->
<div class="label-box">
    <div class="header">
        <span class="title">LABEL PRODUKSI</span>
        <span class="spk">#<?= $items[0]->nospk ?></span>
    </div>

    <?php
    // Robust Dateline Fetching
    $dateline = '-';
    if (isset($items[0]->dateline_tgl)) {
        $dateline = date('d M Y', strtotime($items[0]->dateline_tgl));
    } elseif (isset($items[0]->dateline)) {
        $dateline = date('d M Y', strtotime($items[0]->dateline));
    } elseif (isset($items[0]->tgl_transaksi)) {
        $dateline = date('d M Y', strtotime($items[0]->tgl_transaksi));
    } elseif (isset($items[0]->tgl)) {
        $dateline = date('d M Y', strtotime($items[0]->tgl));
    }
    ?>

    <div class="meta">
        <div class="meta-row">
            <span class="label">KONSUMEN</span>
            <span class="value">: <?= strtoupper($items[0]->konsumen_nama ?? '-') ?></span>
        </div>
        <div class="meta-row">
            <span class="label">DEADLINE</span>
            <span class="value">: <?= $dateline ?></span>
        </div>
    </div>

    <div class="items">
        <div style="font-weight: bold; margin-bottom: 5px; text-decoration: underline;">DAFTAR BARANG:</div>
        <?php foreach ($items as $item): ?>
            <div class="item-row">
                [ ] <?= $item->product_nama ?>
                <?php if ($item->ket_1 == '1'): ?>
                    (1 Sisi)
                <?php elseif ($item->ket_1 == '2'): ?>
                    (2 Sisi - Bolak-Balik)
                <?php endif; ?>
                <span style="float:right;">Qty: <?= $item->qty ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="footer">
        Harap label ini ditempel pada kemasan barang jadi.
    </div>
</div>
</body>

</html>