<!-- Modal Edit BOM -->
<div class="modal fade" id="modalEditBOM" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 1.25rem;">
            <div class="modal-header border-0 bg-light p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="bi bi-pencil-square text-primary fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-slate-900">Edit Material BOM</h5>
                        <p class="text-muted small mb-0">Perbarui kuantitas dan faktor scrap</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBomForm">
                <div class="modal-body p-4">
                    <input type="hidden" name="bom_line_id" id="edit_bom_line_id">
                    <input type="hidden" name="harga_id" id="edit_harga_id_bom" value="">
                    <div class="mb-4 p-3 bg-slate-50 rounded-3 border">
                        <label class="form-label fw-bold small text-uppercase letter-spacing-1 text-muted">MATERIAL TARGET</label>
                        <select class="form-select select2-bahan shadow-none" id="edit_bahan_id" name="bahan_id" required style="width: 100%;">
                            <option value="">-- Cari Material --</option>
                        </select>
                    </div>
                    <!-- Kalkulator Kapasitas -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="p-3 bg-warning bg-opacity-10 rounded-3 border border-warning shadow-sm">
                                <label class="form-label fw-bold small text-dark mb-2"><i class="fas fa-calculator text-warning me-1"></i> INPUT KAPASITAS (1 Bahan Muat Berapa Pcs?)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white fw-bold">1 Bahan muat</span>
                                    <input type="number" id="edit_kapasitas_kalkulator" class="form-control border-start-0 border-end-0" placeholder="Ketik di sini... (Misal: 25)" required step="any" min="0">
                                    <span class="input-group-text bg-white fw-bold">Pcs Produk</span>
                                </div>
                                <div class="form-text text-muted mt-2" style="font-size: 0.75rem;">
                                    <i class="fas fa-magic text-warning me-1"></i> Ketik jumlah kapasitas, dan Kuantitas (Net) di bawah otomatis terisi.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">KUANTITAS (NET)</label>
                            <input type="number" name="qty" id="edit_qty" class="form-control bg-light" step="any" required autocomplete="off" readonly>
                            <div class="form-text mt-1 text-muted" style="font-size: 0.65rem; line-height: 1.2;">
                                <i class="fas fa-info-circle"></i> Otomatis dari kalkulator
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">SCRAP FACTOR (%)</label>
                            <input type="number" name="scrap_factor" id="edit_scrap_factor" class="form-control"
                                step="0.1" autocomplete="off">
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted">ESTIMASI BIAYA SATUAN (OPSIONAL)</label>
                            <input type="number" name="manual_cost" id="edit_manual_cost" class="form-control" step="0.01" autocomplete="off" placeholder="Cth: 1500 (kosongkan jika ada data beli)">
                            <div class="form-text mt-1" style="font-size: 0.65rem; line-height: 1.2;">
                                <i class="fas fa-info-circle text-info"></i> Gunakan ini khusus untuk Barang Operasional/Jasa (seperti Biaya Klik, Listrik) yang tidak memiliki histori pembelian.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>