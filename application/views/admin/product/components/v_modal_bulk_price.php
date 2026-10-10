<!-- Modal Bulk Price Update -->
<div class="modal fade" id="modalBulkPriceUpdate" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-dark border-0">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-coins me-2"></i>Bulk Price Adjuster
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-warning border-0 shadow-sm mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Perhatian:</strong> Fitur ini akan mengubah harga pada banyak produk sekaligus. Pastikan
                    filter sudah benar.
                </div>

                <form id="bulkPriceForm">
                    <div class="mb-3">
                        <label class="form-label fw-bold">1. Pilih Kategori Target</label>
                        <select class="form-select select2-bulk-price" name="category_id" id="bulk_price_category"
                            required>
                            <option value="all">-- Semua Kategori --</option>
                            <?php foreach ($kategori as $kat): ?>
                                <option value="<?= $kat->kategori_id ?>"><?= $kat->kategori_nama ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">2. Metode Penyesuaian</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <div
                                    class="form-check card p-3 border-light-subtle shadow-sm hover-scale transition-all cursor-pointer">
                                    <input class="form-check-input" type="radio" name="adj_method" id="method_pct"
                                        value="percent" checked>
                                    <label class="form-check-label fw-bold d-block" for="method_pct">
                                        <i class="fas fa-percent me-1 text-primary"></i> Persentase
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div
                                    class="form-check card p-3 border-light-subtle shadow-sm hover-scale transition-all cursor-pointer">
                                    <input class="form-check-input" type="radio" name="adj_method" id="method_flat"
                                        value="flat">
                                    <label class="form-check-label fw-bold d-block" for="method_flat">
                                        <i class="fas fa-money-bill-wave me-1 text-success"></i> Nominal Flat
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">3. Nilai Kenaikan (Gunakan tanda minus untuk turun)</label>
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text bg-white border-end-0" id="adj_symbol">%</span>
                            <input type="number" class="form-control border-start-0 ps-0 fw-bold text-primary"
                                name="adj_value" placeholder="Contoh: 10" required>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">4. Target Perubahan</label>
                        <div class="d-flex gap-3 p-3 bg-light rounded border border-light-subtle shadow-sm">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="targets[]" value="harga_1"
                                    id="target_jual" checked>
                                <label class="form-check-label fw-bold" for="target_jual">Harga Jual</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="targets[]" value="hpp"
                                    id="target_hpp">
                                <label class="form-check-label fw-bold" for="target_hpp">HPP (Modal)</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light border-0 p-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm"
                    onclick="submitBulkPriceUpdate()">
                    <i class="fas fa-save me-1"></i> Update Sekarang
                </button>
            </div>
        </div>
    </div>
</div>