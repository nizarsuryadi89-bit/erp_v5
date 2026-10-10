<?php
// Ensure $satuan is always defined
if (!isset($satuan) || !is_array($satuan)) {
    $satuan = array();
}
?>
<!-- Modal Tambah Material -->
<div class="modal fade" id="modalTambahMaterial" tabindex="-1" aria-labelledby="modalTambahMaterialLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahMaterialLabel">
                    <i class="fa fa-plus me-2"></i>Tambah Material Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup modal tambah material"></button>
            </div>
            <form id="formTambahMaterial">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="bahan_nama_display" class="form-label">Bahan Baku</label>
                                <input type="text" class="form-control" id="bahan_nama_display" readonly>
                                <input type="hidden" id="bahan_id" name="bahan_id">
                            </div>
                            <div class="mb-3">
                                <label for="varian" class="form-label">Varian <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="varian" name="varian" required>
                            </div>
                            <div class="mb-3">
                                <label for="barcode" class="form-label">Barcode</label>
                                <input type="text" class="form-control" id="barcode" name="barcode">
                            </div>
                            <div class="mb-3">
                                <label for="satuan_id" class="form-label">Satuan <span class="text-danger">*</span></label>
                                <select class="form-select" id="satuan_id" name="satuan_id" required>
                                    <option value="">Pilih Satuan</option>
                                    <?php if(isset($satuan) && is_array($satuan) && !empty($satuan)): foreach($satuan as $s): ?>
                                        <option value="<?= isset($s->satuan_id) ? $s->satuan_id : '' ?>"><?= isset($s->satuan_nama) ? htmlspecialchars($s->satuan_nama) : '' ?></option>
                                    <?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jenis_barang" class="form-label">Jenis Barang</label>
                                <select class="form-select" id="jenis_barang" name="jenis_barang">
                                    <option value="bahan_baku">Bahan Baku</option>
                                    <option value="operasional">Operasional</option>
                                    <option value="bahan_operasional">Bahan Operasional</option>
                                    <option value="barang_jadi">Barang Jadi</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="harga_beli" class="form-label">Harga Beli</label>
                                <input type="number" class="form-control" id="harga_beli" name="harga_beli" min="0" step="1000">
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i>Simpan Material
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Material -->
<div class="modal fade" id="modalEditMaterial" tabindex="-1" aria-labelledby="modalEditMaterialLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditMaterialLabel">
                    <i class="fa fa-edit me-2"></i>Edit Material
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup modal edit material"></button>
            </div>
            <form id="formEditMaterial">
                <div class="modal-body">
                    <input type="hidden" id="edit_material_id" name="material_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_varian" class="form-label">Varian <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_varian" name="varian" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_barcode" class="form-label">Barcode</label>
                                <input type="text" class="form-control" id="edit_barcode" name="barcode">
                            </div>
                            <div class="mb-3">
                                <label for="edit_satuan_id" class="form-label">Satuan <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_satuan_id" name="satuan_id" required>
                                    <option value="">Pilih Satuan</option>
                                    <?php if(isset($satuan) && is_array($satuan) && !empty($satuan)): foreach($satuan as $s): ?>
                                        <option value="<?= isset($s->satuan_id) ? $s->satuan_id : '' ?>"><?= isset($s->satuan_nama) ? htmlspecialchars($s->satuan_nama) : '' ?></option>
                                    <?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_harga_beli" class="form-label">Harga Beli</label>
                                <input type="number" class="form-control" id="edit_harga_beli" name="harga_beli" min="0" step="1000">
                            </div>
                            <div class="mb-3">
                                <label for="edit_status" class="form-label">Status</label>
                                <select class="form-select" id="edit_status" name="status">
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit_keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fa fa-save me-1"></i>Update Material
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Form Tambah Material
    $('#formTambahMaterial').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-1"></i>Menyimpan...');
        
        fetch('<?= base_url('warehouse/actionAddMaterial') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    title: 'Berhasil!',
                    text: data.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message,
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Terjadi kesalahan saat menyimpan material.',
                icon: 'error'
            });
        })
        .finally(() => {
            submitBtn.prop('disabled', false).html(originalText);
        });
    });

    // Form Edit Material
    $('#formEditMaterial').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-1"></i>Mengupdate...');
        
        fetch('<?= base_url('warehouse/actionEditMaterial') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    title: 'Berhasil!',
                    text: data.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message,
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Terjadi kesalahan saat mengupdate material.',
                icon: 'error'
            });
        })
        .finally(() => {
            submitBtn.prop('disabled', false).html(originalText);
        });
    });

    // Focus management for modals
    $('#modalTambahMaterial').on('shown.bs.modal', function() {
        $('#varian').focus();
    });
    $('#modalEditMaterial').on('shown.bs.modal', function() {
        $('#edit_varian').focus();
    });

    // Remove focus from close button to prevent accessibility issues when modal closes
    $('#modalTambahMaterial').on('hidden.bs.modal', function() {
        $('#formTambahMaterial')[0].reset();
        $('.btn-close').blur();
    });
    $('#modalEditMaterial').on('hidden.bs.modal', function() {
        $('.btn-close').blur();
    });
});
</script>
