<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stiker Aset -
        <?= $aset->aset_nama ?>
    </title>
    <style>
        @page {
            size: 100mm 50mm;
            /* Standar ukuran stiker barcode */
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: #fff;
            color: #000;
            width: 100mm;
            height: 50mm;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            border: 1px dotted #ccc;
            /* Hilangkan di print final jika perlu */
        }

        .sticker-container {
            width: 100%;
            height: 100%;
            padding: 4mm;
            box-sizing: border-box;
            display: flex;
            flex-direction: row;
        }

        .qr-section {
            width: 35%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-right: 1.5px solid #000;
            padding-right: 3mm;
        }

        .qr-section img {
            width: 100%;
            max-width: 32mm;
            height: auto;
        }

        .qr-id {
            font-size: 8pt;
            font-weight: bold;
            margin-top: 2px;
            letter-spacing: 0.5px;
        }

        .info-section {
            width: 65%;
            padding-left: 4mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .company-header {
            font-size: 8pt;
            font-weight: bold;
            border-bottom: 2px solid #000;
            padding-bottom: 2px;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        .asset-name {
            font-size: 11pt;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        .asset-detail {
            font-size: 7.5pt;
            line-height: 1.3;
        }

        .asset-detail span {
            font-weight: bold;
            display: inline-block;
            width: 15mm;
        }

        @media print {
            body {
                border: none !important;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body onload="window.print();">

    <div class="sticker-container">
        <!-- Bagian Kiri: QR Code -->
        <div class="qr-section">
            <img src="<?= base_url('assets/qrcode/aset_' . $aset->aset_id . '.png') ?>" alt="QR Code">
            <div class="qr-id">ASET-
                <?= str_pad($aset->aset_id, 4, '0', STR_PAD_LEFT) ?>
            </div>
        </div>

        <!-- Bagian Kanan: Informasi Barcode Label -->
        <div class="info-section">
            <div>
                <div class="company-header">INVENTARIS TGM PRINT</div>
                <div class="asset-name">
                    <?php
                    // Jika nama terlalu panjang, potong max ~35 karakter
                    $nama = strtoupper($aset->aset_nama);
                    echo (strlen($nama) > 35) ? substr($nama, 0, 35) . '...' : $nama;
                    ?>
                </div>

                <div class="asset-detail mt-1">
                    <div><span>Kategori</span>:
                        <?= strtoupper($aset->aset_kategori) ?>
                    </div>
                    <div><span>Lokasi</span>:
                        <?= strtoupper($aset->nama_perusahaan ?? 'Pusat') ?>
                    </div>
                </div>
            </div>

            <div style="font-size: 6pt; margin-top: auto; font-style: italic;">
                * Scan QR Code ini untuk melihat detail lengkap aset.
            </div>
        </div>
    </div>

</body>

</html>