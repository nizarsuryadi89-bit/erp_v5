<!-- Modal Routing -->
<div class="modal fade" id="modalRouting" tabindex="-1" aria-labelledby="modalRoutingLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
            <div class="modal-header border-0 bg-light p-4" style="border-radius: 24px 24px 0 0;">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="bi bi-diagram-3 text-success fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-slate-900" id="modalRoutingLabel">Routing & Alur Kerja</h5>
                        <p class="text-muted small mb-0">Setup tenaga kerja untuk: <span class="fw-bold text-success"
                                id="routingProductNameHeader">-</span></p>
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
                    <div class="mini-step completed">
                        <div class="step-num">2</div>
                        <div class="step-text">Resep BOM</div>
                    </div>
                    <div class="step-line"></div>
                    <div class="mini-step active">
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
                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-list-task me-2 text-success"></i>Daftar Proses
                        Produksi</h6>
                    <div class="px-3 py-1 rounded-pill bg-success bg-opacity-10 text-success small fw-bold">
                        Total: <span id="routingTotalSteps">0</span> Langkah
                    </div>
                </div>

                <!-- Routing Data Table -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="bg-success p-1 rounded-2"
                                style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-list-ul text-white small"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-0">List Alur Produksi Aktif</h6>
                        </div>
                        <div class="table-responsive rounded-3 overflow-hidden border">
                            <table class="table table-hover mb-0">
                                <thead style="background: #f8fafc; border-bottom: 2px solid #059669;">
                                    <tr class="text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                        <th width="8%" class="py-3 px-4">Urutan</th>
                                        <th width="30%" class="py-3">Proses / Langkah Kerja</th>
                                        <th width="20%" class="py-3 text-center">Work Center</th>
                                        <th width="15%" class="py-3 text-center">Waktu (Min)</th>
                                        <th width="15%" class="py-3 text-end pe-4">Upah / Unit</th>
                                        <th width="12%" class="py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="routingTableBody" style="font-size: 0.85rem;">
                                    <!-- Dynamic content -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Add Routing Form -->
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm"
                            style="background: #f0fdf4; border: 1px dashed #059669 !important; border-radius: 16px;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center gap-2 mb-4">
                                    <i class="bi bi-plus-circle-fill text-success fs-5"></i>
                                    <h6 class="fw-bold text-dark mb-0">Tambah Step Baru ke Alur</h6>
                                </div>
                                <form id="addRoutingForm">
                                    <input type="hidden" id="routingProductIdInput" name="product_id">
                                    <div class="row g-3">
                                        <div class="col-md-2">
                                            <label class="form-label fw-bold text-muted" style="font-size: 0.8rem;">SEQ.
                                                <i class="bi bi-info-circle ms-1" data-bs-toggle="tooltip"
                                                    title="Urutan angka (10, 20, dst)"></i></label>
                                            <input type="number" class="form-control form-control-premium"
                                                id="routing_urutan" name="routing_urutan" min="1" placeholder="10"
                                                required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold text-muted" style="font-size: 0.8rem;">NAMA
                                                PROSES <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-premium"
                                                id="routing_name" name="routing_name"
                                                placeholder="Contoh: Cetak / Laminasi" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold text-muted" style="font-size: 0.8rem;">DEPT
                                                / OPERATOR</label>
                                            <select class="form-select select2-operator" id="operator_id"
                                                name="operator_id" required></select>
                                        </div>
                                        <div class="col-md-2 d-none">
                                            <label class="form-label fw-bold text-muted" style="font-size: 0.8rem;">TIPE
                                                TAHAPAN</label>
                                            <select class="form-select" id="is_finishing" name="is_finishing" required>
                                                <option value="0">Cetak</option>
                                                <option value="1">Finishing</option>
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <label class="form-label fw-bold text-muted" style="font-size: 0.8rem;"
                                                title="Estimasi Waktu (Menit)">WAKTU</label>
                                            <input type="number" class="form-control form-control-premium text-center"
                                                name="est_time" value="0">
                                        </div>
                                        <div class="col-md-1">
                                            <label class="form-label fw-bold text-muted" style="font-size: 0.8rem;"
                                                title="Upah Tukang (Rp)">UPAH</label>
                                            <input type="number" class="form-control form-control-premium text-end"
                                                name="labor_cost" value="0">
                                        </div>
                                        <div class="col-md-9 pt-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-link-45deg fs-5 text-muted"></i>
                                                <select class="form-select select2-bom border-0 bg-transparent p-0"
                                                    id="bom_line_id" name="bom_line_id" required
                                                    style="font-size: 0.8rem; font-weight: 600;">
                                                    <option value="99999">-- Umum (Tanpa Varian Material Khusus) --
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 pt-2 text-end">
                                            <button type="submit"
                                                class="btn btn-success w-100 py-2 rounded-pill shadow-sm">
                                                <i class="bi bi-plus-lg me-2"></i>TAMBAH STEP
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
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