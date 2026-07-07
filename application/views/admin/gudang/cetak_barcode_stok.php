<?php
// FILE: application/views/admin/gudang/cetak_barcode_stok.php
// Print Barcode Layout
?>
<!DOCTYPE html>
<html>

<head>
    <title>Cetak Barcode</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .barcode-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: flex-start;
        }

        .barcode-item {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            width: 200px;
            border-radius: 5px;
            page-break-inside: avoid;
        }

        .item-name {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .item-code {
            font-size: 10px;
            color: #666;
            margin-top: 3px;
        }

        .barcode-img {
            max-width: 100%;
            height: 50px;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
            }

            .barcode-item {
                border: none;
                margin-bottom: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="no-print"
        style="margin-bottom: 20px; padding: 10px; background: #f8f9fa; border-bottom: 1px solid #ddd;">
        <button onclick="window.print()"
            style="padding: 10px 20px; background: #0d6efd; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
            <i class="fa fa-print"></i> Cetak Sekarang
        </button>
        <button onclick="window.close()"
            style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            Tutup
        </button>
    </div>

    <div class="barcode-container">
        <?php if (!empty($items)): ?>
            <?php foreach ($items as $item): ?>
                <?php
                // Calculate copies if specified, default 1
                $copies = isset($item['qty']) ? max(1, (int) $item['qty']) : 1;
                for ($i = 0; $i < $copies; $i++):
                    ?>
                    <div class="barcode-item">
                        <div class="item-name">
                            <?= $item['name'] ?>
                        </div>
                        <?php if (!empty($item['barcode'])): ?>
                            <!-- Assuming barcode generator helper or library is used, here referencing image path -->
                            <!-- Adjust path based on your actual barcode storage or generation URL -->
                            <img src="<?= base_url('assets/uploads/barcode/' . $item['barcode'] . '.png') ?>"
                                alt="<?= $item['barcode'] ?>" class="barcode-img"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <div style="display:none; font-size: 10px; color: red;">No Image</div>
                        <?php else: ?>
                            <div style="padding: 15px; background: #eee; font-size: 10px;">No Barcode</div>
                        <?php endif; ?>
                        <div class="item-code">
                            <?= $item['barcode'] ?>
                        </div>
                    </div>
                <?php endfor; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Tidak ada data barcode untuk dicetak.</p>
        <?php endif; ?>
    </div>
</body>

</html>