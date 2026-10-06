<div class="modal fade" id="modalBulkSetup" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-layers-half me-2"></i>Bulk Apply Template Manufaktur
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info border-0 shadow-sm mb-4">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Gunakan fitur ini untuk menerapkan konfigurasi <strong>BOM (Material)</strong> atau <strong>Routing
                        (Langkah Kerja)</strong> dari satu produk template ke banyak produk target sekaligus.
                </div>

                <form id="bulkSetupForm">
                    <div class="row g-4">
                        <!-- Step 1: Select Template -->
                        <div class="col-md-12">
                            <label class="form-label fw-bold"><span class="badge bg-dark me-2">1</span>Pilih Produk
                                Template (Sumber)</label>
                            <select class="form-select select2-bulk-template" name="template_id" id="bulk_template_id"
                                required>
                                <option value="">-- Cari Produk Template --</option>
                            </select>
                            <div class="form-text">Data BOM & Routing dari produk ini akan disalin.</div>
                        </div>

                        <!-- Step 2: Select Targets -->
                        <div class="col-md-12">
                            <label class="form-label fw-bold"><span class="badge bg-dark me-2">2</span>Pilih Target
                                Produk</label>
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="small text-muted fw-bold">FILTER SUBKATEGORI</label>
                                            <select class="form-select" id="bulk_target_subkategori">
                                                <option value="">-- Semua Subkategori --</option>
                                                <?php foreach ($subkategori as $sk): ?>
                                                    <option value="<?= $sk->subkategori_id ?>"><?= $sk->subkategori_nama ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="small text-muted fw-bold">PRODUK TARGET</label>
                                            <select class="form-select select2-bulk-targets" name="target_ids[]"
                                                id="bulk_target_ids" multiple required>
                                            </select>
                                            <div class="mt-2 d-flex gap-2">
                                                <button type="button" class="btn btn-xs btn-outline-primary"
                                                    onclick="bulkSelectAllTargets()">Pilih Semua di Subkategori
                                                    Ini</button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary"
                                                    onclick="bulkClearTargets()">Hapus Semua</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Options -->
                        <div class="col-md-12">
                            <label class="form-label fw-bold"><span class="badge bg-dark me-2">3</span>Opsi
                                Penyalinan</label>
                            <div class="d-flex gap-4 p-3 bg-light rounded shadow-sm border">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="options[]" value="bom"
                                        id="opt_bom" checked>
                                    <label class="form-check-label fw-bold" for="opt_bom">Salin BOM (Bahan Baku)</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="options[]" value="routing"
                                        id="opt_routing" checked>
                                    <label class="form-check-label fw-bold" for="opt_routing">Salin Routing (Langkah
                                        Kerja)</label>
                                </div>
                            </div>
                            <div class="mt-2 text-danger small">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                Perhatian: Data BOM/Routing yang sudah ada pada produk target akan
                                <strong>DIHAPUS</strong> dan diganti dengan data dari template.
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light border-0 p-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm"
                    onclick="submitBulkSetup()">
                    <i class="bi bi-lightning-fill me-1"></i> Terapkan Template
                </button>
            </div>
        </div>
    </div>
</div>