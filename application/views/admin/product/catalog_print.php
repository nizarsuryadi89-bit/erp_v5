<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Harga Produk LENGKAP</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
            color: #1e293b;
        }

        .print-container {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 20mm;
            box-sizing: border-box;
            background: #fff;
        }

        .header {
            text-align: center;
            margin-bottom: 15mm;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 5mm;
        }

        .header h1 {
            font-size: 24pt;
            margin: 0 0 5px 0;
            color: #0f172a;
        }

        .header p {
            font-size: 10pt;
            color: #64748b;
            margin: 0;
        }

        .company-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10mm;
            font-size: 9pt;
        }

        .product-section {
            page-break-inside: avoid;
            margin-bottom: 10mm;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
        }

        .product-header {
            background-color: #f8fafc;
            padding: 10px 15px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .product-title {
            font-size: 12pt;
            font-weight: 800;
            margin: 0;
            color: #0f172a;
        }

        .product-category {
            font-size: 8pt;
            background: #eef2ff;
            color: #4f46e5;
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: 600;
        }

        .price-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }

        .price-table th, .price-table td {
            padding: 8px 15px;
            text-align: left;
            border-bottom: 1px solid #f1f5f9;
        }

        .price-table th {
            background-color: #f1f5f9;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 8pt;
        }

        .price-table td.qty-col {
            text-align: center;
            font-weight: 600;
            width: 120px;
        }

        .price-table td.price-col {
            text-align: right;
            font-weight: 800;
            color: #0f172a;
        }

        .variant-info {
            font-size: 8pt;
            color: #64748b;
            margin-top: 3px;
        }

        @media print {
            body { background: none; }
            .print-container { padding: 0; width: 100%; }
            .no-print { display: none !important; }
            .product-section { page-break-inside: avoid; }
        }

        .print-btn-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        .btn-print {
            background: #4f46e5;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(79, 70, 229, 0.3);
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: inherit;
        }

        .btn-print:hover { background: #4338ca; }
    </style>
</head>
<body>

    <div class="print-btn-container no-print">
        <button onclick="window.print()" class="btn-print">
            <svg xmlns="http://www.w3.org/polygons" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
            </svg>
            Cetak / Simpan PDF
        </button>
    </div>

    <div class="print-container">
        <div class="header">
            <h1>KATALOG HARGA PRODUK</h1>
            <p>Berlaku per Tanggal: <?= date('d F Y') ?></p>
        </div>

        <?php if(!empty($perusahaan)): ?>
        <div class="company-info">
            <div>
                <strong><?= $perusahaan['nama_perusahaan'] ?></strong><br>
                <?= $perusahaan['alamat_perusahaan'] ?>
            </div>
            <div style="text-align: right;">
                Email: <?= $perusahaan['email'] ?><br>
                Telp: <?= $perusahaan['telepon'] ?>
            </div>
        </div>
        <?php endif; ?>

        <?php 
        $current_category = '';
        foreach($products as $p): 
            if($current_category != $p['kategori_nama']):
                $current_category = $p['kategori_nama'];
        ?>
            <h2 style="margin-top: 30px; margin-bottom: 10px; color: #334155; font-size: 14pt; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px;">
                KATEGORI: <?= strtoupper($current_category) ?>
            </h2>
        <?php endif; ?>

        <div class="product-section">
            <div class="product-header">
                <h3 class="product-title"><?= $p['product_nama'] ?></h3>
                <span class="product-category"><?= $p['subkategori_nama'] ?></span>
            </div>
            
            <?php if(!empty($p['prices'])): ?>
            <table class="price-table">
                <thead>
                    <tr>
                        <th>Spesifikasi / Variasi</th>
                        <th class="qty-col">Kuantitas</th>
                        <th class="price-col">Harga Per <?= $p['satuan_nama'] ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($p['prices'] as $pr): ?>
                    <tr>
                        <td>
                            <strong><?= $pr['detail_product'] ?: $p['product_nama'] ?></strong>
                            <div class="variant-info">
                                <?php 
                                $infos = [];
                                if(!empty($pr['ket_1_label'])) $infos[] = $pr['ket_1_label'];
                                if(!empty($pr['jenisprint_label'])) $infos[] = $pr['jenisprint_label'];
                                echo implode(' | ', $infos);
                                ?>
                            </div>
                        </td>
                        <td class="qty-col">
                            <?= number_format($pr['qty_min'], 0, ',', '.') ?> - <?= number_format($pr['qty_max'], 0, ',', '.') ?>
                        </td>
                        <td class="price-col">
                            Rp <?= number_format($pr['harga_1'], 0, ',', '.') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div style="padding: 15px; text-align: center; color: #94a3b8; font-size: 9pt;">
                <em>Belum ada daftar harga untuk produk ini.</em>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        
    </div>

    <script>
        window.onload = function() {
            // Automatically trigger print dialog on load
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
