<?php
/**
 * Print Goods Pickup Letter - Enhanced Layout
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
<div class="no-print" style="text-align: center; margin: 20px 0; padding: 10px; background: #f8f9fa; border-radius: 5px; font-family: 'Lato', sans-serif;">
    <button class="print-btn" onclick="window.print()">
        <i class="fa fa-print"></i> Cetak Dokumen
    </button>
    <a href="<?= base_url('warehouse/barang_keluar') ?>" class="back-btn">
        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
    </a>
</div>

<!-- ========================================
    MAIN PRINT CONTENT
======================================== -->
<div class="print-container">
    <!-- HEADER SECTION -->
    <div class="print-header">
        <div class="company-logo text-center">
            <!-- Add your company logo here -->
            <img src="<?= base_url('assets/images/') ?>logo.png" alt="Company Logo" style="max-width: 150px;">
        </div>
        <div class="document-title">SURAT JALAN PENGAMBILAN BARANG</div>
        <!-- <div class="document-subtitle">Goods Pickup Letter</div> -->
        <div style="font-size: 14px; color: #666; font-family: 'Lato', sans-serif;">
            <?= date('d F Y', strtotime($getData['pengambilan_tgl'] ?? date('Y-m-d'))) ?>
        </div>
    </div>

    <!-- INFORMATION SECTION -->
    <div class="info-section">
        <div class="info-grid">
            <!-- Left Column -->
            <div>
                <div class="info-item">
                    <span class="info-label">No. Surat Jalan:</span>
                    <span class="info-value"><?= $getData['pengambilan_id'] ?? 'N/A' ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tanggal:</span>
                    <span class="info-value"><?= date_indo($getData['pengambilan_tgl'] ?? date('Y-m-d')) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Pengambil:</span>
                    <span class="info-value"><?= $getData['nama_lengkap'] ?? 'N/A' ?></span>
                </div>
               
            </div>
            
            <!-- Right Column -->
            <div>
                <div class="info-item">
                    <span class="info-label">Telepon:</span>
                    <span class="info-value"><?= $getData['telepon'] ?? 'N/A' ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?= $getData['email'] ?? 'N/A' ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Dibuat Oleh:</span>
                    <span class="info-value"><?= $getData['usernama'] ?? 'System' ?></span>
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
                    <th width="40%">Nama Barang</th>
                    <th width="10%" class="text-center">Qty</th>
                    <th width="15%" class="text-center">Satuan</th>
                    <th width="15%" class="text-center">Kondisi</th>
                    <th width="15%" class="text-center">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                $totalQty = 0;
                foreach ($tampilData as $key => $value) {
                    $totalQty += $value->pengambilan_qty ?? 0;
                ?>
                <tr>
                    <td class="text-center"><?= $no ?></td>
                    <td><?= $value->barang_nama ?? $value->bahan_nama ?? 'N/A' ?></td>
                    <td class="text-center"><?= number_format($value->pengambilan_qty ?? $value->qty ?? 0) ?></td>
                    <td class="text-center"><?= $value->satuan_nama ?? 'PCS' ?></td>
                    <td class="text-center"><?= $value->kondisi ?? 'Baik' ?></td>
                    <td class="text-center"><?= $value->keterangan ?? '-' ?></td>
                </tr>
                <?php
                    $no++;
                }
                ?>
                
                <!-- SUMMARY ROWS -->
                <tr class="total-row">
                    <td colspan="2" class="text-right"><strong>Total Qty:</strong></td>
                    <td class="text-center"><strong><?= number_format($totalQty) ?></strong></td>
                    <td colspan="3"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- ADDITIONAL INFORMATION -->
    <!-- <div class="info-section">
        <div class="info-item">
            <span class="info-label">Tujuan Pengambilan:</span>
            <span class="info-value"><?= $getData['tujuan_pengambilan'] ?? 'Produksi' ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Catatan:</span>
            <span class="info-value"><?= $getData['pengambilan_catatan'] ?? 'Tidak ada catatan' ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Estimasi Kembali:</span>
            <span class="info-value">
                <?= isset($getData['estimasi_kembali']) ? date_indo($getData['estimasi_kembali']) : 'Tidak ada estimasi' ?>
            </span>
        </div>
    </div> -->

    <!-- PICKUP CONDITIONS -->
    <!-- <div class="info-section">
        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h4 style="margin-bottom: 10px; font-weight: 700; color: #333;">Syarat dan Ketentuan:</h4>
            <ol style="margin-left: 20px; line-height: 1.6;">
                <li>Barang yang diambil harus dikembalikan dalam kondisi yang sama</li>
                <li>Pengambilan barang harus disetujui oleh kepala departemen</li>
                <li>Tanggung jawab keamanan barang selama pengambilan ada pada pengambil</li>
                <li>Barang rusak atau hilang akan menjadi tanggung jawab pengambil</li>
                <li>Dokumen ini harus dibawa saat pengambilan dan pengembalian barang</li>
            </ol>
        </div>
    </div> -->

    <!-- FOOTER SECTION -->
    <div class="footer-section">
        <!-- Warehouse Signature -->
        <div class="signature-box">
            <div class="signature-line"></div>
            <div style="margin-top: 10px; font-weight: 700; font-family: 'Lato', sans-serif;">Gudang</div>
            <div style="font-size: 12px; color: #666; font-family: 'Lato', sans-serif;">(Warehouse)</div>
        </div>
        
        <!-- Receiver Signature -->
        <div class="signature-box">
            <div class="signature-line"></div>
            <div style="margin-top: 10px; font-weight: 700; font-family: 'Lato', sans-serif;">Penerima</div>
            <div style="font-size: 12px; color: #666; font-family: 'Lato', sans-serif;">(<?= $getData['pengambil_nama'] ?? 'Receiver' ?>)</div>
        </div>
        
        <!-- Supervisor Signature -->
        <div class="signature-box">
            <div class="signature-line"></div>
            <div style="margin-top: 10px; font-weight: 700; font-family: 'Lato', sans-serif;">Supervisor</div>
            <div style="font-size: 12px; color: #666; font-family: 'Lato', sans-serif;">(Supervisor)</div>
        </div>
    </div>

    <!-- PRINT FOOTER -->
    <div style="margin-top: 40px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px; font-family: 'Lato', sans-serif;">
        <p>Dokumen ini dicetak pada: <?= date('d/m/Y H:i:s') ?> | 
        User: <?= $this->session->userdata('nama') ?? 'System' ?> | 
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
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            window.print();
        }
    });
</script>