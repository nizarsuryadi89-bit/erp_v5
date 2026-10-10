<!-- Modal Edit Routing -->
<div class="modal fade" id="modalEditRouting" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 1.25rem;">
            <div class="modal-header border-0 bg-light p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="bi bi-diagram-3-fill text-warning fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-slate-900">Ubah Langkah Kerja</h5>
                        <p class="text-muted small mb-0">Sesuaikan urutan dan biaya operasional</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="editRoutingForm">
                <div class="modal-body p-4">
                    <input type="hidden" name="routing_id" id="edit_routing_id">
                    <input type="hidden" name="harga_id" id="edit_harga_id_routing" value="">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted">SEQUENCE</label>
                            <input type="number" name="routing_urutan" id="edit_routing_urutan"
                                class="form-control bg-light fw-bold" required autocomplete="off">
                        </div>
                        <div class="col-md-9">
                            <label class="form-label fw-bold small text-muted">NAMA OPERASI / PROSES</label>
                            <input type="text" name="routing_name" id="edit_routing_name" class="form-control" required autocomplete="off">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">WORK CENTER / DEPT</label>
                            <select name="operator_id" id="edit_operator_id" class="form-select" required></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">LINK MATERIAL BOM</label>
                            <select name="bom_line_id" id="edit_bom_line_id_routing" class="form-select"></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">EST. WAKTU (MENIT)</label>
                            <input type="number" name="est_time" id="edit_est_time" class="form-control" autocomplete="off">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">BIAYA KERJA (RP)</label>
                            <input type="text" name="labor_cost" id="edit_labor_cost" class="form-control rupiah-input" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 text-end">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-white rounded-pill px-4 fw-bold shadow-sm">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>