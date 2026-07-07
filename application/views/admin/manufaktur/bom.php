<div class="app-content d-block">
    <div class="container-fluid py-3">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="panel-title"><?= $title ?> : <?= $getproduct['product_nama'] ?></h2>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <p>Jumlah Data : <?= $total_rows ?><br>Kebutuhan Bahan untuk membuat 1 product</p>
                    </div>
                    <div class="col-sm-6 text-end">
                        <a href="<?= base_url('manufaktur')?>" class="btn btn-primary">Home</a>
                        <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalInputData">
                            <i class="bi bi-plus-circle-fill"></i> Tambah BOM
                        </button>
                    </div>
                </div>

                <table class="table table-bordered table-striped mb-0" id="datatable-detail">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Product</th>
                            <th>Bahan</th>
                            <th>Qty</th>
                            <th>Satuan</th>
                            <th width="15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($tampilData)): ?>
                            <?php $no = 1; foreach ($tampilData as $dt): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $dt->product_nama ?></td>
                                    <td><?= $dt->bahan_nama ?></td>
                                    <td>
                                        <?= $dt->quantity ?><br>
                                        <?= $dt->panjang ?> x <?= $dt->lebar ?>
                                    </td>
                                    <td><?= $dt->satuan_nama ?></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditBOM" 
                                                data-bom-line-id="<?= $dt->bom_line_id ?>" 
                                                data-product="<?= $dt->product_nama ?>"
                                                data-bahan-id="<?= $dt->bahan_id ?>"
                                                data-quantity="<?= $dt->quantity ?>"
                                                data-panjang="<?= $dt->panjang ?>"
                                                data-lebar="<?= $dt->lebar ?>"
                                                data-satuan-id="<?= $dt->satuan_id ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalDeleteBOM" 
                                                data-id="<?= $dt->bom_line_id ?>" 
                                                data-product="<?= $dt->product_nama ?>"
                                                data-bahan="<?= $dt->bahan_nama ?>">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Tidak ada data BOM untuk product ini
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit BOM -->
<div class="modal fade" id="modalEditBOM" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="<?= base_url('manufaktur/aksi_update_bom') ?>" method="post" id="formEditBOM">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data BOM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="bom_line_id" id="edit_bom_line_id">
                    <input type="hidden" name="product_id" value="<?= $getproduct['product_id'] ?>">
                    
                    <div class="form-group mb-3">
                        <label>Nama Product</label>
                        <input type="text" class="form-control" id="edit_product_nama" readonly>
                    </div>

                    <div class="form-group mb-3">
                        <label>Pilih Bahan Utama</label>
                        <select name="bahan_id" id="edit_bahan_id" class="form-control" data-plugin-selectTw required>
                            <option value="">-- Pilih Bahan --</option>
                            <?php foreach ($bahan as $p): ?>
                                <option value="<?= $p->bahan_id ?>"><?= $p->bahan_nama ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label>Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" id="edit_quantity" class="form-control" required min="0" step="0.01">
                    </div>

                    <div class="form-group mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="edit_ukuran_enable" name="ukuran_enable" value="1">
                            <label class="form-check-label" for="edit_ukuran_enable">Membutuhkan Ukuran</label>
                        </div>
                    </div>

                    <div id="edit_ukuran_fields" style="display:none;">
                        <div class="form-group mb-3">
                            <label>Panjang (mm)</label>
                            <input type="number" name="panjang" id="edit_panjang" class="form-control" min="0" step="0.01">
                        </div>

                        <div class="form-group mb-3">
                            <label>Lebar (mm)</label>
                            <input type="number" name="lebar" id="edit_lebar" class="form-control" min="0" step="0.01">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label>Satuan</label>
                        <select name="satuan_id" id="edit_satuan_id" class="form-control" data-plugin-selectTw required>
                            <option value="">-- Pilih Satuan --</option>
                            <?php foreach ($satuan as $p): ?>
                                <option value="<?= $p->satuan_id ?>"><?= $p->satuan_nama ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Delete BOM -->
<div class="modal fade" id="modalDeleteBOM" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data BOM ini?</p>
                <div class="alert alert-warning">
                    <strong>Product:</strong> <span id="delete_product_nama"></span><br>
                    <strong>Bahan:</strong> <span id="delete_bahan_nama"></span>
                </div>
            </div>
            <div class="modal-footer">
                <form action="<?= base_url('manufaktur/aksi_delete_bom') ?>" method="post" style="display: inline;">
                    <input type="hidden" name="bom_line_id" id="delete_bom_line_id">
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>

<!-- Script untuk Modal -->
<script>
$(document).ready(function() {
    // Initialize select2 for add modal
    $("#modalInputData select[name='bahan_id']").select2({
        dropdownParent: $("#modalInputData"),
        width: '100%',
        placeholder: '-- Pilih Bahan --',
        allowClear: true,
        theme: 'bootstrap-5'
    });

    // Initialize select2 for edit modal
    $("#modalEditBOM select[name='bahan_id']").select2({
        dropdownParent: $("#modalEditBOM"),
        width: '100%',
        placeholder: '-- Pilih Bahan --',
        allowClear: true,
        theme: 'bootstrap-5'
    });

   

    // Fix focus on search field when opening select2
    $(document).on('select2:open', () => {
        document.querySelector('.select2-container--open .select2-search__field').focus();
    });

    // Modal Edit
    $('#modalEditBOM').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var bom_line_id = button.data('bom-line-id');
        var product_nama = button.data('product');
        var bahan_id = button.data('bahan-id');
        var quantity = button.data('quantity');
        var panjang = button.data('panjang');
        var lebar = button.data('lebar');
        var satuan_id = button.data('satuan-id');
        
        var modal = $(this);
        modal.find('#edit_bom_line_id').val(bom_line_id);
        modal.find('#edit_product_nama').val(product_nama);
        modal.find('#edit_bahan_id').val(bahan_id).trigger('change');
        modal.find('#edit_quantity').val(quantity);
        modal.find('#edit_panjang').val(panjang);
        modal.find('#edit_lebar').val(lebar);
        modal.find('#edit_satuan_id').val(satuan_id).trigger('change');

        // Check ukuran checkbox if panjang or lebar exists
        var hasUkuran = (panjang || lebar);
        modal.find('#edit_ukuran_enable').prop('checked', hasUkuran).trigger('change');
    });
    
    // Modal Delete
    $('#modalDeleteBOM').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var bom_line_id = button.data('id');
        var product_nama = button.data('product');
        var bahan_nama = button.data('bahan');
        
        var modal = $(this);
        modal.find('#delete_bom_line_id').val(bom_line_id);
        modal.find('#delete_product_nama').text(product_nama);
        modal.find('#delete_bahan_nama').text(bahan_nama);
    });

    // Add handler for ukuran checkbox
    $('#edit_ukuran_enable').change(function() {
        if($(this).is(':checked')) {
            $('#edit_ukuran_fields').show();
        } else {
            $('#edit_ukuran_fields').hide();
            $('#edit_panjang, #edit_lebar').val('');
        }
    });
    
    // Add form validation 
    $('#formEditBOM').on('submit', function(e) {
        var ukuranEnabled = $('#edit_ukuran_enable').is(':checked');
        if (ukuranEnabled) {
            var panjang = $('#edit_panjang').val();
            var lebar = $('#edit_lebar').val();
            if (!panjang || !lebar) {
                e.preventDefault();
                alert('Silakan isi ukuran panjang dan lebar');
                return false;
            }
        }
    });
});
</script>

<!-- Modal Tambah Data -->
<div class="modal fade" id="modalInputData" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="<?= base_url('manufaktur/aksi_simpan_bom') ?>" method="post">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalLabelLembur">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Data BOM - Material dan Proses
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <!-- Nama Product -->
                    <div class="form-group mb-3">
                        <label class="fw-bold">Nama Product</label>
                        <input type="text" class="form-control" value="<?= $getproduct['product_nama'] ?>" readonly>
                        <input type="hidden" name="product_id" class="form-control" value="<?= $getproduct['product_id'] ?>">
                    </div>

                    <!-- Pilih Bahan Utama -->
                    <div class="form-group mb-3">
                        <label class="fw-bold">Pilih Bahan Utama</label>
                        <select name="bahan_id" class="form-control select2" required>
                            <option value="">-- Cari & Pilih Bahan Utama --</option>
                            <?php foreach ($bahan as $p): ?>
                                <option value="<?= $p->bahan_id ?>"><?= $p->bahan_nama ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Pilih bahan yang akan digunakan dalam produk ini</div>
                    </div>

                    <!-- Quantity -->
                    <div class="form-group mb-3">
                        <label class="fw-bold">Quantity</label>
                        <input type="number" name="quantity" class="form-control" placeholder="Masukkan jumlah bahan" required>
                        <div class="form-text">Jumlah bahan yang dibutuhkan untuk produksi</div>
                    </div>

                  <div class="form-group mb-3">
                        <label class="fw-bold">
                            <input type="checkbox" id="showDimensions" class="form-check-input"> Apakah membutuhkan panjang dan lebar?
                        </label>
                    </div>

                    <div class="row mb-3" id="dimensiFields" style="display: none;">
                        <!-- Panjang -->
                        <div class="col-md-6">
                            <label class="fw-bold">Panjang (cm)</label>
                            <input type="number" name="panjang" class="form-control" placeholder="Masukkan panjang bahan" min="0">
                            <div class="form-text">Panjang bahan dalam satuan cm</div>
                        </div>

                        <!-- Lebar -->
                        <div class="col-md-6">
                            <label class="fw-bold">Lebar (cm)</label>
                            <input type="number" name="lebar" class="form-control" placeholder="Masukkan lebar bahan" min="0">
                            <div class="form-text">Lebar bahan dalam satuan cm</div>
                        </div>
                    </div>

                    <script>
                        // Menyembunyikan / Menampilkan Panjang dan Lebar berdasarkan checkbox
                        document.getElementById('showDimensions').addEventListener('change', function () {
                            const dimensiFields = document.getElementById('dimensiFields');
                            // Tampilkan atau sembunyikan berdasarkan apakah checkbox dicentang
                            if (this.checked) {
                                dimensiFields.style.display = 'block';
                            } else {
                                dimensiFields.style.display = 'none';
                            }
                        });
                    </script>



                    <!-- Satuan -->
                    <div class="form-group mb-3">
                        <label class="fw-bold">Satuan</label>
                        <select name="satuan_id" class="form-control" required>
                            <?php foreach ($satuan as $p): ?>
                                <option value="<?= $p->satuan_id ?>"><?= $p->satuan_nama ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Pilih satuan yang sesuai dengan bahan ini</div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>Simpan
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Batal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $('#modalInputData').on('hidden.bs.modal', function () {
        // Reset select2 saat modal ditutup
        $('.select2').val('').trigger('change');
    });

    $(document).ready(function() {
        // Add handler for ukuran checkbox in add modal
        $('#add_ukuran_enable').change(function() {
            if($(this).is(':checked')) {
                $('#add_ukuran_fields').show();
            } else {
                $('#add_ukuran_fields').hide();
                $('#add_panjang, #add_lebar').val('');
            }
        });

        // Add form validation for add modal
        $('form[action*="aksi_simpan_bom"]').on('submit', function(e) {
            var ukuranEnabled = $('#add_ukuran_enable').is(':checked');
            if (ukuranEnabled) {
                var panjang = $('#add_panjang').val();
                var lebar = $('#add_lebar').val();
                if (!panjang || !lebar) {
                    e.preventDefault();
                    alert('Silakan isi ukuran panjang dan lebar');
                    return false;
                }
            }
        });
    });
</script>




