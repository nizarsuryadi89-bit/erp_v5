<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>
        <?= $title ?>
    </title>
    <style>
        @page {
            size: auto;
            margin: 0mm;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #fff;
            margin: 20px;
            color: #333;
        }

        .no-print {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 1px solid #dee2e6;
            text-align: center;
        }

        .btn-print {
            background: #1a1c23;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .btn-print:hover {
            background: #333;
        }

        .barcode-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .barcode-item {
            border: 1px solid #eee;
            padding: 15px;
            text-align: center;
            border-radius: 10px;
            background: white;
        }

        .barcode-name {
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 5px;
            text-transform: uppercase;
            display: block;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .barcode-img {
            margin: 10px 0;
            max-width: 100%;
            height: auto;
        }

        .barcode-val {
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 2px;
            color: #555;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                margin: 0;
            }

            .barcode-item {
                border: 1px solid #ddd;
                break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">
            <i class="fa fa-print"></i> CETAK SEKARANG
        </button>
        <p style="font-size: 12px; margin-top: 10px; color: #666;">Pastikan pengaturan paper size dan margin sudah
            sesuai di menu print browser Anda.</p>
    </div>

    <div class="barcode-grid">
        <?php foreach ($items as $item): ?>
            <div class="barcode-item">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <span class="barcode-name" style="text-align: left; flex-grow: 1;">
                            <?= $item['name'] ?>
                    </span>
                    <?php if (!empty($item['unit'])): ?>
                        <span class="badge"
                            style="background: #1a1c23; color: white; font-size: 8px; padding: 2px 5px; border-radius: 4px; margin-left: 5px;">
                            <?= $item['unit'] ?>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="barcode-img">
                    <img src="<?= base_url('assets/uploads/barcode/' . $item['barcode'] . '.png') ?>"
                        onerror="this.src='https://bwipjs-api.metafloor.com/?bcid=code128&text=<?= $item['barcode'] ?>&scale=2&rotate=N&includetext=false';"
                        alt="<?= $item['barcode'] ?>" style="max-height: 55px; width: 100%;">
                </div>

                <div class="barcode-val" style="border-top: 1px dashed #ddd; padding-top: 5px; margin-top: 5px;">
                    <?= $item['barcode'] ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</body>

</html>