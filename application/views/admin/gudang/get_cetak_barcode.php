<!DOCTYPE html>
<html>
<head>
    <title>Cetak Barcode Bahan</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .barcode-label { border: 1px solid #ccc; padding: 20px; margin: 30px auto; width: 400px; text-align: center; }
        .barcode-img { margin-bottom: 10px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom:20px;">
        <button onclick="window.print()">Print</button>
    </div>
    <div class="barcode-label">
        <div class="barcode-img">
            <img src="<?= base_url('assets/uploads/barcode/' . $bahan['barcode'] . '.png') ?>" alt="<?= $bahan['barcode'] ?>" style="max-width:200px;max-height:60px;">
        </div>
        <div><strong><?= $bahan['barcode'] ?></strong></div>
        <div><?= $bahan['bahan_nama'] ?></div>
    </div>
</body>
</html>
