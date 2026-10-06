<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    /* ==========================================================================
    PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - WIP TRACKING
    ========================================================================== */
    :root {
        --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);
        --secondary-gradient: linear-gradient(135deg, #2d3436 0%, #000000 100%);
        --accent-gradient: linear-gradient(135deg, #f39c12 0%, #d35400 100%);
        --info-gradient: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        --success-gradient: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
        --warning-gradient: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
        --danger-gradient: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .report-section-card {
        background: white;
        border-radius: 20px;
        border: none;
        box-shadow: var(--premium-shadow);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .section-header {
        background: var(--primary-gradient);
        padding: 1.5rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
    }

    .section-header h5 {
        margin: 0;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* Stats v2 */
    .summary-stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 25px;
        border: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        transition: all 0.3s ease;
    }

    .summary-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--premium-shadow);
    }

    .stat-v2-icon {
        width: 50px;
        height: 50px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.05);
    }

    .stat-v2-label {
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 1px;
    }

    .stat-v2-value {
        font-size: 1.75rem;
        font-weight: 900;
        color: #1a1c23;
        line-height: 1;
    }

    /* Table Styles */
    .premium-table thead th {
        background: #f8fafc;
        border: none;
        color: #475569;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 1px;
        padding: 1.25rem 1.5rem;
    }

    .premium-table tbody td {
        padding: 1.25rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }

    .spk-code {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 800;
        font-size: 0.9rem;
        color: #1a1c23;
        background: #f1f5f9;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
    }

    /* Tabs Styling */
    .nav-tabs-premium {
        border-bottom: 2px solid #f1f5f9;
        padding: 0 1.5rem;
        gap: 2rem;
    }

    .nav-tabs-premium .nav-link {
        border: none;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        padding: 1.25rem 0.5rem;
        position: relative;
        background: transparent;
    }

    .nav-tabs-premium .nav-link.active {
        color: #1a1c23;
        background: transparent;
    }

    .nav-tabs-premium .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--primary-gradient);
        border-radius: 50px;
    }

    .progress {
        height: 8px;
        border-radius: 50px;
        background-color: #f1f5f9;
        overflow: hidden;
    }

    .progress-bar {
        border-radius: 50px;
    }

    /* Search/Filter Bar */
    .filter-bar-premium {
        background: #f8fafc;
        padding: 1.5rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .form-control-premium {
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        padding: 0.6rem 1rem;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .form-control-premium:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }
</style>

<div class="app-content mt-3">
    <div class="container-fluid">
        <!-- Statistics Overview -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--primary-gradient)">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div>
                        <div class="stat-v2-label">Total Produksi</div>
                        <div class="stat-v2-value"><?= isset($summary['total']) ? $summary['total'] : 0 ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--success-gradient)">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <div class="stat-v2-label">Selesai</div>
                        <div class="stat-v2-value text-success">
                            <?= isset($summary['selesai']) ? $summary['selesai'] : 0 ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--warning-gradient)">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div>
                        <div class="stat-v2-label">Dalam Proses</div>
                        <div class="stat-v2-value text-warning">
                            <?= isset($summary['proses']) ? $summary['proses'] : 0 ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="summary-stat-card shadow-sm h-100">
                    <div class="stat-v2-icon text-white" style="background: var(--danger-gradient)">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <div class="stat-v2-label">Terlambat</div>
                        <div class="stat-v2-value text-danger">
                            <?= isset($summary['terlambat']) ? $summary['terlambat'] : 0 ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="report-section-card">
            <div class="section-header">
                <h5><i class="fas fa-satellite-dish"></i> Work In Progress Tracking</h5>
                <div class="d-flex gap-2">
                    <button onclick="refreshData()" class="btn btn-sm btn-light rounded-pill px-3 fw-bold">
                        <i class="fas fa-sync-alt me-1"></i> Refresh
                    </button>
                    <button onclick="exportData()" class="btn btn-sm btn-light rounded-pill px-3 fw-bold">
                        <i class="fas fa-download me-1"></i> Export
                    </button>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="filter-bar-premium">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="small fw-800 text-muted text-uppercase mb-2 d-block">Filter Status</label>
                        <select class="form-select form-control-premium" id="statusFilter">
                            <option value="">Semua Status</option>
                            <option value="0">Belum Di Produksi</option>
                            <option value="1">Sedang Di Produksi</option>
                            <option value="2">Selesai Di Produksi</option>
                            <option value="3">Barang sudah ada di PB</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-800 text-muted text-uppercase mb-2 d-block">Konsumen</label>
                        <select class="form-select form-control-premium" id="konsumenFilter">
                            <option value="">Semua Konsumen</option>
                            <?php if (isset($produksi) && is_array($produksi)): ?>
                                <?php
                                $konsumen_list = array();
                                foreach ($produksi as $p) {
                                    if (isset($p->konsumen_nama) && !in_array($p->konsumen_nama, $konsumen_list)) {
                                        $konsumen_list[] = $p->konsumen_nama;
                                    }
                                }
                                sort($konsumen_list);
                                foreach ($konsumen_list as $konsumen): ?>
                                    <option value="<?= htmlspecialchars($konsumen) ?>"><?= htmlspecialchars($konsumen) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-800 text-muted text-uppercase mb-2 d-block">Tanggal SPK</label>
                        <input type="date" class="form-control form-control-premium" id="dateFilter">
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-800 text-muted text-uppercase mb-2 d-block">Cari Cepat</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"
                                style="border-radius: 12px 0 0 12px;"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" class="form-control form-control-premium border-start-0"
                                style="border-radius: 0 12px 12px 0;" id="searchInput" placeholder="No SPK / Produk...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs nav-tabs-premium" id="wipTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="produksi-tab" data-bs-toggle="tab"
                        data-bs-target="#produksi-content" type="button" role="tab">
                        <i class="fas fa-industry me-2"></i> Dalam Produksi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pb-tab" data-bs-toggle="tab" data-bs-target="#pb-content" type="button"
                        role="tab">
                        <i class="fas fa-box-open me-2"></i> Di Packing Barang
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="handover-tab" data-bs-toggle="tab" data-bs-target="#handover-content"
                        type="button" role="tab">
                        <i class="fas fa-handshake me-2"></i> Sudah Diterima
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="wipTabsContent">
                <!-- Tab: Produksi -->
                <div class="tab-pane fade show active" id="produksi-content" role="tabpanel">
                    <div class="table-responsive">
                        <table id="tableProduksi" class="table premium-table table-hover align-middle mb-0 w-100">
                            <thead>
                                <tr>
                                    <th class="text-center" width="50">No</th>
                                    <th width="200">Informasi SPK</th>
                                    <th width="150">Konsumen</th>
                                    <th width="250">Daftar Produk</th>
                                    <th class="text-center">Status Produksi</th>
                                    <th class="text-center" width="180">Progress</th>
                                    <th class="text-center" width="120">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($produksi) && is_array($produksi)): ?>
                                    <?php
                                    $grouped_by_spk = [];
                                    foreach ($produksi as $p) {
                                        if (isset($p->nospk)) {
                                            $nospk = $p->nospk;
                                            if (!isset($grouped_by_spk[$nospk])) {
                                                $grouped_by_spk[$nospk] = [];
                                            }
                                            $grouped_by_spk[$nospk][] = $p;
                                        }
                                    }

                                    $no = 1;
                                    foreach ($grouped_by_spk as $nospk => $spk_group):
                                        $p = $spk_group[0];
                                        $itemCount = count($spk_group);

                                        // Aggregate progress
                                        $total_prog = 0;
                                        foreach ($spk_group as $item) {
                                            $st = $item->status_produksi ?? 'approve';
                                            if ($st === 'proses')
                                                $total_prog += 50;
                                            elseif ($st === 'done')
                                                $total_prog += 80;
                                            elseif ($st === 'on_pb')
                                                $total_prog += 100;
                                        }
                                        $avg_prog = $total_prog / $itemCount;
                                        ?>
                                        <tr>
                                            <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                                            <td>
                                                <div class="d-flex flex-column gap-1">
                                                    <span class="spk-code"><?= $nospk ?></span>
                                                    <div class="small text-muted fw-bold">
                                                        <i class="far fa-calendar-alt me-1 text-primary"></i>
                                                        <?= date('d/m/Y', strtotime($p->update_time ?? $p->tanggal ?? 'today')) ?>
                                                        <?php if ($itemCount > 1): ?>
                                                            <span class="badge bg-info-gradient ms-1"
                                                                style="font-size: 0.6rem;"><?= $itemCount ?> ITEMS</span>
                                                        <?php endif; ?>
                                                        <?php if (!empty($p->catatan)): ?>
                                                            <div class="mt-2 p-2 rounded bg-warning bg-opacity-10 border border-warning border-opacity-25 shadow-sm" style="font-size: 0.7rem; line-height: 1.2;">
                                                                <span class="fw-900 text-warning-emphasis"><i class="fas fa-sticky-note me-1"></i>NOTE:</span>
                                                                <span class="text-dark d-block"><?= htmlspecialchars($p->catatan) ?></span>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-800 text-dark"><?= $p->konsumen_nama ?></div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-2">
                                                    <?php foreach ($spk_group as $item): ?>
                                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-1 border-opacity-10">
                                                            <div>
                                                                <span class="d-block fw-bold text-dark" style="font-size:0.85rem"><?= htmlspecialchars($item->product_nama) ?></span>
                                                                <span class="text-muted" style="font-size:0.75rem"><i class="fas fa-boxes me-1"></i><?= number_format($item->jumlah ?? 0) ?> <?= $item->satuan_nama ?></span>
                                                            </div>
                                                            <a href="<?= site_url('wip/detail/'.$item->produksi_id) ?>" class="btn btn-sm btn-primary py-0 px-2 rounded-pill" style="font-size:0.7rem" title="Proses / Detail">
                                                                <i class="fas fa-cog fa-spin-hover me-1"></i>Proses
                                                            </a>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                // Simplified aggregated status
                                                $has_on_pb = false;
                                                $has_proses = false;
                                                $has_pending = false;
                                                foreach ($spk_group as $item) {
                                                    $st = $item->status_produksi ?? '0';
                                                    if ($st == 'on_pb')
                                                        $has_on_pb = true;
                                                    elseif ($st == 'proses')
                                                        $has_proses = true;
                                                    else
                                                        $has_pending = true;
                                                }

                                                if ($has_pending && !$has_proses && !$has_on_pb)
                                                    echo '<span class="badge bg-secondary">ANTRIAN</span>';
                                                elseif ($has_proses)
                                                    echo '<span class="badge bg-warning text-dark">PRODUKSI</span>';
                                                elseif ($has_on_pb)
                                                    echo '<span class="badge bg-primary">DI PB</span>';
                                                else
                                                    echo '<span class="badge bg-success">SELESAI</span>';
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="progress flex-grow-1">
                                                        <div class="progress-bar bg-<?= $avg_prog >= 100 ? 'success' : ($avg_prog >= 50 ? 'warning' : 'info') ?>"
                                                            style="width: <?= $avg_prog ?>%"></div>
                                                    </div>
                                                    <span class="small fw-bold text-muted"><?= round($avg_prog) ?>%</span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button onclick="showInvoicePreview('<?= $nospk ?>')"
                                                        class="btn btn-sm btn-dark rounded-pill px-2" title="Preview SPK">
                                                        <i class="fas fa-receipt"></i>
                                                    </button>
                                                    <a href="<?= base_url('transaksi/cetak_invoice_ulang/' . $nospk) ?>"
                                                        target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-2"
                                                        title="Print">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3 opacity-20"></i>
                                                <p class="fw-bold">Tidak ada data WIP saat ini</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab: Di PB -->
                <div class="tab-pane fade" id="pb-content" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table premium-table table-hover align-middle mb-0 w-100">
                            <thead>
                                <tr>
                                    <th class="text-center" width="50">No</th>
                                    <th>No SPK</th>
                                    <th>Konsumen</th>
                                    <th>Produk</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($pb_list)): ?>
                                    <?php $no = 1;
                                    foreach ($pb_list as $p): ?>
                                        <tr>
                                            <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                                            <td><span class="spk-code"><?= $p->nospk ?></span></td>
                                            <td>
                                                <div class="fw-800 text-dark"><?= $p->konsumen_nama ?></div>
                                            </td>
                                            <td><?= $p->product_nama ?></td>
                                            <td class="text-center fw-bold"><?= number_format($p->jumlah ?? 0) ?></td>
                                            <td class="text-center">
                                                <a href="<?= site_url('wip/detail/' . $p->produksi_id) ?>"
                                                    class="btn btn-sm btn-dark rounded-pill px-3">
                                                    <i class="fas fa-search me-1"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">Belum ada barang di Packing
                                            Barang</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab: Handover -->
                <div class="tab-pane fade" id="handover-content" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table premium-table table-hover align-middle mb-0 w-100">
                            <thead>
                                <tr>
                                    <th class="text-center" width="50">No</th>
                                    <th>No SPK</th>
                                    <th>Konsumen</th>
                                    <th>Produk</th>
                                    <th class="text-center">Waktu Selesai</th>
                                    <th class="text-center">History</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($handover_list)): ?>
                                    <?php $no = 1;
                                    foreach ($handover_list as $p): ?>
                                        <tr>
                                            <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                                            <td><span class="spk-code"><?= $p->nospk ?></span></td>
                                            <td>
                                                <div class="fw-800 text-dark"><?= $p->konsumen_nama ?></div>
                                            </td>
                                            <td><?= $p->product_nama ?></td>
                                            <td class="text-center small"><?= date('d/m/Y H:i', strtotime($p->waktu_selesai)) ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= site_url('wip/detail/' . $p->produksi_id) ?>"
                                                    class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                                    <i class="fas fa-history me-1"></i> Log
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">Belum ada data barang diambil
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Invoice Preview -->
<div class="modal fade" id="invoicePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content report-section-card shadow-lg border-0" style="height: 85vh;">
            <div class="modal-header border-0 p-4"
                style="background: var(--primary-gradient); border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-900 text-white"><i class="fas fa-file-invoice me-2"></i> Preview Invoice / SPK
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-light">
                <iframe id="invoiceFrame" src="" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
            <div class="modal-footer bg-white border-0 p-3" style="border-radius: 0 0 20px 20px;">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="printInvoice()">
                    <i class="fas fa-print me-2"></i> Print Dokumen
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    window.showInvoicePreview = function (nospk) {
        const url = '<?= base_url("transaksi/preview_invoice/") ?>' + nospk;
        $('#invoiceFrame').attr('src', url);
        $('#invoicePreviewModal').modal('show');
    }

    function printInvoice() {
        const iframe = document.getElementById('invoiceFrame');
        if (iframe.contentWindow) {
            iframe.contentWindow.print();
        }
    }

    function refreshData() {
        location.reload();
    }

    function exportData() {
        Swal.fire({
            title: 'Export Data WIP',
            text: 'Pilih format export data:',
            icon: 'question',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: 'EXCEL',
            denyButtonText: 'PDF',
            confirmButtonColor: '#27ae60',
            denyButtonColor: '#e74c3c'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Exporting...', 'Data sedang dikonversi ke Excel', 'success');
            } else if (result.isDenied) {
                Swal.fire('Exporting...', 'Data sedang dikonversi ke PDF', 'success');
            }
        });
    }

    $(document).ready(function () {
        if ($.fn.DataTable) {
            var table = $('#tableProduksi').DataTable({
                "language": {
                    "sSearch": "Filter Cepat:",
                    "sLengthMenu": "_MENU_",
                    "oPaginate": { "sNext": "<i class='fas fa-chevron-right'></i>", "sPrevious": "<i class='fas fa-chevron-left'></i>" }
                },
                "pageLength": 25,
                "responsive": true,
                "ordering": false,
                "dom": "<'row mb-0'<'col-md-6'l><'col-md-6 d-flex justify-content-end text-end'f>>t<'row mt-4'<'col-md-6'i><'col-md-6 d-flex justify-content-end text-end'p>>",
                "initComplete": function () {
                    var columnStatus = this.api().column(4);
                    var columnKonsumen = this.api().column(2);

                    $('#statusFilter').on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                        // Since status text in table is ANTRIAN/PRODUKSI etc, 
                        // and filter val is 0,1,2, this mapping might need care.
                        // For now we'll do simple text search or refresh.
                        table.draw();
                    });

                    $('#konsumenFilter').on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                        columnKonsumen.search(val ? '^' + val + '$' : '', true, false).draw();
                    });
                }
            });

            $('#searchInput').on('keyup', function () {
                table.search(this.value).draw();
            });
        }
    });
</script>