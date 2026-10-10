
<style>
    /* Enhanced Piutang Styles - Black & White Theme */
    .dashboard-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 2.5rem 0;
        margin-bottom: 2rem;
        border-radius: 0;
        box-shadow: 0 8px 30px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }
    
    .dashboard-header::before {
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
    
    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        border-radius: 0;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #2c3e50, #3498db);
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .stat-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }
    
    .stat-icon.bg-primary { background: #3498db; }
    .stat-icon.bg-success { background: #27ae60; }
    .stat-icon.bg-warning { background: #f39c12; }
    .stat-icon.bg-danger { background: #e74c3c; }
    
    .stat-details {
        flex: 1;
    }
    
    .stat-details h4 {
        font-size: 2rem;
        font-weight: 800;
        margin: 0;
        color: #2c3e50;
    }
    
    .stat-details p {
        margin: 0;
        color: #6c757d;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
    }
    
    .filter-section {
        background: white;
        border-radius: 0;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
    }
    
    .filter-section h5 {
        color: #2c3e50;
        font-weight: 700;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .table-responsive {
        border-radius: 0;
        overflow: hidden;
    }
    
    .table {
        margin-bottom: 0;
        border-radius: 0;
    }
    
    .table thead th {
        background: #2c3e50;
        color: white;
        border: none;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem;
        font-size: 0.9rem;
    }
    
    .table tbody td {
        padding: 1rem;
        border-bottom: 1px solid #e9ecef;
        vertical-align: middle;
    }
    
    .table tbody tr:hover {
        background: rgba(44, 62, 80, 0.05);
    }
    
    .btn {
        border-radius: 0;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.75rem 1.5rem;
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    .btn-success {
        background: #27ae60;
        border-color: #27ae60;
    }
    
    .btn-success:hover {
        background: #229954;
        border-color: #229954;
    }
    
    .btn-warning {
        background: #f39c12;
        border-color: #f39c12;
    }
    
    .btn-warning:hover {
        background: #e67e22;
        border-color: #e67e22;
    }
    
    .text-right {
        text-align: right;
    }
    
    .text-center {
        text-align: center;
    }
    
    .text-muted {
        color: #6c757d !important;
    }
    
    .py-4 {
        padding-top: 1.5rem !important;
        padding-bottom: 1.5rem !important;
    }
    
    .mb-3 {
        margin-bottom: 1rem !important;
    }
    
    .fa-3x {
        font-size: 3rem;
    }
    
    .badge {
        border-radius: 0;
        font-weight: 600;
        padding: 0.5rem 1rem;
    }
    
    .customer-info {
        line-height: 1.4;
    }
    
    .customer-name {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }
    
    .customer-phone {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    .amount {
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .amount-positive {
        color: #e74c3c;
    }
    
    .amount-zero {
        color: #27ae60;
    }
    
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 0;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-lunas {
        background-color: #d1e7dd;
        color: #0f5132;
    }
    
    .status-belum-lunas {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .status-jatuh-tempo {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .action-buttons .btn {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }
    
    /* Modal Styles */
    .modal-content {
        border-radius: 0;
        border: none;
        box-shadow: 0 8px 30px rgba(0,0,0,0.2);
    }
    
    .modal-header {
        background: #f39c12;
        color: white;
        border-bottom: 1px solid #e67e22;
        border-radius: 0;
    }
    
    .modal-title {
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .form-label {
        font-weight: 600;
        color: #2c3e50;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
    }
    
    .form-control, .form-select {
        border-radius: 0;
        border: 1px solid #e9ecef;
        padding: 0.75rem;
        font-weight: 500;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }
</style>

<div class="app-content d-block">
    <div class="container-fluid py-3">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-lg-8 col-md-7">
                        <div class="header-content">
                            <div class="header-icon">
                                <i class="fa fa-hand-holding-usd"></i>
                            </div>
                            <div class="header-text">
                                <h1 class="header-title">Laporan Piutang</h1>
                                <h3 class="header-subtitle"><?= $perusahaan ?? 'TGM ERP System' ?></h3>
                                <p class="header-description">Kelola dan monitor piutang konsumen dengan detail pembayaran dan status jatuh tempo</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-5">
                        <div class="header-info">
                            <a href="<?= base_url('laporan') ?>" class="btn btn-outline-light">
                                <i class="fa fa-arrow-left me-2"></i>
                                Kembali
                            </a>
                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="fa fa-users"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-value"><?= count($tampilData) ?></div>
                                    <div class="info-label">Konsumen</div>
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
        </div>

        <!-- Statistics Overview -->
        <div class="stats-overview">
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-icon bg-primary">
                        <i class="fa fa-users"></i>
                    </div>
                    <div class="stat-details">
                        <h4><?= count($tampilData) ?></h4>
                        <p>Total Konsumen</p>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-icon bg-danger">
                        <i class="fa fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-details">
                        <h4>Rp <?= number_format($total_trx['total_piutang']) ?></h4>
                        <p>Total Piutang</p>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-icon bg-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-details">
                        <h4><?= count(array_filter($tampilData, function($item) use ($hariini) { 
                            return isset($item->tempo_tgl) && $item->tempo_tgl < $hariini; 
                        })) ?></h4>
                        <p>Jatuh Tempo</p>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-icon bg-success">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="stat-details">
                        <h4><?= count(array_filter($tampilData, function($item) { 
                            $totalPiutang = $this->M_transaksi->totalPiutangKonsumen($item->konsumen_id)->row_array();
                            return $totalPiutang['total'] == 0; 
                        })) ?></h4>
                        <p>Lunas</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fa fa-table text-primary me-2"></i>Daftar Piutang Konsumen</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="datatable-details">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Konsumen</th>
                                <th style="width: 20%;">Total Piutang</th>
                                <th style="width: 20%;">Status</th>
                                <th style="width: 25%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($tampilData as $dt):
                                $totalPiutang = $this->M_transaksi->totalPiutangKonsumen($dt->konsumen_id)->row_array();
                                $getPiutang = $this->M_transaksi->getPiutangKonsumen($dt->konsumen_id)->result();
                                
                                // Determine status class
                                $statusClass = 'status-belum-lunas';
                                $statusText = 'Belum Lunas';
                                
                                if ($totalPiutang['total'] == 0) {
                                    $statusClass = 'status-lunas';
                                    $statusText = 'Lunas';
                                } elseif (isset($dt->tempo_tgl) && $dt->tempo_tgl < $hariini) {
                                    $statusClass = 'status-jatuh-tempo';
                                    $statusText = 'Jatuh Tempo';
                                }
                            ?>
                            <tr>
                                <td>
                                    <div class="customer-info">
                                        <div class="customer-name"><?= $dt->konsumen_nama ?></div>
                                        <div class="customer-phone">
                                            <i class="fa fa-phone me-1"></i>
                                            <?= $dt->konsumen_nohp ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="amount <?= $totalPiutang['total'] > 0 ? 'amount-positive' : 'amount-zero' ?>">
                                        Rp <?= number_format($totalPiutang['total']) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge <?= $statusClass ?>">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if ($totalPiutang['total'] > 0): ?>
                                            <button type="button" 
                                                    class="btn btn-warning btn-sm"
                                                    onclick="showPaymentModal(<?= $dt->konsumen_id ?>, '<?= $dt->konsumen_nama ?>', <?= $totalPiutang['total'] ?>)">
                                                <i class="fa fa-credit-card me-1"></i>
                                                Bayar
                                            </button>
                                            
                                            <a href="https://api.whatsapp.com/send?phone=<?= $dt->konsumen_nohp ?>&text=<?= urlencode('-TGMPrint- %0AHaii%20'.$dt->konsumen_nama.'%20Piutang%20Anda%0ARp.%20'.number_format($totalPiutang['total']).'%0AMohon%20Segera%20melakukan%20Pembayaran%0AAbaikan%20Pesan%20ini%20Jika%20Sudah%20Melakukan%20Pembayaran') ?>" 
                                               target="_blank"
                                               class="btn btn-success btn-sm">
                                                <i class="fab fa-whatsapp me-1"></i>
                                                WA
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                            $no++;
                            endforeach; 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">
                    <i class="fa fa-credit-card me-2"></i>
                    Pembayaran Piutang
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="paymentForm" onsubmit="processPayment(event)">
                <div class="modal-body">
                    <!-- Customer Information -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Nama Konsumen</label>
                                <input type="text" class="form-control" id="customerName" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Total Piutang</label>
                                <input type="text" class="form-control fw-bold text-danger" id="totalReceivable" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Details -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Jumlah Pembayaran <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="paymentAmount" name="payment_amount" 
                                       placeholder="Masukkan jumlah pembayaran" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                                <select class="form-select" id="paymentMethod" name="payment_method" required>
                                    <option value="">Pilih metode pembayaran</option>
                                    <option value="cash">Tunai</option>
                                    <option value="transfer">Transfer Bank</option>
                                    <option value="qris">QRIS</option>
                                    <option value="edc">EDC</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Tanggal Pembayaran <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="paymentDate" name="payment_date" 
                                       value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Keterangan</label>
                                <input type="text" class="form-control" id="paymentNotes" name="payment_notes" 
                                       placeholder="Keterangan pembayaran (opsional)">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-warning" id="submitPayment">
                        <i class="fa fa-paper-plane me-1"></i> Proses Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Global variables
    let currentCustomerId = null;
    let currentCustomerName = null;
    let currentTotalReceivable = 0;
    
    /**
     * Show payment modal with customer data
     */
    function showPaymentModal(konsumenId, customerName, totalReceivable) {
        currentCustomerId = konsumenId;
        currentCustomerName = customerName;
        currentTotalReceivable = totalReceivable;
        
        // Populate modal with basic data
        $('#customerName').val(customerName);
        $('#totalReceivable').val('Rp ' + numberFormat(totalReceivable));
        $('#paymentAmount').val(totalReceivable);
        
        // Show modal
        var paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        paymentModal.show();
    }
    
    /**
     * Process payment submission
     */
    function processPayment(event) {
        event.preventDefault();
        
        // Validate form
        if (!validatePaymentForm()) {
            return false;
        }
        
        // Show loading state
        const submitBtn = $('#submitPayment');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fa fa-spinner fa-spin me-1"></i> Memproses...');
        submitBtn.prop('disabled', true);
        
        // Collect form data
        const formData = {
            konsumen_id: currentCustomerId,
            payment_amount: $('#paymentAmount').val(),
            payment_method: $('#paymentMethod').val(),
            payment_date: $('#paymentDate').val(),
            payment_notes: $('#paymentNotes').val(),
            total_receivable: currentTotalReceivable
        };
        
        // Submit payment
        $.ajax({
            url: '<?= base_url("keuangan/process_payment") ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('success', 'Pembayaran berhasil diproses!');
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
                    modal.hide();
                    
                    // Reload page after delay
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('error', 'Gagal memproses pembayaran: ' + (response.message || 'Terjadi kesalahan'));
                }
            },
            error: function() {
                showAlert('error', 'Terjadi kesalahan sistem');
            },
            complete: function() {
                // Restore button state
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }
        });
    }
    
    /**
     * Validate payment form
     */
    function validatePaymentForm() {
        const paymentAmount = parseFloat($('#paymentAmount').val());
        const paymentMethod = $('#paymentMethod').val();
        const paymentDate = $('#paymentDate').val();
        
        if (!paymentAmount || paymentAmount <= 0) {
            showAlert('error', 'Jumlah pembayaran harus lebih dari 0');
            $('#paymentAmount').focus();
            return false;
        }
        
        if (paymentAmount > currentTotalReceivable) {
            showAlert('error', 'Jumlah pembayaran tidak boleh melebihi total piutang');
            $('#paymentAmount').focus();
            return false;
        }
        
        if (!paymentMethod) {
            showAlert('error', 'Pilih metode pembayaran');
            $('#paymentMethod').focus();
            return false;
        }
        
        if (!paymentDate) {
            showAlert('error', 'Pilih tanggal pembayaran');
            $('#paymentDate').focus();
            return false;
        }
        
        return true;
    }
    
    /**
     * Show alert message
     */
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa fa-check-circle' : 'fa fa-exclamation-circle';
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Remove existing alerts
        $('.alert').remove();
        
        // Add new alert at top of page
        $('.container-fluid').prepend(alertHtml);
        
        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
    
    /**
     * Format number with commas
     */
    function numberFormat(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }
    
    /**
     * Initialize payment amount validation
     */
    $(document).ready(function() {
        // Validate payment amount on input
        $('#paymentAmount').on('input', function() {
            const amount = parseFloat($(this).val());
            const total = currentTotalReceivable;
            
            if (amount > total) {
                $(this).addClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
                $(this).after('<div class="invalid-feedback">Jumlah melebihi total piutang</div>');
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }
        });
        
        // Auto-focus payment amount when modal opens
        $('#paymentModal').on('shown.bs.modal', function() {
            $('#paymentAmount').focus();
        });
        
        // Initialize DataTable
        $('#datatable-details').DataTable({
            responsive: true,
            language: {
                url: '<?= base_url('assets/js/datatables-id.json') ?>'
            },
            pageLength: 25,
            order: [[1, 'desc']], // Sort by total piutang descending
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel"></i> Export Excel',
                    className: 'btn btn-success btn-sm',
                    title: 'Laporan Piutang - <?= date('d/m/Y') ?>'
                },
                {
                    extend: 'pdf',
                    text: '<i class="fa fa-file-pdf"></i> Export PDF',
                    className: 'btn btn-danger btn-sm',
                    title: 'Laporan Piutang - <?= date('d/m/Y') ?>'
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Print',
                    className: 'btn btn-info btn-sm',
                    title: 'Laporan Piutang - <?= date('d/m/Y') ?>'
                }
            ]
        });
    });
</script>
