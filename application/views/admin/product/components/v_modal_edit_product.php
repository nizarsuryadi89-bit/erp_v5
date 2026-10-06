<!-- Modal Edit Product -->
<div class="modal fade" id="modalEditProduct" tabindex="-1" aria-labelledby="modalEditProductLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="editProductForm" class="needs-validation" novalidate>
            <input type="hidden" name="product_id" id="edit_product_id">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalEditProductLabel">
                        <i class="bi bi-pencil-square me-2"></i>Edit Product: <span id="edit_product_name_title"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Kategori & Sub Kategori -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori_id" id="edit_kategori_id" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($kategori as $k): ?>
                                    <option value="<?= $k->kategori_id ?>"><?= $k->kategori_nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Sub Kategori <span class="text-danger">*</span></label>
                            <select name="subkategori_id" id="edit_subkategori_id" class="form-select subkategori"
                                required>
                                <option value="">-- Pilih Sub Kategori --</option>
                                <?php foreach ($subkategori as $k): ?>
                                    <option value="<?= $k->subkategori_id ?>"><?= $k->subkategori_nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Nama Produk, Tipe, Satuan -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" name="product_nama" id="edit_product_nama" class="form-control" required
                                placeholder="Masukkan nama product">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Jenis <span class="text-danger">*</span></label>
                            <select name="tipe_id" id="edit_tipe_id" class="form-select" required>
                                <option value="">-- Pilih Jenis --</option>
                                <?php foreach ($tipe as $k): ?>
                                    <option value="<?= $k->tipe_id ?>"><?= $k->tipe_nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Satuan <span class="text-danger">*</span></label>
                            <select name="satuan_id" id="edit_satuan_id" class="form-select" required>
                                <option value="">-- Pilih Satuan --</option>
                                <?php foreach ($satuan as $k): ?>
                                    <option value="<?= $k->satuan_id ?>"><?= $k->satuan_nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Pilihan Sisi & Metode -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Pilihan Sisi</label>
                            <select name="side_id" id="edit_side_id" class="form-select">
                                <option value="">-- Pilihan Sisi --</option>
                                <?php foreach ($sisi as $k): ?>
                                    <option value="<?= $k->side_id ?>"><?= $k->side_name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Pilihan Metode Penjualan</label>
                            <select name="metode_id" id="edit_metode_id" class="form-select">
                                <option value="">-- Pilihan Metode Penjualan --</option>
                                <?php foreach ($metode as $k): ?>
                                    <option value="<?= $k->metode_id ?>"><?= $k->metode_nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi Product</label>
                        <textarea name="product_deskripsi" id="edit_product_deskripsi" class="form-control" rows="4"
                            placeholder="Masukkan deskripsi lengkap product..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Perbarui Produk
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Batal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>