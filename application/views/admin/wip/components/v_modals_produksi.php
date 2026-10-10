<?php
// Ensure $operators is defined or passed from controller/view
$operators = $operators ?? [];
?>

<!-- Modal Update Alur (Control Center Preview) -->
<div class="modal fade" id="modalProsesSPK" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content modal-premium shadow-lg border-0">
            <div class="modal-header text-white" style="background: var(--primary-gradient)">
                <h5 class="modal-title fw-900"><i class="fas fa-microchip me-2"></i> Control Center Alur Produksi -
                    #<span id="lblNospk"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="loadingProses" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div class="mt-2 fw-bold text-muted">Mengambil data items...</div>
                </div>
                <div id="contentProses" class="d-none">
                    <div class="table-responsive">
                        <table class="table premium-table align-middle mb-0">
                            <thead>
                                <tr class="bg-light">
                                    <th style="width: 30%; px-4">Item Produk</th>
                                    <th style="width: 20%;">BOM (Bahan Utama)</th>
                                    <th class="text-center" style="width: 10%;">Qty</th>
                                    <th class="text-center" style="width: 15%;">Status</th>
                                    <th class="text-center" style="width: 25%; px-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="listProdukRouting" class="border-top-0"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold"
                    data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Selesai SPK -->
<div class="modal fade" id="modalSelesaiSPK" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-premium shadow-lg">
            <div class="modal-header" style="background: var(--success-gradient)">
                <h5 class="modal-title fw-900 text-white"><i class="fas fa-check-circle me-2"></i> Finalisasi Produksi
                    SPK - #<span id="lblNospkSelesai"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="loadingSelesai" class="text-center py-5">
                    <div class="spinner-border text-success" role="status"></div>
                </div>
                <div id="contentSelesai" class="d-none">
                    <div class="text-center mb-4">
                        <h5 class="fw-800 text-dark mb-1">Konfirmasi Penyelesaian SPK</h5>
                        <p class="text-muted small">Harap verifikasi detail pengerjaan sebelum menyelesaikan seluruh
                            pesanan ini.</p>
                    </div>

                    <div class="table-responsive rounded-3 border mb-4">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="bg-light">
                                <tr style="font-size: 0.7rem;" class="text-uppercase fw-bold text-muted">
                                    <th class="ps-3 py-2">Nama Pesanan</th>
                                    <th class="py-2 text-center">Ukuran</th>
                                    <th class="py-2 text-center">Penggunaan Bahan</th>
                                    <th class="py-2 text-center">Penggunaan Roll</th>
                                    <th class="pe-3 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="listProdukSelesai" style="font-size: 0.85rem;">
                                <!-- Injected by JS -->
                            </tbody>
                        </table>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-7">
                            <div class="card bg-light border-0 rounded-4">
                                <div class="card-body p-4 text-center">
                                    <label class="form-label fw-bold text-success mb-2">PIN Verifikasi Operator</label>
                                    <input type="password"
                                        class="form-control form-control-lg premium-input border-success border-opacity-50 text-center fw-900"
                                        id="selesaiSpkPin" maxlength="6" placeholder="PIN 4-6 Digit"
                                        style="letter-spacing: 0.8rem; font-size: 1.8rem;" inputmode="numeric">
                                    <div class="form-text mt-2 small">Masukkan PIN Produksi Anda untuk verifikasi akhir.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top p-3 px-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-800"
                    data-bs-dismiss="modal">Batal</button>
                <button type="button" onclick="ProductionApp.submitSelesaiSPK()"
                    class="btn btn-success rounded-pill px-5 fw-900 shadow-sm transition-all">
                    Selesaikan Seluruh SPK <i class="fas fa-chevron-right ms-2"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Selesai Finishing (Quick Process) -->
<div class="modal fade" id="modalSelesaiFinishing" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content modal-premium shadow-lg">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%)">
                <h5 class="modal-title fw-900 text-white"><i class="fas fa-magic me-2"></i> Finalisasi Finishing</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <!-- Left Column: Input Form (PIN, Mesin side-by-side) -->
                    <div class="col-lg-7 col-md-12">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6" id="sectionFinMesin">
                                <div class="p-3 bg-light rounded-3 border border-primary border-opacity-10 h-100 shadow-sm d-flex flex-column justify-content-between">
                                    <label class="form-label fw-bold text-primary mb-2" style="font-size: 0.85rem;"><i class="fas fa-cog me-1"></i> Pilih Mesin Finishing <span class="badge bg-secondary ms-2" id="finMesinBadge">Opsional</span></label>
                                    <select class="form-select form-select-lg premium-input border-primary border-opacity-25" id="finMesinId">
                                        <option value="">-- Pilih Mesin --</option>
                                    </select>
                                    <div class="form-text small text-primary opacity-75 mt-2" style="font-size: 0.7rem;">Pilih mesin yang Anda gunakan untuk finishing.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 border border-primary border-opacity-10 h-100 shadow-sm d-flex flex-column justify-content-between">
                                    <label class="form-label fw-bold text-primary mb-2" style="font-size: 0.85rem;"><i class="fas fa-key me-1"></i> PIN Verifikasi Petugas</label>
                                    <input type="password" class="form-control form-control-lg premium-input text-center fw-bold border-primary border-opacity-25"
                                        id="finOperatorPin" placeholder="PIN 4-6 Digit" maxlength="6"
                                        style="letter-spacing: 0.5rem; font-size: 1.4rem;" inputmode="numeric">
                                    <div class="form-text small text-primary opacity-75 mt-2" style="font-size: 0.7rem;">Gunakan PIN Produksi Anda untuk verifikasi.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Item List -->
                    <div class="col-lg-5 col-md-12 border-start border-light ps-lg-4">
                        <p class="text-muted fw-bold mb-3" style="font-size: 0.85rem;"><i class="fas fa-list me-1"></i> Konfirmasi Hasil per Job:</p>
                        <div id="finishingItemList" class="d-flex flex-column gap-3 overflow-auto" style="max-height: 380px;">
                            <!-- Items populated via JS -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top p-3 px-4 d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-pill px-5 fw-900 shadow-sm" onclick="submitBatchFinishing()">
                    Simpan Perubahan <i class="fas fa-save ms-2"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Visual Nesting -->
<div class="modal fade" id="modalVisualizeNesting" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content modal-premium shadow-lg border-0 bg-dark text-white">
            <div class="modal-header border-bottom border-white border-opacity-10 py-3">
                <h5 class="modal-title fw-900 text-info"><i class="fas fa-project-diagram me-2"></i> Simulasi
                    Nesting (Preview Produksi)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-9">
                        <div class="bg-black rounded-4 p-4 border border-white border-opacity-10 shadow-inner"
                            style="min-height: 400px; position: relative; overflow-x: auto;">
                            <div id="nestingCanvas"
                                style="position: relative; background: #222; min-width: 100%; height: 500px; border: 2px dashed #444; border-radius: 8px;">
                                <!-- Visual items will be injected here -->
                            </div>
                        </div>
                        <div
                            class="mt-3 d-flex justify-content-between align-items-center bg-white bg-opacity-5 p-3 rounded-3">
                            <div class="nest-meta-item">
                                <div class="small text-muted mb-1 text-uppercase ls-1" style="font-size: 0.6rem;">
                                    Lebar Yang Diperlukan</div>
                                <div class="h5 mb-0 fw-900 text-info"><span id="visualReqWidth">0</span>m</div>
                            </div>
                            <div class="nest-meta-item text-center">
                                <div class="small text-muted mb-1 text-uppercase ls-1" style="font-size: 0.6rem;">
                                    Rekomendasi Roll Bahan</div>
                                <select class="form-select form-select-sm fw-900 text-warning bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-pill mx-auto mt-1" id="visualRollSuggestion" onchange="visualizeNesting(true)" style="max-width: 160px; text-align: center; cursor: pointer;"></select>
                            </div>
                            <div class="nest-meta-item text-center">
                                <div class="small text-muted mb-1 text-uppercase ls-1" style="font-size: 0.6rem;">
                                    Est. Meter Lari</div>
                                <div class="h5 mb-0 fw-900 text-info"><span id="visualRollHeight">0</span>m</div>
                            </div>
                            <div class="nest-meta-item text-end">
                                <div class="small text-muted mb-1 text-uppercase ls-1" style="font-size: 0.6rem;">
                                    Efisensi Bahan</div>
                                <div class="h5 mb-0 fw-bold text-success" id="visualEfficiency">0%</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-white bg-opacity-5 border-0 rounded-4 h-100">
                            <div class="card-body">
                                <h6 class="fw-800 text-uppercase ls-1 mb-4">Urutan Cetak</h6>
                                <div id="visualItemList" class="overflow-auto" style="max-height: 400px;">
                                    <!-- Item summary list -->
                                </div>
                                <hr class="border-white border-opacity-10 my-4">
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-primary rounded-pill fw-900"
                                        onclick="batchProcessNesting()">
                                        Setujui & Proses Ripping
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Selesai Nesting & Potong Stok -->
<div class="modal fade" id="modalNestingCompletion" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content modal-premium shadow-lg border-0">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-800"><i class="fas fa-warehouse me-2"></i> Update Stok & Selesai Batch</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formNestingCompletion">
                    <div class="row g-4">
                        <!-- Left Column: Input Fields split horizontally -->
                        <div class="col-lg-7 col-md-12">
                            <!-- Section 1: Usage Info & Input Usage side-by-side -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded-3 border h-100 shadow-sm">
                                        <label class="small fw-bold text-muted text-uppercase ls-1 mb-2 d-block">Informasi Pemakaian</label>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted small">Total Job:</span>
                                            <span class="fw-bold text-dark" id="compJobCount">0</span>
                                        </div>
                                        <!-- Actual Usage UI (Large Format only) -->
                                        <div class="d-flex justify-content-between mb-2 lf-only-stat">
                                            <span class="text-muted small">Estimasi Meter Lari:</span>
                                            <span class="h6 mb-0 fw-900 text-success"><span id="compTotalUsage">0</span> M</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center pt-2 border-top lf-only-stat">
                                            <span class="text-muted small text-uppercase" style="font-size: 0.65rem;">Rekomendasi Lebar:</span>
                                            <span class="badge bg-warning text-dark border border-warning border-opacity-25 rounded-pill px-2" id="compRecRoll">--</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6" id="actualUsageContainer">
                                    <div class="p-3 bg-light rounded-3 border h-100 shadow-sm d-flex flex-column justify-content-between">
                                        <label class="form-label fw-bold text-dark small text-uppercase mb-2" id="labelActualUsage">Jumlah Bahan Terpakai</label>
                                        <div class="input-group shadow-sm">
                                            <input type="number" step="0.01" class="form-control premium-input fw-bold fs-5 text-center py-2" id="compActualUsage" required>
                                            <span class="input-group-text fw-bold text-muted small" id="textActualUsage">UNIT</span>
                                        </div>
                                        <div class="form-text text-danger pt-1 mb-0" style="font-size: 0.65rem; line-height: 1.2;"><i class="fas fa-exclamation-circle me-1"></i> Sesuaikan jika ada waste.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Machine & Stock side-by-side -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6" id="sectionCompMesin">
                                    <div class="p-3 bg-light rounded-3 border h-100 shadow-sm">
                                        <label class="form-label fw-bold text-dark small text-uppercase mb-1" id="labelCompMesin">Pilih Mesin Produksi <span class="badge bg-danger ms-1" id="compMesinBadge">Wajib</span></label>
                                        <select class="form-select premium-input" id="compMesinId">
                                            <option value="">-- Pilih Mesin --</option>
                                        </select>
                                        <div class="form-text small text-muted mt-2" style="font-size: 0.65rem;">Pilih mesin pengerjaan.</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded-3 border h-100 shadow-sm">
                                        <label class="form-label fw-bold text-dark small text-uppercase mb-1" id="labelPilihStokGudang">Pilih Stok Bahan (Gudang)</label>
                                        <select class="form-select premium-input" id="compMaterialId" required>
                                            <option value="">-- Pilih Stok --</option>
                                        </select>
                                        <div class="form-text small text-muted mt-2" id="helpPilihStokGudang" style="font-size: 0.65rem;">Pilih fisik stok gudang.</div>
                                        <div id="bomWarningMessage" class="form-text text-danger mt-1 fw-bold d-none" style="font-size: 0.65rem;"><i class="fas fa-exclamation-triangle me-1"></i> BOM KOSONG! Pilih manual.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: Operator PIN Verifikasi (Centered Card) -->
                            <div class="p-3 bg-light rounded-4 border border-primary border-opacity-10 text-center shadow-sm">
                                <label class="form-label fw-bold text-dark small text-uppercase mb-1 d-block"><i class="fas fa-key text-primary me-1"></i> PIN Verifikasi Petugas (Operator)</label>
                                <div class="d-flex justify-content-center">
                                    <input type="password" class="form-control form-control-lg premium-input text-center fw-900 shadow-sm border-primary border-opacity-25"
                                        id="compOperatorPin" placeholder="PIN 4-6 Digit" maxlength="6"
                                        style="letter-spacing: 0.5rem; font-size: 1.4rem; max-width: 280px;" inputmode="numeric">
                                </div>
                                <div class="form-text small text-muted mt-1" style="font-size: 0.7rem;">Gunakan PIN Anda untuk menyelesaikan pengerjaan.</div>
                            </div>
                        </div>

                        <!-- Right Column: Job Confirmation & Search -->
                        <div class="col-lg-5 col-md-12 border-start border-light ps-lg-4">
                            <div class="mb-4" id="sectionSearchBahan" style="display: none;">
                                <label class="form-label fw-bold text-danger small text-uppercase"><i class="fas fa-exclamation-triangle me-1"></i> Bahan Tidak Terdeteksi di BOM</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="searchBahanInput" placeholder="Cari Nama Bahan (Sticker, Flexy, dll)...">
                                </div>
                                <div id="searchBahanResults" class="list-group mt-2 shadow-sm" style="max-height: 180px; overflow-y: auto; display: none;"></div>
                            </div>

                            <div class="mb-4">
                                <label class="small fw-bold text-muted text-uppercase ls-1">Konfirmasi Hasil per Job</label>
                                <div id="compItemList" class="mt-2 border rounded-3 overflow-auto bg-light p-1 shadow-inner" style="max-height: 420px;">
                                    <!-- Items will be injected here via JS -->
                                </div>
                                <div class="form-text mt-2 text-danger" style="font-size: 0.7rem;"><i class="fas fa-info-circle me-1"></i> Item yang ditandai <b>GAGAL (REJEK)</b> tetap pada tahap aktif. Hasil dan pemakaian bahan aktual dicatat.</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light border-top p-3 px-4 d-flex justify-content-between align-items-center w-100">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success rounded-pill fw-900 shadow-sm px-5" onclick="submitBatchCompletion(false)">
                    <i class="fas fa-check-circle me-2"></i> Konfirmasi Selesai
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Preview Modals -->
<div class="modal fade" id="modalPreviewSPK" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content modal-premium" style="height: 85vh;">
            <div class="modal-header">
                <h6 class="modal-title fw-800"><i class="fas fa-file-invoice me-2"></i> Pratinjau Dokumen SPK -
                    #<span id="lblNospkPreview"></span></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-light">
                <iframe id="iframePreview" src="" class="w-100 h-100 border-0"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview Tag -->
<div class="modal fade" id="modalPreviewTag" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-premium" style="height: 80vh;">
            <div class="modal-header">
                <h6 class="modal-title fw-800"><i class="fas fa-tag me-2"></i> Pratinjau Tag Produksi</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-light">
                <iframe id="iframePreviewTag" src="" class="w-100 h-100 border-0"></iframe>
            </div>
            <div class="modal-footer bg-white border-top shadow-sm">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary rounded-pill px-4"
                    onclick="document.getElementById('iframePreviewTag').contentWindow.print()">
                    <i class="fas fa-print me-2"></i> Cetak Tag
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Lapor Rijek / Masalah -->
<div class="modal fade" id="modalLaporRijek" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-premium shadow-lg border-0">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-900"><i class="fas fa-exclamation-triangle me-2"></i> Lapor Masalah Produksi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formLaporRijek">
                    <input type="hidden" id="rijekNospk" name="nospk">

                    <div class="alert alert-light border-danger border-start border-3 text-danger mb-4">
                        <i class="fas fa-info-circle me-1"></i>
                        SPK <strong>#<span id="lblRijekNospk"></span></strong> akan ditandai bermasalah.
                    </div>

                    <!-- Item Selection Area -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted ls-1">Pilih Item
                            Bermasalah</label>
                        <div class="border rounded-3 p-0 overflow-hidden">
                            <div class="bg-light p-2 border-bottom d-flex justify-content-between align-items-center">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="checkbox" id="checkAllRijek"
                                        onclick="toggleAllRijek(this)">
                                    <label class="form-check-label small fw-bold" for="checkAllRijek">Pilih
                                        Semua</label>
                                </div>
                                <small class="text-muted fst-italic" id="countRijekSelected">0 terpilih</small>
                            </div>
                            <div id="rijekItemsContainer" class="p-2" style="max-height: 150px; overflow-y: auto;">
                                <!-- Items injected by JS -->
                            </div>
                        </div>
                        <div class="form-text text-danger small" id="rijekItemError" style="display:none;">
                            <i class="fas fa-exclamation-circle"></i> Harap pilih minimal satu item!
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Jenis Masalah</label>
                        <select class="form-select premium-input" id="rijekJenis" required>
                            <option value="">-- Pilih Kendala --</option>
                            <option value="GAGAL_PRINT">Gagal Print (Head Nabrak / Banding)</option>
                            <option value="BAHAN_RUSAK">Bahan Rusak / Cacat</option>
                            <option value="FINISHING_GAGAL">Finishing Gagal (Cutting/Laminasi)</option>
                            <option value="SALAH_FILE">Salah File / Desain</option>
                            <option value="MESIN_TROUBLE">Mesin Trouble</option>
                            <option value="LAINNYA">Lainnya</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-danger">PIN Verifikasi Operator</label>
                        <input type="password"
                            class="form-control form-control-lg premium-input border-danger border-opacity-50 text-center fw-bold"
                            id="rijekPin" maxlength="6" placeholder="Masukkan 4-6 Digit PIN"
                            style="letter-spacing: 0.5rem; font-size: 1.5rem;" inputmode="numeric">
                        <div class="form-text text-center small">Gunakan PIN Produksi Anda untuk verifikasi identitas.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Keterangan / Detail</label>
                        <textarea class="form-control premium-input" id="rijekKeterangan" rows="3"
                            placeholder="Jelaskan detail kerusakan..."></textarea>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch bg-light p-3 rounded-3 border">
                            <input class="form-check-input ms-0 me-2" type="checkbox" id="rijekReprint"
                                style="float: none;">
                            <label class="form-check-label fw-bold text-danger" for="rijekReprint">Ajukan Cetak Ulang
                                (Reprint)?</label>
                            <div class="small text-muted mt-1">Permintaan dicatat untuk ditinjau supervisor. Riwayat produksi dan pemakaian bahan tetap tersimpan.
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-danger rounded-pill fw-bold shadow-sm"
                            onclick="submitLaporRijek()">
                            <i class="fas fa-paper-plane me-2"></i> Krim Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<!-- Modal Panduan Operator Lengkap -->
<div class="modal fade" id="modalManualGuide" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white fw-bold">
                    <i class="fas fa-book-reader me-2"></i>
                    Panduan Lengkap Operator - Modul Produksi WIP v2.0
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs nav-fill border-bottom" role="tablist" style="background: #f8f9fa;">
                    <li class="nav-item">
                        <a class="nav-link active fw-bold" data-bs-toggle="tab" href="#guideOverview">
                            <i class="fas fa-info-circle me-1"></i> Ringkasan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bold" data-bs-toggle="tab" href="#guideWorkflow">
                            <i class="fas fa-tasks me-1"></i> Alur Kerja
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bold" data-bs-toggle="tab" href="#guideTips">
                            <i class="fas fa-lightbulb me-1"></i> Tips & Trik
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bold" data-bs-toggle="tab" href="#guideTrouble">
                            <i class="fas fa-wrench me-1"></i> Troubleshooting
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bold" data-bs-toggle="tab" href="#guideExamples">
                            <i class="fas fa-file-alt me-1"></i> Contoh Kasus
                        </a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content p-4">
                    <!-- Tab 1: Overview -->
                    <div class="tab-pane fade show active" id="guideOverview">
                        <h5 class="fw-bold text-primary mb-3">
                            <i class="fas fa-rocket me-2"></i>Apa yang Baru di WIP v2?
                        </h5>

                        <div class="alert alert-success border-0 shadow-sm mb-3">
                            <h6 class="fw-bold">✅ Filter Cabang & Titipan Antar-Cabang</h6>
                            <ul class="mb-0 small">
                                <li><strong>Filter Cabang:</strong> Admin/Owner kini bisa memantau antrian khusus di cabang tertentu melalui dropdown cabang di kanan atas.</li>
                                <li><strong>Pekerjaan Titipan:</strong> SPK yang berasal dari cabang lain namun diproduksi di cabang Anda akan diberi label khusus <span class="badge bg-danger text-white">TITIPAN DARI ...</span> agar mudah dibedakan.</li>
                                <li><strong>Operator Mode:</strong> Daftar produksi secara otomatis terkunci pada lokasi kerja operator masing-masing demi keamanan.</li>
                            </ul>
                        </div>

                        <div class="alert alert-info border-0 shadow-sm mb-3">
                            <h6 class="fw-bold">✅ Mode "Per Bahan" & "Per SPK" (Khusus LF & A3+)</h6>
                            <ul class="mb-0 small">
                                <li><strong>Per Bahan (Material-Centric):</strong> Item dikelompokkan berdasarkan nama bahan (misal: Art Carton 260gr, Flexy 280gr). Sangat mempercepat kerja karena operator tidak perlu bolak-balik ganti roll/kertas di mesin! Terdapat fitur Ripping & Nesting.</li>
                                <li><strong>Per SPK (Order-Centric):</strong> Tampilan klasik per nomor SPK untuk memantau apakah pesanan pelanggan sudah komplit atau belum.</li>
                            </ul>
                        </div>

                        <div class="alert alert-primary border-0 shadow-sm mb-3">
                            <h6 class="fw-bold">✅ Loading Ekstra Cepat & Pembagian Departemen</h6>
                            <ul class="mb-0 small">
                                <li>Sistem sekarang membaca data per kategori (Server-Side). Saat Anda berpindah kategori (misal dari A3+ ke Merchandise), sistem akan memuat ulang hanya data departemen tersebut. Ini membuat halaman terbuka secara kilat (Instant Load).</li>
                            </ul>
                        </div>

                        <div class="alert alert-warning border-0 shadow-sm mb-3">
                            <h6 class="fw-bold">✅ Keamanan dengan PIN Verifikasi</h6>
                            <ul class="mb-0 small">
                                <li>Semua aksi (Mulai, Selesai, Lapor Rijek) memerlukan <strong>PIN Produksi</strong> dari masing-masing operator. Jejak digital (Audit Trail) tercatat rapi.</li>
                            </ul>
                        </div>

                        <hr class="my-4">

                        <h5 class="fw-bold text-danger mb-3">
                            <i class="fas fa-key me-2"></i>Cara Mendapatkan PIN Produksi
                        </h5>
                        <ol class="small">
                            <li>Hubungi Admin atau HRD</li>
                            <li>Minta PIN Produksi (4-6 digit)</li>
                            <li><strong class="text-danger">JANGAN BAGIKAN PIN</strong> ke operator lain</li>
                            <li>Jika lupa PIN, hubungi Admin untuk reset</li>
                        </ol>

                        <div class="alert alert-danger border-0 shadow-sm mt-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>PENTING:</strong> PIN adalah tanggung jawab pribadi. Jika ada masalah, yang
                            bertanggung jawab adalah pemilik PIN!
                        </div>
                    </div>

                    <!-- Tab 2: Workflow -->
                    <div class="tab-pane fade" id="guideWorkflow">
                        <h5 class="fw-bold text-primary mb-4">
                            <i class="fas fa-route me-2"></i>Alur Kerja Produksi
                        </h5>

                        <!-- Step 1 -->
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-primary text-white fw-bold">
                                <i class="fas fa-filter me-2"></i>Langkah 1: Setup Tampilan
                            </div>
                            <div class="card-body">
                                <ol class="small mb-0">
                                    <li>Jika Anda Admin, pilih <strong>Cabang</strong> dari dropdown pojok kanan atas.</li>
                                    <li>Pilih <strong>Kategori Produksi</strong> (A3+, Large Format, Merchandise, dll) dari dropdown Kategori. Halaman akan reload sekilas khusus untuk menarik data Anda.</li>
                                    <li>Gunakan mode <strong>Per Bahan</strong> untuk efisiensi produksi massal, atau <strong>Per SPK</strong> untuk memeriksa keseluruhan pesanan.</li>
                                </ol>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-info text-white fw-bold">
                                <i class="fas fa-eye me-2"></i>Langkah 2: Lihat Daftar Pekerjaan
                            </div>
                            <div class="card-body">
                                <p class="small mb-2">Anda akan melihat tabel dengan kolom:</p>
                                <ul class="small mb-3">
                                    <li><strong>No SPK:</strong> Nomor SPK dari Sales</li>
                                    <li><strong>Produk:</strong> Nama produk yang akan diproduksi</li>
                                    <li><strong>Qty:</strong> Jumlah yang harus diproduksi</li>
                                    <li><strong>Konsumen:</strong> Nama pelanggan</li>
                                    <li><strong>Status:</strong> Status pekerjaan saat ini</li>
                                    <li><strong>Aksi:</strong> Tombol untuk mulai/selesai</li>
                                </ul>
                                <p class="small mb-2 fw-bold">Status Pekerjaan:</p>
                                <ul class="small mb-0">
                                    <li>🟡 <strong>Pending:</strong> Belum dimulai</li>
                                    <li>🔵 <strong>In Progress:</strong> Sedang dikerjakan</li>
                                    <li>🟢 <strong>Done:</strong> Sudah selesai</li>
                                    <li>📦 <strong>On PB:</strong> Sudah di Pengambilan Barang</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-success text-white fw-bold">
                                <i class="fas fa-play me-2"></i>Langkah 3: Mulai Pekerjaan
                            </div>
                            <div class="card-body">
                                <ol class="small mb-2">
                                    <li>Cari pekerjaan yang mau dikerjakan</li>
                                    <li>Klik tombol <strong>"Mulai"</strong> atau <strong>"Start"</strong></li>
                                    <li>Masukkan <strong>PIN Produksi</strong> Anda</li>
                                    <li>Klik <strong>"Konfirmasi"</strong></li>
                                    <li>Status berubah menjadi <strong>"In Progress"</strong></li>
                                </ol>
                                <div class="alert alert-warning border-0 mb-0 small">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Catatan:</strong> Hanya 1 step yang bisa "In Progress" per SPK. Jika ada
                                    step lain yang masih "In Progress", selesaikan dulu!
                                </div>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-danger text-white fw-bold">
                                <i class="fas fa-check-circle me-2"></i>Langkah 4: Selesaikan Pekerjaan
                            </div>
                            <div class="card-body">
                                <h6 class="fw-bold text-primary">Untuk Produk METER (Sticker, Banner Roll)</h6>
                                <ol class="small mb-3">
                                    <li>Klik tombol <strong>"Selesai"</strong></li>
                                    <li>Masukkan:
                                        <ul>
                                            <li><strong>Panjang Terpakai (Meter):</strong> Masukkan panjang yang dipakai
                                            </li>
                                            <li><strong>Panjang Rusak (Meter):</strong> Jika ada yang rusak</li>
                                            <li><strong>Pilih Bahan:</strong> Pilih roll/material yang dipakai</li>
                                            <li><strong>PIN Operator:</strong> Masukkan PIN Anda</li>
                                        </ul>
                                    </li>
                                    <li>Klik <strong>"Proses"</strong></li>
                                </ol>

                                <h6 class="fw-bold text-success">Untuk Produk PCS (X-Banner, Roll-Up, Neon Box)</h6>
                                <ol class="small mb-3">
                                    <li>Klik tombol <strong>"Selesai"</strong></li>
                                    <li>Masukkan:
                                        <ul>
                                            <li><strong>Jumlah Diproduksi (Pcs):</strong> Masukkan jumlah yang
                                                diproduksi</li>
                                            <li><strong>Jumlah Rusak (Pcs):</strong> Jika ada yang rusak</li>
                                            <li><strong>Luas Otomatis:</strong> Sistem akan hitung otomatis (P × L ×
                                                Qty)</li>
                                            <li><strong>Pilih Bahan:</strong> Pilih roll/material yang dipakai</li>
                                            <li><strong>PIN Operator:</strong> Masukkan PIN Anda</li>
                                        </ul>
                                    </li>
                                    <li>Klik <strong>"Proses"</strong></li>
                                </ol>

                                <h6 class="fw-bold text-warning">Untuk Produk A3+ / SATUAN (Cetak Photo, Flyer, Brosur)
                                </h6>
                                <ol class="small mb-3">
                                    <li>Klik tombol <strong>"Selesai"</strong></li>
                                    <li>Masukkan:
                                        <ul>
                                            <li><strong>Jumlah Diproduksi (Lembar):</strong> Masukkan jumlah lembar yang
                                                dicetak</li>
                                            <li><strong>Jumlah Rusak (Lembar):</strong> Jika ada yang rusak/gagal cetak
                                            </li>
                                            <li><strong>Luas Otomatis:</strong> Sistem akan hitung berdasarkan ukuran
                                                lembar × qty</li>
                                            <li><strong>Pilih Bahan:</strong> Pilih jenis kertas/media yang dipakai (Art
                                                Paper, HVS, dll)</li>
                                            <li><strong>PIN Operator:</strong> Masukkan PIN Anda</li>
                                        </ul>
                                    </li>
                                    <li>Klik <strong>"Proses"</strong></li>
                                </ol>

                                <div class="alert alert-info border-0 mb-0 small">
                                    <i class="fas fa-calculator me-1"></i>
                                    <strong>Auto-Calculate:</strong> Untuk produk PCS dan A3+/SATUAN, sistem otomatis
                                    menghitung luas
                                    total berdasarkan dimensi produk × jumlah yang diproduksi. Untuk produk METER,
                                    sistem langsung pakai panjang yang Anda input.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: Tips -->
                    <div class="tab-pane fade" id="guideTips">
                        <h5 class="fw-bold text-primary mb-4">
                            <i class="fas fa-lightbulb me-2"></i>Tips & Trik Produksi
                        </h5>

                        <div class="accordion" id="tipsAccordion">
                            <div class="accordion-item border-0 shadow-sm mb-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#tip1">
                                        <i class="fas fa-search me-2 text-primary"></i>Cara Cari Pekerjaan Cepat
                                    </button>
                                </h2>
                                <div id="tip1" class="accordion-collapse collapse show" data-bs-parent="#tipsAccordion">
                                    <div class="accordion-body small">
                                        <ul class="mb-0">
                                            <li>Gunakan <strong>Search Box</strong> di atas tabel</li>
                                            <li>Ketik No SPK atau nama produk</li>
                                            <li>Tabel akan filter otomatis</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 shadow-sm mb-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#tip2">
                                        <i class="fas fa-filter me-2 text-success"></i>Cara Filter Berdasarkan Status
                                    </button>
                                </h2>
                                <div id="tip2" class="accordion-collapse collapse" data-bs-parent="#tipsAccordion">
                                    <div class="accordion-body small">
                                        <ul class="mb-0">
                                            <li>Klik dropdown <strong>"Filter Status"</strong></li>
                                            <li>Pilih status yang mau dilihat:
                                                <ul>
                                                    <li><strong>Pending:</strong> Pekerjaan yang belum dimulai</li>
                                                    <li><strong>In Progress:</strong> Pekerjaan yang sedang dikerjakan
                                                    </li>
                                                    <li><strong>Done:</strong> Pekerjaan yang sudah selesai</li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 shadow-sm mb-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#tip3">
                                        <i class="fas fa-box me-2 text-warning"></i>Cara Pilih Bahan/Material
                                    </button>
                                </h2>
                                <div id="tip3" class="accordion-collapse collapse" data-bs-parent="#tipsAccordion">
                                    <div class="accordion-body small">
                                        <ol class="mb-2">
                                            <li>Klik kolom <strong>"Pilih Bahan"</strong></li>
                                            <li>Ketik nama bahan (misal: "Albatros")</li>
                                            <li>Pilih dari dropdown yang muncul</li>
                                            <li>Pastikan pilih roll yang masih ada stoknya</li>
                                        </ol>
                                        <div class="alert alert-warning border-0 mb-0">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            <strong>Catatan:</strong> Jika bahan tidak muncul, hubungi Admin Gudang!
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 shadow-sm mb-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-bold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#tip4">
                                        <i class="fas fa-undo me-2 text-danger"></i>Jika Salah Input
                                    </button>
                                </h2>
                                <div id="tip4" class="accordion-collapse collapse" data-bs-parent="#tipsAccordion">
                                    <div class="accordion-body small">
                                        <h6 class="fw-bold text-success">Jika belum klik "Proses":</h6>
                                        <ul class="mb-2">
                                            <li>Tutup modal dan buka lagi</li>
                                            <li>Input ulang dengan benar</li>
                                        </ul>
                                        <h6 class="fw-bold text-danger">Jika sudah klik "Proses":</h6>
                                        <ul class="mb-2">
                                            <li>Hubungi Supervisor atau Admin</li>
                                            <li>Mereka bisa koreksi di sistem</li>
                                            <li><strong class="text-danger">JANGAN</strong> input ulang sendiri (akan
                                                double deduction)</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 4: Troubleshooting -->
                    <div class="tab-pane fade" id="guideTrouble">
                        <h5 class="fw-bold text-danger mb-4">
                            <i class="fas fa-wrench me-2"></i>Troubleshooting
                        </h5>

                        <div class="card border-danger mb-3">
                            <div class="card-header bg-danger text-white fw-bold">
                                <i class="fas fa-key me-2"></i>Problem: PIN Tidak Valid
                            </div>
                            <div class="card-body small">
                                <h6 class="fw-bold text-danger">Penyebab:</h6>
                                <ul>
                                    <li>PIN salah</li>
                                    <li>PIN belum diset oleh Admin</li>
                                    <li>PIN untuk cabang lain</li>
                                </ul>
                                <h6 class="fw-bold text-success">Solusi:</h6>
                                <ul class="mb-0">
                                    <li>Cek ulang PIN Anda</li>
                                    <li>Hubungi Admin untuk verifikasi</li>
                                    <li>Minta reset PIN jika lupa</li>
                                </ul>
                            </div>
                        </div>

                        <div class="card border-warning mb-3">
                            <div class="card-header bg-warning text-dark fw-bold">
                                <i class="fas fa-box me-2"></i>Problem: Bahan Tidak Muncul
                            </div>
                            <div class="card-body small">
                                <h6 class="fw-bold text-danger">Penyebab:</h6>
                                <ul>
                                    <li>Bahan belum diinput ke sistem</li>
                                    <li>Bahan sudah habis/tidak aktif</li>
                                    <li>Salah ketik nama bahan</li>
                                </ul>
                                <h6 class="fw-bold text-success">Solusi:</h6>
                                <ul class="mb-0">
                                    <li>Cek ejaan nama bahan</li>
                                    <li>Hubungi Admin Gudang untuk cek stok</li>
                                    <li>Minta Admin input bahan baru jika perlu</li>
                                </ul>
                            </div>
                        </div>

                        <div class="card border-info mb-3">
                            <div class="card-header bg-info text-white fw-bold">
                                <i class="fas fa-ban me-2"></i>Problem: Tombol "Mulai" Tidak Bisa Diklik
                            </div>
                            <div class="card-body small">
                                <h6 class="fw-bold text-danger">Penyebab:</h6>
                                <ul>
                                    <li>Ada step lain yang masih "In Progress"</li>
                                    <li>Status produksi sudah "Done"</li>
                                    <li>Pekerjaan untuk cabang lain</li>
                                </ul>
                                <h6 class="fw-bold text-success">Solusi:</h6>
                                <ul class="mb-0">
                                    <li>Selesaikan step yang "In Progress" dulu</li>
                                    <li>Cek status produksi di kolom "Status"</li>
                                    <li>Hubungi Supervisor jika masih bingung</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 5: Examples -->
                    <div class="tab-pane fade" id="guideExamples">
                        <h5 class="fw-bold text-success mb-4">
                            <i class="fas fa-file-alt me-2"></i>Contoh Kasus Nyata
                        </h5>

                        <!-- Example 1 -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-primary text-white fw-bold">
                                <i class="fas fa-image me-2"></i>Kasus 1: Produksi X-Banner 60x160 (5 pcs)
                            </div>
                            <div class="card-body">
                                <h6 class="fw-bold text-dark">Situasi:</h6>
                                <ul class="small">
                                    <li>SPK: 2-16-0226-349</li>
                                    <li>Produk: X-Banner 60x160</li>
                                    <li>Qty Order: 5 pcs</li>
                                    <li>Bahan: Albatros 440gr Roll 2 (Stok: 10 Roll = 500m)</li>
                                </ul>

                                <h6 class="fw-bold text-dark">Langkah Operator:</h6>
                                <div class="bg-light p-3 rounded mb-2">
                                    <p class="small mb-1 fw-bold">1. RIP Step:</p>
                                    <ul class="small mb-0">
                                        <li>Klik "Mulai" → Input PIN → Klik "Konfirmasi"</li>
                                        <li>Klik "Selesai" → Input PIN → Klik "Proses"</li>
                                        <li>(Tidak pakai bahan)</li>
                                    </ul>
                                </div>

                                <div class="bg-light p-3 rounded mb-2">
                                    <p class="small mb-1 fw-bold">2. Print Step:</p>
                                    <ul class="small mb-0">
                                        <li>Klik "Mulai" → Input PIN → Klik "Konfirmasi"</li>
                                        <li>Klik "Selesai"</li>
                                        <li>Form muncul:
                                            <ul>
                                                <li>Jumlah Diproduksi: <strong>5</strong> pcs</li>
                                                <li>Jumlah Rusak: <strong>0</strong> pcs</li>
                                                <li>Luas Otomatis: <strong>4.8 m²</strong> (0.6 × 1.6 × 5)</li>
                                                <li>Pilih Bahan: <strong>Albatros 440gr Roll 2</strong></li>
                                                <li>PIN: <strong>1234</strong></li>
                                            </ul>
                                        </li>
                                        <li>Klik "Proses"</li>
                                    </ul>
                                </div>

                                <div class="alert alert-success border-0 mb-0">
                                    <h6 class="fw-bold">Hasil:</h6>
                                    <ul class="small mb-0">
                                        <li>Status: <span class="badge bg-success">Done</span></li>
                                        <li>Stok Albatros: 10 - 0.096 = <strong>9.904 Roll</strong></li>
                                        <li>Log: "Produksi LF (PCS) SPK: 2-16-0226-349. X-Banner 60x160. Area: 4.8m²
                                            (0.6x1.6x5 pcs)"</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Example 2 -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-success text-white fw-bold">
                                <i class="fas fa-sticky-note me-2"></i>Kasus 2: Produksi Sticker Custom (3.5 meter)
                            </div>
                            <div class="card-body">
                                <h6 class="fw-bold text-dark">Situasi:</h6>
                                <ul class="small">
                                    <li>SPK: 2-14-0226-216</li>
                                    <li>Produk: Sticker Vinyl Custom</li>
                                    <li>Qty Order: 3.5 meter</li>
                                    <li>Bahan: Vinyl Glossy Roll 1 (Stok: 5 Roll = 250m)</li>
                                </ul>

                                <h6 class="fw-bold text-dark">Langkah Operator:</h6>
                                <div class="bg-light p-3 rounded mb-2">
                                    <p class="small mb-1 fw-bold">Print Step:</p>
                                    <ul class="small mb-0">
                                        <li>Klik "Mulai" → Input PIN → Klik "Konfirmasi"</li>
                                        <li>Klik "Selesai"</li>
                                        <li>Form muncul:
                                            <ul>
                                                <li>Panjang Terpakai: <strong>3.5</strong> meter</li>
                                                <li>Panjang Rusak: <strong>0.2</strong> meter (ada sedikit gagal)</li>
                                                <li>Pilih Bahan: <strong>Vinyl Glossy Roll 1</strong></li>
                                                <li>PIN: <strong>1234</strong></li>
                                            </ul>
                                        </li>
                                        <li>Klik "Proses"</li>
                                    </ul>
                                </div>

                                <div class="alert alert-success border-0 mb-0">
                                    <h6 class="fw-bold">Hasil:</h6>
                                    <ul class="small mb-0">
                                        <li>Status: <span class="badge bg-success">Done</span></li>
                                        <li>Stok Vinyl: 5 - 0.074 = <strong>4.926 Roll</strong></li>
                                        <li>Log: "Produksi LF SPK: 2-14-0226-216. Pakai: 3.7 m (3.5 + 0.2 rusak)"</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Example 3: A3+ Photo Print -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-warning text-dark fw-bold">
                                <i class="fas fa-camera me-2"></i>Kasus 3: Cetak Photo A3+ (20 lembar)
                            </div>
                            <div class="card-body">
                                <h6 class="fw-bold text-dark">Situasi:</h6>
                                <ul class="small">
                                    <li>SPK: 2-03-0226-127</li>
                                    <li>Produk: Photo Print A3+ (32.9 x 48.3 cm)</li>
                                    <li>Qty Order: 20 lembar</li>
                                    <li>Bahan: Photo Paper Glossy A3+ (Stok: 500 lembar)</li>
                                </ul>

                                <h6 class="fw-bold text-dark">Langkah Operator:</h6>
                                <div class="bg-light p-3 rounded mb-2">
                                    <p class="small mb-1 fw-bold">1. RIP Step:</p>
                                    <ul class="small mb-0">
                                        <li>Klik "Mulai" → Input PIN → Klik "Konfirmasi"</li>
                                        <li>Klik "Selesai" → Input PIN → Klik "Proses"</li>
                                        <li>(Tidak pakai bahan)</li>
                                    </ul>
                                </div>

                                <div class="bg-light p-3 rounded mb-2">
                                    <p class="small mb-1 fw-bold">2. Print A3+ Step:</p>
                                    <ul class="small mb-0">
                                        <li>Klik "Mulai" → Input PIN → Klik "Konfirmasi"</li>
                                        <li>Klik "Selesai"</li>
                                        <li>Form muncul:
                                            <ul>
                                                <li>Jumlah Diproduksi: <strong>20</strong> lembar</li>
                                                <li>Jumlah Rusak: <strong>2</strong> lembar (ada yang salah warna)</li>
                                                <li>Luas Otomatis: <strong>3.18 m²</strong> (0.329 × 0.483 × 20)</li>
                                                <li>Pilih Bahan: <strong>Photo Paper Glossy A3+</strong></li>
                                                <li>PIN: <strong>5678</strong></li>
                                            </ul>
                                        </li>
                                        <li>Klik "Proses"</li>
                                    </ul>
                                </div>

                                <div class="alert alert-success border-0 mb-0">
                                    <h6 class="fw-bold">Hasil:</h6>
                                    <ul class="small mb-0">
                                        <li>Status: <span class="badge bg-success">Done</span></li>
                                        <li>Stok Photo Paper: 500 - 22 = <strong>478 lembar</strong> (20 OK + 2 rusak)
                                        </li>
                                        <li>Log: "Produksi A3+ SPK: 2-03-0226-127. Photo A3+. Area: 3.18m² (32.9x48.3cm
                                            × 20 lembar, 2 rusak)"</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Example 4: Business Card -->
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-info text-white fw-bold">
                                <i class="fas fa-id-card me-2"></i>Kasus 4: Cetak Kartu Nama (500 pcs)
                            </div>
                            <div class="card-body">
                                <h6 class="fw-bold text-dark">Situasi:</h6>
                                <ul class="small">
                                    <li>SPK: 2-03-0226-145</li>
                                    <li>Produk: Kartu Nama 9x5.5 cm</li>
                                    <li>Qty Order: 500 pcs (5 box @ 100 pcs)</li>
                                    <li>Bahan: Art Paper 260gr A3+ (Stok: 1000 lembar)</li>
                                    <li>Catatan: 1 lembar A3+ bisa jadi 8 kartu nama (cutting)</li>
                                </ul>

                                <h6 class="fw-bold text-dark">Langkah Operator:</h6>
                                <div class="bg-light p-3 rounded mb-2">
                                    <p class="small mb-1 fw-bold">1. Print A3+ Step:</p>
                                    <ul class="small mb-0">
                                        <li>Klik "Mulai" → Input PIN → Klik "Konfirmasi"</li>
                                        <li>Cetak di A3+, 500 kartu = butuh ±63 lembar A3+ (500÷8)</li>
                                        <li>Klik "Selesai"</li>
                                        <li>Form muncul:
                                            <ul>
                                                <li>Jumlah Diproduksi: <strong>65</strong> lembar A3+ (termasuk
                                                    cadangan)</li>
                                                <li>Jumlah Rusak: <strong>3</strong> lembar (ada yang salah posisi)</li>
                                                <li>Luas Otomatis: <strong>10.33 m²</strong> (0.329 × 0.483 × 65)</li>
                                                <li>Pilih Bahan: <strong>Art Paper 260gr A3+</strong></li>
                                                <li>PIN: <strong>9012</strong></li>
                                            </ul>
                                        </li>
                                        <li>Klik "Proses"</li>
                                    </ul>
                                </div>

                                <div class="bg-light p-3 rounded mb-2">
                                    <p class="small mb-1 fw-bold">2. Cutting Step:</p>
                                    <ul class="small mb-0">
                                        <li>Klik "Mulai" → Input PIN → Klik "Konfirmasi"</li>
                                        <li>Potong 65 lembar A3+ jadi 520 kartu nama (65×8)</li>
                                        <li>Klik "Selesai"</li>
                                        <li>Form muncul:
                                            <ul>
                                                <li>Jumlah Diproduksi: <strong>500</strong> pcs</li>
                                                <li>Jumlah Rusak: <strong>20</strong> pcs (sisa potongan + cacat)</li>
                                                <li>PIN: <strong>9012</strong></li>
                                            </ul>
                                        </li>
                                        <li>Klik "Proses"</li>
                                    </ul>
                                </div>

                                <div class="alert alert-success border-0 mb-0">
                                    <h6 class="fw-bold">Hasil:</h6>
                                    <ul class="small mb-0">
                                        <li>Status Print: <span class="badge bg-success">Done</span></li>
                                        <li>Status Cutting: <span class="badge bg-success">Done</span></li>
                                        <li>Stok Art Paper: 1000 - 68 = <strong>932 lembar</strong> (65 OK + 3 rusak)
                                        </li>
                                        <li>Hasil Jadi: <strong>500 pcs kartu nama</strong> siap finishing/packing</li>
                                        <li>Log Print: "Produksi A3+ SPK: 2-03-0226-145. Kartu Nama. Area: 10.33m² (65
                                            lembar A3+, 3 rusak)"</li>
                                        <li>Log Cutting: "Cutting SPK: 2-03-0226-145. Hasil: 500 pcs OK, 20 pcs rusak"
                                        </li>
                                    </ul>
                                </div>

                                <div class="alert alert-info border-0 mb-0 mt-2 small">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    <strong>Tips:</strong> Untuk produk yang perlu multiple step (print → cutting →
                                    laminating), setiap step dicatat terpisah. Input jumlah yang sesuai dengan unit di
                                    step tersebut (lembar untuk print, pcs untuk cutting).
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Tutup
                </button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i>Cetak Panduan
                </button>
            </div>
        </div>
    </div>
</div>
