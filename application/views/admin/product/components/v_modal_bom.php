<!-- Modal BOM -->
<div class="modal fade" id="modalBom" tabindex="-1" aria-labelledby="modalBomLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
            <div class="modal-header border-0 bg-light p-4" style="border-radius: 24px 24px 0 0;">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="bi bi-gear-wide-connected text-warning fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-slate-900" id="modalBomLabel">BOM & Resep Bahan</h5>
                        <p class="text-muted small mb-0">Setup material untuk: <span class="fw-bold text-warning"
                                id="bomProductNameHeader">-</span></p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <!-- Step Tracker -->
                <div class="mini-sop-tracker mb-4 mt-3">
                    <div class="mini-step completed">
                        <div class="step-num">0</div>
                        <div class="step-text">Config Form</div>
                    </div>
                    <div class="step-line"></div>
                    <div class="mini-step completed">
                        <div class="step-num">1</div>
                        <div class="step-text">Identitas</div>
                    </div>
                    <div class="step-line"></div>
                    <div class="mini-step active">
                        <div class="step-num">2</div>
                        <div class="step-text">Resep BOM</div>
                    </div>
                    <div class="step-line"></div>
                    <div class="mini-step">
                        <div class="step-num">3</div>
                        <div class="step-text">Alur Kerja</div>
                    </div>
                    <div class="step-line"></div>
                    <div class="mini-step">
                        <div class="step-num">4</div>
                        <div class="step-text">Matriks Harga</div>
                    </div>
                </div>

                <!-- Stats Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-flask me-2 text-warning"></i>Daftar Bahan Baku /
                        Komponen</h6>
                </div>

                <!-- BOM Data Table -->
                <div class="table-responsive rounded-3 overflow-hidden border mb-4">
                    <table class="table table-hover mb-0" id="bomTable">
                        <thead style="background: #f8fafc; border-bottom: 2px solid #1e293b;">
                            <tr class="text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <th width="5%" class="py-3 px-4">No</th>
                                <th width="35%" class="py-3">Bahan Baku / Komponen (Varian)</th>
                                <th width="12%" class="py-3 text-center"
                                    title="Kebutuhan murni bahan per 1 unit produk">Kebutuhan Bersih</th>
                                <th width="10%" class="py-3 text-center"
                                    title="Persentase cadangan/toleransi kerusakan">Limbah %</th>
                                <th width="15%" class="py-3 text-center"
                                    title="Total bahan yang dibutuhkan setelah limbah">Total Kebutuhan</th>
                                <th width="15%" class="py-3">Dimensi (P x L)</th>
                                <th width="8%" class="py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="bomTableBody" style="font-size: 0.85rem;">
                            <!-- Dynamic content -->
                        </tbody>
                    </table>
                </div>

                <!-- Add BOM Form -->
                <div class="card border-0 shadow-sm"
                    style="background: #f8fafc; border: 1px dashed #1e293b !important; border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-2 mb-4">
                            <i class="bi bi-patch-plus-fill text-primary fs-5"></i>
                            <h6 class="fw-bold text-dark mb-0">Tambah Bahan Baku ke Resep (BOM)</h6>
                        </div>
                        <form id="addBomForm">
                            <input type="hidden" id="bomProductIdInput" name="product_id">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold small text-primary mb-1"
                                        style="font-size: 0.82rem;">TERAPKAN KE BANYAK VARIASI (MULTI-SELECT)</label>
                                    <select class="form-select shadow-none form-control-premium select2-multiple" id="bomVariasiSelect" name="harga_id[]" multiple="multiple" data-placeholder="-- Biarkan kosong untuk Semua Variasi (Global) --">
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="bahan_id" class="form-label fw-bold text-muted"
                                        style="font-size: 0.82rem;">MATERIAL / VARIAN <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select select2-bahan" id="bahan_id" name="bahan_id"
                                        required></select>
                                </div>
                                <div class="col-md-2">
                                    <label for="qty" class="form-label fw-bold text-muted" style="font-size: 0.82rem;"
                                        title="Kebutuhan bahan baku untuk membuat 1 pcs/unit produk jadi">KEBUTUHAN PER
                                        PCS <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control form-control-premium text-center" id="qty"
                                        name="qty" step="0.0001" min="0" required>
                                </div>
                                <div class="col-md-2">
                                    <label for="scrap_factor" class="form-label fw-bold text-muted"
                                        style="font-size: 0.82rem;"
                                        title="Persentase toleransi bahan rusak/terbuang">TOLERANSI LIMBAH %</label>
                                    <input type="number" class="form-control form-control-premium text-center"
                                        id="scrap_factor" name="scrap_factor" value="0" min="0" max="100">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold text-muted" style="font-size: 0.82rem;"
                                        title="Metode perhitungan konsumsi bahan baku">TIPE KALKULASI</label>
                                    <select class="form-select form-control-premium text-center" name="calc_type"
                                        id="calc_type" required style="font-weight: 500;">
                                        <option value="fixed" selected>1. Fixed</option>
                                        <option value="area">2. Area (P x L)</option>
                                        <option value="length">3. Length (P)</option>
                                        <option value="weight">4. Weight</option>
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-dark w-100 py-2 rounded-pill shadow-sm">
                                        <i class="bi bi-check-circle me-2"></i>SIMPAN
                                    </button>
                                </div>
                                <div class="col-12 mt-3">
                                    <textarea class="form-control form-control-premium border-0" name="keterangan"
                                        rows="1" placeholder="Catatan internal untuk operator produksi (opsional)..."
                                        style="font-size: 0.8rem; background: rgba(255,255,255,0.5);"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>