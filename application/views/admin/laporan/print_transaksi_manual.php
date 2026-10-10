<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Transaksi Manual - <?= date('F Y', strtotime($bulan . '-01')) ?></title>
    
    <!-- Link Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            --success-color: #27ae60;
            --info-color: #3498db;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --bg-light: #f8f9fa;
            --text-dark: #2c3e50;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            line-height: 1.6;
            color: var(--text-dark);
            background: var(--bg-light);
            padding: 40px;
        }

        /* Container for Print */
        .print-container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            position: relative;
        }
        
        /* Header Integrated Style */
        .report-header {
            background: var(--primary-gradient);
            color: white;
            padding: 2.5rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-main h1 {
            font-size: 2rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .header-main p {
            font-size: 1.1rem;
            opacity: 0.8;
            font-weight: 300;
        }

        .header-meta {
            text-align: right;
        }

        .meta-item {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .meta-item strong {
            display: block;
            font-size: 1.2rem;
            letter-spacing: 0.5px;
        }
        
        /* Statistics Grid */
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border: 1px solid #e9ecef;
            position: relative;
            overflow: hidden;
            border-top: 4px solid #2c3e50;
        }

        .stat-card.success { border-top-color: var(--success-color); }
        .stat-card.info { border-top-color: var(--info-color); }
        .stat-card.warning { border-top-color: var(--warning-color); }

        .stat-label {
            font-size: 0.75rem;
            color: #6c757d;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            display: block;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-dark);
        }

        /* Section Title */
        .section-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #eee;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: #3498db;
        }
        
        /* Payment Methods Grid */
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 2.5rem;
        }

        .method-card {
            background: #f8f9fa;
            padding: 1rem;
            border: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .method-name {
            font-weight: 600;
            color: #495057;
        }

        .method-count {
            background: var(--primary-gradient);
            color: white;
            padding: 2px 10px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 0.85rem;
        }
        
        /* Table Premium Style */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }
        
        .report-table th {
            background: #f1f3f5;
            color: #2c3e50;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.7rem;
            padding: 12px 10px;
            border-bottom: 2px solid #dee2e6;
            text-align: left;
        }
        
        .report-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #f1f3f5;
            color: #495057;
            vertical-align: top;
        }
        
        .report-table tr:hover {
            background: #fcfcfc;
        }

        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .fw-bold { font-weight: 700 !important; }
        
        /* Badges */
        .badge {
            padding: 4px 10px;
            border-radius: 0;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            display: inline-block;
        }
        
        .badge-paid {
            background: #e7f5ee;
            color: #1e7e34;
            border: 1px solid #c3e6cb;
        }
        
        .badge-pending {
            background: #fff9e6;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .nota-badge {
            background: #f0f2f5;
            color: #2c3e50;
            padding: 2px 6px;
            font-family: monospace;
            font-weight: bold;
        }
        
        /* Footer */
        .report-footer {
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            color: #6c757d;
            font-size: 0.85rem;
        }
        
        /* Floating Controls */
        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .btn-print {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 0;
            cursor: pointer;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }
        
        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        
        /* Multi-line breakdown styling */
        .product-info { font-weight: 600; color: #2c3e50; }
        .consumer-info { font-size: 0.8rem; display: block; }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .print-container {
                box-shadow: none;
                max-width: none;
                width: 100%;
                padding: 20px;
            }
            .print-controls {
                display: none !important;
            }
            .report-header {
                background: #f8f9fa !important;
                color: #2c3e50 !important;
                border-bottom: 2px solid #2c3e50;
                padding: 1.5rem 0;
            }
            .header-main p, .header-meta .meta-item { color: #666 !important; }
            .stat-card {
                border: 1px solid #ddd !important;
                break-inside: avoid;
            }
            .report-table th { background: #eee !important; color: #000 !important; }
            .badge { border: 1px solid #999 !important; color: #000 !important; background: none !important; }
        }
    </style>
</head>
<body>
    <div class="print-controls">
        <button class="btn-print" onclick="window.print()">
            <i class="fa fa-print"></i> CETAK LAPORAN
        </button>
    </div>
    
    <div class="print-container">
        <!-- Integrated Header -->
        <header class="report-header">
            <div class="header-main">
                <h1>Rekap Transaksi Manual</h1>
                <p>PT TGM - Laporan Operasional Transaksi</p>
            </div>
            <div class="header-meta">
                <div class="meta-item">
                    Periode Laporan
                    <strong><?= date('F Y', strtotime($bulan . '-01')) ?></strong>
                </div>
                <div class="meta-item">
                    Dicetak Pada
                    <strong><?= date('d M Y, H:i') ?></strong>
                </div>
            </div>
        </header>
        
        <!-- Stats Summary -->
        <div class="stats-overview">
            <div class="stat-card">
                <span class="stat-label">Jumlah Transaksi</span>
                <div class="stat-value"><?= number_format($total_transaksi) ?> <small style="font-size: 1rem; opacity: 0.6;">Nota</small></div>
            </div>
            <div class="stat-card success">
                <span class="stat-label">Total Pendapatan</span>
                <div class="stat-value">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></div>
            </div>
            <div class="stat-card warning">
                <span class="stat-label">Total Piutang</span>
                <div class="stat-value">Rp <?= number_format($total_piutang, 0, ',', '.') ?></div>
            </div>
            <div class="stat-card info">
                <span class="stat-label">Rata-rata / TRX</span>
                <div class="stat-value">Rp <?= $total_transaksi > 0 ? number_format($total_pendapatan / $total_transaksi, 0, ',', '.') : '0' ?></div>
            </div>
        </div>
        
        <!-- Payment Breakdown -->
        <?php if (!empty($metode_pembayaran)): ?>
        <h2 class="section-title"><i class="fas fa-wallet"></i> Komposisi Pembayaran</h2>
        <div class="payment-methods">
            <?php foreach ($metode_pembayaran as $metode => $jumlah): ?>
            <div class="method-card">
                <span class="method-name"><?= ucfirst($metode) ?></span>
                <span class="method-count"><?= $jumlah ?> Nota</span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <!-- Transactions Table -->
        <h2 class="section-title"><i class="fas fa-list-alt"></i> Detail Transaksi Nota Manual</h2>
        <table class="report-table">
            <thead>
                <tr>
                    <th width="10%">SPK / No. Nota</th>
                    <th width="12%">Waktu Trx</th>
                    <th width="18%">Konsumen</th>
                    <th>Detail Produk</th>
                    <th class="text-center" width="5%">Qty</th>
                    <th class="text-right" width="12%">Tagihan</th>
                    <th class="text-right" width="12%">Bayar</th>
                    <th class="text-center" width="8%">Status</th>
                    <th width="12%">Metode & Ref</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($transaksi)): ?>
                    <?php foreach ($transaksi as $row): ?>
                    <tr>
                        <td><span class="nota-badge"><?= $row->nospk ?></span></td>
                        <td>
                            <div class="fw-bold"><?= date('d/m/Y', strtotime($row->created_at)) ?></div>
                            <small class="text-muted"><?= date('H:i', strtotime($row->created_at)) ?> WIB</small>
                        </td>
                        <td>
                            <span class="product-info"><?= $row->konsumen_nama ?></span>
                            <span class="consumer-info"><i class="fa fa-phone me-1" style="font-size: 0.7rem;"></i> <?= $row->konsumen_nohp ?? '-' ?></span>
                        </td>
                        <td><span class="product-info"><?= $row->product_nama ?></span></td>
                        <td class="text-center fw-bold"><?= number_format($row->qty) ?></td>
                        <td class="text-right fw-bold" style="color: #2c3e50;">Rp <?= number_format($row->total_tagihan, 0, ',', '.') ?></td>
                        <td class="text-right fw-bold" style="color: var(--success-color);">Rp <?= number_format($row->total_bayar ?? 0, 0, ',', '.') ?></td>
                        <td class="text-center">
                            <span class="badge <?= $row->status_pembayaran == 'paid' ? 'badge-paid' : 'badge-pending' ?>">
                                <?= $row->status_pembayaran == 'paid' ? 'Lunas' : 'Piutang' ?>
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold fs-6"><?= $row->metode ? ucfirst($row->metode) : '-' ?></div>
                            <small class="text-muted"><?= $row->referensi ?: '<i style="opacity: 0.5;">No Ref</i>' ?></small>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="fas fa-folder-open mb-2 d-block fs-3" style="opacity: 0.2;"></i>
                            Tidak ada data transaksi manual untuk periode ini
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- Footer -->
        <footer class="report-footer">
            <div class="footer-left">
                &copy; <?= date('Y') ?> TGM ERP Integrated Report System
            </div>
            <div class="footer-right">
                Halaman 1 dari 1 (Dokumen Sah Sistem)
            </div>
        </footer>
    </div>
    
    <script>
        // Visual polish for browser view
        document.title = "Print - Rekap Transaksi Manual - " + "<?= date('F Y', strtotime($bulan . '-01')) ?>";
    </script>
</body>
</html>
