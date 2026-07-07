<?php $this->load->view('layout_admin/header_modul'); ?>
<?php
$level = $this->session->userdata('level');
$isAdmin = in_array((int) $level, [1, 17], true);
?>
<link rel="stylesheet" href="<?= base_url('assets/css/wip_produksi.css') ?>">

<style>
    /* Additional Styles specific to Control Center which are not in wip_produksi.css yet */
    .timeline-step {
        position: relative;
        padding-left: 3rem;
        margin-bottom: 2rem;
    }

    .timeline-step::before {
        content: '';
        position: absolute;
        left: 14px;
        top: 0;
        bottom: -2rem;
        width: 2px;
        background: #e9ecef;
    }

    .timeline-step:last-child::before {
        display: none;
    }

    .timeline-icon {
        position: absolute;
        left: 0;
        top: 0;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        background: white;
        border: 2px solid #e9ecef;
        z-index: 1;
    }

    .timeline-step.active .timeline-icon {
        border-color: #3498db;
        color: #3498db;
    }

    .timeline-step.done .timeline-icon {
        background: #2ecc71;
        border-color: #2ecc71;
        color: white;
    }
</style>

<?php
// Check completion status
$isAllDone = true;
if (!empty($items)) {
    foreach ($items as $itm) {
        if ($itm->status_produksi != 'done') {
            $isAllDone = false;
            break;
        }
    }
} else {
    $isAllDone = false;
}
?>
<div class="app-content mt-3">
    <!-- Header Card: SPK Info -->
    <div class="report-section-card mb-4">
        <div class="section-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1">
                    <i class="fas fa-microchip me-2"></i> Control Center Produksi
                    <span class="badge bg-white text-dark ms-2 fw-bold shadow-sm">#<?= $spk_header->nospk ?></span>
                </h5>
                <div class="small text-white opacity-75">
                    <i class="fas fa-user-circle me-1"></i> Konsumen: <b><?= $spk_header->konsumen_nama ?></b>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <?php if ($isAllDone): ?>
                    <button
                        onclick="if(confirm('Semua item selesai. Kirim SPK ini ke Pengambilan Barang (PB)?')) window.location.href='<?= site_url('wip/kirim_pb_spk/' . $spk_header->nospk) ?>'"
                        class="btn btn-warning btn-sm rounded-pill fw-bold shadow-sm animate__animated animate__pulse animate__infinite">
                        <i class="fas fa-truck-loading me-1"></i> KIRIM KE PB
                    </button>
                <?php endif; ?>

                <button onclick="startAllItems('<?= $spk_header->nospk ?>')"
                    class="btn btn-primary btn-sm rounded-pill fw-bold shadow-sm">
                    <i class="fas fa-play-circle me-1"></i> Mulai Semua
                </button>
                <button onclick="finishAllItems('<?= $spk_header->nospk ?>')"
                    class="btn btn-success btn-sm rounded-pill fw-bold shadow-sm">
                    <i class="fas fa-check-double me-1"></i> Selesai Semua
                </button>
                <a href="<?= site_url('wip/guide_revisi') ?>" target="_blank"
                    class="btn btn-danger btn-sm rounded-pill fw-bold shadow-sm">
                    <i class="fas fa-book-open me-1"></i> Panduan Revisi
                </a>
                <a href="<?= site_url('wip/produksi') ?>" class="btn btn-light btn-sm rounded-pill fw-bold shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Items Loop -->
    <!-- Flat Table View for High Speed Operation -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3 text-uppercase small fw-bold text-muted border-0">Item Produksi</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted text-center border-0" width="100">
                            Qty</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted border-0">Status / Tahapan</th>
                        <th class="px-4 py-3 text-uppercase small fw-bold text-muted text-end border-0" width="200">
                            Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php foreach ($items as $index => $item): ?>
                        <?php
                        // Logic Active Step
                        $activeStep = null;
                        $nextStep = null;
                        if (!empty($item->routing_steps)) {
                            foreach ($item->routing_steps as $s) {
                                if ($s->status_produksi == '1') {
                                    $activeStep = $s;
                                    break;
                                }
                                if ($s->status_produksi == '0' && !$nextStep) {
                                    $nextStep = $s;
                                }
                            }
                        }

                        // Determine Action Button
                        $addAttr = 'style="transform: scale(1.1);"'; // Make button slightly larger
                        $actionBtn = '';
                        $stepLabel = '';

                        // Custom Action for Large Format (Category 2) in Proses Status - ONLY FOR PRINTING (STEP 1)
                        $isPrintingStep = ($activeStep && $activeStep->routing_urutan == 1);
                        if ($item->kategori_id == 2 && $item->status_produksi == 'proses' && $isPrintingStep) {
                            $stepLabel = '<div class="text-warning fw-bold small text-uppercase mb-1">SEDANG CETAK</div>
                                          <div class="fw-bold text-dark"><i class="fas fa-cog fa-spin text-warning me-1"></i> RIP AKTIF</div>';
                            $actionBtn = '<button ' . $addAttr . ' onclick="ProductionApp.finishItemIndividual(' . $item->produksi_id . ')" 
                                           class="btn btn-success btn-sm rounded-pill fw-900 shadow-sm px-4 py-2">
                                           <i class="fas fa-check me-1"></i> SELESAI
                                           </button>';
                        } elseif ($item->status_produksi == 'done') {
                            $stepLabel = '<span class="badge bg-success rounded-pill px-3"><i class="fas fa-check-double me-1"></i> SELESAI</span>';
                            // Tombol Revisi
                            $actionBtn = '<button onclick=\'reviseItem(' . $item->produksi_id . ', ' . json_encode($item->routing_steps) . ')\' class="btn btn-sm btn-outline-danger rounded-pill fw-bold shadow-sm opacity-75" title="Revisi Status"><i class="fas fa-undo me-1"></i> Revisi</button>';
                        } elseif ($activeStep) {
                            $stepLabel = '<div class="text-warning fw-bold small text-uppercase mb-1">SEDANG PROSES</div>
                                          <div class="fw-bold text-dark"><i class="fas fa-cog fa-spin text-warning me-1"></i> ' . $activeStep->routing_name . '</div>
                                          <div class="small text-muted">Oleh: ' . ($activeStep->operator_nama ?: '-') . '</div>';

                            $bomQty = (float) ($activeStep->step_bom_qty ?? 0);
                            $estQty = ($bomQty > 0) ? ($bomQty * $item->qty) : $item->qty;
                            $bomMatId = $activeStep->bom_bahan_id ?? 0;

                            $satuanStep = $activeStep->bom_bahan_satuan ?: $item->satuan_nama;
                            
                            $actionBtn = '<button ' . $addAttr . ' onclick="ProductionApp.advanceStep(' . $item->produksi_id . ', \'next\', \'' . addslashes($activeStep->routing_name) . '\', ' . $estQty . ', ' . $bomMatId . ', ' . (float) ($activeStep->qty_bahan_pakai ?? 0) . ', \'' . addslashes($satuanStep ?: 'Unit') . '\', ' . $item->kategori_id . ')" 
                                           class="btn btn-success btn-sm rounded-pill fw-bold shadow-sm px-4 py-2">
                                           <i class="fas fa-check me-1"></i> SELESAI
                                           </button>';
                        } elseif ($nextStep) {
                            // Check sequence
                            $sUrutan = $nextStep->routing_urutan;
                            $currentSeq = $item->current_step_urutan ?? 0;
                            $canStart = ($sUrutan == ($currentSeq + 1)) || (!$currentSeq && $sUrutan == 1);

                            if ($canStart) {
                                $stepLabel = '<div class="text-primary fw-bold small text-uppercase mb-1">ANTRIAN SELANJUTNYA</div>
                                              <div class="fw-bold text-dark">' . $nextStep->routing_name . '</div>';
                                $actionBtn = '<button ' . $addAttr . ' onclick="ProductionApp.advanceStep(' . $item->produksi_id . ', \'start\', \'' . addslashes($nextStep->routing_name) . '\', 0, 0, 0, \'Unit\', ' . $item->kategori_id . ')" 
                                              class="btn btn-primary btn-sm rounded-pill fw-bold shadow-sm px-4 py-2">
                                              <i class="fas fa-play me-1"></i> MULAI ' . strtoupper(htmlspecialchars($nextStep->routing_name)) . '
                                              </button>';
                            } else {
                                $stepLabel = '<div class="text-muted fw-bold small text-uppercase">MENUNGGU ANTRIAN</div>';
                                $actionBtn = '<button disabled class="btn btn-secondary btn-sm rounded-pill px-3"><i class="fas fa-lock me-1"></i> Locked</button>';
                            }
                        } elseif (empty($item->routing_steps) && $item->status_produksi !== 'done') {
                            // FALLBACK FOR NO ROUTING STEPS SET (Upgrade to allow Material Selection)
                            if ($item->status_produksi === 'proses') {
                                // Calculate estQty for fallback (Length if available, otherwise Qty)
                                $p = (float) $item->panjang;
                                $estQtyFallback = ($p > 0) ? $p : (float) $item->qty;

                                // Attempt to find first BOM material if routing is empty (to auto-select in dropdown)
                                $bomBahanIdFallback = 0;
                                $bomRes = $this->db->get_where('bom_lines', ['product_id' => $item->product_id])->row();
                                if ($bomRes)
                                    $bomBahanIdFallback = $bomRes->bahan_id;

                                $stepLabel = '<div class="text-warning fw-bold small text-uppercase mb-1">PROSES LANGSUNG</div>
                                              <div class="fw-bold text-dark"><i class="fas fa-cog fa-spin text-warning me-1"></i> PEMBUATAN</div>';
                                $szSat = addslashes($item->satuan_nama ?: 'Unit');
                                $actionBtn = '<button ' . $addAttr . ' onclick="ProductionApp.advanceStep(' . $item->produksi_id . ', \'next\', \'Pembuatan\', ' . $estQtyFallback . ', ' . $bomBahanIdFallback . ', 0, \'' . $szSat . '\', ' . $item->kategori_id . ')" 
                                               class="btn btn-success btn-sm rounded-pill fw-bold shadow-sm px-4 py-2">
                                               <i class="fas fa-check-double me-1"></i> SELESAI
                                               </button>';
                            } else {
                                $stepLabel = '<div class="text-primary fw-bold small text-uppercase mb-1">PROSES LANGSUNG</div>
                                              <div class="fw-bold text-muted">Tanpa Tuntunan Routing</div>';
                                $szSat = addslashes($item->satuan_nama ?: 'Unit');
                                $actionBtn = '<button ' . $addAttr . ' onclick="ProductionApp.advanceStep(' . $item->produksi_id . ', \'start\', \'Proses Produksi\', 0, 0, 0, \'' . $szSat . '\', ' . $item->kategori_id . ')" 
                                               class="btn btn-primary btn-sm rounded-pill fw-bold shadow-sm px-4 py-2">
                                               <i class="fas fa-play me-1"></i> MULAI LANGSUNG
                                               </button>';
                            }
                        }

                        if ($isAdmin) {
                            $actionBtn .= '<br><button onclick=\'adminEditItem(' . json_encode([
                                'produksi_id' => $item->produksi_id,
                                'product_nama' => $item->product_nama,
                                'qty' => $item->qty,
                                'satuan_nama' => $item->satuan_nama,
                                'panjang' => $item->panjang,
                                'lebar' => $item->lebar,
                                'catatan' => $item->catatan,
                                'steps' => $item->routing_steps
                            ]) . ')\' class="btn btn-link text-info btn-sm fw-bold mt-2" style="text-decoration:none"><i class="fas fa-edit me-1"></i> ADMIN EDIT</button>';
                        }

                        // Metadata string
                        $metaStr = "";
                        if ($item->val_ket_1)
                            $metaStr .= '<span class="badge bg-light text-dark border me-1">' . $item->label_ket_1 . ': ' . $item->val_ket_1 . '</span>';
                        if ($item->val_jenisprint)
                            $metaStr .= '<span class="badge bg-light text-dark border me-1">' . $item->val_jenisprint . '</span>';

                        $p = (float) $item->panjang;
                        $l = (float) $item->lebar;
                        $dimensi = "";
                        if ($p > 0 && $l > 0)
                            $dimensi = '<span class="text-muted ms-1">• ' . $p . ' x ' . $l . ' meter</span>';

                        // Grouping Key for Batch Actions
                        $bomBahanId = 0;
                        $bomBahanNama = 'Tanpa Bahan';
                        $bomP = 0;
                        $bomL = 0;
                        $mId = '0';
                        if (!empty($item->routing_steps)) {
                            foreach ($item->routing_steps as $step) {
                                if ($step->status_produksi == '1' && !empty($step->mesin_id)) {
                                    $mId = $step->mesin_id;
                                }
                                if (!empty($step->bom_bahan_id) && $bomBahanId == 0) {
                                    $bomBahanId = $step->bom_bahan_id;
                                    $bomBahanNama = $step->bom_bahan_nama;
                                    $bomP = (float) $step->bom_p;
                                    $bomL = (float) $step->bom_l;
                                }
                            }
                        }
                        $curBahan = $bomBahanNama;
                        $matKey = $mId . '|' . $curBahan;
                        ?>
                        <tr class="<?= $rowClass ?> tr-nesting-item" data-material="<?= htmlspecialchars($matKey) ?>">
                            <td class="d-none">
                                <input type="checkbox" class="check-item-nesting" data-id="<?= $item->produksi_id ?>"
                                    data-panjang="<?= $item->panjang ?>" data-lebar="<?= $item->lebar ?>"
                                    data-qty="<?= $item->qty ?>" data-bahan-id="<?= $bomBahanId ?>"
                                    data-bom-p="<?= $bomP ?>" data-bom-l="<?= $bomL ?>"
                                    data-status="<?= $item->status_produksi ?>"
                                    data-kategori-id="<?= $item->kategori_id ?>"
                                    data-step-urutan="<?= $item->current_step_urutan ?? 1 ?>">
                            </td>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                        style="width: 32px; height: 32px; min-width:32px">
                                        <?= $index + 1 ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark fs-6"><?= $item->product_nama ?></div>
                                        <div class="small text-muted mb-1"><?= $item->kategori_nama ?>     <?= $dimensi ?>
                                        </div>

                                        <!-- Instruksi Produksi Eksplisit -->
                                        <div class="mb-2">
                                            <?php if (isset($item->approve) && $item->approve == 2): ?>
                                                <span
                                                    class="badge bg-warning text-dark border border-warning shadow-sm px-2 py-1"><i
                                                        class="fas fa-flask me-1"></i> WAJIB SAMPEL DULU</span>
                                            <?php else: ?>
                                                <span
                                                    class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 shadow-sm px-2 py-1"><i
                                                        class="fas fa-print me-1"></i> CETAK SEMUA / LANGSUNG</span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="d-flex flex-wrap"><?= $metaStr ?></div>
                                        <?php if ($item->catatan): ?>
                                            <div class="text-danger small fst-italic mt-1"><i
                                                    class="fas fa-exclamation-circle me-1"></i> <?= $item->catatan ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center py-3">
                                <div class="fw-900 text-dark fs-5"><?= $item->qty ?></div>
                                <div class="small text-muted text-uppercase" style="font-size:0.65rem">
                                    <?= $item->satuan_nama ?>
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="mb-2">
                                    <?= $stepLabel ?>
                                </div>
                                <!-- Dynamic Routing Info -->
                                <?php if (!empty($item->routing_steps)): ?>
                                    <div class="mt-2 pt-2 border-top border-light border-opacity-50">
                                        <div class="d-flex flex-column gap-2" style="max-width: 200px;">
                                            <?php foreach ($item->routing_steps as $idx => $step): ?>
                                                <?php
                                                $statusText = 'ANTRI';
                                                $statusClass = 'text-muted';
                                                $icon = 'fa-circle';
                                                $bgClass = 'bg-light';
                                                if ($step->status_produksi == '1') {
                                                    $statusText = 'PROSES';
                                                    $statusClass = 'text-warning';
                                                    $icon = 'fa-cog fa-spin';
                                                    $bgClass = 'bg-warning bg-opacity-10';
                                                } elseif ($step->status_produksi == '2') {
                                                    $statusText = 'OK';
                                                    $statusClass = 'text-success';
                                                    $icon = 'fa-check-circle';
                                                    $bgClass = 'bg-success bg-opacity-10';
                                                }
                                                ?>
                                                <div class="d-flex align-items-center gap-2 p-1 px-2 rounded-pill <?= $bgClass ?>"
                                                    style="font-size: 0.65rem; border: 1px solid rgba(0,0,0,0.03)">
                                                    <i class="fas <?= $icon ?> <?= $statusClass ?>" style="width: 12px;"></i>
                                                    <div class="d-flex flex-column" style="line-height: 1.1;">
                                                        <span class="text-dark fw-bold text-uppercase"
                                                            style="letter-spacing: 0.02em;">
                                                            <?= $step->routing_name ?>
                                                        </span>
                                                        <?php if ($step->operator_nama): ?>
                                                            <span class="text-muted" style="font-size: 0.55rem; font-weight: 500;">
                                                                <i class="fas fa-user-edit me-1" style="font-size: 0.5rem;"></i>
                                                                <?= $step->operator_nama ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <span class="ms-auto fw-900 <?= $statusClass ?>" style="font-size: 0.6rem;">
                                                        <?= $statusText ?>
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <?= $actionBtn ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Revisi -->
<div class="modal fade" id="modalRevisi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered shadow-sm">
        <div class="modal-content border-0">
            <div class="modal-header bg-danger text-white border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="fas fa-undo me-2"></i> Revisi Item Produksi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <input type="hidden" id="revisi_produksi_id">
                <div class="mb-4">
                    <div class="display-6 text-danger mb-2"><i class="fas fa-exclamation-triangle"></i></div>
                    <p class="text-dark fw-bold mb-1">Kembalikan status item ke PROSES?</p>
                    <p class="text-muted small">
                        Gunakan fitur ini jika terjadi kesalahan atau perlu cetak ulang (reprint).<br>
                        Isi <b>0</b> jika hanya ingin mengulang proses tanpa membuang bahan baru.
                    </p>
                </div>

                <div class="px-4 mb-3">
                    <label class="form-label fw-bold text-dark mb-1 small text-uppercase">Mundur ke Tahap:</label>
                    <select id="revisi_target_step"
                        class="form-select border-danger border-opacity-25 fw-bold text-dark shadow-sm">
                        <!-- Populated by JS -->
                    </select>
                </div>

                <div class="px-4 mb-3">
                    <label class="form-label fw-bold text-dark mb-1 small text-uppercase">Jumlah Lembar Rijek</label>
                    <div class="input-group input-group shadow-sm">
                        <input type="number" id="revisi_qty"
                            class="form-control fw-bold text-center border-danger border-opacity-25" value="0" min="0">
                        <span
                            class="input-group-text border-danger border-opacity-25 bg-danger bg-opacity-10 text-danger fw-bold">Lembar</span>
                    </div>
                </div>

                <div class="px-4">
                    <label class="form-label fw-bold text-dark mb-1 small text-uppercase">Alasan Revisi /
                        Masalah</label>
                    <textarea id="revisi_alasan" class="form-control border-danger border-opacity-25 shadow-sm"
                        placeholder="Contoh: Salah laminasi / Potongan miring / Warna tidak sesuai" rows="2"></textarea>
                    <div class="form-text mt-2 text-danger small" style="font-size: 0.65rem;">
                        <i class="fas fa-info-circle me-1"></i> Wajib diisi untuk audit trail masalah produksi.
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-light bg-opacity-50">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                    data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnSubmitRevision" onclick="submitRevision()"
                    class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
                    Proses Revisi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Admin Edit (Super Edit) -->
<div class="modal fade" id="modalAdminEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-shield me-2"></i> Super Edit (Administrator)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="edit_produksi_id">

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">Produk (Info)</label>
                    <input type="text" id="edit_product_nama" class="form-control bg-light border-0 fw-bold" readonly>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold">Jumlah (Qty)</label>
                        <input type="number" id="edit_qty" class="form-control fw-bold border-info">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold">Satuan</label>
                        <input type="text" id="edit_satuan" class="form-control bg-light border-0" readonly>
                    </div>
                </div>

                <div class="row g-3 mb-3" id="edit_dimensi_row">
                    <div class="col-6">
                        <label class="form-label fw-bold small text-muted text-uppercase">Panjang (m)</label>
                        <input type="number" step="0.01" id="edit_panjang" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small text-muted text-uppercase">Lebar (m)</label>
                        <input type="number" step="0.01" id="edit_lebar" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">Catatan Produksi</label>
                    <textarea id="edit_catatan" class="form-control" rows="2"></textarea>
                </div>

                <hr>
                <h6 class="fw-bold mb-3"><i class="fas fa-history me-2"></i> Penyesuaian History Tahapan</h6>
                <div id="edit_steps_container" class="border rounded p-3 bg-light bg-opacity-50"
                    style="max-height: 300px; overflow-y: auto;">
                    <!-- Steps will be injected here -->
                </div>

                <div class="alert alert-warning border-0 small mt-3 mb-0">
                    <i class="fas fa-exclamation-circle me-1"></i> Perubahan ini akan langsung mengubah data master
                    produksi. <br>
                    Gunakan hanya untuk **koreksi kekeliruan input**.
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-light bg-opacity-50">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                    data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnSaveAdminEdit" onclick="saveAdminEdit()"
                    class="btn btn-info text-white rounded-pill px-4 fw-bold shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Load JS Logic & Modals -->
<?php $this->load->view('admin/wip/components/v_modals_produksi'); ?>
<script>
    const WIP_CONFIG = {
        baseUrl: '<?= site_url('wip/') ?>',
        siteUrl: '<?= site_url() ?>',
        csrfTokenName: '<?= $this->security->get_csrf_token_name() ?>',
        csrfHash: '<?= $this->security->get_csrf_hash() ?>',
        materials: <?= json_encode($bahan_list ?? []) ?>,
        operators: <?= json_encode($operators ?? []) ?>
    };
</script>
<!-- Load ProductionApp logic -->
<script src="<?= base_url('assets/js/wip_produksi.js?v=' . time()) ?>"></script>

<script>
    function startAllItems(nospk) {
        if (!confirm('Mulai semua item di SPK ini?')) return;

        const formData = new FormData();
        formData.append('nospk', nospk);
        formData.append(WIP_CONFIG.csrfTokenName, WIP_CONFIG.csrfHash);

        fetch(WIP_CONFIG.baseUrl + 'start_all_spk_items', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update CSRF for next request if needed, though reload handles it
                    WIP_CONFIG.csrfHash = data.csrfHash || WIP_CONFIG.csrfHash;
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Gagal: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan sistem.');
            });
    }
    function finishAllItems(nospk) {
        if (!confirm('Selesaikan semua item yang sedang PROSES di SPK ini?')) return;

        const formData = new FormData();
        formData.append('nospk', nospk);
        formData.append(WIP_CONFIG.csrfTokenName, WIP_CONFIG.csrfHash);

        fetch(WIP_CONFIG.baseUrl + 'finish_all_spk_items', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    WIP_CONFIG.csrfHash = data.csrfHash || WIP_CONFIG.csrfHash;
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Gagal: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan sistem.');
            });
    }

    function reviseItem(prodId, steps) {
        document.getElementById('revisi_produksi_id').value = prodId;
        document.getElementById('revisi_qty').value = 0;
        document.getElementById('revisi_alasan').value = '';

        const select = document.getElementById('revisi_target_step');
        select.innerHTML = '';

        if (steps && steps.length > 0) {
            // Sort by sequence to be safe
            steps.sort((a, b) => (parseInt(a.routing_urutan) || 0) - (parseInt(b.routing_urutan) || 0));

            steps.forEach(s => {
                // Allow reverting to any step that was DONE or is currently PROSES
                if (s.status_produksi == '2' || s.status_produksi == '1') {
                    const opt = document.createElement('option');
                    opt.value = s.detail_produksi_id;
                    opt.innerHTML = `TAHAP: ${s.routing_name.toUpperCase()}`;
                    if (s.status_produksi == '1') opt.selected = true; // Default to active step if any
                    select.appendChild(opt);
                }
            });

            // If no active step, default to selecting the last done step
            if (!select.value && select.options.length > 0) {
                select.options[select.options.length - 1].selected = true;
            }
        } else {
            const opt = document.createElement('option');
            opt.value = '0';
            opt.text = 'Default (Utama)';
            select.appendChild(opt);
        }

        const modalRev = new bootstrap.Modal(document.getElementById('modalRevisi'));
        modalRev.show();
    }

    function submitRevision() {
        const prodId = document.getElementById('revisi_produksi_id').value;
        const targetStepId = document.getElementById('revisi_target_step').value;
        const qty = document.getElementById('revisi_qty').value;
        const alasan = document.getElementById('revisi_alasan').value.trim();

        if (qty === '' || isNaN(qty) || qty < 0) {
            alert('Jumlah rijek tidak valid.');
            return;
        }

        if (!alasan) {
            alert('Silakan tuliskan alasan melakukan revisi!');
            document.getElementById('revisi_alasan').focus();
            return;
        }

        const btn = document.getElementById('btnSubmitRevision');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';
        btn.disabled = true;

        const fd = new FormData();
        fd.append('produksi_id', prodId);
        fd.append('target_step_id', targetStepId);
        fd.append('qty_revisi', qty);
        fd.append('alasan_revisi', alasan);
        fd.append(WIP_CONFIG.csrfTokenName, WIP_CONFIG.csrfHash);

        fetch(WIP_CONFIG.baseUrl + 'revise_item_process', {
            method: 'POST',
            body: fd
        })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Gagal merevisi item.');
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }
            })
            .catch(e => {
                console.error(e);
                alert('Error sistem.');
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            });
    }

    function adminEditItem(data) {
        document.getElementById('edit_produksi_id').value = data.produksi_id;
        document.getElementById('edit_product_nama').value = data.product_nama;
        document.getElementById('edit_qty').value = data.qty;
        document.getElementById('edit_satuan').value = data.satuan_nama || '';
        document.getElementById('edit_panjang').value = data.panjang || 0;
        document.getElementById('edit_lebar').value = data.lebar || 0;
        document.getElementById('edit_catatan').value = data.catatan || '';

        // Render Steps
        const container = document.getElementById('edit_steps_container');
        container.innerHTML = '';

        if (data.steps && data.steps.length > 0) {
            data.steps.forEach(s => {
                const stepDiv = document.createElement('div');
                stepDiv.className = 'mb-4 pb-3 border-bottom step-edit-row';
                stepDiv.dataset.id = s.detail_produksi_id;

                let matOptions = '<option value="">-- Tanpa Bahan --</option>';
                WIP_CONFIG.materials.forEach(m => {
                    const selected = (m.bahan_id == s.material_id) ? 'selected' : '';
                    matOptions += `<option value="${m.bahan_id}" ${selected}>${m.bahan_nama} (${m.satuan})</option>`;
                });

                let opOptions = '<option value="">-- Belum Di-set --</option>';
                WIP_CONFIG.operators.forEach(op => {
                    const selected = (op.karyawan_id == s.operator_id) ? 'selected' : '';
                    opOptions += `<option value="${op.karyawan_id}" ${selected}>${op.nama_lengkap}</option>`;
                });

                stepDiv.innerHTML = `
                    <div class="fw-bold text-primary mb-2 small text-uppercase">TAHAP: ${s.routing_name}</div>
                    <div class="mb-2">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="small text-muted mb-1">Bahan Digunakan</label>
                                <select class="form-select form-select-sm edit-step-material">${matOptions}</select>
                            </div>
                            <div class="col-6">
                                <label class="small text-muted mb-1">Petugas (Operator)</label>
                                <select class="form-select form-select-sm edit-step-operator">${opOptions}</select>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-4">
                            <label class="small text-muted mb-1">Bahan Pakai</label>
                            <input type="number" step="0.01" class="form-control form-control-sm edit-step-pakai" value="${s.qty_bahan_pakai || 0}">
                        </div>
                        <div class="col-4">
                            <label class="small text-muted mb-1">Bahan Rijek</label>
                            <input type="number" step="0.01" class="form-control form-control-sm edit-step-rusak" value="${s.qty_bahan_rusak || 0}">
                        </div>
                        <div class="col-4">
                            <label class="small text-muted mb-1">Produk Rusak</label>
                            <input type="number" class="form-control form-control-sm edit-step-missing" value="${s.qty_missing || 0}">
                        </div>
                    </div>
                `;
                container.appendChild(stepDiv);
            });

            // Initialize Select2 on the new elements
            $('.edit-step-operator, .edit-step-material').select2({
                dropdownParent: $('#modalAdminEdit'),
                theme: 'bootstrap-5',
                width: '100%'
            });
        } else {
            container.innerHTML = '<div class="text-center text-muted small py-3">Tidak ada data tahapan.</div>';
        }

        const modalEdit = new bootstrap.Modal(document.getElementById('modalAdminEdit'));
        modalEdit.show();
    }

    function saveAdminEdit() {
        const pid = document.getElementById('edit_produksi_id').value;
        const qty = document.getElementById('edit_qty').value;
        const p = document.getElementById('edit_panjang').value;
        const l = document.getElementById('edit_lebar').value;
        const catatan = document.getElementById('edit_catatan').value;

        // Collect Step Edits
        const stepRows = document.querySelectorAll('.step-edit-row');
        const stepData = [];
        stepRows.forEach(row => {
            stepData.push({
                id: row.dataset.id,
                material_id: row.querySelector('.edit-step-material').value,
                operator_id: row.querySelector('.edit-step-operator').value,
                pakai: row.querySelector('.edit-step-pakai').value,
                rusak: row.querySelector('.edit-step-rusak').value,
                missing: row.querySelector('.edit-step-missing').value
            });
        });

        if (!qty || qty <= 0) {
            alert('Qty harus lebih dari 0.');
            return;
        }

        const btn = document.getElementById('btnSaveAdminEdit');
        btn.disabled = true;
        btn.innerHTML = 'Menyimpan...';

        const fd = new FormData();
        fd.append('produksi_id', pid);
        fd.append('qty', qty);
        fd.append('panjang', p);
        fd.append('lebar', l);
        fd.append('catatan', catatan);
        fd.append('step_updates', JSON.stringify(stepData));
        fd.append(WIP_CONFIG.csrfTokenName, WIP_CONFIG.csrfHash);

        fetch(WIP_CONFIG.baseUrl + 'admin_save_item_edit', {
            method: 'POST',
            body: fd
        })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    location.reload();
                } else {
                    alert(res.message);
                    btn.disabled = false;
                    btn.innerHTML = 'Simpan Perubahan';
                }
            });
    }
</script>