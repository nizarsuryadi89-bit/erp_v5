<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $title ?>
    </title>
    <style>
        /* CSS Untuk mengatur kertas agar saat diprint berulang akan pindah halaman dengan rapi */
        @page {
            size: 100mm 50mm;
            /* Standar ukuran stiker barcode (sama seperti file individual) */
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: #fff;
            color: #000;
        }

        /* 
         * Setiap div .page-break ini akan dicetak tepat di satu stiker terpisah (kertas barcode)
         * dan dipaksa memisahkan diri dari stiker selanjutnya.
         */
        .page-break {
            width: 100mm;
            height: 50mm;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            border: 1px dotted #ccc;
            /* Hapus atau setting none untuk print beneran */
            page-break-after: always;
            /* Memaksa printer pindah stiker setelah elemen ini selesai di render */
        }

        .page-break:last-child {
            page-break-after: auto;
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
                background: transparent;
            }

            .page-break {
                border: none !important;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body onload="window.print();">

    <?php foreach ($aset_list as $aset): ?>
        <div class="page-break">
            <div class="sticker-container">
                <!-- Bagian Kiri: QR Code -->
                <div class="qr-section">
                    <?php
                    $qr_path = FCPATH . 'assets/qrcode/aset_' . $aset->aset_id . '.png';
                    // Hanya tampilkan jika file QR code benaran sudah sukses digenerate agar tidak error image di print
                    if (file_exists($qr_path)):
                        ?>
                        <img src="<?= base_url('assets/qrcode/aset_' . $aset->aset_id . '.png') ?>" alt="QR Code">
                    <?php else: ?>
                        <div style="font-size:8px; border:1px solid #000; padding:10px; text-align:center;">QR Blank</div>
                    <?php endif; ?>
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
        </div>
    <?php endforeach; ?>

</body>

</html>