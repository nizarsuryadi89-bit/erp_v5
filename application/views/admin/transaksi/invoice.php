<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - <?= $nospk ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }

        .invoice-container {
            position: relative;
            max-width: 80mm;
            margin: 0 auto;
            padding: 10px;
            background: #fff;
            overflow: hidden;
        }

        .watermark {
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 46px;
            font-weight: bold;
            color: rgba(0, 0, 0, 0.08);
            border: 3px solid rgba(0, 0, 0, 0.08);
            padding: 5px 15px;
            border-radius: 8px;
            letter-spacing: 5px;
            text-transform: uppercase;
            z-index: 0;
            pointer-events: none;
            user-select: none;
        }

        .branded-watermark {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
            user-select: none;
            opacity: 0.05;
        }

        .branded-text {
            position: absolute;
            font-weight: bold;
            font-size: 24px;
            white-space: nowrap;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .company-info {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
        }

        .invoice-title {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin: 15px 0;
            text-transform: uppercase;
        }

        .invoice-details {
            margin-bottom: 15px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .detail-label {
            font-weight: bold;
            min-width: 80px;
        }

        .detail-value {
            text-align: right;
        }

        .customer-info {
            border: 1px solid #ddd;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 3px;
        }

        .customer-title {
            font-weight: bold;
            margin-bottom: 5px;
            border-bottom: 1px solid #eee;
            padding-bottom: 3px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
            font-size: 10px;
        }

        .items-table th {
            background: #f5f5f5;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-section {
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-bottom: 15px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .total-label {
            font-weight: bold;
        }

        .total-value {
            font-weight: bold;
        }

        .payment-info {
            border: 1px solid #ddd;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 3px;
        }

        .payment-title {
            font-weight: bold;
            margin-bottom: 5px;
            border-bottom: 1px solid #eee;
            padding-bottom: 3px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-paid {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        @media print {
            body {
                font-size: 10px;
            }

            .invoice-container {
                max-width: none;
                padding: 5px;
            }

            .no-print {
                display: none;
            }
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .print-button:hover {
            background: #0056b3;
        }
    </style>
</head>

<body>
    <button class="print-button no-print" onclick="window.print()">
        <i class="fa fa-print"></i> Cetak Invoice
    </button>

    <div class="invoice-container">
        <div class="branded-watermark">
            <div class="branded-text" style="top: 50px; left: 50%; transform: translateX(-50%);">Sanggar Tari Dewa
                Motekar</div>
            <div class="branded-text" style="top: 250px; left: 50%; transform: translateX(-50%);">Sanggar Tari Dewa
                Motekar</div>
            <div class="branded-text" style="top: 450px; left: 50%; transform: translateX(-50%);">Sanggar Tari Dewa
                Motekar</div>
            <div class="branded-text" style="top: 650px; left: 20px;">Sanggar Tari Dewa Motekar</div>
        </div>

        <?php if ($transaksi->status_pembayaran == 'paid'): ?>
            <div class="watermark">LUNAS</div>
        <?php endif; ?>

        <!-- Header -->
        <div class="header">
            <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo" width="100">
            <!-- <div class="company-name">Sanggar Tari Dewa Motekar</div> -->
            <div class="company-info">
                <?= $perusahaan->alamat_perusahaan ?? 'Alamat Perusahaan' ?>
                Telp: <?= $perusahaan->telepon ?? '-' ?><br>
                Email: <?= $perusahaan->email ?? '-' ?>
            </div>
        </div>

        <!-- Invoice Title -->
        <div class="invoice-title">Invoice Pembayaran</div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <div class="detail-row">
                <span class="detail-label">No. Invoice:</span>
                <span class="detail-value"><?= $transaksi->nospk ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Tanggal:</span>
                <span class="detail-value">
                    <?= date('d/m/Y', strtotime($transaksi->created_at)) ?>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Jam:</span>
                <span class="detail-value"><?= date('H:i', strtotime($transaksi->created_at)) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">
                    <span
                        class="status-badge <?= $transaksi->status_pembayaran == 'paid' ? 'status-paid' : 'status-pending' ?>">
                        <?= $transaksi->status_pembayaran == 'paid' ? 'Lunas' : 'Piutang' ?>
                    </span>
                </span>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="customer-info">
            <div class="customer-title">Costumer</div>
            <div><strong>Nama:</strong> <?= $transaksi->konsumen_nama ?></div>
            <div><strong>Alamat:</strong> <?= $transaksi->konsumen_alamat ?? '-' ?></div>
            <div><strong>Telp:</strong> <?= $transaksi->konsumen_nohp ?? '-' ?></div>
            <div><strong>Email:</strong> <?= $transaksi->konsumen_email ?? '-' ?></div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Harga</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($transaksi->products)):
                    foreach ($transaksi->products as $item): ?>
                        <tr>
                            <td><?= $item->product_nama ?></td>
                            <td class="text-right"><?= number_format($item->qty) ?></td>
                            <td class="text-right">Rp <?= number_format($item->unit_price, 0, ',', '.') ?></td>
                            <td class="text-right">Rp <?= number_format($item->unit_price * $item->qty, 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
            </tbody>
        </table>

        <!-- Total Section -->
        <div class="total-section">
            <div class="total-row">
                <span class="total-label">Total Tagihan:</span>
                <span class="total-value">Rp <?= number_format($transaksi->total_tagihan, 0, ',', '.') ?></span>
            </div>
            <div class="total-row">
                <span class="total-label">Total Bayar:</span>
                <span class="total-value">Rp <?= number_format($transaksi->total_bayar, 0, ',', '.') ?></span>
            </div>
            <?php if ($transaksi->status_pembayaran == 'pending'): ?>
                <div class="total-row">
                    <span class="total-label">Sisa Piutang:</span>
                    <span class="total-value">Rp
                        <?= number_format($transaksi->total_tagihan - $transaksi->total_bayar, 0, ',', '.') ?></span>
                </div>
            <?php endif; ?>
        </div>
        <div class="total-row" style="margin-top: 5px; font-style: italic;">
            <span class="total-label" style="font-weight: normal;">Terbilang:</span>
            <span class="total-value" style="font-weight: bold; font-size: 11px;">
                <?= ucwords(number_to_words($transaksi->total_tagihan)) ?> Rupiah
            </span>
        </div>
        <!-- Payment Info -->
        <?php if ($transaksi->metode): ?>
            <div class="payment-info">
                <div class="payment-title">Informasi Pembayaran</div>
                <div><strong>Metode:</strong> <?= ucfirst($transaksi->metode) ?></div>
                <?php if ($transaksi->referensi): ?>
                    <div><strong>Referensi:</strong> <?= $transaksi->referensi ?></div>
                <?php endif; ?>
                <?php if ($transaksi->nama_bank): ?>
                    <div><strong>Bank:</strong> <?= $transaksi->nama_bank ?></div>
                    <div><strong>No. Rekening:</strong> <?= $transaksi->no_rekening ?></div>
                    <div><strong>Atas Nama:</strong> <?= $transaksi->atas_nama ?></div>
                <?php endif; ?>
                <?php if ($transaksi->ewallet_nama): ?>
                    <div><strong>E-Wallet:</strong> <?= $transaksi->ewallet_nama ?></div>
                <?php endif; ?>
                <?php if ($transaksi->edc_nama): ?>
                    <div><strong>EDC:</strong> <?= $transaksi->edc_nama ?></div>
                <?php endif; ?>
                <?php if ($transaksi->keterangan): ?>
                    <div><strong>Keterangan:</strong> <?= $transaksi->keterangan ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Footer -->
        <div class="footer">
            <div>Terima kasih telah berbelanja di <?= $perusahaan->nama_perusahaan ?? 'TGM ERP' ?></div>
            <div>Dokumen ini adalah Bukti Pembayaran Sah</div>
            <div>Simpan invoice ini sebagai bukti pembayaran</div>
        </div>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() {
        //     window.print();
        // };

        // Close window after print (optional)
        window.onafterprint = function () {
            // window.close();
        };
    </script>
</body>

</html>