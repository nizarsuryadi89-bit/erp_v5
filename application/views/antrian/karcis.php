<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karcis Antrian</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            padding: 20px;
        }

        .karcis-container {
            width: 58mm;
            background: white;
            margin: 0 auto;
            padding: 5px;
            border: none;
            box-shadow: none;
            text-align: center;
        }

        .header {
            border-bottom: 2px dashed #ccc;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .title {
            font-size: 12px;
            color: #000;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .counter-name {
            font-size: 12px;
            color: #000;
        }

        .nomor-antrian-section {
            padding: 10px 0;
            border-bottom: 1px dashed #000;
            margin-bottom: 10px;
        }

        .label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }

        .nomor-antrian {
            font-size: 48px;
            font-weight: bold;
            color: #000;
            font-family: sans-serif;
            line-height: 1;
            margin: 10px 0;
        }

        .info-section {
            text-align: left;
            font-size: 12px;
            line-height: 1.8;
            color: #666;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            color: #333;
        }

        .info-value {
            text-align: right;
        }

        .footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px dashed #ccc;
            font-size: 10px;
            color: #999;
            text-align: center;
        }

        .barcode {
            margin: 15px 0;
            font-family: 'Courier New', monospace;
            font-size: 20px;
            letter-spacing: 2px;
            color: #333;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .karcis-container {
                width: 100%;
                margin: 0;
                box-shadow: none;
                page-break-after: always;
            }
        }

        @page {
            size: 58mm auto;
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="karcis-container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">
                <?= isset($perusahaan['nama_perusahaan']) ? $perusahaan['nama_perusahaan'] : 'TGM ERP'; ?>
            </div>
            <div class="title">KARCIS ANTRIAN</div>
            <div class="counter-name">
                <?= $antrian['counter_kode'] ? 'DESK ' . $antrian['counter_kode'] : $antrian['counter_nama']; ?></div>
        </div>

        <!-- Nomor Antrian -->
        <div class="nomor-antrian-section">
            <div class="label">Nomor Antrian</div>
            <div class="nomor-antrian"><?= $antrian['nomor_antrian']; ?></div>
            <div class="barcode">|<?= $antrian['nomor_antrian']; ?>|</div>
        </div>

        <!-- Info -->
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Waktu Ambil:</span>
                <span class="info-value"><?= date('H:i:s', strtotime($antrian['waktu_ambil'])); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal:</span>
                <span class="info-value"><?= date('d-m-Y', strtotime($antrian['waktu_ambil'])); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">
                    <?php
                    $status_text = '';
                    switch ($antrian['status']) {
                        case 'menunggu':
                            $status_text = 'MENUNGGU';
                            break;
                        case 'dipanggil':
                            $status_text = 'DIPANGGIL';
                            break;
                        case 'sedang_dilayani':
                            $status_text = 'DILAYANI';
                            break;
                        case 'selesai':
                            $status_text = 'SELESAI';
                            break;
                        case 'batal':
                            $status_text = 'DIBATALKAN';
                            break;
                    }
                    echo $status_text;
                    ?>
                </span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Terima kasih telah menggunakan layanan kami</p>
            <p style="margin-top: 5px;">Mohon tunggu hingga nomor antrian Anda dipanggil</p>
        </div>
    </div>

    <script>
        window.onload = function () {
            window.print();
            // Close window after print dialog is closed (or immediately if silent print is enabled)
            // Small delay to ensure print dialog opens first
            setTimeout(function () {
                window.close();
            }, 500);
        };
    </script>
</body>

</html>