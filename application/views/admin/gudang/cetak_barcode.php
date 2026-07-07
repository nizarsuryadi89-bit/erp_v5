<!DOCTYPE html>
<html>
<head>
    <title>Cetak Barcode</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .barcode-table { width: 100%; border-collapse: collapse; }
        .barcode-table td { padding: 10px; text-align: center; vertical-align: top; }
        .barcode-label { border: 1px solid #ccc; padding: 10px; margin: 5px; display: inline-block; }
        .barcode-img { margin-bottom: 5px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom:20px;">
        <button onclick="window.print()">Print</button>
    </div>
    <h2 style="text-align:center;">Cetak Barcode Semua Bahan</h2>
    <table class="barcode-table">
        <tr>
        <?php
        $col = 0;
        foreach ($bahan as $b) {
            if ($col > 0 && $col % 4 == 0) echo '</tr><tr>';
            ?>
            <td>
                <div class="barcode-label">
                    <div class="barcode-img">
                        <img src="<?= base_url('assets/uploads/barcode/' . $b->barcode . '.png') ?>" alt="<?= $b->barcode ?>" style="max-width:150px;max-height:50px;">
                    </div>
                    <div><strong><?= $b->barcode ?></strong></div>
                    <div><?= $b->bahan_nama ?></div>
                </div>
            </td>
            <?php
            $col++;
        }
        ?>
        </tr>
    </table>
</body>
</html>
