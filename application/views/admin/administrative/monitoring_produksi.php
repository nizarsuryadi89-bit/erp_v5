<?php $this->load->view('layout_admin/header_modul'); ?>

<div class="p-3">
    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white overflow-hidden" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 fw-bold">Update Terakhir</h6>
                        <i class="fas fa-clock opacity-50"></i>
                    </div>
                    <h3 class="fw-bold mb-0" id="card-last-update">
                        <?= !empty($production_updates) ? date('H:i', strtotime($production_updates[0]->update_time)) : '-' ?>
                    </h3>
                    <small class="opacity-75">Siklus produksi hari ini</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white overflow-hidden" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 fw-bold">Material Terpakai</h6>
                        <i class="fas fa-box-open opacity-50"></i>
                    </div>
                    <h3 class="fw-bold mb-0" id="card-material-count"><?= count($daily_usage) ?></h3>
                    <small class="opacity-75">Varian bahan baku aktif</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info text-white overflow-hidden" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 fw-bold">Proses Selesai</h6>
                        <i class="fas fa-check-double opacity-50"></i>
                    </div>
                    <?php
                    $done_count = 0;
                    foreach ($production_updates as $pu)
                        if ($pu->status_produksi == '2')
                            $done_count++;
                    ?>
                    <h3 class="fw-bold mb-0" id="card-done-count"><?= $done_count ?></h3>
                    <small class="opacity-75">Step produksi tervalidasi</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-dark overflow-hidden" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 fw-bold">Total Log Hari Ini</h6>
                        <i class="fas fa-list-ul opacity-50"></i>
                    </div>
                    <h3 class="fw-bold mb-0" id="card-log-count"><?= count($logs) ?></h3>
                    <small class="opacity-75">Entri penggunaan stok</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body p-3">
            <form method="GET" action="<?= base_url('administrative/monitoring_produksi') ?>"
                class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i
                                class="fas fa-calendar-alt text-muted"></i></span>
                        <input type="date" name="start_date" class="form-control border-start-0"
                            value="<?= $start_date ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i
                                class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search_spk" class="form-control border-start-0"
                            placeholder="Cari No SPK (Ex: 2-62-0226-188)" value="<?= $search_spk ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm px-4 rounded-pill w-100">
                        <i class="fas fa-sync-alt me-1"></i> Refresh / Cari
                    </button>
                </div>
                <?php if ($search_spk): ?>
                    <div class="col-md-2">
                        <a href="<?= base_url('administrative/monitoring_produksi') ?>"
                            class="btn btn-light btn-sm px-4 rounded-pill w-100">
                            Clear Search
                        </a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <!-- 1. Real-time Production Updates -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-satellite-dish text-danger me-2"></i>Live Production Flow
                    </h5>
                    <span class="badge bg-soft-danger text-danger rounded-pill px-3">End-to-End Tracking</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 600px;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4">Waktu</th>
                                    <th>SPK / Produk / Lokasi</th>
                                    <th>Konsumen</th>
                                    <th>Operator</th>
                                    <th>Routing / Status</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-center pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="small" id="table-production-body">
                                <?php if (empty($production_updates)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">Belum ada aktivitas produksi /
                                            SPK tidak ditemukan.</td>
                                    </tr>
                                <?php endif; ?>
                                    <?php
                                    function getLokasiColor($alias) {
                                        $alias = strtoupper($alias);
                                        switch ($alias) {
                                            case 'TGM': return 'bg-dark text-white';
                                            case 'KPO': return 'bg-primary text-white';
                                            case 'KBB': return 'bg-danger text-white';
                                            case 'PBR': return 'bg-success text-white';
                                            case 'TSM': return 'bg-warning text-dark';
                                            case 'MLG': return 'bg-info text-white';
                                            case 'SDA': return 'bg-secondary text-white';
                                            default: return 'bg-soft-info text-info';
                                        }
                                    }
                                    ?>
                                    <?php foreach ($production_updates as $pu):
                                    // Status per Routing/Step
                                    $status_class = '';
                                    $status_label = '';
                                    if ($pu->status_produksi == '1') {
                                        $status_class = 'bg-primary';
                                        $status_label = 'PROCESS';
                                    } else if ($pu->status_produksi == '2') {
                                        $status_class = 'bg-success';
                                        $status_label = 'DONE';
                                    } else if ($pu->status_produksi == '3') {
                                        $status_class = 'bg-danger';
                                        $status_label = 'REJECT';
                                    } else {
                                        $status_class = 'bg-secondary';
                                        $status_label = 'WAITING';
                                    }

                                    // Global Tracking Status (Life Cycle)
                                    $utama_class = 'bg-light text-dark';
                                    $utama_label = strtoupper($pu->status_utama);
                                    if ($pu->status_utama == 'on_costumer') {
                                        $utama_class = 'bg-dark text-white';
                                        $utama_label = 'AT CONSUMER';
                                    } elseif ($pu->status_utama == 'on_pb') {
                                        $utama_class = 'bg-warning text-dark';
                                        $utama_label = 'READY AT PB';
                                    } elseif ($pu->status_utama == 'done') {
                                        $utama_class = 'bg-success text-white';
                                        $utama_label = 'PRODUCTION DONE';
                                    }
                                    ?>
                                    <tr>
                                        <td class="ps-4 text-muted"><?= date('H:i:s', strtotime($pu->update_time)) ?></td>
                                        <td>
                                            <div class="fw-bold text-primary">#<?= $pu->nospk ?></div>
                                            <div class="text-muted text-truncate mb-1" style="max-width: 150px;">
                                                <?= $pu->product_nama ?>
                                            </div>
                                            <div class="d-flex gap-1 flex-wrap">
                                                <span class="badge <?= $utama_class ?> rounded-pill"
                                                    style="font-size: 8px;"><?= $utama_label ?></span>
                                                <span class="badge <?= getLokasiColor($pu->lokasi_cetak) ?> rounded-pill" style="font-size: 8px;">
                                                    <i class="fas fa-map-marker-alt" style="font-size: 7px;"></i> <?= $pu->lokasi_cetak ?: '-' ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold small"><?= $pu->konsumen_nama ?: '-' ?></div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                                    style="width: 25px; height: 25px; font-size: 10px;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <span class="fw-bold"><?= $pu->operator_nama ?: 'System' ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="mb-1 fw-bold"><?= $pu->routing_name ?></div>
                                            <span class="badge <?= $status_class ?> rounded-pill"
                                                style="font-size: 9px;"><?= $status_label ?></span>
                                        </td>
                                        <td class="text-end fw-bold">
                                            <?= number_format($pu->qty_output ?: $pu->qty_input, 0) ?>
                                        </td>
                                        <td class="text-center pe-4">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button type="button"
                                                    class="btn btn-warning btn-xs rounded-pill px-2 shadow-sm"
                                                    onclick='openKoreksiProduksi(<?= json_encode($pu) ?>)'
                                                    title="Koreksi Produksi">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="<?= base_url('cekpesanan/spk/' . urlencode(encrypt_spk($pu->nospk))) ?>" target="_blank"
                                                    class="btn btn-info btn-xs rounded-pill px-2 shadow-sm"
                                                    title="Lihat SPK">
                                                    <i class="fas fa-search"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Daily Consumption Summary -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-chart-pie text-success me-2"></i>Konsumsi Bahan Hari Ini
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4">Material / Varian</th>
                                    <th class="text-end pe-4">Total Pakai</th>
                                </tr>
                            </thead>
                            <tbody class="small" id="table-usage-body">
                                <?php if (empty($daily_usage)): ?>
                                    <tr>
                                        <td colspan="2" class="text-center py-5 text-muted">Belum ada bahan yang dikonsumsi
                                            hari ini.</td>
                                    </tr>
                                <?php endif; ?>
                                <?php foreach ($daily_usage as $usage): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold"><?= $usage->bahan_nama ?></div>
                                            <small class="text-muted"><?= $usage->material_name ?></small>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="fw-bold text-success" style="font-size: 1.1rem;">
                                                <?= number_format($usage->total_usage_produksi, 0, ',', '.') ?>
                                                <small class="text-muted fw-normal"
                                                    style="font-size: 0.8rem;"><?= $usage->satuan_produksi ?></small>
                                            </div>
                                            <?php if ($usage->konversi > 1): ?>
                                                <div class="text-muted" style="font-size: 10px;">
                                                    Setara: <?= number_format($usage->total_usage_gudang, 3, ',', '.') ?>
                                                    <?= $usage->satuan_gudang ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="text-muted small" style="font-size: 10px;"><?= $usage->freq ?>x
                                                Penggunaan</div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 3. Recent Material Logs (The 'How' it's used) -->
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white py-3 border-0 ps-4">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-history text-info me-2"></i>Log Penggunaan Detail</h5>
                </div>
                <div class="card-body p-0 overflow-auto" style="max-height: 300px;">
                    <div class="timeline p-3" id="timeline-logs-body">
                        <?php if (empty($logs)): ?>
                            <div class="text-center py-4 text-muted small">Tidak ada log detail stok.</div>
                        <?php endif; ?>
                        <?php foreach ($logs as $log): ?>
                            <div class="d-flex mb-3 pb-2 border-bottom border-light">
                                <div class="me-3 text-center" style="min-width: 50px;">
                                    <div class="fw-bold small text-primary mb-0">
                                        <?= date('H:i', strtotime($log->created_at)) ?>
                                    </div>
                                    <div class="text-muted" style="font-size: 9px;">
                                        <?= date('d/m', strtotime($log->created_at)) ?>
                                    </div>
                                </div>
                                <div>
                                    <div class="small fw-bold mb-1">
                                        <span class="text-danger">-<?= number_format($log->qty_out, 3, ',', '.') ?>
                                            <?= $log->satuan_nama ?></span>
                                        &bull; <?= $log->bahan_nama ?> (<?= $log->material_name ?>)
                                    </div>
                                    <div class="text-muted" style="font-size: 10px;">
                                        <i class="fas fa-info-circle me-1"></i> <?= $log->catatan ?>
                                        <div class="mt-1"><i class="fas fa-user-edit me-1"></i> Operator:
                                            <strong><?= $log->username ?: 'System' ?></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Koreksi Produksi Admin -->
<div class="modal fade" id="modalKoreksiProduksi" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="<?= base_url('administrative/update_koreksi_produksi') ?>" method="POST"
            class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-dark text-white border-0 py-3" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-tools me-2 text-warning"></i>KOREKSI AKTIVITAS PRODUKSI #<span
                        id="koreksi_spk"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <input type="hidden" name="detail_produksi_id" id="prod_detail_id">

                <div class="alert alert-warning border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Perhatian:</strong> Perubahan pada <em>Qty Bahan Pakai/Rusak</em> akan secara otomatis
                    menyesuaikan nilai stok di log gudang.
                </div>

                <div class="row g-3">
                    <!-- Info Bar -->
                    <div class="col-12">
                        <div class="p-3 bg-white rounded border-start border-4 border-primary shadow-sm mb-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="small text-muted d-block fw-bold mb-0">PRODUK</label>
                                    <span class="fw-bold" id="koreksi_produk">-</span>
                                </div>
                                <div class="col-md-6">
                                    <label class="small text-muted d-block fw-bold mb-0">STEP / ROUTING</label>
                                    <span class="fw-bold text-primary" id="koreksi_routing">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quantities -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Qty Input (SPK)</label>
                        <input type="number" name="qty_input" id="koreksi_qty_in" class="form-control border-2">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Qty Output (Hasil)</label>
                        <input type="number" name="qty_output" id="koreksi_qty_out"
                            class="form-control border-2 bg-soft-success text-success fw-bold">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-danger">Bahan Pakai (<span
                                class="lbl-satuan">Unit</span>)</label>
                        <input type="number" step="0.001" name="qty_bahan_pakai" id="koreksi_pakai"
                            class="form-control border-2 border-danger">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Bahan Rusak (<span
                                class="lbl-satuan">Unit</span>)</label>
                        <input type="number" step="0.001" name="qty_bahan_rusak" id="koreksi_rusak"
                            class="form-control border-2">
                    </div>

                    <!-- Material Mapping -->
                    <div class="col-md-12 mt-3">
                        <label class="form-label small fw-bold">Mapping Material (Untuk Stok)</label>
                        <select name="material_id" id="koreksi_material" class="form-select border-2 select2-koreksi">
                            <option value="">-- Tanpa Bahan Baku --</option>
                            <?php foreach ($materials as $m): ?>
                                <option value="<?= $m->material_id ?>"><?= $m->bahan_nama ?> (<?= $m->varian ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 mt-3">
                        <label class="form-label small fw-bold">Alasan Koreksi / Catatan Tambahan</label>
                        <textarea name="catatan" id="koreksi_catatan" class="form-control border-2" rows="2" required
                            placeholder="Contoh: Salah input qty lbr, seharusnya hvs pak..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-white" style="border-radius: 0 0 20px 20px;">
                <button type="button" class="btn btn-light px-4 rounded-pill" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-dark px-4 rounded-pill shadow">
                    <i class="fas fa-save me-2 text-warning"></i>SIMPAN PERUBAHAN
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .bg-soft-primary {
        background: #eef2ff;
    }

    .bg-soft-info {
        background: #e0f2fe;
    }

    .bg-soft-danger {
        background: #fff1f2;
    }

    .bg-soft-success {
        background: #f0fdf4;
    }

    .bg-soft-warning {
        background: #fffbeb;
    }

    .text-danger {
        color: #e11d48 !important;
    }

    .text-primary {
        color: #4f46e5 !important;
    }

    .text-success {
        color: #16a34a !important;
    }

    .table-hover tbody tr:hover {
        background-color: #f8fafc;
    }

    ::-webkit-scrollbar {
        width: 6px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
</style>

<script>
    $(document).ready(function () {
        // Auto Refresh every 30 seconds via AJAX
        setInterval(function () {
            if ($('.modal.show').length === 0) {
                refreshMonitoringData();
            }
        }, 30000);

        <?php if ($this->session->flashdata('success')): ?>
            Swal.fire('Berhasil!', '<?= $this->session->flashdata('success') ?>', 'success');
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
            Swal.fire('Error!', '<?= $this->session->flashdata('error') ?>', 'error');
        <?php endif; ?>
    });

    function getLokasiColorJS(alias) {
        if (!alias) return 'bg-soft-info text-info';
        alias = alias.toUpperCase();
        switch (alias) {
            case 'TGM': return 'bg-dark text-white shadow-sm';
            case 'KPO': return 'bg-primary text-white shadow-sm';
            case 'KBB': return 'bg-danger text-white shadow-sm';
            case 'PBR': return 'bg-success text-white shadow-sm';
            case 'TSM': return 'bg-warning text-dark shadow-sm';
            case 'MLG': return 'bg-info text-white shadow-sm';
            case 'SDA': return 'bg-secondary text-white shadow-sm';
            default: return 'bg-soft-info text-info';
        }
    }

    function formatNumber(num, decimals = 0) {
        return parseFloat(num).toLocaleString('id-ID', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
    }

    function refreshMonitoringData() {
        const startDate = $('input[name="start_date"]').val();
        const searchSpk = $('input[name="search_spk"]').val();

        $.ajax({
            url: "<?= base_url('administrative/monitoring_produksi_ajax') ?>",
            type: "GET",
            data: {
                start_date: startDate,
                search_spk: searchSpk
            },
            dataType: "json",
            success: function (res) {
                // 1. Update Cards
                if (res.production_updates && res.production_updates.length > 0) {
                    const lastDate = new Date(res.production_updates[0].update_time);
                    $('#card-last-update').text(lastDate.getHours().toString().padStart(2, '0') + ':' + lastDate.getMinutes().toString().padStart(2, '0'));
                }
                $('#card-material-count').text(res.daily_usage ? res.daily_usage.length : 0);
                $('#card-log-count').text(res.logs ? res.logs.length : 0);

                let doneCount = 0;
                if (res.production_updates) {
                    res.production_updates.forEach(pu => { if (pu.status_produksi == '2') doneCount++; });
                }
                $('#card-done-count').text(doneCount);

                // 2. Update Production Flow Table
                let htmlProd = '';
                if (!res.production_updates || res.production_updates.length === 0) {
                    htmlProd = '<tr><td colspan="7" class="text-center py-5 text-muted">Belum ada aktivitas produksi / SPK tidak ditemukan.</td></tr>';
                } else {
                    res.production_updates.forEach(pu => {
                        let statusClass = 'bg-secondary', statusLabel = 'WAITING';
                        if (pu.status_produksi == '1') { statusClass = 'bg-primary'; statusLabel = 'PROCESS'; }
                        else if (pu.status_produksi == '2') { statusClass = 'bg-success'; statusLabel = 'DONE'; }
                        else if (pu.status_produksi == '3') { statusClass = 'bg-danger'; statusLabel = 'REJECT'; }

                        let utamaClass = 'bg-light text-dark', utamaLabel = (pu.status_utama || '').toUpperCase();
                        if (pu.status_utama == 'on_costumer') { utamaClass = 'bg-dark text-white'; utamaLabel = 'AT CONSUMER'; }
                        else if (pu.status_utama == 'on_pb') { utamaClass = 'bg-warning text-dark'; utamaLabel = 'READY AT PB'; }
                        else if (pu.status_utama == 'done') { utamaClass = 'bg-success text-white'; utamaLabel = 'PRODUCTION DONE'; }

                        const updateTime = new Date(pu.update_time).toLocaleTimeString('id-ID', { hour12: false });
                        const qty = formatNumber(pu.qty_output || pu.qty_input);
                        const jsonData = JSON.stringify(pu).replace(/'/g, "&apos;");

                        htmlProd += `
                            <tr>
                                <td class="ps-4 text-muted">${updateTime}</td>
                                <td>
                                    <div class="fw-bold text-primary">#${pu.nospk}</div>
                                    <div class="text-muted text-truncate mb-1" style="max-width: 150px;">${pu.product_nama}</div>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <span class="badge ${utamaClass} rounded-pill" style="font-size: 8px;">${utamaLabel}</span>
                                        <span class="badge ${getLokasiColorJS(pu.lokasi_cetak)} rounded-pill" style="font-size: 8px;">
                                            <i class="fas fa-map-marker-alt" style="font-size: 7px;"></i> ${pu.lokasi_cetak || '-'}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold small">${pu.konsumen_nama || '-'}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 25px; height: 25px; font-size: 10px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <span class="fw-bold">${pu.operator_nama || 'System'}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="mb-1 fw-bold">${pu.routing_name}</div>
                                    <span class="badge ${statusClass} rounded-pill" style="font-size: 9px;">${statusLabel}</span>
                                </td>
                                <td class="text-end fw-bold">${qty}</td>
                                <td class="text-center pe-4">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" class="btn btn-warning btn-xs rounded-pill px-2 shadow-sm" onclick='openKoreksiProduksi(${jsonData})' title="Koreksi Produksi">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="<?= base_url('cekpesanan/spk/') ?>${pu.nospk_encrypted}" target="_blank" class="btn btn-info btn-xs rounded-pill px-2 shadow-sm" title="Lihat SPK">
                                            <i class="fas fa-search"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>`;
                    });
                }
                $('#table-production-body').html(htmlProd);

                // 3. Update Daily Usage Table
                let htmlUsage = '';
                if (!res.daily_usage || res.daily_usage.length === 0) {
                    htmlUsage = '<tr><td colspan="2" class="text-center py-5 text-muted">Belum ada bahan yang dikonsumsi hari ini.</td></tr>';
                } else {
                    res.daily_usage.forEach(u => {
                        let konversiHtml = '';
                        if (parseFloat(u.konversi) > 1) {
                            konversiHtml = `<div class="text-muted" style="font-size: 10px;">Setara: ${formatNumber(u.total_usage_gudang, 3)} ${u.satuan_gudang}</div>`;
                        }
                        htmlUsage += `
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">${u.bahan_nama}</div>
                                    <small class="text-muted">${u.material_name}</small>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="fw-bold text-success" style="font-size: 1.1rem;">
                                        ${formatNumber(u.total_usage_produksi)}
                                        <small class="text-muted fw-normal" style="font-size: 0.8rem;">${u.satuan_produksi}</small>
                                    </div>
                                    ${konversiHtml}
                                    <div class="text-muted small" style="font-size: 10px;">${u.freq}x Penggunaan</div>
                                </td>
                            </tr>`;
                    });
                }
                $('#table-usage-body').html(htmlUsage);

                // 4. Update Logs Timeline
                let htmlLogs = '';
                if (!res.logs || res.logs.length === 0) {
                    htmlLogs = '<div class="text-center py-4 text-muted small">Tidak ada log detail stok.</div>';
                } else {
                    res.logs.forEach(l => {
                        const logTimeStr = new Date(l.created_at);
                        const logTime = logTimeStr.getHours().toString().padStart(2, '0') + ':' + logTimeStr.getMinutes().toString().padStart(2, '0');
                        const logDate = logTimeStr.getDate().toString().padStart(2, '0') + '/' + (logTimeStr.getMonth() + 1).toString().padStart(2, '0');
                        htmlLogs += `
                            <div class="d-flex mb-3 pb-2 border-bottom border-light">
                                <div class="me-3 text-center" style="min-width: 50px;">
                                    <div class="fw-bold small text-primary mb-0">${logTime}</div>
                                    <div class="text-muted" style="font-size: 9px;">${logDate}</div>
                                </div>
                                <div>
                                    <div class="small fw-bold mb-1">
                                        <span class="text-danger">-${formatNumber(l.qty_out, 3)} ${l.satuan_nama}</span>
                                        &bull; ${l.bahan_nama} (${l.material_name})
                                    </div>
                                    <div class="text-muted" style="font-size: 10px;">
                                        <i class="fas fa-info-circle me-1"></i> ${l.catatan}
                                        <div class="mt-1"><i class="fas fa-user-edit me-1"></i> Operator: <strong>${l.username || 'System'}</strong></div>
                                    </div>
                                </div>
                            </div>`;
                    });
                }
                $('#timeline-logs-body').html(htmlLogs);
            }
        });
    }

    function openKoreksiProduksi(data) {
        $('#prod_detail_id').val(data.detail_produksi_id);
        $('#koreksi_spk').text(data.nospk);
        $('#koreksi_produk').text(data.product_nama);
        $('#koreksi_routing').text(data.routing_name);
        $('.lbl-satuan').text(data.satuan_nama || 'Unit');

        $('#koreksi_qty_in').val(data.qty_input);
        $('#koreksi_qty_out').val(data.qty_output);
        $('#koreksi_pakai').val(data.qty_bahan_pakai);
        $('#koreksi_rusak').val(data.qty_bahan_rusak);
        $('#koreksi_material').val(data.material_id);
        $('#koreksi_catatan').val(data.catatan);

        new bootstrap.Modal(document.getElementById('modalKoreksiProduksi')).show();
    }
</script>