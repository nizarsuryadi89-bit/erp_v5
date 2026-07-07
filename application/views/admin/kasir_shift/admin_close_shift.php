<?php
// Copy styles from close_shift.php
?>
<style>
    /* Enhanced Close Shift Form Styles - Consistent with Template */
    .shift-form-header {
        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        color: white;
        padding: 2.5rem 0;
        margin-bottom: 2rem;
        border-radius: 0;
        box-shadow: 0 8px 30px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }
    
    .shift-form-header::before {
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
        margin: 0 0 0.5rem 0;
        opacity: 0.9;
    }
    
    .header-description {
        font-size: 1rem;
        margin: 0;
        opacity: 0.8;
    }
    
    .shift-summary-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border: 1px solid #e0e0e0;
    }
    
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }
    
    .summary-item {
        text-align: center;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
    }
    
    .summary-label {
        font-size: 0.875rem;
        color: #666;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    
    .summary-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c3e50;
    }
    
    .summary-value.positive {
        color: #28a745;
    }
    
    .summary-value.negative {
        color: #dc3545;
    }
    
    .shift-form-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border: 1px solid #e0e0e0;
    }
    
    .form-header {
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .form-header h5 {
        margin: 0;
        color: #2c3e50;
        font-weight: 600;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .form-control {
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 0.75rem;
        font-size: 1rem;
    }
    
    .form-control:focus {
        border-color: #f39c12;
        box-shadow: 0 0 0 0.2rem rgba(243, 156, 18, 0.25);
    }
    
    .form-text {
        font-size: 0.875rem;
        color: #666;
        margin-top: 0.25rem;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        color: white;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(243, 156, 18, 0.4);
    }
    
    .btn-cancel {
        display: block;
        text-align: center;
        margin-top: 1rem;
        color: #666;
        text-decoration: none;
    }
    
    .btn-cancel:hover {
        color: #333;
    }
    
    .alert-warning-admin {
        background: #fff3cd;
        border: 1px solid #ffc107;
        color: #856404;
        padding: 1rem;
        border-radius: 6px;
        margin-bottom: 1.5rem;
    }
</style>

<div class="app-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="shift-form-header">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-7">
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="fa fa-user-shield"></i>
                        </div>
                        <div class="header-text">
                            <h1 class="header-title">Tutup Shift Kasir (Admin)</h1>
                            <h3 class="header-subtitle">Admin: <?= $user['username'] ?? 'Administrator' ?></h3>
                            <p class="header-description">Tutup shift kasir yang lupa ditutup. Shift akan ditutup dengan data saat ini.</p>
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

        <!-- Warning Alert -->
        <div class="alert-warning-admin">
            <i class="fa fa-exclamation-triangle me-2"></i>
            <strong>Peringatan:</strong> Anda sedang menutup shift kasir sebagai admin. Pastikan data yang dimasukkan sudah benar. 
            Shift akan ditutup dengan data transaksi saat ini.
        </div>

        <!-- Shift Summary -->
        <div class="shift-summary-card">
            <h5 class="mb-3">
                <i class="fa fa-info-circle me-2"></i>
                Informasi Shift
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Shift ID:</strong> #<?= $shift->shift_id ?></p>
                    <p><strong>Kasir ID:</strong> <?= $shift->user_id ?></p>
                    <p><strong>Tanggal Shift:</strong> <?= date('d/m/Y', strtotime($shift->tanggal_shift)) ?></p>
                    <p><strong>Jam Buka:</strong> <?= date('H:i:s', strtotime($shift->jam_open)) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Modal Awal:</strong> Rp <?= number_format($shift->modal_awal, 0, ',', '.') ?></p>
                    <p><strong>Status:</strong> <span class="badge bg-warning"><?= strtoupper($shift->status) ?></span></p>
                </div>
            </div>
            
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-label">Total Penjualan Tunai</div>
                    <div class="summary-value positive">Rp <?= number_format($total_cash_sales, 0, ',', '.') ?></div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Total Penjualan Non-Tunai</div>
                    <div class="summary-value positive">Rp <?= number_format($total_non_cash_sales, 0, ',', '.') ?></div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Total Penjualan</div>
                    <div class="summary-value positive">Rp <?= number_format($total_cash_sales + $total_non_cash_sales, 0, ',', '.') ?></div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Uang Fisik Diharapkan</div>
                    <div class="summary-value">Rp <?= number_format($shift->modal_awal + $total_cash_sales, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="shift-form-card">
            <div class="form-header">
                <h5>
                    <i class="fa fa-user-shield me-2"></i>
                    Form Penutupan Shift (Admin)
                </h5>
            </div>
            <div class="form-body">
                <?php if (validation_errors()): ?>
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        <?= validation_errors() ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        <?= $this->session->flashdata('error') ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle me-2"></i>
                        <?= $this->session->flashdata('success') ?>
                    </div>
                <?php endif; ?>

                <?= form_open('KasirShift/admin_close_shift/' . $shift->shift_id, ['id' => 'adminCloseShiftForm']) ?>
                    
                    <div class="form-group">
                        <label for="uang_fisik_tutup" class="form-label">
                            <i class="fa fa-money me-2"></i>Uang Fisik Saat Tutup (Rp) <small class="text-muted">(Opsional)</small>
                        </label>
                        <input type="number" 
                               class="form-control" 
                               id="uang_fisik_tutup" 
                               name="uang_fisik_tutup" 
                               value="<?= set_value('uang_fisik_tutup') ?>" 
                               placeholder="Kosongkan untuk menggunakan nilai yang dihitung otomatis"
                               min="0"
                               step="1">
                        <div class="form-text">
                            Jika dikosongkan, sistem akan menggunakan nilai yang dihitung otomatis berdasarkan modal awal + penjualan tunai
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="total_pengeluaran" class="form-label">
                            <i class="fa fa-minus-circle me-2"></i>Total Pengeluaran (Rp)
                        </label>
                        <input type="number" 
                               class="form-control" 
                               id="total_pengeluaran" 
                               name="total_pengeluaran" 
                               value="<?= set_value('total_pengeluaran', '0') ?>" 
                               placeholder="Masukkan total pengeluaran kas kecil"
                               min="0"
                               step="1">
                        <div class="form-text">
                            Total pengeluaran kas kecil selama shift (opsional)
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="catatan_tutup" class="form-label">
                            <i class="fa fa-sticky-note me-2"></i>Catatan Penutupan
                        </label>
                        <textarea class="form-control" 
                                  id="catatan_tutup" 
                                  name="catatan_tutup" 
                                  rows="4" 
                                  placeholder="Catatan tambahan untuk penutupan shift oleh admin..."><?= set_value('catatan_tutup', 'Ditutup oleh Admin karena kasir lupa menutup shift') ?></textarea>
                        <div class="form-text">
                            Catatan khusus atau informasi penting untuk penutupan shift oleh admin
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn-submit" onclick="return confirm('Yakin ingin menutup shift ini? Shift akan ditutup dengan data saat ini.')">
                            <i class="fa fa-user-shield me-2"></i>
                            Tutup Shift (Admin)
                        </button>
                    </div>
                <?= form_close() ?>

                <a href="<?= base_url('KasirShift') ?>" class="btn-cancel">
                    <i class="fa fa-times me-2"></i>
                    Batal
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-calculate expected cash
    const modalAwal = <?= $shift->modal_awal ?>;
    const totalCashSales = <?= $total_cash_sales ?>;
    const expectedCash = modalAwal + totalCashSales;
    
    // Set placeholder for uang_fisik_tutup
    const uangFisikInput = document.getElementById('uang_fisik_tutup');
    if (uangFisikInput && !uangFisikInput.value) {
        uangFisikInput.placeholder = 'Kosongkan untuk menggunakan: Rp ' + expectedCash.toLocaleString('id-ID');
    }
});
</script>

