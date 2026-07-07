<style>
    /* Enhanced Kasir Shift Management Styles - Consistent with Template */
    .shift-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 2.5rem 0;
        margin-bottom: 2rem;
        border-radius: 0;
        box-shadow: 0 8px 30px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }
    
    .shift-header::before {
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
    
    .header-info {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        position: relative;
        z-index: 2;
    }
    
    .info-card {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 0;
        padding: 1rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 120px;
        transition: all 0.3s ease;
    }
    
    .info-card:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }
    
    .info-icon {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    
    .info-content {
        flex: 1;
    }
    
    .info-value {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 0.25rem;
    }
    
    .info-label {
        font-size: 0.8rem;
        opacity: 0.8;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    /* Shift Status Card Styles */
    .shift-status-card {
        background: white;
        border-radius: 0;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        margin-bottom: 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .shift-status-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #2c3e50, #34495e);
    }
    
    .status-icon {
        width: 80px;
        height: 80px;
        border-radius: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: white;
        margin: 0 auto 1.5rem;
    }
    
    .status-icon.open {
        background: linear-gradient(135deg, #28a745, #218838);
    }
    
    .status-icon.closed {
        background: linear-gradient(135deg, #dc3545, #c82333);
    }
    
    .status-icon.pending {
        background: linear-gradient(135deg, #ffc107, #e0a800);
    }
    
    .status-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }
    
    .status-description {
        font-size: 1rem;
        color: #6c757d;
        margin-bottom: 1.5rem;
    }
    
    /* Action Buttons Styles */
    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 1.5rem;
    }
    
    .btn-shift {
        padding: 0.875rem 1.75rem;
        border-radius: 0;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.3s ease;
        border: none;
        min-width: 140px;
        justify-content: center;
    }
    
    .btn-open {
        background: linear-gradient(135deg, #28a745, #218838);
        color: white;
    }
    
    .btn-open:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(40, 167, 69, 0.4);
        color: white;
        text-decoration: none;
    }
    
    .btn-close {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
    }
    
    .btn-close:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(220, 53, 69, 0.4);
        color: white;
        text-decoration: none;
    }
    
    .btn-secondary {
        background: linear-gradient(135deg, #6c757d, #5a6268);
        color: white;
    }
    
    .btn-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(108, 117, 125, 0.4);
        color: white;
        text-decoration: none;
    }
    
    /* Shift History Styles */
    .shift-history {
        background: white;
        border-radius: 0;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        overflow: hidden;
    }
    
    .history-header {
        background: linear-gradient(135deg, #2c3e50, #34495e);
        color: white;
        padding: 1.5rem;
    }
    
    .history-header h5 {
        margin: 0;
        font-weight: 600;
    }
    
    .history-content {
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
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .header-title {
            font-size: 2rem;
        }
        
        .header-subtitle {
            font-size: 1.1rem;
        }
        
        .action-buttons {
            flex-direction: column;
            align-items: center;
        }
        
        .btn-shift {
            width: 100%;
            max-width: 300px;
        }
        
        .header-info {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .info-card {
            min-width: auto;
        }
    }
</style>

<div class="app-content">
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="shift-header">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-7">
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="fa fa-cash-register"></i>
                        </div>
                        <div class="header-text">
                            <h1 class="header-title">Kasir Shift Management</h1>
                            <h3 class="header-subtitle"><?= $user['username'] ?? 'Kasir' ?></h3>
                            <p class="header-description">
                                Kelola shift kasir untuk memulai dan mengakhiri sesi transaksi dengan laporan keuangan yang akurat
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-5">
                    <div class="header-info">
                        <div class="info-card">
                            <a href="<?= base_url('transaksi') ?>" style="color: white; text-decoration: none;">
                                <i class="fa fa-arrow-left"></i>
                                Kembali
                            </a>
                        </div>
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fa fa-calendar-alt"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-value"><?= date('d') ?></div>
                                <div class="info-label"><?= date('M Y') ?></div>
                            </div>
                        </div>
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fa fa-clock"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-value"><?= date('H:i') ?></div>
                                <div class="info-label">Waktu</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shift Status Card -->
        <div class="shift-status-card">
            <?php 
            $user_level = $this->session->userdata('level');
            $is_admin = ($user_level == 1);
            $is_kasir = ($user_level == 3);
            
            // Handle multiple shifts for admin
            if ($is_admin && is_array($current_shift) && !empty($current_shift)): ?>
                <!-- Admin View: Multiple Open Shifts -->
                <div class="status-icon open">
                    <i class="fa fa-list"></i>
                </div>
                <h2 class="status-title">Shift Aktif (<?= count($current_shift) ?> Shift)</h2>
                <p class="status-description mb-4">
                    Daftar semua shift kasir yang masih terbuka. Admin dapat menutup shift yang lupa ditutup oleh kasir.
                </p>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Shift ID</th>
                                <th>Kasir</th>
                                <th>Tanggal</th>
                                <th>Jam Buka</th>
                                <th>Modal Awal</th>
                                <th>Total Penjualan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($current_shift as $shift): 
                                // Get kasir name from joined data or fallback
                                $kasir_name = !empty($shift->nama_lengkap) ? $shift->nama_lengkap : 
                                             (!empty($shift->username) ? $shift->username : 'User #' . $shift->user_id);
                                
                                // Get sales data
                                try {
                                    $shift_cash = $KasirShift_model->get_total_cash_sales($shift->shift_id);
                                    $shift_non_cash = $KasirShift_model->get_total_non_cash_sales($shift->shift_id);
                                    $shift_total = $shift_cash + $shift_non_cash;
                                } catch (Exception $e) {
                                    $shift_total = 0;
                                    log_message('error', 'Error calculating shift sales: ' . $e->getMessage());
                                }
                            ?>
                            <tr>
                                <td><strong>#<?= $shift->shift_id ?></strong></td>
                                <td><?= htmlspecialchars($kasir_name) ?></td>
                                <td><?= date('d/m/Y', strtotime($shift->tanggal_shift)) ?></td>
                                <td><?= date('H:i', strtotime($shift->jam_open)) ?></td>
                                <td>Rp <?= number_format($shift->modal_awal, 0, ',', '.') ?></td>
                                <td>Rp <?= number_format($shift_total, 0, ',', '.') ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('KasirShift/admin_close_shift/' . $shift->shift_id) ?>" 
                                           class="btn btn-sm btn-warning"
                                           title="Tutup Shift (Admin)">
                                            <i class="fa fa-user-shield"></i> Tutup
                                        </a>
                                        <a href="<?= base_url('KasirShift/shift_detail/' . $shift->shift_id) ?>" 
                                           class="btn btn-sm btn-primary"
                                           title="Lihat Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
            <?php elseif ($current_shift && !is_array($current_shift)): ?>
                <!-- Kasir View: Single Shift -->
                <div class="status-icon open">
                    <i class="fa fa-check-circle"></i>
                </div>
                <h2 class="status-title">Shift Aktif</h2>
                <p class="status-description">
                    Shift Anda sedang berjalan sejak <?= date('H:i', strtotime($current_shift->jam_open)) ?> 
                    dengan modal awal Rp <?= number_format($current_shift->modal_awal, 0, ',', '.') ?>
                    <?php 
                    // Get real-time sales data for current shift
                    // Only calculate if shift is still open (jam_close is null)
                    if (empty($current_shift->jam_close)) {
                        try {
                    $current_cash = $KasirShift_model->get_total_cash_sales($current_shift->shift_id);
                    $current_non_cash = $KasirShift_model->get_total_non_cash_sales($current_shift->shift_id);
                    $current_total = $current_cash + $current_non_cash;
                    if ($current_total > 0): ?>
                    <br>Total penjualan: Rp <?= number_format($current_total, 0, ',', '.') ?>
                            <?php endif;
                        } catch (Exception $e) {
                            // Silently handle error - don't break the page
                            log_message('error', 'Error calculating shift sales: ' . $e->getMessage());
                        }
                    }
                    ?>
                </p>
                <div class="action-buttons">
                    <?php if ($is_kasir): ?>
                    <a href="<?= base_url('KasirShift/close_shift') ?>" 
                       class="btn-shift btn-close"
                       title="Tutup shift saat ini">
                        <i class="fa fa-times-circle"></i>
                        Tutup Shift
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($is_admin): ?>
                    <a href="<?= base_url('KasirShift/admin_close_shift/' . $current_shift->shift_id) ?>" 
                       class="btn-shift btn-warning"
                       title="Tutup shift sebagai admin (jika kasir lupa)">
                        <i class="fa fa-user-shield"></i>
                        Tutup Shift (Admin)
                    </a>
                    <?php endif; ?>
                    
                    <a href="<?= base_url('KasirShift/shift_detail/' . $current_shift->shift_id) ?>" 
                       class="btn-shift btn-secondary"
                       title="Lihat detail shift saat ini">
                        <i class="fa fa-eye"></i>
                        Lihat Detail
                    </a>
                </div>
            <?php else: ?>
                <!-- No Active Shift -->
                <div class="status-icon pending">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
                <h2 class="status-title">Tidak Ada Shift Aktif</h2>
                <p class="status-description">
                    Anda harus membuka shift terlebih dahulu sebelum dapat melakukan transaksi. 
                    Shift akan mencatat modal awal dan semua transaksi selama sesi berlangsung.
                </p>
                <div class="action-buttons">
                    <a href="<?= base_url('KasirShift/open_shift') ?>" 
                       class="btn-shift btn-open"
                       title="Buka shift baru">
                        <i class="fa fa-play-circle"></i>
                        Buka Shift Baru
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Shift History Section -->
        <div class="shift-history">
            <div class="history-header">
                <h5>
                    <i class="fa fa-history me-2"></i>
                    Riwayat Shift
                </h5>
            </div>
            <div class="history-content">
                <?php if (!empty($shift_history)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Shift ID</th>
                                    <th>Tanggal</th>
                                    <th>Jam Buka</th>
                                    <th>Jam Tutup</th>
                                    <th>Modal Awal</th>
                                    <th>Total Penjualan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($shift_history as $shift): ?>
                                <tr>
                                    <td><strong>#<?= $shift->shift_id ?></strong></td>
                                    <td><?= date('d/m/Y', strtotime($shift->tanggal_shift)) ?></td>
                                    <td><?= date('H:i', strtotime($shift->jam_open)) ?></td>
                                    <td>
                                        <?= $shift->jam_close ? date('H:i', strtotime($shift->jam_close)) : '-' ?>
                                    </td>
                                    <td>Rp <?= number_format($shift->modal_awal, 0, ',', '.') ?></td>
                                    <td>
                                        <?php 
                                        // Get real-time sales data from tbl_pembayaran
                                        // Only calculate if shift is still open or has valid data
                                        try {
                                            if (empty($shift->jam_close) || $shift->status == 'open') {
                                        $total_cash = $KasirShift_model->get_total_cash_sales($shift->shift_id);
                                        $total_non_cash = $KasirShift_model->get_total_non_cash_sales($shift->shift_id);
                                        $total_sales = $total_cash + $total_non_cash;
                                            } else {
                                                // Use stored values if shift is closed
                                                $total_sales = ($shift->total_penjualan_tunai ?? 0) + ($shift->total_penjualan_non_tunai ?? 0);
                                            }
                                        } catch (Exception $e) {
                                            // Fallback to stored values on error
                                            $total_sales = ($shift->total_penjualan_tunai ?? 0) + ($shift->total_penjualan_non_tunai ?? 0);
                                            log_message('error', 'Error calculating shift sales: ' . $e->getMessage());
                                        }
                                        ?>
                                        Rp <?= number_format($total_sales, 0, ',', '.') ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= $shift->status ?>">
                                            <?= $shift->status == 'open' ? 'Aktif' : 'Selesai' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('KasirShift/shift_detail/' . $shift->shift_id) ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Lihat Detail">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <?php if ($shift->status == 'closed'): ?>
                                            <a href="<?= base_url('KasirShift/export_shift_report/' . $shift->shift_id) ?>" 
                                               class="btn btn-sm btn-outline-success" 
                                               title="Export Laporan">
                                                <i class="fa fa-download"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fa fa-history"></i>
                        <h5>Belum Ada Riwayat Shift</h5>
                        <p>Riwayat shift Anda akan muncul di sini setelah Anda membuka shift pertama.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-refresh shift status every 30 seconds
setInterval(function() {
    fetch('<?= base_url('KasirShift/check_shift_status') ?>')
        .then(response => response.json())
        .then(data => {
            // Update status if needed
            if (data.has_open_shift) {
                // Redirect to refresh the page if shift status changed
                if (!document.querySelector('.status-icon.open')) {
                    location.reload();
                }
            } else {
                // Redirect to refresh the page if no shift
                if (document.querySelector('.status-icon.open')) {
                    location.reload();
                }
            }
        })
        .catch(error => console.error('Error checking shift status:', error));
}, 30000);
</script>
