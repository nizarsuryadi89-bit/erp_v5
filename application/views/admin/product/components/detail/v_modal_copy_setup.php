<!-- Modal Copy BOM & Routing -->
<div class="modal fade" id="modalCopySetup" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 1.25rem;">
            <div class="modal-header border-0 bg-light p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="bi bi-files text-info fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-slate-900">Salin Setup ke Variasi Lain</h5>
                        <p class="text-muted small mb-0">Terapkan BOM & Routing dari satu variasi ke variasi lainnya dengan cepat.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="copySetupForm">
                <div class="modal-body p-4">
                    <div class="alert alert-warning small mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Perhatian:</strong> BOM dan Routing pada variasi tujuan yang Anda centang akan dihapus dan diganti dengan konfigurasi yang baru dari variasi sumber.
                    </div>
                    
                    <div class="mb-4 p-3 bg-slate-50 rounded-3 border">
                        <label class="form-label fw-bold small text-primary">COPY DARI SUMBER INI:</label>
                        <select class="form-select shadow-none" name="source_harga_id" id="copy_source_harga_id" required>
                            <option value="">-- Semua Variasi (Global) --</option>
                            <?php foreach ($variations as $var): ?>
                                <option value="<?= $var->harga_id ?>">
                                    <?= $var->display_name ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">PASTE KE VARIASI BERIKUT:</label>
                        <div class="d-flex justify-content-between mb-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="$('.copy-target-checkbox').prop('checked', true)">Pilih Semua</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="$('.copy-target-checkbox').prop('checked', false)">Batalkan Semua</button>
                        </div>
                        <div class="border rounded-3 p-3 overflow-auto" style="max-height: 250px; background: #fff;">
                            <div class="form-check mb-2 pb-2 border-bottom">
                                <input class="form-check-input copy-target-checkbox" type="checkbox" name="target_harga_ids[]" value="" id="target_global">
                                <label class="form-check-label" for="target_global">
                                    -- Semua Variasi (Global) --
                                </label>
                            </div>
                            <?php foreach ($variations as $var): ?>
                                <div class="form-check mb-2 pb-2 border-bottom">
                                    <input class="form-check-input copy-target-checkbox" type="checkbox" name="target_harga_ids[]" value="<?= $var->harga_id ?>" id="target_<?= $var->harga_id ?>">
                                    <label class="form-check-label" for="target_<?= $var->harga_id ?>">
                                        <?= $var->display_name ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 text-end">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-white rounded-pill px-4 fw-bold shadow-sm">Terapkan Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>