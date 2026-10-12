<div id="nestingSummary"
    class="d-none fixed-bottom bg-dark text-white shadow-lg border-top border-primary border-4 py-3 animate__animated animate__slideInUp"
    style="z-index: 1050; border-top-left-radius: 20px; border-top-right-radius: 20px;">
    <div class="container-fluid px-5">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-4">
                <div class="nesting-stat text-center border-end border-white border-opacity-25 pe-4">
                    <div class="small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.65rem;">Item</div>
                    <div class="h4 mb-0 fw-900 text-info" id="nestCount">0</div>
                </div>
                <div class="nesting-stat text-center border-end border-white border-opacity-25 pe-4">
                    <div class="small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.65rem;">Luas
                        (+Toleransi)</div>
                    <div class="h4 mb-0 fw-900 text-warning"><span id="nestTotalArea">0.00</span> <small
                            class="fw-bold">M²</small></div>
                </div>
                <div class="nesting-stat text-center border-end border-white border-opacity-25 pe-4">
                    <div class="small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.65rem;">Saran
                        Lebar Bahan</div>
                    <div class="h4 mb-0 fw-900 text-success" id="nestWidthSuggest">0 cm</div>
                </div>
                <div class="nesting-stat text-center pe-4">
                    <div class="small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.65rem;">Est. Meter
                        Lari</div>
                    <div class="h4 mb-0 fw-900 text-white"><span id="nestLinear">0.00</span> <small
                            class="fw-bold">M</small></div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <button type="button" class="btn btn-outline-light rounded-pill px-4 fw-800 btn-sm"
                    onclick="clearNestingSelection()">Batal</button>
                <button type="button" class="btn btn-info rounded-pill px-4 fw-900 shadow-sm"
                    onclick="visualizeNesting()" id="visualizeNestingBtn">
                    <i class="fas fa-eye me-2"></i> Simulasi Visual
                </button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-900 shadow-sm"
                    onclick="batchProcessNesting()" id="btnRipping">
                    <i class="fas fa-magic me-2"></i> Proses Ripping
                </button>
                <button type="button" class="btn btn-success rounded-pill px-4 fw-900 shadow-sm d-none"
                    onclick="openNestingCompletionModal()" id="btnSelesaiStok">
                    <i class="fas fa-check-double me-2"></i> Selesai & Potong Stok
                </button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-900 shadow-sm d-none"
                    onclick="openFinishingCompletionModal()" id="btnSelesaiFinishing"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <i class="fas fa-check-circle me-2"></i> Selesaikan Tahapan
                </button>
            </div>
        </div>
    </div>
</div>