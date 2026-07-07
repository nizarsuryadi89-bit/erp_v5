<?php $this->load->view('layout_admin/header_modul'); ?>

<link rel="stylesheet" href="<?= base_url('assets/css/wip_produksi.css') ?>">
<style>
    /* Handover Specific Styles */
    .aging-badge {
        font-size: 0.65rem;
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 900;
        letter-spacing: 0.5px;
    }
    .payment-status-pill {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }
    .btn-handover-main {
        background: #1a1c23;
        color: white;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        width: 100%;
    }
    .btn-handover-main:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        color: white;
    }
    .btn-handover-main:active {
        transform: scale(0.98);
    }
    .hover-rotate:hover i {
        transform: rotate(180deg);
    }
    /* Branch Differentiation */
    .branch-row {
        border-left: 5px solid rgba(0,0,0,0.1);
        transition: all 0.2s ease;
    }
    .branch-row:hover {
        background-color: rgba(0,0,0,0.02) !important;
    }
    .photo-preview-container {
        width: 100%;
        height: 200px;
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: #f8fafc;
        cursor: pointer;
        position: relative;
        transition: all 0.3s ease;
    }
    .photo-preview-container:hover {
        border-color: #3498db;
        background: #f1f5f9;
    }
    .photo-preview-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .photo-preview-container .placeholder {
        text-align: center;
        color: #94a3b8;
    }
    .photo-preview-container .placeholder i {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        display: block;
    }
    .thumb-handover {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        transition: transform 0.2s;
        border: 2px solid white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .thumb-handover:hover {
        transform: scale(1.2);
    }
    .photo-preview-container-compact {
        width: 100%;
        height: 110px;
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: #f1f5f9;
        cursor: pointer;
        position: relative;
        transition: all 0.2s ease;
    }
    .photo-preview-container-compact:hover {
        border-color: #3498db;
        background: #e2e8f0;
    }
    .photo-preview-container-compact .placeholder i {
        font-size: 1.5rem;
        margin-bottom: 4px;
        display: block;
    }
    /* DataTable Custom Theme overriding wip css if needed */
    .dataTables_wrapper .dataTables_length select {
        border-radius: 12px !important;
        padding-left: 12px !important;
        font-weight: 800 !important;
        border: 1px solid #e2e8f0 !important;
        margin-right: 10px;
    }
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 50px !important;
        padding: 8px 20px !important;
        border: 1px solid #e2e8f0 !important;
        font-weight: 700 !important;
        margin-left: 10px;
        transition: all 0.3s ease;
        background: #f8fafc;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        background: white;
        border-color: #3498db !important;
        box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.1) !important;
        width: 300px;
    }
    .dataTables_filter label {
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.75rem;
        color: #64748b;
    }
</style>

<?php
// Aging Helper function
function getAgingLabel($datetime)
{
    if (empty($datetime) || $datetime === '0000-00-00 00:00:00' || $datetime === '1970-01-01 00:00:00')
        return ['label' => 'BARU', 'class' => 'bg-info text-white'];
        
    try {
        $start = new DateTime($datetime);
        $now = new DateTime();
        $diff = $start->diff($now);
        
        $total_days = $diff->days; // 'days' gives total days elapsed

        if ($total_days > 3)
            return ['label' => $total_days . ' HARI+', 'class' => 'bg-danger text-white animate-pulse'];
        if ($total_days > 0)
            return ['label' => $total_days . ' HARI', 'class' => 'bg-warning text-dark'];
        if ($diff->h > 0 || $total_days > 0)
            return ['label' => $diff->h . ' JAM', 'class' => 'bg-success text-white'];
            
    } catch (Exception $e) {
        // Fallback if parsing fails
    }
    
    return ['label' => 'BARU', 'class' => 'bg-primary text-white'];
}

// Branch Identification Helper
function getBranchColor($id)
{
    $colors = [
        1 => 'bg-dark text-white',      // Pusat
        2 => 'bg-primary text-white',   // Cabang A
        3 => 'bg-success text-white',   // Cabang B
        4 => 'bg-warning text-dark',    // Cabang C
        5 => 'bg-info text-white',      // Cabang D
        6 => 'bg-danger text-white',    // Cabang E
    ];
    return isset($colors[$id]) ? $colors[$id] : 'bg-secondary text-white';
}

// Stats Calculation: Dual metrics (Total Units & SPK Groups)
$pbCount = 0;
$pbSpkCount = !empty($detail_pb) ? count($detail_pb) : 0;
if (!empty($detail_pb)) {
    foreach ($detail_pb as $pb) {
        $pbCount += (int) ($pb->total_items ?? 1);
    }
}

$hoCount = 0;
$filteredHandover = [];
$twoDaysAgo = strtotime('-2 days 00:00:00');

if (!empty($detail_handover)) {
    foreach ($detail_handover as $ho) {
        // Only include in list and stats if within 2 days
        if (strtotime($ho->max_update) >= $twoDaysAgo) {
            $filteredHandover[] = $ho;
            $hoCount += (int) ($ho->total_items ?? 1);
        }
    }
}
$detail_handover = $filteredHandover;
$hoSpkCount = count($detail_handover);

// Extract All Branches for Filtering (from master data)
$branches = [];
if (!empty($list_cabang)) {
    foreach ($list_cabang as $c) {
        $branches[$c->id_perusahaan] = $c->nama_perusahaan;
    }
} else {
    // Fallback logic if master data fails
    foreach (array_merge($detail_pb ?? [], $detail_handover ?? []) as $row) {
        if (!isset($branches[$row->perusahaan_id])) {
            $branches[$row->perusahaan_id] = $row->asal_cabang ?: 'PUSAT';
        }
    }
}
ksort($branches);
?>

<div class="app-content mt-3">
    <!-- Main Content Card -->
    <div class="report-section-card">
        <div class="section-header">
            <h5>
                <i class="fas fa-shipping-fast"></i> Pengambilan Barang (PB HUB)
                <?php
                $br_name = $this->session->userdata('nama_perusahaan');
                if ($br_name)
                    echo " <span class='badge bg-white text-dark ms-2'>[" . strtoupper($br_name) . "]</span>";
                ?>
            </h5>
            <div class="d-flex align-items-center gap-2">
                <button type="button" data-bs-toggle="modal" data-bs-target="#modalGuidePB"
                    class="btn btn-sm btn-info text-white rounded-pill px-3 fw-bold shadow-sm">
                    <i class="fas fa-question-circle me-1"></i> Panduan
                </button>
                <button onclick="location.reload()" class="btn btn-sm btn-light rounded-pill px-3 fw-bold">
                    <i class="fas fa-sync-alt me-1"></i> Refresh Data
                </button>
            </div>
        </div>

        <div class="d-none flex-column flex-md-row border-bottom w-100 animate__animated animate__fadeInDown">
            <div class="flex-fill bg-warning bg-opacity-10 px-4 py-3 d-flex align-items-center justify-content-between border-end border-warning border-opacity-25">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                        <i class="fas fa-box fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark"><?= number_format($pbCount) ?> ITEM SIAP DISERAHKAN</h6>
                        <p class="small mb-0 text-muted opacity-75">Total <?= $pbSpkCount ?> SPK menunggu serah terima di PB HUB.</p>
                    </div>
                </div>
            </div>

            <div class="flex-fill bg-success bg-opacity-10 px-4 py-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                        <i class="fas fa-check-double fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark"><?= number_format($hoCount) ?> ITEM SUDAH DISERAHKAN</h6>
                        <p class="small mb-0 text-muted opacity-75">Riwayat <?= $hoSpkCount ?> SPK diserahkan (2 hari terakhir).</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white px-4 py-3 border-bottom border-light d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2">
                <?php 
                $user_role_id = $this->session->userdata('level');
                // TGM ERP v4: Akses filter cabang diberikan ke semua role di PB Hub
                // agar petugas PB bisa mensortir barang titipan dari cabang lain
                $can_switch = true;
                if ($can_switch): 
                ?>
                <select id="branchFilter" class="form-select form-select-sm rounded-pill border-0 bg-light fw-bold" style="width: 200px;">
                    <option value="">Semua Cabang (Global)</option>
                    <?php foreach ($branches as $id => $name): ?>
                        <option value="<?= $name ?>"><?= strtoupper($name) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php endif; ?>

                <div class="d-flex align-items-center gap-2 bg-light rounded-pill px-3 py-1 border">
                    <input type="date" id="dateStart" class="form-control form-control-sm border-0 bg-transparent shadow-none p-0 fw-bold text-muted" style="width: 110px;" title="Mulai Tanggal" value="<?= date('Y-m-d', strtotime('-7 days')) ?>">
                    <span class="text-muted fw-bold">-</span>
                    <input type="date" id="dateEnd" class="form-control form-control-sm border-0 bg-transparent shadow-none p-0 fw-bold text-muted" style="width: 110px;" title="Sampai Tanggal" value="<?= date('Y-m-d') ?>">
                    <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-1" onclick="resetDateFilter()" title="Reset Tanggal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <ul class="nav nav-tabs-premium mb-0" id="pbTabs" role="tablist" style="border: none;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pb-tab" data-bs-toggle="tab" data-bs-target="#pb-content" type="button" role="tab">
                            <i class="fas fa-list me-1"></i> ANTRIAN <span class="badge bg-secondary bg-opacity-75 ms-1 rounded-pill" style="font-size: 0.7em; vertical-align: text-top;"><?= $pbSpkCount ?? count($detail_pb ?? []) ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="handover-tab" data-bs-toggle="tab" data-bs-target="#handover-content" type="button" role="tab">
                            <i class="fas fa-history me-1"></i> RIWAYAT <span class="badge bg-secondary bg-opacity-75 ms-1 rounded-pill" style="font-size: 0.7em; vertical-align: text-top;"><?= $hoSpkCount ?? count($detail_handover ?? []) ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tracking-tab" data-bs-toggle="tab" data-bs-target="#tracking-content" type="button" role="tab" onclick="let frm = document.getElementById('trackingIframe'); if(!frm.src || frm.src === window.location.href) frm.src = '<?= site_url('wip/produksi?iframe=1') ?>';">
                            <i class="fas fa-search-location me-1"></i> TRACKING SPK
                        </button>
                    </li>
                </ul>
            </div>

            <div>
                <button type="button" class="btn btn-sm btn-danger rounded-pill px-4 fw-bold shadow-sm" onclick="bulkHandover()">
                    <i class="fas fa-truck-loading me-1"></i> SERAHKAN SEKALIGUS
                </button>
            </div>
        </div>

        <div class="p-0">
            <div class="tab-content" id="pbTabsContent">
            <!-- TAB 1: ANTRIAN PENGAMBILAN -->
            <div class="tab-pane fade show active" id="pb-content">
                <div class="table-responsive">
                    <table class="table premium-table table-hover align-middle mb-0" id="tableDetailPB">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;" class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox" id="checkAllPB" style="width: 22px; height: 22px;">
                                            </div>
                                        </th>
                                        <th style="width: 100px;" class="text-center">TGL. PESAN</th>
                                        <th>IDENTITAS PESANAN</th>
                                        <th>PELANGGAN & PEMBAYARAN</th>
                                        <th>VERIFIKASI ITEM</th>
                                        <th class="text-center">AKSI FINAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (($detail_pb ?? []) as $d): ?>
                                        <?php 
                                        $aging = getAgingLabel($d->tgl_spk);
                                        $isPiutang = !empty($d->piutang_status) && $d->piutang_status != 'LUNAS';
                                        
                                        $payStatus = strtolower($d->status_pembayaran);
                                        
                                        if ($isPiutang) {
                                            $payStatus = 'piutang';
                                            $payLabel = 'PIUTANG';
                                            $payColor = 'bg-danger';
                                        } else {
                                            $payLabel = $payStatus == 'paid' ? 'LUNAS' : ($payStatus == 'pending' ? 'TUNDA' : 'BELUM DISET');
                                            $payColor = $payStatus == 'paid' ? 'bg-success' : ($payStatus == 'pending' ? 'bg-danger' : 'bg-warning');
                                        }
                                        $branchColor = getBranchColor($d->perusahaan_id);
                                        $isInternal = ($d->perusahaan_id == $this->session->userdata('perusahaan_id'));
                                        ?>
                                        <tr class="<?= !$isInternal ? 'branch-row' : '' ?>" 
                                            data-branch="<?= strtoupper($d->asal_cabang ?: 'PUSAT') ?>"
                                            data-date="<?= date('Y-m-d', strtotime($d->tgl_spk ?? date('Y-m-d'))) ?>">
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input check-item-pb" type="checkbox" 
                                                           data-id="<?= (int) $d->produksi_id ?>" 
                                                           data-nospk="<?= $d->nospk ?>" 
                                                           data-pay="<?= $payStatus ?>"
                                                           style="width: 20px; height: 20px;">
                                                </div>
                                            </td>
                                            <td class="text-center align-middle" data-label="TGL. PESAN" data-order="<?= strtotime($d->tgl_spk ?? date('Y-m-d H:i:s')) ?>">
                                                <div class="fw-900 text-dark" style="font-size: 0.75rem;">
                                                    <?= date('d/m/Y', strtotime($d->tgl_spk ?? date('Y-m-d H:i:s'))) ?>
                                                </div>
                                                <div class="text-muted fw-bold mb-1" style="font-size: 0.65rem;">
                                                    <?= date('H:i', strtotime($d->tgl_spk ?? date('Y-m-d H:i:s'))) ?>
                                                </div>
                                                <?php if (!empty($d->tgl_kirim_pb) && $d->tgl_kirim_pb !== '0000-00-00 00:00:00'): ?>
                                                    <?php $agingPb = getAgingLabel($d->tgl_kirim_pb); ?>
                                                    <span class="badge <?= $agingPb['class'] ?>" style="font-size: 0.55rem; padding: 3px 5px;" title="Masuk PB: <?= date('d/m/Y H:i', strtotime($d->tgl_kirim_pb)) ?>">
                                                        <i class="fas fa-history me-1"></i><?= $agingPb['label'] ?> DI PB
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td data-label="INFO SPK">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="bg-light p-2 rounded-3 text-center cursor-pointer hover-scale border"
                                                        style="min-width: 80px;" onclick="verifySPK('<?= $d->nospk ?>')">
                                                        <div class="fw-900 text-primary">#<?= $d->nospk ?></div>
                                                        <div style="font-size: 0.5rem;" class="text-muted fw-bold mt-1"><i
                                                                class="fas fa-search me-1"></i> VERIFIKASI</div>
                                                    </div>

                                                    <div class="p-2 border rounded-3 <?= $branchColor ?> shadow-sm" style="line-height: 1.2; min-width: 100px;">
                                                        <div style="font-size: 0.55rem;" class="fw-900 ls-1 opacity-75">
                                                            <i class="fas <?= $isInternal ? 'fa-home' : 'fa-building' ?> me-1"></i> 
                                                            <?= $isInternal ? 'INTERNAL' : 'CABANG' ?>
                                                        </div>
                                                        <div style="font-size: 0.7rem;" class="fw-900 text-uppercase text-truncate">
                                                            <?= $d->asal_cabang ?: 'PUSAT' ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td data-label="PELANGGAN">
                                                <div class="fw-900 text-dark"><?= strtoupper($d->konsumen_nama) ?></div>
                                                <div class="mt-1 d-flex flex-column align-items-start gap-1">
                                                    <span
                                                        class="badge rounded-pill <?= $payStatus == 'paid' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> px-3 border py-1 fw-900">
                                                        <span class="payment-status-pill <?= $payColor ?>"></span>
                                                        <?= $payLabel ?>
                                                    </span>
                                                    <?php if (!empty($d->nama_jenis_transaksi)): ?>
                                                        <?php 
                                                            $is_marketplace = (strtoupper($d->nama_jenis_transaksi) === 'MARKETPLACE' && !empty($d->marketplace_nama));
                                                            $display_metode = $is_marketplace ? $d->marketplace_nama : $d->nama_jenis_transaksi;
                                                            $icon = 'fa-shopping-cart';
                                                            $bg_class = 'bg-info';
                                                            $border_class = 'border-info';
                                                            $text_class = 'text-info-emphasis';
                            
                                                            if ($is_marketplace) {
                                                                $mn = strtolower($display_metode);
                                                                if (strpos($mn, 'shopee') !== false) {
                                                                    $icon = 'fa-shopping-bag';
                                                                    $bg_class = 'bg-danger';
                                                                    $border_class = 'border-danger';
                                                                    $text_class = 'text-danger';
                                                                } else if (strpos($mn, 'toko') !== false || strpos($mn, 'tokopedia') !== false) {
                                                                    $icon = 'fa-store';
                                                                    $bg_class = 'bg-success';
                                                                    $border_class = 'border-success';
                                                                    $text_class = 'text-success';
                                                                } else if (strpos($mn, 'tiktok') !== false) {
                                                                    $icon = 'fa-music';
                                                                    $bg_class = 'bg-dark';
                                                                    $border_class = 'border-dark';
                                                                    $text_class = 'text-dark';
                                                                } else if (strpos($mn, 'lazada') !== false) {
                                                                    $icon = 'fa-heart';
                                                                    $bg_class = 'bg-primary';
                                                                    $border_class = 'border-primary';
                                                                    $text_class = 'text-primary';
                                                                } else {
                                                                    $icon = 'fa-store-alt';
                                                                }
                                                            } else if (strtoupper($d->nama_jenis_transaksi) === 'ONLINE') {
                                                                $icon = 'fa-globe';
                                                                $bg_class = 'bg-primary';
                                                                $border_class = 'border-primary';
                                                                $text_class = 'text-primary';
                                                            } else if (stripos($d->nama_jenis_transaksi, 'EMAIL') !== false || stripos($d->nama_jenis_transaksi, 'WA') !== false) {
                                                                $icon = 'fa-envelope';
                                                                $bg_class = 'bg-secondary';
                                                                $border_class = 'border-secondary';
                                                                $text_class = 'text-secondary';
                                                            }
                                                        ?>
                                                        <span class="badge <?= $bg_class ?> bg-opacity-10 <?= $text_class ?> border <?= $border_class ?> border-opacity-25 shadow-sm fw-900 text-uppercase" style="font-size: 0.65rem;">
                                                            <i class="fas <?= $icon ?> me-1"></i> <?= $display_metode ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-dark border border-dark border-opacity-25 shadow-sm fw-900 text-uppercase" style="font-size: 0.65rem;">
                                                            <i class="fas fa-store me-1"></i> OFFLINE
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td data-label="CEK JUMLAH">
                                                <div class="d-flex flex-column gap-1">
                                                    <?php if (!empty($d->breakdown)): ?>
                                                        <!-- Breakdown View untuk UX Lebih Jelas -->
                                                        <?php foreach ($d->breakdown as $bd): ?>
                                                            <div
                                                                class="d-flex justify-content-between align-items-center bg-light px-2 py-1 rounded-3 border">
                                                                <div class="small fw-800 text-dark lh-1">
                                                                    <div class="text-truncate"
                                                                        style="max-width: 120px; font-size: 0.7rem;">
                                                                        <?= $bd->product_nama ?>
                                                                    </div>
                                                                    <div class="text-muted" style="font-size: 0.65rem;">
                                                                        <?= number_format($bd->total_qty, 0) ?>
                                                                        <?= strtoupper($bd->satuan_nama) ?>
                                                                    </div>
                                                                </div>
                                                                <div
                                                                    class="small fw-900 <?= $bd->total_ready >= $bd->total_qty ? 'text-success' : 'text-warning' ?>">
                                                                    <i
                                                                        class="fas <?= $bd->total_ready >= $bd->total_qty ? 'fa-check-double' : 'fa-spinner' ?> me-1 opacity-50"></i><?= number_format($bd->total_ready, 0) ?>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <!-- Fallback Legacy -->
                                                        <div class=" small fw-900 text-primary mb-1 ls-1">ITEM:
                                                            <?= $d->total_items ?> PRODUK
                                                        </div>
                                                        <div class="small fw-800 text-muted mb-1 ls-1">ORD:
                                                            <?= number_format($d->total_qty, 0) ?>
                                                        </div>
                                                        <div class="fs-5 fw-900 text-dark ls-1">SIAP:
                                                            <?= number_format($d->total_qty_output, 0) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="text-center" data-label="AKSI">
                                                <button
                                                    onclick="handover(this, <?= (int) $d->produksi_id ?>, '<?= $d->nospk ?>', <?= (float) $d->total_qty_output ?>, '<?= $payStatus ?>', '<?= addslashes(strtoupper($d->konsumen_nama)) ?>', '<?= addslashes(strtoupper($d->asal_cabang ?: 'PUSAT')) ?>', '<?= !empty($d->tgl_spk) ? date('d/m/Y', strtotime($d->tgl_spk)) : date('d/m/Y') ?>', '<?= !empty($d->nama_jenis_transaksi) ? addslashes(strtoupper($is_marketplace ? $d->marketplace_nama : $d->nama_jenis_transaksi)) : 'OFFLINE' ?>')"
                                                    class="btn btn-handover-main shadow-sm">
                                                    SERAHKAN <i class="fas fa-handshake ms-2"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
            </div>

            <!-- TAB 2: RIWAYAT (ARSIP) -->
            <div class="tab-pane fade" id="handover-content">
                <div class="table-responsive">
                    <table class="table premium-table table-hover align-middle mb-0" id="tableDetailHandover">
                                <thead>
                                    <tr>
                                        <th>WAKTU</th>
                                        <th>NO SPK</th>
                                        <th>KONSUMEN</th>
                                        <th>LOGISTIK / KURIR</th>
                                        <th class="text-center">BUKTI</th>
                                        <th class="text-center">STATUS</th>
                                        <th class="text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (($detail_handover ?? []) as $d): ?>
                                        <?php 
                                        $branchColor = getBranchColor($d->perusahaan_id);
                                        $isInternal = ($d->perusahaan_id == $this->session->userdata('perusahaan_id'));
                                        ?>
                                        <tr class="<?= !$isInternal ? 'branch-row' : '' ?>" data-branch="<?= strtoupper($d->asal_cabang ?: 'PUSAT') ?>" data-date="<?= date('Y-m-d', strtotime($d->max_update)) ?>">
                                            <td data-label="WAKTU" data-order="<?= strtotime($d->max_update) ?>">
                                                <div class="small fw-bold text-muted"><?= date('d/m/Y', strtotime($d->max_update)) ?></div>
                                                <div class="fw-900 text-dark"><?= date('H:i', strtotime($d->max_update)) ?></div>
                                            </td>
                                            <td data-label="SPK">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="fw-900 text-primary cursor-pointer"
                                                        onclick="verifySPK('<?= $d->nospk ?>')">#<?= $d->nospk ?></div>
                                                    
                                                    <span class="badge <?= $branchColor ?> rounded-pill" style="font-size: 0.6rem; padding: 4px 8px;">
                                                        <?= strtoupper($d->asal_cabang ?: 'PUSAT') ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td data-label="KONSUMEN">
                                                <div class="fw-900 text-dark"><?= strtoupper($d->konsumen_nama) ?></div>
                                                <div class="mt-1">
                                                    <?php if (!empty($d->nama_jenis_transaksi)): ?>
                                                        <?php 
                                                            $is_marketplace = (strtoupper($d->nama_jenis_transaksi) === 'MARKETPLACE' && !empty($d->marketplace_nama));
                                                            $display_metode = $is_marketplace ? $d->marketplace_nama : $d->nama_jenis_transaksi;
                                                            $icon = 'fa-shopping-cart';
                                                            $bg_class = 'bg-info';
                                                            $border_class = 'border-info';
                                                            $text_class = 'text-info-emphasis';
                            
                                                            if ($is_marketplace) {
                                                                $mn = strtolower($display_metode);
                                                                if (strpos($mn, 'shopee') !== false) {
                                                                    $icon = 'fa-shopping-bag';
                                                                    $bg_class = 'bg-danger';
                                                                    $border_class = 'border-danger';
                                                                    $text_class = 'text-danger';
                                                                } else if (strpos($mn, 'toko') !== false || strpos($mn, 'tokopedia') !== false) {
                                                                    $icon = 'fa-store';
                                                                    $bg_class = 'bg-success';
                                                                    $border_class = 'border-success';
                                                                    $text_class = 'text-success';
                                                                } else if (strpos($mn, 'tiktok') !== false) {
                                                                    $icon = 'fa-music';
                                                                    $bg_class = 'bg-dark';
                                                                    $border_class = 'border-dark';
                                                                    $text_class = 'text-dark';
                                                                } else if (strpos($mn, 'lazada') !== false) {
                                                                    $icon = 'fa-heart';
                                                                    $bg_class = 'bg-primary';
                                                                    $border_class = 'border-primary';
                                                                    $text_class = 'text-primary';
                                                                } else {
                                                                    $icon = 'fa-store-alt';
                                                                }
                                                            } else if (strtoupper($d->nama_jenis_transaksi) === 'ONLINE') {
                                                                $icon = 'fa-globe';
                                                                $bg_class = 'bg-primary';
                                                                $border_class = 'border-primary';
                                                                $text_class = 'text-primary';
                                                            } else if (stripos($d->nama_jenis_transaksi, 'EMAIL') !== false || stripos($d->nama_jenis_transaksi, 'WA') !== false) {
                                                                $icon = 'fa-envelope';
                                                                $bg_class = 'bg-secondary';
                                                                $border_class = 'border-secondary';
                                                                $text_class = 'text-secondary';
                                                            }
                                                        ?>
                                                        <span class="badge <?= $bg_class ?> bg-opacity-10 <?= $text_class ?> border <?= $border_class ?> border-opacity-25 shadow-sm fw-900 text-uppercase" style="font-size: 0.65rem;">
                                                            <i class="fas <?= $icon ?> me-1"></i> <?= $display_metode ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-dark border border-dark border-opacity-25 shadow-sm fw-900 text-uppercase" style="font-size: 0.65rem;">
                                                            <i class="fas fa-store me-1"></i> OFFLINE
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td data-label="LOG PENYEDIAAN">
                                                <div
                                                    class="p-2 bg-light rounded-3 shadow-sm border-start border-primary border-3">
                                                    <div class="small text-dark fw-bold" style="white-space: pre-wrap;">
                                                        <?= $d->catatan ?: '<span class="text-muted italic opacity-50">Tidak ada log catatan</span>' ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center" data-label="FOTO">
                                                <?php if ($d->foto_handover): ?>
                                                    <img src="<?= base_url('uploads/handover/' . $d->foto_handover) ?>" 
                                                         class="thumb-handover" 
                                                         onclick="showLargePhoto('<?= base_url('uploads/handover/' . $d->foto_handover) ?>')">
                                                <?php else: ?>
                                                    <span class="text-muted small">Tanpa Foto</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center" data-label="STATUS">
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-4 py-2 rounded-pill fw-900">
                                                    <i class="fas fa-check-circle me-1"></i> SELESAI
                                                </span>
                                            </td>
                                            <td class="text-center" data-label="AKSI">
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger rounded-pill px-3 fw-900 shadow-sm"
                                                        onclick="reproduceRijek('<?= $d->nospk ?>', '<?= addslashes($d->konsumen_nama) ?>')">
                                                    <i class="fas fa-redo-alt me-1"></i> RIJEK
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
            <!-- TAB 3: TRACKING SPK -->
            <div class="tab-pane fade" id="tracking-content">
                <div class="p-0 bg-white" style="height: 80vh;">
                    <iframe id="trackingIframe" src="" style="width: 100%; height: 100%; border: none;"></iframe>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        const tablePB = $('#tableDetailPB').DataTable({
            responsive: true,
            dom: '<"d-flex justify-content-between align-items-center p-4 border-bottom"<"l-wrapper"l><"f-wrapper"f>>rt<"d-flex justify-content-between align-items-center p-4"<"i-wrapper"i><"p-wrapper"p>>',
            language: {
                search: "CARI ANTRIAN:",
                lengthMenu: "TAMPIL _MENU_ BARIS",
                info: "MENAMPILKAN _START_ - _END_ DARI _TOTAL_ DATA",
                paginate: {
                    previous: '<i class="fas fa-chevron-left text-muted"></i>',
                    next: '<i class="fas fa-chevron-right text-muted"></i>'
                }
            },
            order: [[1, 'desc']]
        });

        const tableHO = $('#tableDetailHandover').DataTable({
            responsive: true,
            dom: '<"d-flex justify-content-between align-items-center p-4 border-bottom"<"l-wrapper"l><"f-wrapper"f>>rt<"d-flex justify-content-between align-items-center p-4"<"i-wrapper"i><"p-wrapper"p>>',
            language: {
                search: "CARI RIWAYAT:",
                lengthMenu: "TAMPIL _MENU_ BARIS",
                info: "MENAMPILKAN _START_ - _END_ DARI _TOTAL_ DATA",
                paginate: {
                    previous: '<i class="fas fa-chevron-left text-muted"></i>',
                    next: '<i class="fas fa-chevron-right text-muted"></i>'
                }
            },
            order: [[0, 'desc']]
        });

        // Filter Cabang & Tanggal (DataTable Custom Filter)
        $.fn.dataTable.ext.search.push(
            function (settings, data, dataIndex) {
                // 1. Branch Filter
                const branchFilterElem = $('#branchFilter');
                const selectedBranch = branchFilterElem.length ? branchFilterElem.val().toUpperCase() : "";
                
                // 2. Date Filter
                const start = $('#dateStart').val();
                const end = $('#dateEnd').val();
                
                const row = settings.nTable.id === "tableDetailPB" 
                    ? tablePB.row(dataIndex).node() 
                    : tableHO.row(dataIndex).node();
                
                const branchValue = $(row).data('branch') || "";
                const rowDate = $(row).data('date') || ""; // Format: YYYY-MM-DD

                // Branch logic
                if (selectedBranch !== "" && branchValue !== selectedBranch) return false;

                // Date logic
                if (start !== "" && rowDate < start) return false;
                if (end !== "" && rowDate > end) return false;

                return true;
            }
        );

        $('#branchFilter, #dateStart, #dateEnd').on('change', function () {
            tablePB.draw();
            tableHO.draw();
        });

        // Trigger initial filter on page load
        tablePB.draw();
        tableHO.draw();

        // Logika Check All
        $('#checkAllPB').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('.check-item-pb').prop('checked', isChecked);
        });

    function resetDateFilter() {
        // Reset ke default 7 hari terakhir
        let today = new Date();
        let lastWeek = new Date(today);
        lastWeek.setDate(lastWeek.getDate() - 7);
        
        let formatDate = (date) => {
            let d = date.getDate().toString().padStart(2, '0');
            let m = (date.getMonth() + 1).toString().padStart(2, '0');
            let y = date.getFullYear();
            return `${y}-${m}-${d}`;
        };

        $('#dateStart').val(formatDate(lastWeek));
        $('#dateEnd').val(formatDate(today));
        
        $('#tableDetailPB').DataTable().draw();
        $('#tableDetailHandover').DataTable().draw();
    }

        // Logika Barcode Scanner
        $('#barcodeScanner').focus();
        $('#barcodeScanner').on('keypress', function (e) {
            if (e.which == 13) { processManualBarcode(); }
        });
        $(document).keydown(function (e) {
            if (e.altKey && e.keyCode == 83) { // Alt + S
                $('#barcodeScanner').focus();
                e.preventDefault();
            }
        });
    });

    function bulkHandover() {
        const selectedItems = $('.check-item-pb:checked');
        
        if (selectedItems.length === 0) {
            Swal.fire({
                title: 'PILIH DATA',
                text: 'Silakan ceklis minimal satu SPK dalam antrian untuk melakukan serah terima massal.',
                icon: 'info',
                confirmButtonText: 'MENGERTI',
                customClass: { confirmButton: 'btn btn-primary rounded-pill px-4' },
                buttonsStyling: false
            });
            return;
        }

        const items = [];
        const nospks = [];
        let unpaidFound = false;

        selectedItems.each(function() {
            const nospk = $(this).data('nospk');
            const id = $(this).data('id');
            const payStatus = $(this).data('pay');
            
            items.push({ id, nospk, payStatus });
            if (!nospks.includes(nospk)) nospks.push(nospk);
            if (payStatus !== 'paid') unpaidFound = true;
        });

        // Modal 1: Ringkasan & Konfirmasi
        let warningHtml = `<div class="p-3 bg-light rounded-4 border text-start">
            <h6 class="fw-900 text-dark mb-1"><i class="fas fa-layer-group me-2 text-primary"></i>RINGKASAN PILIHAN</h6>
            <hr class="my-2 opacity-50">
            <div class="d-flex justify-content-between mb-2">
                <span class="small fw-bold text-muted">TOTAL SPK DIPILIH:</span>
                <span class="fw-900 text-primary fs-5">${nospks.length} Pesanan</span>
            </div>`;
                
        if (unpaidFound) {
            warningHtml += `<div class="alert alert-danger py-2 px-3 mt-3 border-0 small fw-bold">
                <i class="fas fa-exclamation-triangle me-2"></i>PERHATIAN: Ada pesanan yang BELUM LUNAS dalam daftar ini!
            </div>`;
        }
        warningHtml += `</div>`;

        Swal.fire({
            title: 'KONFIRMASI MASSAL',
            html: warningHtml,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'LANJUTKAN KE PENGIRIMAN',
            cancelButtonText: 'BATAL',
            customClass: {
                confirmButton: 'btn btn-primary rounded-pill px-4 py-2 me-2',
                cancelButton: 'btn btn-light rounded-pill px-4 py-2'
            },
            buttonsStyling: false
        }).then((confirmRes) => {
            if (confirmRes.isConfirmed) {
                // Modal 3: Detail Handover
                Swal.fire({
                    title: 'DETAIL SERAH TERIMA MASSAL',
                    width: '850px',
                    html: `
                        <div class="container-fluid px-0 text-start">
                            <div class="row g-3">
                                <div class="col-md-5 border-end pe-3">
                                    <div class="p-3 bg-light rounded-4 border mb-3">
                                        <h6 class="fw-900 text-dark mb-2 small"><i class="fas fa-layer-group me-2 text-primary"></i>RINGKASAN MASSAL</h6>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="small fw-bold text-muted">TOTAL SPK:</span>
                                            <span class="fw-900 text-primary small">${nospks.length} Pesanan</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="small fw-bold text-muted">TOTAL ITEM:</span>
                                            <span class="fw-900 text-primary small">${items.length} Produk</span>
                                        </div>
                                    </div>
                                    
                                    <div class="p-3 bg-primary bg-opacity-10 rounded-4 border border-primary border-opacity-25 shadow-sm text-center">
                                        <label class="info-label mb-2 d-block small fw-bold text-primary text-uppercase"><i class="fas fa-key me-2"></i>PIN PETUGAS (WAJIB)</label>
                                        <input type="password" id="bulk-pb-pin" class="form-control border-0 shadow-sm rounded-4 p-2 fs-3 text-center fw-bold" placeholder="****" maxlength="4" inputmode="numeric" style="letter-spacing: 0.5rem; background: white;">
                                    </div>
                                </div>
                                <div class="col-md-7 ps-3">
                                    <label class="info-label mb-2 d-block small fw-bold text-muted text-uppercase">PENGAMBILAN VIA</label>
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <input type="radio" class="btn-check" name="bulk-via" id="bvia1" value="Diambil Langsung" checked>
                                            <label class="btn btn-outline-primary w-100 rounded-3 fw-bold small py-2" for="bvia1">LANGSUNG</label>
                                        </div>
                                        <div class="col-6">
                                            <input type="radio" class="btn-check" name="bulk-via" id="bvia2" value="Kurir / Ojek Online">
                                            <label class="btn btn-outline-primary w-100 rounded-3 fw-bold small py-2" for="bvia2">OJEK/KURIR</label>
                                        </div>
                                    </div>
                                    
                                    <div class="row g-2">
                                        <div class="col-7">
                                            <label class="info-label mb-2 d-block small fw-bold text-muted text-uppercase">CATATAN MASSAL</label>
                                            <textarea id="bulk-note" class="form-control border-0 shadow-sm rounded-4 p-2 small" rows="5" placeholder="Penerima / Resi..."></textarea>
                                        </div>
                                        <div class="col-5 text-center">
                                            <label class="info-label mb-2 d-block small fw-bold text-muted text-uppercase">FOTO BUKTI</label>
                                            <div class="photo-preview-container-compact m-0" onclick="document.getElementById('bulk-photo').click()" style="height: 125px;">
                                                <div class="placeholder" id="bulk-photo-placeholder">
                                                    <i class="fas fa-camera"></i>
                                                    <p class="mb-0 fw-bold small">AMBIL FOTO</p>
                                                </div>
                                                <img id="bulk-photo-preview" class="d-none w-100 h-100 object-fit-cover rounded-3">
                                                <input type="file" id="bulk-photo" class="d-none" accept="image/*" capture="environment" onchange="previewHandoverPhoto(this, 'bulk-photo-preview', 'bulk-photo-placeholder')">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'SINKRONKAN SEMUA',
                    cancelButtonText: 'BATAL',
                    customClass: {
                        confirmButton: 'btn btn-handover-main py-3',
                        cancelButton: 'btn btn-light rounded-pill px-4 py-2 mt-2'
                    },
                    buttonsStyling: false,
                    preConfirm: () => {
                        const pin = $('#bulk-pb-pin').val();
                        if (!pin || pin.length < 4) {
                            Swal.showValidationMessage('Harap masukkan 4 digit PIN Petugas!');
                            return false;
                        }
                        return {
                            via: $('input[name="bulk-via"]:checked').val(),
                            note: $('#bulk-note').val(),
                            photo: $('#bulk-photo')[0].files[0],
                            pin: pin
                        };
                    }
                }).then((res) => {
                    if (res.isConfirmed) {
                        Swal.fire({ title: 'MEMPROSES MASSAL...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                        
                        const formData = new FormData();
                        items.forEach(i => formData.append('ids[]', i.id));
                        nospks.forEach(spk => formData.append('nospks[]', spk));
                        formData.append('pengambilan_via', res.value.via);
                        formData.append('pengambilan_note', res.value.note);
                        formData.append('pin', res.value.pin);
                        if (res.value.photo) {
                            formData.append('foto_handover', res.value.photo);
                        }

                        $.ajax({
                            url: '<?= site_url('Pb/bulk_handover_to_customer') ?>',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            dataType: 'json',
                            success: (resp) => {
                                if (resp && resp.success) {
                                    Swal.fire({ icon: 'success', title: 'BERHASIL', text: resp.message, timer: 1500, showConfirmButton: false }).then(() => location.reload());
                                } else {
                                    Swal.fire('GAGAL', resp.message || 'Terjadi kesalahan sistem', 'error');
                                }
                            },
                            error: (xhr, status, error) => {
                                Swal.fire('GAGAL', 'Terjadi kesalahan koneksi atau server: ' + error, 'error');
                            }
                        });
                    }
                });
            }
        });
    }

    function processManualBarcode() {
        let nospk = $('#barcodeScanner').val().trim();
        if (!nospk) return;
        $('#barcodeScanner').val(''); // Bersihkan segera

        processScanResult(nospk);
    }

    function processScanResult(nospk) {
        let found = false;
        let targetRow = null;

        // Cari di Tabel PB
        $('#tableDetailPB tbody tr').each(function () {
            let rowText = $(this).text().toUpperCase();
            if (rowText.includes(nospk.toUpperCase())) {
                found = true;
                targetRow = $(this);
                return false;
            }
        });

        if (found) {
            targetRow.addClass('bg-primary bg-opacity-10');
            $('html, body').animate({ scrollTop: targetRow.offset().top - 200 }, 500);
            targetRow.find('.btn-handover-main').click();
        } else {
            Swal.fire({
                icon: 'info',
                title: 'SPK TIDAK DITEMUKAN',
                text: 'SPK #' + nospk + ' tidak ada dalam antrian PB. Cek status produksinya?',
                showCancelButton: true,
                confirmButtonText: 'CEK STATUS',
                cancelButtonText: 'TUTUP'
            }).then((r) => { if (r.isConfirmed) verifySPK(nospk); });
        }
    }

    function verifySPK(nospk) {
        Swal.fire({
            title: '<i class="fas fa-search-plus me-2 text-primary"></i> VERIFIKASI RINCIAN SPK #' + nospk,
            html: '<div id="verify-spinner" class="p-5 text-center"><i class="fas fa-spinner fa-spin fa-3x text-primary opacity-25"></i><p class="mt-3 fw-bold text-muted">Menarik data produksi...</p></div><div id="verify-content"></div>',
            width: '900px',
            showConfirmButton: false,
            showCloseButton: true,
            customClass: { popup: 'rounded-4 border-0 shadow-lg' },
            didOpen: () => {
                $.ajax({
                    url: '<?= site_url('Pb/get_pb_verify_details_ajax') ?>',
                    type: 'POST',
                    data: { nospk: nospk },
                    dataType: 'json',
                    success: function (resp) {
                        $('#verify-spinner').hide();
                        if (resp.success && resp.items.length > 0) {
                            let html = `
                                <div class="text-start p-2">
                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="ps-3 py-3 fw-900 text-muted small" style="width:40%">NAMA PRODUK</th>
                                                    <th class="py-3 fw-900 text-muted small text-center">PESANAN</th>
                                                    <th class="py-3 fw-900 text-muted small text-center">OUTPUT</th>
                                                    <th class="pe-3 py-3 fw-900 text-muted small">STATUS ALUR</th>
                                                </tr>
                                            </thead>
                                            <tbody>`;

                            resp.items.forEach(item => {
                                let flowHtml = '';
                                item.flow.forEach(f => {
                                    let statusColor = f.status_produksi == '4' ? 'bg-success' : (f.status_produksi == '3' ? 'bg-primary' : 'bg-secondary opacity-50');
                                    flowHtml += `<span class="badge ${statusColor} me-1" style="font-size:0.55rem; padding: 2px 6px;" title="${f.routing_name}">${f.routing_name.charAt(0)}</span>`;
                                });

                                let editBtn = '';
                                // Check if user level is authorized (1, 17, 21)
                                const userLevel = parseInt('<?= $this->session->userdata("level") ?>');
                                if ([1, 17, 21].includes(userLevel) && item.last_detail_id) {
                                    editBtn = `<button class="btn btn-sm btn-outline-primary ms-2 p-1 border-0" style="font-size: 0.7rem;" onclick="editOutputQty(${item.last_detail_id}, ${item.qty_final}, '${nospk}')" title="Koreksi Output"><i class="fas fa-pencil-alt"></i></button>`;
                                }

                                html += `
                                    <tr class="border-bottom">
                                        <td class="ps-3 py-3">
                                            <div class="fw-900 text-dark">${item.product_nama}</div>
                                            <div class="text-muted small fw-bold mt-1">ID Prod: #${item.produksi_id}</div>
                                        </td>
                                        <td class="text-center py-3 fw-900 text-muted small">${parseInt(item.qty).toLocaleString('id-ID')}</td>
                                        <td class="text-center py-3 fw-900 text-primary">
                                            ${parseInt(item.qty_final).toLocaleString('id-ID')}
                                            ${editBtn}
                                        </td>
                                        <td class="pe-3 py-3">${flowHtml}</td>
                                    </tr>`;
                            });

                            html += `</tbody></table></div>
                                    <div class="mt-4 p-3 bg-primary bg-opacity-10 rounded-4 border border-primary border-opacity-25">
                                        <div class="d-flex align-items-center gap-3">
                                            <i class="fas fa-info-circle text-primary fs-4"></i>
                                            <div class="small fw-bold text-primary">Pastikan seluruh item produk di atas telah lengkap sesuai jumlah OUTPUT sebelum diserahkan kepada kurir atau konsumen.</div>
                                        </div>
                                    </div>
                                </div>`;
                            $('#verify-content').html(html);
                        } else {
                            $('#verify-content').html('<div class="p-5 text-center text-danger">Data tidak ditemukan atau terjadi kesalahan.</div>');
                        }
                    },
                    error: function (xhr, status, error) {
                        $('#verify-spinner').hide();
                        $('#verify-content').html('<div class="p-5 text-center text-danger">Terjadi kesalahan pada server: ' + error + '</div>');
                    }
                });
            }
        });
    }

    function editOutputQty(detail_id, current_qty, nospk) {
        Swal.fire({
            title: 'KOREKSI JUMLAH OUTPUT',
            text: 'Masukkan jumlah output yang benar untuk item ini:',
            input: 'number',
            inputValue: current_qty,
            showCancelButton: true,
            confirmButtonText: 'SIMPAN PERUBAHAN',
            cancelButtonText: 'BATAL',
            customClass: {
                confirmButton: 'btn btn-primary px-4 py-2 rounded-3 me-2',
                cancelButton: 'btn btn-light px-4 py-2 rounded-3'
            },
            buttonsStyling: false,
            inputValidator: (value) => {
                if (!value || value < 0) {
                    return 'Jumlah tidak valid!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url('Pb/update_output_qty_ajax') ?>',
                    type: 'POST',
                    data: {
                        detail_id: detail_id,
                        qty: result.value
                    },
                    dataType: 'json',
                    success: function (resp) {
                        if (resp.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'BERHASIL',
                                text: resp.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                verifySPK(nospk); // Refresh modal
                            });
                        } else {
                            Swal.fire('GAGAL', resp.message || 'Gagal memperbarui database.', 'error');
                        }
                    },
                    error: function (xhr, status, error) {
                        Swal.fire('GAGAL', 'Terjadi kesalahan koneksi atau server: ' + error, 'error');
                    }
                });
            }
        });
    }

    function handover(btn, id, nospk, qty, payStatus, konsumen, cabang, deadline, metodePemesanan) {
        // SAFETY FIRST: Praktik Terbaik ERP
        if (payStatus !== 'paid') {
            Swal.fire({
                title: 'PERINGATAN PEMBAYARAN!',
                text: "SPK ini belum berstatus LUNAS. Pastikan pembayaran sudah diselesaikan atau ada kesepakatan tertulis sebelum menyerahkan barang.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#1a1c23',
                confirmButtonText: 'TETAP LANJUT',
                cancelButtonText: 'BATAL'
            }).then((result) => {
                if (result.isConfirmed) { launchHandoverFlow(btn, nospk, qty, konsumen, cabang, deadline, payStatus, metodePemesanan); }
            });
        } else {
            launchHandoverFlow(btn, nospk, qty, konsumen, cabang, deadline, payStatus, metodePemesanan);
        }
    }

    function launchHandoverFlow(btn, nospk, qty, konsumen, cabang, deadline, payStatus, metodePemesanan) {
        // Tahap 1: Ambil item yang sedang on_pb
        Swal.fire({
            title: 'MENYIAPKAN ITEM...',
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: '<?= site_url('Pb/get_pb_verify_details_ajax') ?>',
            type: 'POST',
            data: { nospk: nospk },
            dataType: 'json',
            success: function (resp) {
                if (!resp.success || !resp.items || resp.items.length === 0) {
                    Swal.fire('Kesalahan', resp.message || 'Gagal memuat item siap.', 'error');
                    return;
                }

                // Filter item yang benar-benar on_pb
                let readyItems = resp.items;

                let totalItems = 0;
                let totalOutput = 0;
                let itemsHtml = ``;

                readyItems.forEach(item => {
                    if (item.status_produksi === 'on_pb') {
                        totalItems++;
                        totalOutput += parseInt(item.qty_final) || 0;
                        
                        let sisiText = '';
                        let sisiVal = item.sisi || '';
                        
                        if (item.metadata) {
                            try {
                                let meta = JSON.parse(item.metadata);
                                if (!sisiVal) sisiVal = meta.sisi || meta.ket_1 || '';
                            } catch (e) {}
                        }
                        
                        if (sisiVal) {
                            let sLower = String(sisiVal).toLowerCase().trim();
                            if (sLower === '1 side' || sLower === '1' || sLower === '1 sisi') {
                                sisiText = ' (1 Sisi)';
                            } else if (sLower === '2 side' || sLower === '2' || sLower === '2 sisi') {
                                sisiText = ' (2 Sisi)';
                            }
                        }
                        
                        let displayNama = item.product_nama + sisiText;

                        itemsHtml += `
                            <label class="struk-item-row" style="cursor: pointer; display: flex; align-items: center; margin-bottom: 4px;">
                                <input type="checkbox" name="prod_ids" value="${item.produksi_id}" checked style="margin-right: 5px;">
                                <div style="flex-grow: 1; display: flex; justify-content: space-between;">
                                    <span style="max-width: 350px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${displayNama}">☑ ${displayNama}</span>
                                    <span>Pesan:${item.qty} &nbsp;Output:${item.qty_final} ☑</span>
                                </div>
                            </label>`;
                    }
                });

                Swal.fire({
                    title: 'DOKUMEN SERAH TERIMA',
                    width: '750px',
                    html: `
<style>
.struk-container {
    font-family: 'Courier New', Courier, monospace;
    color: #000;
    background: #fdfdfd;
    padding: 20px;
    border: 1px dashed #000;
    text-align: left;
    font-size: 14px;
    width: 100%;
}
.struk-header {
    text-align: center;
    border-bottom: 1px dashed #000;
    padding-bottom: 10px;
    margin-bottom: 15px;
    font-weight: bold;
    font-size: 18px;
    letter-spacing: 1px;
}
.struk-section {
    border-bottom: 1px dashed #000;
    padding-bottom: 15px;
    margin-bottom: 15px;
}
.struk-table {
    width: 100%;
    border-collapse: collapse;
}
.struk-table td {
    vertical-align: top;
    padding: 3px 0;
}
.radio-struk {
    display: flex;
    gap: 8px;
    align-items: center;
    margin-bottom: 6px;
    cursor: pointer;
}
.input-struk {
    background: transparent;
    border: none;
    font-family: 'Courier New', Courier, monospace;
    width: 100%;
    outline: none;
    font-weight: bold;
}
.input-struk:focus {
    background: rgba(0,0,0,0.05);
}
.btn-handover-struk {
    background: transparent;
    color: #000;
    border: 1px dashed #000;
    padding: 10px 20px;
    font-weight: bold;
    cursor: pointer;
    text-transform: uppercase;
    font-family: 'Courier New', Courier, monospace;
    width: 100%;
    letter-spacing: 2px;
    transition: all 0.2s;
    font-size: 16px;
}
.btn-handover-struk:hover {
    background: #000;
    color: #fff;
}
.photo-struk {
    height: 60px;
    background: transparent;
    border: 1px dashed #000;
    color: #000;
    border-radius: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}
</style>

<div class="struk-container">
    <div class="struk-header">SERAH TERIMA BARANG</div>
    
    <div class="struk-section">
        <table class="struk-table">
            <tr><td width="130">SPK</td><td>: #${nospk}</td></tr>
            <tr><td>Pelanggan</td><td>: ${konsumen}</td></tr>
            <tr><td>Cabang</td><td>: ${cabang}</td></tr>
            <tr><td>Asal Pesanan</td><td>: <b>${metodePemesanan || 'OFFLINE'}</b></td></tr>
            <tr><td>Status Bayar</td><td>: ${payStatus == 'paid' ? '☑ LUNAS' : '☐ BELUM LUNAS'}</td></tr>
            <tr><td>Deadline</td><td>: ${deadline}</td></tr>
        </table>
    </div>

    <div class="struk-section">
        <div style="margin-bottom: 10px; font-weight: bold;">VERIFIKASI ITEM</div>
        <div style="border-bottom: 1px dashed #000; margin-bottom: 10px;"></div>
        ${itemsHtml}
        <div style="border-top: 1px dashed #000; margin-top: 10px; padding-top: 10px;">
            <div style="display: flex; align-items: center;">
                <span style="width: 130px;">STATUS</span>
                <span>: 🟢 SEMUA ITEM LENGKAP</span>
            </div>
        </div>
    </div>

    <div class="struk-section">
        <table class="struk-table">
            <tr>
                <td width="50%" style="border-right: 1px dashed #000; padding-right: 15px;">
                    <div style="font-weight: bold; margin-bottom: 10px;">PENGAMBILAN VIA</div>
                    <div style="border-bottom: 1px dashed #000; margin-bottom: 10px;"></div>
                    <label class="radio-struk"><input type="radio" name="via" value="Diambil Langsung" checked onchange="document.getElementById('lblMetode').innerText='Ambil Langsung'"> Ambil Langsung</label>
                    <label class="radio-struk"><input type="radio" name="via" value="Kurir / Ojek Online" onchange="document.getElementById('lblMetode').innerText='Kurir'"> Kurir</label>
                </td>
                <td style="padding-left: 15px;">
                    <div style="font-weight: bold; margin-bottom: 10px;">PENERIMA</div>
                    <div style="border-bottom: 1px dashed #000; margin-bottom: 10px;"></div>
                    <div style="margin-bottom: 2px;">Nama Penerima</div>
                    <div style="display: flex; margin-bottom: 10px;">[ <input type="text" id="swal-note" class="input-struk" placeholder="${konsumen}"> ]</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="struk-section">
        <table class="struk-table">
            <tr>
                <td width="50%" style="border-right: 1px dashed #000; padding-right: 15px;">
                    <div style="font-weight: bold; margin-bottom: 10px;">FOTO BUKTI</div>
                    <div style="border-bottom: 1px dashed #000; margin-bottom: 10px;"></div>
                    <div class="photo-struk" onclick="openWebcamStruk('photo-preview', 'photo-placeholder', 'handover-photo')">
                        <div id="photo-placeholder">
                            <span class="fw-bold">[ Live Cam / File ]</span>
                        </div>
                        <img id="photo-preview" class="d-none w-100 h-100 object-fit-cover">
                        <input type="file" id="handover-photo" class="d-none" accept="image/*" capture="environment" onchange="previewHandoverPhoto(this, 'photo-preview', 'photo-placeholder')">
                    </div>
                </td>
                <td style="padding-left: 15px;">
                    <div style="font-weight: bold; margin-bottom: 10px;">VALIDASI PETUGAS</div>
                    <div style="border-bottom: 1px dashed #000; margin-bottom: 10px;"></div>
                    <div style="margin-bottom: 2px;">PIN PETUGAS</div>
                    <div style="display: flex;">[ <input type="password" id="single-pb-pin" class="input-struk text-center" placeholder="* * * *" maxlength="4" inputmode="numeric" style="letter-spacing: 5px;"> ]</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="struk-section" style="border-bottom: none; margin-bottom: 0;">
        <div style="font-weight: bold; margin-bottom: 10px;">RINGKASAN</div>
        <div style="border-bottom: 1px dashed #000; margin-bottom: 10px;"></div>
        <table class="struk-table">
            <tr><td width="150">Total Item</td><td>: ${totalItems}</td></tr>
            <tr><td>Total Output</td><td>: ${totalOutput}</td></tr>
            <tr><td>Metode</td><td>: <span id="lblMetode">Ambil Langsung</span></td></tr>
            <tr><td>Status Verifikasi</td><td>: 100% Lengkap</td></tr>
        </table>
    </div>
</div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'VALIDASI & SERAHKAN',
                    cancelButtonText: 'BATAL',
                    customClass: {
                        confirmButton: 'btn btn-primary btn-handover-main py-3 px-5 mt-2',
                        cancelButton: 'btn btn-light rounded-pill px-4 fw-bold mt-2'
                    },
                    buttonsStyling: false,
                    preConfirm: () => {
                        const via = document.querySelector('input[name="via"]:checked').value;
                        const note = document.getElementById('swal-note').value;
                        const prodIds = Array.from(document.querySelectorAll('input[name="prod_ids"]:checked')).map(cb => cb.value);
                        const photo = document.getElementById('handover-photo').files[0];
                        const pin = document.getElementById('single-pb-pin').value;

                        if (prodIds.length === 0) {
                            Swal.showValidationMessage('Harap pilih minimal satu item!');
                            return false;
                        }
                        if (!pin || pin.length < 4) {
                            Swal.showValidationMessage('Harap masukkan 4 digit PIN Petugas!');
                            return false;
                        }

                        return { via: via, note: note, prodIds: prodIds, photo: photo, pin: pin };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; }
                        
                        const formData = new FormData();
                        formData.append('nospk', nospk);
                        formData.append('pengambilan_via', result.value.via);
                        formData.append('pengambilan_note', result.value.note);
                        formData.append('pin', result.value.pin);
                        result.value.prodIds.forEach(id => formData.append('produksi_ids[]', id));
                        if (result.value.photo) {
                            formData.append('foto_handover', result.value.photo);
                        }

                        $.ajax({
                            url: '<?= site_url('Pb/handover_to_customer') ?>',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            dataType: 'json',
                            success: function (resp) {
                                if (resp && resp.success) {
                                    Swal.fire({ icon: 'success', title: 'SERAH TERIMA BERHASIL', text: resp.message, showConfirmButton: false, timer: 1500 }).then(() => location.reload());
                                } else {
                                    Swal.fire('GAGAL', resp.message || 'Terjadi kesalahan saat menyimpan data.', 'error');
                                    if (btn) { btn.disabled = false; btn.innerHTML = 'SERAHKAN <i class="fas fa-handshake ms-2"></i>'; }
                                }
                            },
                            error: function (xhr, status, error) {
                                Swal.fire('GAGAL', 'Terjadi kesalahan koneksi atau server: ' + error, 'error');
                                if (btn) { btn.disabled = false; btn.innerHTML = 'SERAHKAN <i class="fas fa-handshake ms-2"></i>'; }
                            }
                        });
                    }
                });
            }
        });
    }

    function previewSPK(nospk) {
        $('#lblNospkPreview').text(nospk);
        $('#iframePreview').attr('src', '<?= site_url('transaksi/preview_invoice/') ?>' + nospk);
        $('#modalPreviewSPK').modal('show');
    }

    function previewTag(id) {
        $('#iframePreviewTag').attr('src', '<?= site_url('Pb/print_tag/') ?>' + id);
        $('#modalPreviewTag').modal('show');
    }

    function previewHandoverPhoto(input, imgId, placeholderId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#' + imgId).attr('src', e.target.result).removeClass('d-none');
                $('#' + placeholderId).addClass('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    let currentWebcamStream = null;
    function openWebcamStruk(imgId, placeholderId, fileInputId) {
        Swal.fire({
            title: 'AMBIL FOTO BUKTI',
            html: `
                <div style="position: relative; width: 100%; max-width: 400px; margin: 0 auto; background: #000; border-radius: 8px; overflow: hidden;">
                    <video id="webcam-video" autoplay playsinline style="width: 100%; display: block;"></video>
                    <canvas id="webcam-canvas" style="display: none;"></canvas>
                </div>
                <div class="mt-3">
                    <button type="button" class="btn btn-outline-secondary w-100 fw-bold" onclick="document.getElementById('${fileInputId}').click(); Swal.close();">
                        <i class="fas fa-folder-open me-2"></i> ATAU PILIH DARI GALERI / FILE
                    </button>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-camera"></i> POTRET SEKARANG',
            cancelButtonText: 'BATAL',
            customClass: {
                confirmButton: 'btn btn-primary py-3 px-4 fw-bold mt-2',
                cancelButton: 'btn btn-light rounded-pill px-4 fw-bold mt-2'
            },
            buttonsStyling: false,
            didOpen: () => {
                const video = document.getElementById('webcam-video');
                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    // Try to get back camera on mobile, or just any camera on desktop
                    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                        .then(function(stream) {
                            currentWebcamStream = stream;
                            video.srcObject = stream;
                        })
                        .catch(function(error) {
                            console.error("Camera error:", error);
                            Swal.showValidationMessage('Kamera tidak dapat diakses. Pastikan browser memiliki izin kamera.');
                        });
                } else {
                    Swal.showValidationMessage('Browser Anda tidak mendukung akses kamera langsung.');
                }
            },
            willClose: () => {
                if (currentWebcamStream) {
                    currentWebcamStream.getTracks().forEach(track => track.stop());
                    currentWebcamStream = null;
                }
            },
            preConfirm: () => {
                const video = document.getElementById('webcam-video');
                const canvas = document.getElementById('webcam-canvas');
                if (!currentWebcamStream || video.videoWidth === 0) {
                    Swal.showValidationMessage('Kamera belum siap atau tidak ditemukan.');
                    return false;
                }
                
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                return new Promise((resolve) => {
                    canvas.toBlob((blob) => {
                        resolve(blob);
                    }, 'image/jpeg', 0.8); // Compress slightly for faster upload
                });
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                const blob = result.value;
                const url = URL.createObjectURL(blob);
                
                // Tampilkan preview di kotak struk
                $('#' + imgId).attr('src', url).removeClass('d-none');
                $('#' + placeholderId).addClass('d-none');
                
                // Konversi Blob hasil jepretan menjadi File lalu pasang di input file asli
                const file = new File([blob], "webcam_snap_" + Date.now() + ".jpg", { type: "image/jpeg" });
                
                // Gunakan DataTransfer (didukung mayoritas browser modern) untuk mengisi FileList
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                document.getElementById(fileInputId).files = dataTransfer.files;
                
                // Trigger event change agar sama seperti pilih file normal
                $('#' + fileInputId).trigger('change');
            }
        });
    }

    function showLargePhoto(src) {
        Swal.fire({
            imageUrl: src,
            imageWidth: '100%',
            imageAlt: 'Bukti Handover',
            showCloseButton: true,
            showConfirmButton: false,
            customClass: {
                image: 'rounded-4 shadow-lg'
            }
        });
    }

    function reproduceRijek(nospk, konsumen) {
        Swal.fire({
            title: '<h5 class="fw-900 text-danger mb-0 ls-n1">KEMBALIKAN KE PRODUKSI</h5>',
            html: `
                <div class="p-3 text-start bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-4 mb-3">
                    <p class="small fw-bold text-danger mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i> PERHATIAN: Tindakan ini akan mengembalikan SPK #${nospk} 
                        (${konsumen}) ke Antrian Produksi. Seluruh riwayat pengerjaan produksi akan di-reset (kecuali Deskprint, Admin, Kasir).
                    </p>
                </div>
                <div class="text-start">
                    <label class="small fw-900 text-muted ls-1 mb-2">ALASAN PENGEMBALIAN (MANDATORY)</label>
                    <textarea id="rijek-reason" class="form-control border-0 shadow-sm rounded-4 p-3 mb-3" rows="3" placeholder="Contoh: Warna pudar, Salah potong, atau Item kurang..."></textarea>
                    
                    <div class="bg-primary bg-opacity-10 rounded-4 border border-primary border-opacity-25 p-3 text-center">
                        <label class="small fw-900 text-primary ls-1 mb-2"><i class="fas fa-key me-2"></i>PIN ADMIN / SUPERVISOR</label>
                        <input type="password" id="rijek-pin" class="form-control border-0 shadow-sm rounded-4 p-2 fs-3 text-center fw-bold" placeholder="****" maxlength="4" inputmode="numeric" style="letter-spacing: 0.5rem; background: white;">
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'KEMBALIKAN KE PRODUKSI',
            cancelButtonText: 'BATAL',
            customClass: {
                confirmButton: 'btn btn-danger btn-lg px-5 py-3 fw-bold mt-2',
                cancelButton: 'btn btn-light btn-lg px-4 fw-bold mt-2'
            },
            buttonsStyling: false,
            width: '550px',
            preConfirm: () => {
                const reason = $('#rijek-reason').val();
                const pin = $('#rijek-pin').val();
                
                if (!reason) {
                    Swal.showValidationMessage('Harap isi alasan pengembalian!');
                    return false;
                }
                if (!pin || pin.length < 4) {
                    Swal.showValidationMessage('PIN Petugas wajib 4 digit!');
                    return false;
                }
                return { nospk, reason, pin };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ 
                    title: 'MENGEMBALIKAN KE PRODUKSI...', 
                    allowOutsideClick: false, 
                    didOpen: () => { Swal.showLoading(); } 
                });

                $.ajax({
                    url: '<?= site_url('Pb/reproduce_rijek_ajax') ?>',
                    type: 'POST',
                    data: result.value,
                    dataType: 'json',
                    success: function(resp) {
                        if (resp.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'DIKEMBALIKAN KE PRODUKSI',
                                html: `<div class="p-4 rounded-4 bg-light">
                                            <p class="mb-2 fw-bold text-muted">NOMOR SPK YANG DI-RESET:</p>
                                            <h2 class="fw-900 text-danger ls-n1">#${resp.new_nospk}</h2>
                                            <p class="mt-3 small text-muted">Seluruh status tahap produksi (kecuali pra-cetak) telah dikembalikan ke dalam <b>Antrian Operator</b>.</p>
                                       </div>`,
                                confirmButtonText: 'TUTUP',
                                customClass: { confirmButton: 'btn btn-primary rounded-pill px-5' },
                                buttonsStyling: false
                            }).then(() => {
                                // Pindah ke tab antrian
                                $('#pb-tab').click();
                                location.reload();
                            });
                        } else {
                            Swal.fire('GAGAL', resp.message || 'Error tidak diketahui', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('GAGAL', 'Internal Server Error: ' + error, 'error');
                    }
                });
            }
        });
    }
</script>

<!-- MODALS: UKURAN TABLET PREMIUM -->
<!-- MODAL SOP GUIDE -->
<div class="modal fade" id="modalGuidePB" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg rounded-4 overflow-hidden border-0">
            <div class="modal-header bg-dark text-white p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-warning rounded-circle p-2 me-3">
                        <i class="fas fa-book-open text-dark"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-900 ls-1 mb-0">PANDUAN PENGAMBILAN BARANG (PB)</h5>
                        <small class="text-white-50 fw-bold">Standar Operasional Prosedur Serah Terima & Logistik WIP</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-light">
                <div class="p-4 p-lg-5">
                    <!-- Filosopi -->
                    <div class="row mb-5">
                        <div class="col-12">
                            <div class="bg-white rounded-4 p-4 shadow-sm border-start border-5 border-primary">
                                <div class="row align-items-center">
                                    <div class="col-md-9">
                                        <h4 class="fw-900 text-dark mb-3"><i class="fas fa-lightbulb text-warning me-2"></i>Filosofi PB: "Fisik vs Pembayaran"</h4>
                                        <p class="fw-600 text-muted mb-0">
                                            Tim PB bertanggung jawab atas <strong>BARANG FISIK</strong> yang ada di lokasi Anda, tidak peduli dari cabang mana order itu berasal.
                                            Jika barangnya ada di gudang saya, sayalah yang menyerahkannya.
                                        </p>
                                    </div>
                                    <div class="col-md-3 text-center border-start d-none d-md-block">
                                        <i class="fas fa-boxes fa-3x text-primary opacity-25"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="h-100 bg-white rounded-4 p-4 shadow-sm border border-opacity-10 position-relative pt-5">
                                <div class="position-absolute top-0 start-0 ms-4 mt-n3 bg-primary text-white fw-900 rounded-3 px-3 py-2 shadow-sm" style="margin-top: -20px;">01</div>
                                <div class="bg-light rounded-3 p-3 d-inline-block mb-3"><i class="fas fa-search-location text-primary fs-4"></i></div>
                                <h5 class="fw-900 text-dark mb-2">Identifikasi & Filter</h5>
                                <p class="small text-muted mb-0">Pisahkan Milik Sendiri vs Titipan Cabang. Gunakan filter "Asal Cabang" untuk grouping pengiriman internal.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="h-100 bg-white rounded-4 p-4 shadow-sm border border-opacity-10 position-relative pt-5">
                                <div class="position-absolute top-0 start-0 ms-4 mt-n3 bg-primary text-white fw-900 rounded-3 px-3 py-2 shadow-sm" style="margin-top: -20px;">02</div>
                                <div class="bg-light rounded-3 p-3 d-inline-block mb-3"><i class="fas fa-barcode text-primary fs-4"></i></div>
                                <h5 class="fw-900 text-dark mb-2">Scan & Verifikasi</h5>
                                <p class="small text-muted mb-0">Pindai Barcode SPK/Tag. Periksa jumlah <strong>OUTPUT</strong> vs Pesanan. Supervisor bisa koreksi jumlah jika ada salah input.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="h-100 bg-white rounded-4 p-4 shadow-sm border border-opacity-10 position-relative pt-5">
                                <div class="position-absolute top-0 start-0 ms-4 mt-n3 bg-primary text-white fw-900 rounded-3 px-3 py-2 shadow-sm" style="margin-top: -20px;">03</div>
                                <div class="bg-light rounded-3 p-3 d-inline-block mb-3"><i class="fas fa-handshake text-primary fs-4"></i></div>
                                <h5 class="fw-900 text-dark mb-2">Eksekusi Serah Terima</h5>
                                <p class="small text-muted mb-0">Pilih kurir (Ojek Online/Langsung). Wajib isi Nama Penerima/Resi dan lampirkan Foto Bukti.</p>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-5 g-4">
                        <div class="col-md-6">
                            <div class="bg-warning bg-opacity-10 rounded-4 p-4 h-100 border border-warning border-opacity-25">
                                <h5 class="fw-900 text-dark mb-3"><i class="fas fa-exchange-alt me-2 text-warning"></i>SOP KHUSUS: TRANSFER CABANG</h5>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="bg-white p-3 rounded-3 shadow-sm border-0 small fw-bold mb-2"><span class="badge bg-warning text-dark me-2">1</span>Pilih Opsi "Kurir / Ekspedisi"</div>
                                        <div class="bg-white p-3 rounded-3 shadow-sm border-0 small fw-bold mb-2"><span class="badge bg-warning text-dark me-2">2</span>Isi Nama Driver Internal di Catatan</div>
                                        <div class="bg-white p-3 rounded-3 shadow-sm border-0 small fw-bold"><span class="badge bg-warning text-dark me-2">3</span>Wajib Cetak Surat Jalan (Manifest)</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-primary bg-opacity-10 rounded-4 p-4 h-100 border border-primary border-opacity-25">
                                <h5 class="fw-900 text-dark mb-3"><i class="fas fa-database me-2 text-primary"></i>KEBIJAKAN MANAJEMEN DATA</h5>
                                <p class="small fw-bold text-muted mb-3">Untuk menjaga performa sistem tetap optimal:</p>
                                <ul class="list-group list-group-flush bg-transparent">
                                    <li class="list-group-item bg-transparent border-0 px-0 py-1 small fw-bold text-dark">
                                        <i class="fas fa-history text-primary me-2"></i> Tab <strong>RIWAYAT</strong> hanya menampilkan data 2 HARI TERKHIR.
                                    </li>
                                    <li class="list-group-item bg-transparent border-0 px-0 py-1 small fw-bold text-dark">
                                        <i class="fas fa-info-circle text-primary me-2"></i> Data lama tetap tersimpan permanen di database pusat.
                                    </li>
                                    <li class="list-group-item bg-transparent border-0 px-0 py-1 small fw-bold text-dark">
                                        <i class="fas fa-search-plus text-primary me-2"></i> Gunakan modul "Laporan Penjualan" untuk arsip lama.
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white p-4">
                <button type="button" class="btn btn-light rounded-pill px-5 fw-bold" data-bs-dismiss="modal">TUTUP</button>
                <a href="<?= site_url('Pb/guide') ?>" target="_blank" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                    <i class="fas fa-external-link-alt me-2"></i> BUKA VERSI CETAK
                </a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPreviewInvoice" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-4 overflow-hidden border-0" style="height: 90vh;">
            <div class="modal-header bg-dark text-white p-4">
                <h5 class="modal-title fw-900 ls-1"><i class="fas fa-file-invoice me-3 text-primary"></i>INVOICE #<span
                        id="lblNospkPreview"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-secondary bg-opacity-10">
                <iframe id="iframePreview" class="w-100 h-100 border-0"></iframe>
            </div>
            <div class="modal-footer bg-white p-4">
                <button type="button" class="btn btn-light rounded-pill px-5 fw-bold"
                    data-bs-dismiss="modal">TUTUP</button>
                <button onclick="document.getElementById('iframePreview').contentWindow.print()"
                    class="btn btn-primary btn-handover-main w-auto px-5"><i class="fas fa-print me-2"></i> CETAK
                    INVOICE</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPreviewTag" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-4 overflow-hidden border-0" style="height: 80vh;">
            <div class="modal-header bg-primary text-white p-4">
                <h5 class="modal-title fw-900 ls-1 text-center w-100"><i class="fas fa-tag me-3"></i>LABEL TAG</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-secondary bg-opacity-10">
                <iframe id="iframePreviewTag" class="w-100 h-100 border-0"></iframe>
            </div>
            <div class="modal-footer bg-white p-4">
                <button type="button" class="btn btn-light rounded-pill px-5 fw-bold"
                    data-bs-dismiss="modal">TUTUP</button>
                <button onclick="document.getElementById('iframePreviewTag').contentWindow.print()"
                    class="btn btn-primary btn-handover-main w-auto px-5"><i class="fas fa-print me-2"></i> CETAK
                    TAG</button>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrCode;
    $('#btnStartScan').on('click', function () {
        const reader = $('#reader');
        if (reader.hasClass('d-none')) {
            reader.removeClass('d-none');
            $(this).html('<i class="fas fa-times text-danger fs-4"></i>');
            $(this).attr('title', 'Tutup Kamera');

            html5QrCode = new Html5Qrcode("reader");
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            html5QrCode.start({ facingMode: "environment" }, config, (decodedText) => {
                // Berhasil: decodedText adalah No SPK
                // Matikan kamera
                html5QrCode.stop().then(() => {
                    reader.addClass('d-none');
                    $('#btnStartScan').html('<i class="fas fa-camera text-primary fs-4"></i>');
                    processScanResult(decodedText);
                });
            }).catch(err => {
                Swal.fire('Kesalahan Kamera', 'Gagal mengakses kamera: ' + err, 'error');
                reader.addClass('d-none');
            });
        } else {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    reader.addClass('d-none');
                    $(this).html('<i class="fas fa-camera text-primary fs-4"></i>');
                });
            }
        }
    });
</script>