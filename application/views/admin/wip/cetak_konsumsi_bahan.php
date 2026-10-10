<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Konsumsi Bahan</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Lato', sans-serif; box-sizing: border-box; }
        body { font-size: 14px; line-height: 1.5; color: #333; margin: 0; padding: 0; }
        .print-container { max-width: 900px; margin: 0 auto; padding: 40px; }
        .header { text-align: center; border-bottom: 2px solid #333; margin-bottom: 30px; padding-bottom: 20px; }
        .title { font-size: 24px; font-weight: 900; margin: 0; text-transform: uppercase; }
        .subtitle { font-size: 16px; font-weight: 400; margin: 5px 0 0; color: #555; }
        
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
            <h1 class="title">Laporan Konsumsi Bahan (WIP)</h1>
            <p class="subtitle">
                Periode: 
                <?= date('d M Y', strtotime($start_date)) ?> 
                s/d 
                <?= date('d M Y', strtotime($end_date)) ?>
            </p>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th class="text-center" width="5%">No</th>
                    <th width="35%">Nama Material</th>
                    <th class="text-center" width="12%">Penggunaan Pusat</th>
                    <th class="text-center" width="13%">Penggunaan Cabang</th>
                    <th class="text-center" width="15%">Total Penggunaan</th>
                    <th class="text-center" width="15%">Konversi ke Satuan Gudang</th>
                    <th class="text-center" width="5%">Frekuensi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($summary)): ?>
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data konsumsi bahan pada periode ini</td>
                </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($summary as $row): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td>
                                <strong><?= htmlspecialchars($row->bahan_nama ?? '-') ?></strong><br>
                                <small style="color: #666;"><?= htmlspecialchars($row->material_nama ?? '-') ?></small>
                            </td>
                            <td class="text-center">
                                <?= str_replace(',00', '', number_format($row->total_produksi_pusat, 2, ',', '.')) ?> <?= htmlspecialchars($row->satuan_produksi ?? $row->satuan_gudang ?? '') ?>
                            </td>
                            <td class="text-center">
                                <?= str_replace(',00', '', number_format($row->total_produksi_cabang, 2, ',', '.')) ?> <?= htmlspecialchars($row->satuan_produksi ?? $row->satuan_gudang ?? '') ?>
                            </td>
                            <td class="text-center">
                                <strong><?= str_replace(',00', '', number_format($row->total_produksi, 2, ',', '.')) ?> <?= htmlspecialchars($row->satuan_produksi ?? $row->satuan_gudang ?? '-') ?></strong>
                            </td>
                            <td class="text-center">
                                <?php if (!empty($row->satuan_produksi) && !empty($row->satuan_gudang) && strtolower(trim($row->satuan_produksi)) !== strtolower(trim($row->satuan_gudang))): ?>
                                    <?= str_replace(',00', '', number_format($row->total_gudang, 2, ',', '.')) ?> <?= htmlspecialchars($row->satuan_gudang) ?>
                                    <div style="font-size: 0.75rem; color: #666; margin-top: 3px;">
                                        (1 <?= htmlspecialchars($row->satuan_gudang) ?> = <?= str_replace(',00', '', number_format($row->konversi ?? 1, 2, ',', '.')) ?> <?= htmlspecialchars($row->satuan_produksi) ?>)
                                    </div>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?= $row->frekuensi ?>x
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div class="signature-section">
            <div class="signature-box">
                <p style="margin-bottom: 5px;">Tanggal Cetak: <?= date('d F Y') ?></p>
                <p>Mengetahui,</p>
                <div class="signature-line"></div>
                <strong>Bagian Produksi</strong>
            </div>
        </div>
    </div>
    
    <script>
        window.onload = function() {
            setTimeout(() => window.print(), 500);
        }
    </script>
</body>
</html>
