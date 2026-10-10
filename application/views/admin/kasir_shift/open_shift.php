<style>
    /* Enhanced Open Shift Form Styles - Consistent with Template */
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
        background: linear-gradient(135deg, #28a745, #218838);
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
        background: linear-gradient(135deg, #28a745, #218838);
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
        box-shadow: 0 8px 20px rgba(40, 167, 69, 0.4);
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
    
    .info-box {
        background: #e7f3ff;
        border: 1px solid #b3d9ff;
        border-radius: 0;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .info-box h6 {
        color: #0066cc;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    
    .info-box p {
        color: #0066cc;
        margin: 0;
        font-size: 0.9rem;
    }
    
    /* Validation feedback styles */
    .form-control.is-valid {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    
    .form-control.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    
    .form-control.is-valid:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    
    .form-control.is-invalid:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    
    /* Validation message styles */
    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: #dc3545;
    }
    
    .valid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: #28a745;
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
                            <i class="fa fa-play-circle"></i>
                        </div>
                        <div class="header-text">
                            <h1 class="header-title">Buka Shift Kasir</h1>
                            <h3 class="header-subtitle"><?= $user['username'] ?? 'Kasir' ?></h3>
                            <p class="header-description">Mulai sesi shift baru dengan mencatat modal awal dan informasi penting lainnya</p>
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

        <!-- Form Card -->
        <div class="shift-form-card">
            <div class="form-header">
                <h5>
                    <i class="fa fa-cash-register me-2"></i>
                    Form Pembukaan Shift
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

                <div class="info-box">
                    <h6><i class="fa fa-info-circle me-2"></i>Informasi Shift</h6>
                    <p>
                        <strong>Tanggal Shift:</strong> <?= date('d F Y') ?><br>
                        <strong>Waktu Buka:</strong> <?= date('H:i:s') ?><br>
                        <strong>Kasir:</strong> <?= $user['username'] ?? 'Kasir' ?>
                    </p>
                </div>

                <?= form_open('KasirShift/open_shift', ['id' => 'openShiftForm']) ?>
                    <div class="form-group">
                        <label for="modal_awal" class="form-label">
                            <i class="fa fa-money me-2"></i>Modal Awal (Rp)
                        </label>
                                                 <input type="number" 
                                class="form-control" 
                                id="modal_awal" 
                                name="modal_awal" 
                                value="<?= set_value('modal_awal') ?>" 
                                placeholder="Masukkan jumlah modal awal"
                                min="1"
                                step="1"
                                required>
                         <div class="form-text">
                             Masukkan jumlah uang yang tersedia di laci kasir saat memulai shift
                         </div>
                         <div class="invalid-feedback" id="modal_awal_error">
                             Modal awal harus lebih dari 0
                         </div>
                        <div class="valid-feedback" id="modal_awal_success">
                            Modal awal valid
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="catatan" class="form-label">
                            <i class="fa fa-sticky-note me-2"></i>Catatan (Opsional)
                        </label>
                        <textarea class="form-control" 
                                  id="catatan" 
                                  name="catatan" 
                                  rows="4" 
                                  placeholder="Catatan tambahan untuk shift ini..."><?= set_value('catatan') ?></textarea>
                        <div class="form-text">
                            Catatan khusus atau informasi penting untuk shift ini
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn-submit">
                            <i class="fa fa-play-circle me-2"></i>
                            Buka Shift
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
    // Format modal input with thousand separators
    const modalInput = document.getElementById('modal_awal');
    const form = document.getElementById('openShiftForm');
    
         // Real-time validation feedback
     function validateModalInput() {
         const value = modalInput.value.replace(/\D/g, '');
         const numericValue = parseInt(value) || 0;
         const errorDiv = document.getElementById('modal_awal_error');
         const successDiv = document.getElementById('modal_awal_success');
         
         // Remove any existing validation classes
         modalInput.classList.remove('is-valid', 'is-invalid');
         errorDiv.style.display = 'none';
         successDiv.style.display = 'none';
         
         if (numericValue > 0) {
             modalInput.classList.add('is-valid');
             successDiv.style.display = 'block';
             return true;
         } else if (numericValue === 0 && value !== '') {
             modalInput.classList.add('is-invalid');
             errorDiv.style.display = 'block';
             return false;
         }
         return true; // Allow empty for initial state
     }
    
    modalInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
            e.target.value = value;
        }
        
        // Validate in real-time
        validateModalInput();
    });
    
    // Handle form submission
    form.addEventListener('submit', function(e) {
        // Remove formatting before submission
        const modalValue = modalInput.value.replace(/\D/g, '');
        const numericValue = parseInt(modalValue) || 0;
        
        // Set the numeric value back to the input
        modalInput.value = numericValue;
        
                 if (!modalValue || numericValue <= 0) {
             e.preventDefault();
             
             // Show validation error
             modalInput.classList.add('is-invalid');
             
             // Show SweetAlert or alert
             if (typeof Swal !== 'undefined') {
                 Swal.fire({
                     icon: 'error',
                     title: 'Validasi Gagal',
                     text: 'Modal awal harus lebih dari 0',
                     confirmButtonText: 'OK'
                 });
             } else {
                 alert('Modal awal harus lebih dari 0');
             }
            
            modalInput.focus();
            return false;
        }
        
        // Show success feedback
        modalInput.classList.add('is-valid');
    });
    
    // Auto-focus on modal input
    modalInput.focus();
    
    // Initial validation
    validateModalInput();
});
</script>
