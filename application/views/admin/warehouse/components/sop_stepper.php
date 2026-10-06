<?php 
$active_step = isset($active_step) ? $active_step : 0; 
?>
<div class="registration-workflow mb-5">
    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 28px; background: white;">
        <div class="row g-0">
            <div class="col-lg-3 p-4 d-flex flex-column justify-content-center text-white" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%);">
                <h4 class="fw-900 mb-2 mt-0"><i class="fas fa-clipboard-check me-2 text-warning"></i>SOP HARIAN</h4>
                <p class="small opacity-75 mb-0">Ikuti 6 rutinitas ini setiap hari (Pagi - Sore) untuk memastikan tidak ada selisih stok gudang.</p>
            </div>
            <div class="col-lg-9 p-0">
                <div class="row g-0 h-100">
                    <!-- STEP 1 -->
                    <div class="col border-end" style="transition: all 0.3s; <?= ($active_step > 0 && $active_step != 1) ? 'opacity: 0.4; filter: grayscale(80%);' : '' ?> <?= ($active_step == 1) ? 'background: #f8fafc; box-shadow: inset 0 0 10px rgba(0,0,0,0.05); transform: scale(1.02); z-index: 10;' : '' ?>">
                        <a href="<?= base_url('warehouse/requests') ?>"
                            class="text-decoration-none p-3 d-block h-100 workflow-step <?= ($active_step == 1) ? 'step-danger' : '' ?>">
                            <div class="text-center">
                                <div class="step-number mx-auto mb-2" style="background:#fee2e2; color:#ef4444; border-color:#fca5a5;">1</div>
                                <h6 class="fw-900 text-danger mb-1 small">PENGAJUAN</h6>
                                <p class="text-muted mb-0" style="font-size: 0.6rem;">Validasi Permintaan</p>
                            </div>
                        </a>
                    </div>
                    <!-- STEP 2 -->
                    <div class="col border-end" style="transition: all 0.3s; <?= ($active_step > 0 && $active_step != 2) ? 'opacity: 0.4; filter: grayscale(80%);' : '' ?> <?= ($active_step == 2) ? 'background: #f8fafc; box-shadow: inset 0 0 10px rgba(0,0,0,0.05); transform: scale(1.02); z-index: 10;' : '' ?>">
                        <a href="<?= base_url('warehouse/laporan_stok_kritis') ?>"
                            class="text-decoration-none p-3 d-block h-100 workflow-step">
                            <div class="text-center">
                                <div class="step-number mx-auto mb-2" style="background:#fef3c7; color:#d97706; border-color:#fde68a;">2</div>
                                <h6 class="fw-900 text-warning mb-1 small" style="color:#d97706 !important;">STOK KRITIS</h6>
                                <p class="text-muted mb-0" style="font-size: 0.6rem;">Cek Batas Minimum</p>
                            </div>
                        </a>
                    </div>
                    <!-- STEP 3 -->
                    <div class="col border-end" style="transition: all 0.3s; <?= ($active_step > 0 && $active_step != 3) ? 'opacity: 0.4; filter: grayscale(80%);' : '' ?> <?= ($active_step == 3) ? 'background: #f8fafc; box-shadow: inset 0 0 10px rgba(0,0,0,0.05); transform: scale(1.02); z-index: 10;' : '' ?>">
                        <a href="<?= base_url('warehouse/purchase') ?>"
                            class="text-decoration-none p-3 d-block h-100 workflow-step">
                            <div class="text-center">
                                <div class="step-number mx-auto mb-2" style="background:#e0f2fe; color:#0284c7; border-color:#bae6fd;">3</div>
                                <h6 class="fw-900 text-info mb-1 small" style="color:#0284c7 !important;">INBOUND</h6>
                                <p class="text-muted mb-0" style="font-size: 0.6rem;">Penerimaan Supplier</p>
                            </div>
                        </a>
                    </div>
                    <!-- STEP 4 -->
                    <div class="col border-end" style="transition: all 0.3s; <?= ($active_step > 0 && $active_step != 4) ? 'opacity: 0.4; filter: grayscale(80%);' : '' ?> <?= ($active_step == 4) ? 'background: #f8fafc; box-shadow: inset 0 0 10px rgba(0,0,0,0.05); transform: scale(1.02); z-index: 10;' : '' ?>">
                        <a href="<?= base_url('warehouse/dispatch') ?>"
                            class="text-decoration-none p-3 d-block h-100 workflow-step">
                            <div class="text-center">
                                <div class="step-number mx-auto mb-2" style="background:#dbeafe; color:#2563eb; border-color:#bfdbfe;">4</div>
                                <h6 class="fw-900 text-primary mb-1 small">OUTBOUND</h6>
                                <p class="text-muted mb-0" style="font-size: 0.6rem;">Kirim ke Produksi</p>
                            </div>
                        </a>
                    </div>
                    <!-- STEP 5 -->
                    <div class="col border-end" style="transition: all 0.3s; <?= ($active_step > 0 && $active_step != 5) ? 'opacity: 0.4; filter: grayscale(80%);' : '' ?> <?= ($active_step == 5) ? 'background: #f8fafc; box-shadow: inset 0 0 10px rgba(0,0,0,0.05); transform: scale(1.02); z-index: 10;' : '' ?>">
                        <a href="<?= base_url('warehouse/ledger') ?>"
                            class="text-decoration-none p-3 d-block h-100 workflow-step">
                            <div class="text-center">
                                <div class="step-number mx-auto mb-2" style="background:#f3f4f6; color:#4b5563; border-color:#e5e7eb;">5</div>
                                <h6 class="fw-900 text-dark mb-1 small">KARTU STOK</h6>
                                <p class="text-muted mb-0" style="font-size: 0.6rem;">Sinkronisasi Akhir</p>
                            </div>
                        </a>
                    </div>
                    <!-- STEP 6 -->
                    <div class="col" style="transition: all 0.3s; <?= ($active_step > 0 && $active_step != 6) ? 'opacity: 0.4; filter: grayscale(80%);' : '' ?> <?= ($active_step == 6) ? 'background: #f8fafc; box-shadow: inset 0 0 10px rgba(0,0,0,0.05); transform: scale(1.02); z-index: 10;' : '' ?>">
                        <a href="<?= base_url('warehouse/opname') ?>"
                            class="text-decoration-none p-3 d-block h-100 workflow-step">
                            <div class="text-center">
                                <div class="step-number mx-auto mb-2" style="background:#d1fae5; color:#059669; border-color:#a7f3d0;">6</div>
                                <h6 class="fw-900 text-success mb-1 small">OPNAME</h6>
                                <p class="text-muted mb-0" style="font-size: 0.6rem;">Cek Fisik Harian</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
