<!-- Modal Input Data -->
<div class="modal fade" id="modalInputData" tabindex="-1" aria-labelledby="modalInputDataLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="post" action="<?= base_url('manufaktur/actionAdd') ?>" class="needs-validation" novalidate>
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalInputDataLabel">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Product Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">


                    <!-- Kategori & Sub Kategori -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori_id" id="kategori_id" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($kategori as $k): ?>
                                    <option value="<?= $k->kategori_id ?>"><?= $k->kategori_nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Sub Kategori <span class="text-danger">*</span></label>
                            <select name="subkategori_id" class="form-select subkategori" required>
                                <option value="">-- Pilih Sub Kategori --</option>
                                <?php foreach ($subkategori as $k): ?>
                                    <option value="<?= $k->subkategori_id ?>"><?= $k->subkategori_nama ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Nama Produk, Tipe, Satuan -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" name="product_nama" class="form-control" required
                                placeholder="Masukkan nama product">

                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Jenis <span class="text-danger">*</span></label>
                            <select name="tipe_id" class="form-select" required>
                                <option value="">-- Pilih Jenis --</option>
                                <?php foreach ($tipe as $k): ?>
                                    <option value="<?= $k->tipe_id ?>"><?= $k->tipe_nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Satuan <span class="text-danger">*</span></label>
                            <select name="satuan_id" class="form-select" required>
                                <option value="">-- Pilih Satuan --</option>
                                <?php foreach ($satuan as $k): ?>
                                    <option value="<?= $k->satuan_id ?>" <?= isset($getproduct['satuan_id']) && $k->satuan_id == $getproduct['satuan_id'] ? 'selected' : '' ?>>
                                        <?= $k->satuan_nama ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Bahan Baku -->


                    <!-- Pilihan Sisi -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Pilihan Metode Penjualan</label>
                            <select name="metode_id" class="form-select">
                                <option value="">-- Pilihan Metode Penjualan --</option>
                                <?php foreach ($metode as $k): ?>
                                    <option value="<?= $k->metode_id ?>" <?= isset($getproduct['metode_id']) && $k->metode_id == $getproduct['metode_id'] ? 'selected' : '' ?>>
                                        <?= $k->metode_nama ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi Product</label>
                        <textarea name="product_deskripsi" class="form-control product-description" rows="4"
                            placeholder="Masukkan deskripsi lengkap product..."><?= htmlspecialchars($getproduct['product_deskripsi'] ?? '') ?></textarea>
                        <div class="form-text">Deskripsi detail tentang product (spesifikasi, kegunaan, dll)
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Simpan Product
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Batal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>