<style>
    /* Enhanced Shift Detail Styles - Consistent with Template */
    .shift-detail-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 2.5rem 0;
        margin-bottom: 2rem;
        border-radius: 0;
        box-shadow: 0 8px 30px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }
    
    .shift-detail-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        pointer-events: none;
    }
    
    .header-content {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        position: relative;
        z-index: 2;
    }
    
    .header-icon {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .header-text {
        flex: 1;
    }
    
    .header-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin: 0 0 0.5rem 0;
        line-height: 1.2;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    
    .header-subtitle {
        font-size: 1.3rem;
        font-weight: 400;
        margin: 0 0 0.75rem 0;
        opacity: 0.9;
        font-weight: 300;
    }
    
    .header-description {
        font-size: 1rem;
        margin: 0;
        opacity: 0.8;
        line-height: 1.5;
        max-width: 500px;
    }
    
    .shift-info-card {
        background: white;
        border-radius: 0;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        margin-bottom: 2rem;
        overflow: hidden;
    }
    
    .info-header {
        background: linear-gradient(135deg, #17a2b8, #138496);
        color: white;
        padding: 1.5rem;
    }
    
    .info-header h5 {
        margin: 0;
        font-weight: 600;
        font-size: 1.3rem;
    }
    
    .info-content {
        padding: 1.5rem;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .info-item {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 0;
        border-left: 4px solid #17a2b8;
    }
    
    .info-label {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }
    
    .info-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c3e50;
    }
    
    .financial-summary {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 0;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .financial-summary h6 {
        color: #856404;
        margin-bottom: 1rem;
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .financial-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    
    .financial-item {
        background: white;
        padding: 1rem;
        border-radius: 0;
        border: 1px solid #ffeaa7;
    }
    
    .financial-label {
        font-size: 0.8rem;
        color: #856404;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }
    
    .financial-value {
        font-size: 1.2rem;
        font-weight: 700;
        color: #2c3e50;
    }
    
    .transactions-card {
        background: white;
        border-radius: 0;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        overflow: hidden;
    }
    
    .transactions-header {
        background: linear-gradient(135deg, #2c3e50, #34495e);
        color: white;
        padding: 1.5rem;
    }
    
    .transactions-header h5 {
        margin: 0;
        font-weight: 600;
    }
    
    .transactions-content {
        padding: 1.5rem;
    }
    
    .table {
        margin-bottom: 0;
    }
    
    .table thead th {
        background: #f8f9fa;
        border-bottom: 2px solid #e9ecef;
        color: #2c3e50;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.85rem;
        padding: 1rem 0.75rem;
    }
    
    .table tbody td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
        border-bottom: 1px solid #f8f9fa;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 0;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-open { 
        background: #d4edda; 
        color: #155724; 
    }
    
    .status-closed { 
        background: #f8d7da; 
        color: #721c24; 
    }
    
    .payment-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 0;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .payment-cash {
        background: #d4edda;
        color: #155724;
    }
    
    .payment-noncash {
        background: #cce5ff;
        color: #004085;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    
    .empty-state h5 {
        margin-bottom: 0.5rem;
        color: #2c3e50;
    }
    
    .empty-state p {
        margin-bottom: 1.5rem;
    }
    
    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
    }
    
    .btn-action {
        padding: 0.75rem 1.5rem;
        border-radius: 0;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        border: none;
    }
    
    .btn-export {
        background: linear-gradient(135deg, #28a745, #218838);
        color: white;
    }
    
    .btn-export:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(40, 167, 69, 0.4);
        color: white;
        text-decoration: none;
    }
    
    .btn-back {
        background: linear-gradient(135deg, #6c757d, #5a6268);
        color: white;
    }
    
    .btn-back:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(108, 117, 125, 0.4);
        color: white;
        text-decoration: none;
    }
</style>

<div class="app-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="shift-detail-header">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-7">
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="fa fa-file-alt"></i>
                        </div>
                        <div class="header-text">
                            <h1 class="header-title">Detail Shift #<?= $shift->shift_id ?></h1>
                            <h3 class="header-subtitle"><?= $user['username'] ?? 'Kasir' ?></h3>
                            <p class="header-description">Informasi lengkap shift kasir termasuk transaksi dan laporan keuangan</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-5">
                    <div class="text-end">
                        <a href="<?= base_url('KasirShift') ?>" class="btn btn-outline-light">
                            <i class="fa fa-arrow-left me-2"></i>
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shift Information -->
        <div class="shift-info-card">
            <div class="info-header">
                <h5>
                    <i class="fa fa-info-circle me-2"></i>
                    Informasi Shift
                </h5>
            </div>
            <div class="info-content">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Shift ID</div>
                        <div class="info-value">#<?= $shift->shift_id ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tanggal Shift</div>
                        <div class="info-value"><?= date('d F Y', strtotime($shift->tanggal_shift)) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Jam Buka</div>
                        <div class="info-value"><?= date('H:i', strtotime($shift->jam_open)) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Jam Tutup</div>
                        <div class="info-value">
                            <?= $shift->jam_close ? date('H:i', strtotime($shift->jam_close)) : 'Belum ditutup' ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <span class="status-badge status-<?= $shift->status ?>">
                                <?= $shift->status == 'open' ? 'Aktif' : 'Selesai' ?>
                            </span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Durasi</div>
                        <div class="info-value">
                            <?php 
                            $open_time = strtotime($shift->jam_open);
                            $close_time = $shift->jam_close ? strtotime($shift->jam_close) : time();
                            $duration = $close_time - $open_time;
                            $hours = floor($duration / 3600);
                            $minutes = floor(($duration % 3600) / 60);
                            echo $hours . 'j ' . $minutes . 'm';
                            ?>
                        </div>
                    </div>
                </div>

                <?php if ($shift->catatan): ?>
                <div class="financial-summary">
                    <h6><i class="fa fa-sticky-note me-2"></i>Catatan Shift</h6>
                    <p style="margin: 0; color: #856404; white-space: pre-line;"><?= $shift->catatan ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="shift-info-card">
            <div class="info-header">
                <h5>
                    <i class="fa fa-chart-bar me-2"></i>
                    Ringkasan Keuangan
                </h5>
            </div>
            <div class="info-content">
                <div class="financial-summary">
                    <h6><i class="fa fa-calculator me-2"></i>Perhitungan Keuangan</h6>
                    <div class="financial-grid">
                        <div class="financial-item">
                            <div class="financial-label">Modal Awal</div>
                            <div class="financial-value">Rp <?= number_format($shift->modal_awal, 0, ',', '.') ?></div>
                        </div>
                        <div class="financial-item">
                            <div class="financial-label">Penjualan Tunai</div>
                            <div class="financial-value">Rp <?= number_format($shift->total_penjualan_tunai, 0, ',', '.') ?></div>
                        </div>
                        <div class="financial-item">
                            <div class="financial-label">Penjualan Non-Tunai</div>
                            <div class="financial-value">Rp <?= number_format($shift->total_penjualan_non_tunai, 0, ',', '.') ?></div>
                        </div>
                        <div class="financial-item">
                            <div class="financial-label">Total Penjualan</div>
                            <div class="financial-value">Rp <?= number_format($shift->total_penjualan_tunai + $shift->total_penjualan_non_tunai, 0, ',', '.') ?></div>
                        </div>
                        <div class="financial-item">
                            <div class="financial-label">Pengeluaran</div>
                            <div class="financial-value">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></div>
                        </div>
                        <div class="financial-item">
                            <div class="financial-label">Uang Fisik Tutup</div>
                            <div class="financial-value">Rp <?= number_format($shift->uang_fisik_tutup, 0, ',', '.') ?></div>
                        </div>
                                                 <div class="financial-item">
                             <div class="financial-label">Selisih</div>
                             <?php 
                             $expected_cash = $shift->modal_awal + $shift->total_penjualan_tunai - $total_pengeluaran;
                             $calculated_selisih = $shift->uang_fisik_tutup - $expected_cash;
                             ?>
                             <div class="financial-value" style="color: <?= $calculated_selisih >= 0 ? '#28a745' : '#dc3545' ?>;">
                                 Rp <?= number_format($calculated_selisih, 0, ',', '.') ?>
                                 (<?= $calculated_selisih >= 0 ? 'Lebih' : 'Kurang' ?>)
                             </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>

                 <!-- Pengeluaran Details -->
         <?php if (!empty($pengeluaran_details)): ?>
         <div class="transactions-card">
             <div class="transactions-header">
                 <h5>
                     <i class="fa fa-money-bill-wave me-2"></i>
                     Detail Pengeluaran (<?= count($pengeluaran_details) ?> item)
                 </h5>
             </div>
             <div class="transactions-content">
                 <div class="table-responsive">
                     <table class="table table-hover">
                         <thead>
                             <tr>
                                 <th>No</th>
                                 <th>Kategori</th>
                                 <th>Detail</th>
                                 <th>Penerima</th>
                                 <th>Jumlah</th>
                                 <th>Tanggal</th>
                             </tr>
                         </thead>
                         <tbody>
                             <?php foreach ($pengeluaran_details as $index => $pengeluaran): ?>
                             <tr>
                                 <td><?= $index + 1 ?></td>
                                 <td><strong><?= $pengeluaran->kategori_pengeluaran ?: 'Umum' ?></strong></td>
                                 <td><?= $pengeluaran->pengeluaran_detail ?: '-' ?></td>
                                 <td><?= $pengeluaran->pengeluaran_penerima ?: '-' ?></td>
                                 <td><strong class="text-danger">Rp <?= number_format($pengeluaran->pengeluaran_jumlah, 0, ',', '.') ?></strong></td>
                                 <td><?= date('d/m/Y H:i', strtotime($pengeluaran->pengeluaran_tgl)) ?></td>
                             </tr>
                             <?php endforeach; ?>
                         </tbody>
                     </table>
                 </div>
             </div>
         </div>
         <?php endif; ?>

         <!-- Transactions -->
         <div class="transactions-card">
             <div class="transactions-header">
                 <h5>
                     <i class="fa fa-list me-2"></i>
                     Detail Transaksi (<?= count($transactions) ?> transaksi)
                 </h5>
             </div>
            <div class="transactions-content">
                <?php if (!empty($transactions)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>SPK</th>
                                    <th>Konsumen</th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $index => $transaction): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><strong><?= $transaction->nospk ?></strong></td>
                                    <td>
                                        <div><?= $transaction->konsumen_nama ?: 'Guest' ?></div>
                                        <small class="text-muted"><?= $transaction->konsumen_email ?: '-' ?></small>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($transaction->pembayaran_tgl . ' ' . $transaction->pembayaran_jam)) ?></td>
                                    <td><strong>Rp <?= number_format($transaction->grand_total, 0, ',', '.') ?></strong></td>
                                    <td>
                                                                        <span class="payment-badge payment-<?= strtolower($transaction->metode) == 'cash' ? 'cash' : 'noncash' ?>">
                                    <?= $transaction->metode ?>
                                </span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= $transaction->status_transaksi == '1' ? 'closed' : 'open' ?>">
                                            <?= $transaction->status_transaksi == '1' ? 'Selesai' : 'Pending' ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fa fa-inbox"></i>
                        <h5>Tidak Ada Transaksi</h5>
                        <p>Tidak ada transaksi yang dilakukan selama shift ini.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <?php if ($shift->status == 'closed'): ?>
            <a href="<?= base_url('KasirShift/export_shift_report/' . $shift->shift_id) ?>" class="btn-action btn-export">
                <i class="fa fa-download"></i>
                Export Laporan
            </a>
            <?php endif; ?>
            <a href="<?= base_url('KasirShift') ?>" class="btn-action btn-back">
                <i class="fa fa-arrow-left"></i>
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
