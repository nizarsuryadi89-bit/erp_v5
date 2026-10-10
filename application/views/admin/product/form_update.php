<div class="app-content d-block">
    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Update Product : <?= $getproduct['product_nama'] ?></h4>
                <p class="text-muted mb-0">Edit informasi product yang sudah ada</p>
            </div>
            <div>
                <a href="javascript:history.back()" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
                <a href="<?= base_url($controller.'/') ?>" class="btn btn-outline-warning">
                    <i class="bi bi-x-circle me-1"></i>Batal
                </a>
            </div>
        </div>

        <form method="post" action="<?= base_url('manufaktur/actionUpdateproduct') ?>" class="needs-validation" novalidate>
            <input type="hidden" name="product_id" value="<?= $getproduct['product_id'] ?>">
            <!-- Basic Information Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi Dasar Product</h5>
                </div>
                <div class="card-body">
                    <!-- Kategori dan Sub Kategori -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori_id" id="kategori_id" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($kategori as $kat): ?>
                                    <option value="<?= $kat->kategori_id ?>" 
                                        <?= ($kat->kategori_id == $getproduct['kategori_id']) ? 'selected' : '' ?>>
                                        <?= $kat->kategori_nama ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Pilih kategori utama product</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Sub Kategori</label>
                            <select name="subkategori_id" id="subkategori_id" class="form-select">
                                <option value="0">-- Pilih Sub Kategori --</option>
                                <?php foreach($subkategori as $k): ?>
                                    <option value="<?= $k->subkategori_id ?>" 
                                        <?= ($k->subkategori_id == $getproduct['subkategori_id']) ? 'selected' : '' ?>>
                                        <?= $k->subkategori_nama ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Pilih sub kategori (opsional)</div>
                        </div>
                    </div>

                    

                    <!-- Nama Product dan Satuan -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Product <span class="text-danger">*</span></label>
                            <input type="text" name="product_nama" class="form-control" required 
                                   placeholder="Masukkan nama product" value="<?= $getproduct['product_nama'] ?>">
                            <div class="form-text">Nama lengkap product yang akan ditampilkan</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Tipe</label>
                            <select name="tipe_id" id="tipe_id" class="form-select">
                                <option value="">-- Pilih Tipe --</option>
                                <?php foreach($type as $k): ?>
                                    <option value="<?= $k->tipe_id ?>" 
                                        <?= ($k->tipe_id == $getproduct['tipe_id']) ? 'selected' : '' ?>>
                                        <?= $k->tipe_nama ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Tipe product</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Satuan <span class="text-danger">*</span></label>
                            <select name="satuan_id" id="satuan_id" class="form-select" required>
                                <option value="">-- Pilih Satuan --</option>
                                <?php foreach($satuan as $k): ?>
                                    <option value="<?= $k->satuan_id ?>" 
                                        <?= ($k->satuan_id == $getproduct['satuan_id']) ? 'selected' : '' ?>>
                                        <?= $k->satuan_nama ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Satuan pengukuran</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Pilihan Metode Penjualan</label>
                            <select name="metode_id" class="form-select">
                                <option value="">-- Pilihan Metode Penjualan --</option>
                                <?php foreach ($metode as $k): ?>
                                    <option value="<?= $k->metode_id ?>" 
                                        <?= isset($getproduct['detail_id']) && $k->metode_id == $getproduct['detail_id'] ? 'selected' : '' ?>>
                                        <?= $k->metode_nama ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Product Dijual Secara Onsite / Online</div>
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Deskripsi Product</label>
                            <textarea name="product_deskripsi" class="form-control" rows="4" 
                                      placeholder="Masukkan deskripsi lengkap product..." 
                                      style="white-space: pre-wrap; font-family: inherit;">
                                <?= htmlspecialchars($getproduct['product_deskripsi']) ?>
                            </textarea>
                            <div class="form-text">Deskripsi detail tentang product (spesifikasi, kegunaan, dll)</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <button type="submit" class="btn btn-primary btn-lg me-3">
                        <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
                    </button>
                    <a href="<?= base_url($controller.'/') ?>" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-x-circle me-2"></i>Batal
                    </a>
                </div>
            </div>
        </form>

        <!-- Skala Harga Section -->
        <div class="card shadow-sm" id="skalaHargaSection">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-currency-dollar me-2"></i>Skala Harga Product</h5>
            </div>
            <div class="card-body" id="skalaHargaContent">
                <?php $this->load->view('admin/product/add_harga/universal_pricing'); ?>
            </div>
        </div>
    </div>
</div>

<link href="<?= base_url('assets/select2/css/select2.min.css') ?>" rel="stylesheet" />
<script src="<?= base_url('assets/select2/js/select2.full.min.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
.spin {
    animation: spin 1s linear infinite;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
<script>
// Form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// Dynamic subkategori loading (if needed)
document.getElementById('kategori_id').addEventListener('change', function() {
    // Add AJAX call here if needed to load subkategori dynamically
});


// ==================== SKALA HARGA FUNCTIONS ====================

// Handle tambah harga baru dengan AJAX
$(document).on('submit', 'form[action*="addHarga"]', function(e) {
    e.preventDefault();
    
    const form = $(this);
    const formData = form.serialize();
    const productId = form.find('input[name="product_id"]').val();
    const submitBtn = form.find('button[type="submit"]');
    const originalText = submitBtn.html();
    
    // Disable button and show loading
    submitBtn.prop('disabled', true).html('<i class="bi bi-arrow-clockwise spin"></i> Menambahkan...');
    
    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: formData,
        dataType: 'json',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            if (response.success) {
                // Reload bagian skala harga setelah berhasil tambah
                reloadSkalaHarga(productId);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: response.message || 'Gagal menambahkan harga baru'
                });
                submitBtn.prop('disabled', false).html(originalText);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            // Try to parse response as JSON
            let errorMessage = 'Terjadi kesalahan saat menambahkan harga baru';
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.message) {
                    errorMessage = response.message;
                }
            } catch(e) {
                // If not JSON, try to reload anyway (might be redirect response)
                reloadSkalaHarga(productId);
                return;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: errorMessage
            });
            submitBtn.prop('disabled', false).html(originalText);
        }
    });
});

// Fungsi untuk reload bagian skala harga
function reloadSkalaHarga(productId) {
    if (!productId) {
        productId = '<?= $getproduct['product_id'] ?>';
    }
    
    // Show loading indicator
    $('#skalaHargaContent').html('<div class="text-center p-4"><i class="bi bi-arrow-clockwise spin"></i> Memuat data...</div>');
    
    $.ajax({
        url: '<?= base_url('manufaktur/edit-product/') ?>' + productId,
        type: 'GET',
        success: function(response) {
            // Extract bagian skala harga dari response HTML
            const tempDiv = $('<div>').html(response);
            const newSkalaHarga = tempDiv.find('#skalaHargaContent').html();
            
            if (newSkalaHarga && newSkalaHarga.trim() !== '') {
                $('#skalaHargaContent').html(newSkalaHarga);
                
                // Re-initialize format rupiah untuk input baru
                setTimeout(function() {
                    // Format rupiah untuk semua input harga dan HPP
                    $('.harga-field, .hpp-field').off('input').on('input', function () {
                        let value = $(this).val().replace(/[^\d]/g, '');
                        $(this).val(value.replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
                    });
                }, 100);
            } else {
                // Fallback: reload halaman jika tidak berhasil extract
                console.warn('Tidak dapat mengekstrak konten skala harga, melakukan reload halaman...');
                location.reload();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error reloading skala harga:', error);
            // Fallback: reload halaman
            location.reload();
        }
    });
}

// Handle hapus harga dengan AJAX
$(document).on('click', '.btn-hapus-harga', function(e) {
    e.preventDefault();
    const href = $(this).data('href');
    const productId = '<?= $getproduct['product_id'] ?>';
    
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data harga ini tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: href,
                type: 'GET',
                success: function() {
                    // Reload bagian skala harga setelah hapus
                    reloadSkalaHarga(productId);
                },
                error: function() {
                    // Fallback: reload halaman
                    location.reload();
                }
            });
        }
    });
});
</script>
