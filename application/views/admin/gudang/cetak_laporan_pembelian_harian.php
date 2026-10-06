<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Lato', sans-serif; box-sizing: border-box; }
        body { font-size: 14px; line-height: 1.5; color: #333; margin: 0; padding: 0; }
        .print-container { max-width: 900px; margin: 0 auto; padding: 40px; }
        .header { text-align: center; border-bottom: 2px solid #333; margin-bottom: 30px; padding-bottom: 20px; }
        .title { font-size: 24px; font-weight: 900; margin: 0; text-transform: uppercase; }
        .subtitle { font-size: 16px; font-weight: 400; margin: 5px 0 0; color: #555; }
        
        .summary-box { display: flex; justify-content: space-between; margin-bottom: 30px; padding: 20px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 8px; }
        .summary-item { text-align: center; flex: 1; }
        .summary-label { font-size: 12px; color: #666; text-transform: uppercase; font-weight: bold; margin-bottom: 5px; }
        .summary-value { font-size: 20px; font-weight: 900; color: #111; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 13px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f1f5f9; font-weight: 700; color: #333; text-transform: uppercase; font-size: 12px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row td { font-weight: 900; background-color: #f8f9fa; }
        
        .signature-section { margin-top: 50px; display: flex; justify-content: flex-end; }
        .signature-box { text-align: center; width: 250px; }
        .signature-line { border-bottom: 1px solid #333; height: 80px; margin-bottom: 10px; }
        
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .print-container { padding: 0; }
            .hide-print { display: none !important; }
        }
        
        .btn-print { background: #333; color: white; border: none; padding: 10px 20px; font-size: 14px; cursor: pointer; border-radius: 4px; display: block; margin: 20px auto; }
    </style>
</head>
<body>
    <button class="btn-print hide-print" onclick="window.print()">🖨️ Cetak Laporan</button>
    
    <div class="print-container">
        <div class="header">
            <h1 class="title">Laporan Pembelian Harian</h1>
            <p class="subtitle">Tanggal: <?= $formatted_date ?></p>
        </div>
        
        <div class="summary-box">
            <div class="summary-item">
                <div class="summary-label">Total Transaksi</div>
                <div class="summary-value"><?= $total_transaksi ?></div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Nilai Pembelian</div>
                <div class="summary-value">Rp <?= number_format($total_nilai, 0, ',', '.') ?></div>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th class="text-center" width="5%">No</th>
                    <th width="20%">No Faktur</th>
                    <th width="35%">Supplier</th>
                    <th width="20%">Keterangan</th>
                    <th class="text-right" width="20%">Total (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pembelian_data)): ?>
                <tr>
                    <td colspan="5" class="text-center">Tidak ada transaksi pada tanggal ini</td>
                </tr>
                <?php else: ?>
                    <?php $no = 1; foreach($pembelian_data as $p): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><strong><?= $p->no_faktur ?: '-' ?></strong></td>
                        <td><?= $p->supplier_nama ?: '-' ?></td>
                        <td><?= $p->keterangan ?: '-' ?></td>
                        <td class="text-right"><?= number_format($p->pembelian_total, 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="4" class="text-right">TOTAL KESELURUHAN</td>
                        <td class="text-right"><?= number_format($total_nilai, 0, ',', '.') ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div class="signature-section">
            <div class="signature-box">
                <p style="margin-bottom: 5px;">Depok, <?= date('d F Y') ?></p>
                <p>Mengetahui,</p>
                <div class="signature-line"></div>
                <strong>Bagian Gudang / Purchasing</strong>
            </div>
        </div>
    </div>
    
    <script>
        window.onload = function() {
            // setTimeout(() => window.print(), 500);
        }
    </script>
</body>
</html>
