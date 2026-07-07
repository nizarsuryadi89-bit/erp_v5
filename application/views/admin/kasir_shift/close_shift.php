<style>
    /* Enhanced Close Shift Form Styles - Consistent with Template */
    .shift-form-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
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
    
    .shift-summary-card {
        background: white;
        border-radius: 0;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        margin-bottom: 2rem;
        overflow: hidden;
    }
    
    .summary-header {
        background: linear-gradient(135deg, #17a2b8, #138496);
        color: white;
        padding: 1.5rem;
    }
    
    .summary-header h5 {
        margin: 0;
        font-weight: 600;
        font-size: 1.3rem;
    }
    
    .summary-content {
        padding: 1.5rem;
    }
    
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .summary-item {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 0;
        border-left: 4px solid #17a2b8;
    }
    
    .summary-label {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }
    
    .summary-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c3e50;
    }
    
    .shift-form-card {
        background: white;
        border-radius: 0;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        overflow: hidden;
        max-width: 600px;
        margin: 0 auto;
    }
    
    .form-header {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
        padding: 1.5rem;
        text-align: center;
    }
    
    .form-header h5 {
        margin: 0;
        font-weight: 600;
        font-size: 1.3rem;
    }
    
    .form-body {
        padding: 2rem;
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
        border-radius: 0;
        border: 1px solid #e9ecef;
        padding: 0.75rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #2c3e50;
        box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.25);
    }
    
    .form-text {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
        border: none;
        border-radius: 0;
        padding: 0.75rem 2rem;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        width: 100%;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(220, 53, 69, 0.4);
    }
    
    .btn-cancel {
        background: linear-gradient(135deg, #6c757d, #5a6268);
        color: white;
        border: none;
        border-radius: 0;
        padding: 0.75rem 2rem;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        text-align: center;
        margin-top: 1rem;
        width: 100%;
    }
    
    .btn-cancel:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(108, 117, 125, 0.4);
        color: white;
        text-decoration: none;
    }
    
    .alert {
        border-radius: 0;
        border: none;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
    }
    
    .alert-success {
        background: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
    }
    
    .calculation-box {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 0;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .calculation-box h6 {
        color: #856404;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    
    .calculation-box p {
        color: #856404;
        margin: 0;
        font-size: 0.9rem;
    }
    
    .expected-cash {
        font-size: 1.2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-top: 0.5rem;
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
                            <i class="fa fa-times-circle"></i>
                        </div>
                        <div class="header-text">
                            <h1 class="header-title">Tutup Shift Kasir</h1>
                            <h3 class="header-subtitle"><?= $user['username'] ?? 'Kasir' ?></h3>
                            <p class="header-description">Akhiri sesi shift dengan menghitung total transaksi dan memverifikasi uang fisik</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-5">
                    <div class="text-end">
                        <a href="<?= base_url('kasirshift') ?>" class="btn btn-outline-light">
                            <i class="fa fa-arrow-left me-2"></i>
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shift Summary -->
        <div class="shift-summary-card">
            <div class="summary-header">
                <h5>
                    <i class="fa fa-chart-bar me-2"></i>
                    Ringkasan Shift #<?= $current_shift->shift_id ?>
                </h5>
            </div>
            <div class="summary-content">
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-label">Modal Awal</div>
                        <div class="summary-value">Rp <?= number_format($current_shift->modal_awal, 0, ',', '.') ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Penjualan Tunai</div>
                        <div class="summary-value">Rp <?= number_format($total_cash_sales, 0, ',', '.') ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Penjualan Non-Tunai</div>
                        <div class="summary-value">Rp <?= number_format($total_non_cash_sales, 0, ',', '.') ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Total Penjualan</div>
                        <div class="summary-value">Rp <?= number_format($total_cash_sales + $total_non_cash_sales, 0, ',', '.') ?></div>
                    </div>
                </div>
                
                <div class="calculation-box">
                    <h6><i class="fa fa-calculator me-2"></i>Perhitungan Uang Fisik yang Diharapkan</h6>
                    <p>
                        <strong>Modal Awal:</strong> Rp <?= number_format($current_shift->modal_awal, 0, ',', '.') ?><br>
                        <strong>+ Penjualan Tunai:</strong> Rp <?= number_format($total_cash_sales, 0, ',', '.') ?><br>
                        <strong>- Pengeluaran:</strong> Rp 0 (akan diinput)
                    </p>
                    <div class="expected-cash">
                        Uang Fisik yang Diharapkan: Rp <?= number_format($current_shift->modal_awal + $total_cash_sales, 0, ',', '.') ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="shift-form-card">
            <div class="form-header">
                <h5>
                    <i class="fa fa-cash-register me-2"></i>
                    Form Penutupan Shift
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

                <?= form_open('KasirShift/close_shift', ['id' => 'closeShiftForm']) ?>
                    
                    <div class="form-group">
                        <label for="uang_fisik_tutup" class="form-label">
                            <i class="fa fa-money me-2"></i>Uang Fisik Saat Tutup (Rp)
                        </label>
                        <input type="number" 
                               class="form-control" 
                               id="uang_fisik_tutup" 
                               name="uang_fisik_tutup" 
                               value="<?= set_value('uang_fisik_tutup') ?>" 
                               placeholder="Masukkan jumlah uang fisik yang dihitung"
                               min="0"
                               step="1"
                               required>
                        <div class="form-text">
                            Hitung dan masukkan jumlah uang fisik yang ada di laci kasir
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
                            <i class="fa fa-sticky-note me-2"></i>Catatan Penutupan (Opsional)
                        </label>
                        <textarea class="form-control" 
                                  id="catatan_tutup" 
                                  name="catatan_tutup" 
                                  rows="4" 
                                  placeholder="Catatan tambahan untuk penutupan shift..."><?= set_value('catatan_tutup') ?></textarea>
                        <div class="form-text">
                            Catatan khusus atau informasi penting untuk penutupan shift
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn-submit">
                            <i class="fa fa-times-circle me-2"></i>
                            Tutup Shift
                        </button>
                    </div>
                <?= form_close() ?>

                <a href="<?= base_url('kasirshift') ?>" class="btn-cancel">
                    <i class="fa fa-times me-2"></i>
                    Batal
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Format currency inputs with thousand separators
    const currencyInputs = ['uang_fisik_tutup', 'total_pengeluaran'];
    
    currencyInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value) {
                    // Only format if it's a valid number
                    const numValue = parseInt(value);
                    if (!isNaN(numValue)) {
                        e.target.value = numValue.toLocaleString('id-ID');
                    }
                }
            });
        }
    });
    
    // Handle form submission
    document.getElementById('closeShiftForm').addEventListener('submit', function(e) {
        // Remove formatting before submission
        currencyInputs.forEach(inputId => {
            const input = document.getElementById(inputId);
            if (input) {
                const value = input.value.replace(/\D/g, '');
                input.value = value;
            }
        });
        
        const uangFisik = document.getElementById('uang_fisik_tutup').value;
        if (!uangFisik || uangFisik <= 0) {
            e.preventDefault();
            alert('Uang fisik saat tutup harus diisi dan lebih dari 0');
            document.getElementById('uang_fisik_tutup').focus();
            return false;
        }
    });
    
    // Auto-focus on uang fisik input
    document.getElementById('uang_fisik_tutup').focus();
    
    // Real-time calculation display
    const uangFisikInput = document.getElementById('uang_fisik_tutup');
    const pengeluaranInput = document.getElementById('total_pengeluaran');
    const expectedCash = <?= $current_shift->modal_awal + $total_cash_sales ?>;
    
    function updateCalculation() {
        const uangFisik = parseInt(uangFisikInput.value.replace(/\D/g, '')) || 0;
        const pengeluaran = parseInt(pengeluaranInput.value.replace(/\D/g, '')) || 0;
        const selisih = uangFisik - (expectedCash - pengeluaran);
        
        // Update expected cash display
        const expectedCashElement = document.querySelector('.expected-cash');
        if (expectedCashElement) {
            const expectedAmount = expectedCash - pengeluaran;
            expectedCashElement.innerHTML = `
                Uang Fisik yang Diharapkan: Rp ${expectedAmount.toLocaleString('id-ID')}<br>
                <small style="color: ${selisih >= 0 ? '#28a745' : '#dc3545'};">
                    Selisih: Rp ${Math.abs(selisih).toLocaleString('id-ID')} ${selisih >= 0 ? '(Lebih)' : '(Kurang)'}
                </small>
            `;
        }
    }
    
    uangFisikInput.addEventListener('input', updateCalculation);
    pengeluaranInput.addEventListener('input', updateCalculation);
});
</script>
