<?php
/**
 * Print Purchase Order - Enhanced Layout
 * Optimized for printing with comprehensive information
 */
?>

<!-- ========================================
    GOOGLE FONTS - LATO
======================================== -->
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">

<!-- ========================================
    PRINT-OPTIMIZED CSS STYLES
======================================== -->
<style>
    /* Global Font Settings */
    * {
        font-family: 'Lato', sans-serif;
    }

    body {
        font-family: 'Lato', sans-serif;
        font-weight: 400;
        line-height: 1.6;
    }

    /* Screen Styles */
    .print-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        font-family: 'Lato', sans-serif;
    }

    .print-header {
        text-align: center;
        margin-bottom: 30px;
        border-bottom: 2px solid #333;
        padding-bottom: 20px;
        font-family: 'Lato', sans-serif;
    }

    .company-logo {
        max-width: 150px;
        margin-bottom: 10px;
    }

    .document-title {
        font-size: 24px;
        font-weight: 700;
        margin: 10px 0;
        color: #333;
        font-family: 'Lato', sans-serif;
        letter-spacing: 0.5px;
    }

    .document-subtitle {
        font-size: 16px;
        color: #666;
        margin-bottom: 10px;
        font-weight: 400;
        font-family: 'Lato', sans-serif;
    }

    .info-section {
        margin-bottom: 25px;
        font-family: 'Lato', sans-serif;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .info-item {
        display: flex;
        margin-bottom: 8px;
        font-family: 'Lato', sans-serif;
    }

    .info-label {
        font-weight: 700;
        min-width: 120px;
        color: #333;
        font-family: 'Lato', sans-serif;
    }

    .info-value {
        color: #555;
        font-weight: 400;
        font-family: 'Lato', sans-serif;
    }

    .table-container {
        margin: 20px 0;
        font-family: 'Lato', sans-serif;
    }

    .print-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        font-family: 'Lato', sans-serif;
    }

    .print-table th {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        padding: 12px 8px;
        text-align: left;
        font-weight: 700;
        font-size: 14px;
        font-family: 'Lato', sans-serif;
        letter-spacing: 0.3px;
    }

    .print-table td {
        border: 1px solid #dee2e6;
        padding: 10px 8px;
        font-size: 13px;
        font-family: 'Lato', sans-serif;
        font-weight: 400;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .total-row {
        background-color: #f8f9fa;
        font-weight: 700;
        font-family: 'Lato', sans-serif;
    }

    .footer-section {
        margin-top: 40px;
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 30px;
        font-family: 'Lato', sans-serif;
    }

    .signature-box {
        text-align: center;
        border-top: 1px solid #333;
        padding-top: 10px;
        margin-top: 50px;
        font-family: 'Lato', sans-serif;
    }

    .signature-line {
        border-top: 1px solid #333;
        width: 200px;
        margin: 50px auto 10px;
    }

    /* Print Styles */
    @media print {
        body {
            margin: 0;
            padding: 15px;
            font-family: 'Lato', sans-serif !important;
            font-size: 12px;
            line-height: 1.4;
            background: white !important;
            font-weight: 400;
        }

        .no-print {
            display: none !important;
        }

        .print-container {
            max-width: none;
            padding: 0;
            font-family: 'Lato', sans-serif !important;
        }

        .print-header {
            margin-bottom: 20px;
            font-family: 'Lato', sans-serif !important;
        }

        .document-title {
            font-size: 20px;
            font-weight: 700;
            font-family: 'Lato', sans-serif !important;
        }

        .info-grid {
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .print-table th,
        .print-table td {
            padding: 8px 6px;
            font-size: 11px;
            font-family: 'Lato', sans-serif !important;
        }

        .print-table th {
            font-weight: 700;
        }

        .print-table td {
            font-weight: 400;
        }

        .footer-section {
            margin-top: 30px;
            page-break-inside: avoid;
            font-family: 'Lato', sans-serif !important;
        }

        /* Page break control */
        .page-break {
            page-break-before: always;
        }

        /* Ensure tables don't break across pages */
        .print-table {
            page-break-inside: avoid;
        }

        /* Ensure all text uses Lato font */
        * {
            font-family: 'Lato', sans-serif !important;
        }
    }

    /* Button styles for screen */
    .print-btn {
        background: #007bff;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        margin: 10px 5px;
        font-family: 'Lato', sans-serif;
        font-weight: 500;
    }

    .back-btn {
        background: #6c757d;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        margin: 10px 5px;
        font-family: 'Lato', sans-serif;
        font-weight: 500;
    }

    /* Additional Lato font weights for specific elements */
    .bold-text {
        font-weight: 700;
        font-family: 'Lato', sans-serif;
    }

    .light-text {
        font-weight: 300;
        font-family: 'Lato', sans-serif;
    }

    .medium-text {
        font-weight: 500;
        font-family: 'Lato', sans-serif;
    }

    .heavy-text {
        font-weight: 900;
        font-family: 'Lato', sans-serif;
    }
</style>

<!-- ========================================
    PRINT CONTROLS
======================================== -->
<div class="no-print"
    style="text-align: center; margin: 20px 0; padding: 10px; background: #f8f9fa; border-radius: 5px; font-family: 'Lato', sans-serif;">
    <button class="print-btn" onclick="window.print()">
        <i class="fa fa-print"></i> Cetak Dokumen
    </button>
    <a href="<?= base_url('warehouse/barang_masuk') ?>" class="back-btn">
        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
    </a>
</div>

<!-- ========================================
    MAIN PRINT CONTENT
======================================== -->
<div class="print-container">
    <!-- HEADER SECTION -->
    <div class="print-header">
        <div class="company-logo">
            <!-- Add your company logo here -->
            <!-- <img src="<?= base_url('assets/images/logo/') ?>logo.png" alt="Company Logo" style="max-width: 150px;"> -->
        </div>
        <div class="document-title">SURAT PEMBELIAN BARANG</div>
        <div class="document-subtitle">Purchase Order Document</div>
        <div style="font-size: 14px; color: #666; font-family: 'Lato', sans-serif;">
            <?= date('d F Y', strtotime($getData['pembelian_tgl'])) ?>
        </div>
    </div>

    <!-- INFORMATION SECTION -->
    <div class="info-section">
        <div class="info-grid">
            <!-- Left Column -->
            <div>
                <div class="info-item">
                    <span class="info-label">No. Pembelian:</span>
                    <span class="info-value"><?= $getData['pembelian_id'] ?? 'N/A' ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tanggal:</span>
                    <span class="info-value"><?= date_indo($getData['pembelian_tgl']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Supplier:</span>
                    <span class="info-value"><?= $getData['supplier_nama'] ?? 'N/A' ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Alamat Supplier:</span>
                    <span class="info-value"><?= $getData['supplier_alamat'] ?? 'N/A' ?></span>
                </div>
            </div>

            <!-- Right Column -->
            <div>
                <div class="info-item">
                    <span class="info-label">Telepon:</span>
                    <span class="info-value"><?= $getData['supplier_telepon'] ?? 'N/A' ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?= $getData['supplier_email'] ?? 'N/A' ?></span>
                </div>

            </div>
        </div>
    </div>

    <!-- ITEMS TABLE -->
    <div class="table-container">
        <table class="print-table">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="40%">Nama Bahan</th>
                    <th width="10%" class="text-center">Qty</th>
                    <th width="15%" class="text-center">Satuan</th>
                    <th width="15%" class="text-right">Harga Satuan</th>
                    <th width="15%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $totalQty = 0;
                foreach ($tampilData as $key => $value) {
                    $jumlah = $value->qty * $value->harga_beli;
                    $totalQty += $value->qty;
                    ?>
                    <tr>
                        <td class="text-center"><?= $no ?></td>
                        <td><?= $value->bahan_nama ?></td>
                        <td class="text-center"><?= number_format($value->qty) ?></td>
                        <td class="text-center"><?= $value->satuan_nama ?? 'PCS' ?></td>
                        <td class="text-right">Rp <?= number_format($value->harga_beli) ?></td>
                        <td class="text-right">Rp <?= number_format($jumlah) ?></td>
                    </tr>
                    <?php
                    $no++;
                }
                ?>

                <!-- SUMMARY ROWS -->
                <tr class="total-row">
                    <td colspan="2" class="text-right"><strong>Total Qty:</strong></td>
                    <td class="text-center"><strong><?= number_format($totalQty) ?></strong></td>
                    <td></td>
                    <td class="text-right"><strong>Total:</strong></td>
                    <td class="text-right"><strong>Rp <?= number_format($getData['pembelian_total']) ?></strong></td>
                </tr>

                <?php if (isset($getData['pembelian_ppn']) && $getData['pembelian_ppn'] > 0): ?>
                    <tr>
                        <td colspan="5" class="text-right">PPN (11%):</td>
                        <td class="text-right">Rp <?= number_format($getData['pembelian_ppn']) ?></td>
                    </tr>
                <?php endif; ?>

                <?php if (isset($getData['pembelian_diskon']) && $getData['pembelian_diskon'] > 0): ?>
                    <tr>
                        <td colspan="5" class="text-right">Diskon:</td>
                        <td class="text-right">- Rp <?= number_format($getData['pembelian_diskon']) ?></td>
                    </tr>
                <?php endif; ?>

                <tr class="total-row">
                    <td colspan="5" class="text-right"><strong>GRAND TOTAL:</strong></td>
                    <td class="text-right"><strong>Rp <?= number_format($getData['pembelian_total']) ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- ADDITIONAL INFORMATION -->
    <div class="info-section">

        <div class="info-item">
            <span class="info-label">Metode Pembayaran:</span>
            <span class="info-value"><?= $getData['metode_bayar'] ?? 'Tunai' ?></span>
        </div>

    </div>

    <!-- FOOTER SECTION -->
    <div class="footer-section">
        <!-- Supplier Signature -->
        <div class="signature-box">
            <!-- <div class="signature-line"></div> -->
            <div style="margin-top: 10px; font-weight: 700; font-family: 'Lato', sans-serif;">Supplier</div>
            <div style="font-size: 12px; color: #666; font-family: 'Lato', sans-serif;">
                (<?= $getData['supplier_nama'] ?>)</div>
        </div>

        <!-- Witness Signature -->
        <div class="signature-box">
            <!-- <div class="signature-line"></div> -->
            <div style="margin-top: 10px; font-weight: 700; font-family: 'Lato', sans-serif;">Saksi</div>
            <div style="font-size: 12px; color: #666; font-family: 'Lato', sans-serif;">(Witness)</div>
        </div>

        <!-- Company Signature -->
        <div class="signature-box">
            <!-- <div class="signature-line"></div> -->
            <div style="margin-top: 10px; font-weight: 700; font-family: 'Lato', sans-serif;">Perusahaan</div>
            <div style="font-size: 12px; color: #666; font-family: 'Lato', sans-serif;">(Company)</div>
        </div>
    </div>

    <!-- PRINT FOOTER -->
    <div
        style="margin-top: 40px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px; font-family: 'Lato', sans-serif;">
        <p>Dokumen ini dicetak pada: <?= date('d/m/Y H:i:s') ?> |
            User: <?= $this->session->userdata('username') ?? 'System' ?> |
            Halaman 1 dari 1</p>
    </div>
</div>

<!-- ========================================
    PRINT SCRIPT
======================================== -->
<script>
    // Auto-print when page loads (optional)
    // window.onload = function() {
    //     window.print();
    // };

    // Print function
    function printDocument() {
        window.print();
    }

    // Add print event listener
    document.addEventListener('keydown', function (e) {
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            window.print();
        }
    });
</script>