<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #1a1c23 0%, #2c3e50 100%);
        --accent-blue: #3b82f6;
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        --slate-dark: #1e293b;
        --slate-muted: #64748b;
    }

    body {
        background: #f8fafc;
        font-family: 'Inter', 'Segoe UI', sans-serif;
    }

    .audit-hero {
        background: var(--primary-gradient);
        color: white;
        padding: 3rem 0;
        border-radius: 0 0 30px 30px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        margin-bottom: -3rem;
        position: relative;
        z-index: 1;
    }

    .hero-title {
        font-size: 2rem;
        font-weight: 900;
        letter-spacing: -1px;
        margin-bottom: 0.5rem;
    }

    .audit-content-wrapper {
        position: relative;
        z-index: 10;
        padding-bottom: 3rem;
    }

    .audit-card {
        background: white;
        border-radius: 24px;
        box-shadow: var(--premium-shadow);
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }

    .table-premium th {
        background: #f8fafc;
        color: var(--slate-dark);
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        padding: 1rem 1.5rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .table-premium td {
        padding: 1.25rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: var(--slate-muted);
        font-size: 0.9rem;
    }

    .table-premium tbody tr {
        transition: all 0.2s;
    }

    .table-premium tbody tr:hover {
        background: #f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .badge-action {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
    }
    .badge-in { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .badge-out { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .badge-info { background: #e0f2fe; color: #075985; border: 1px solid #bae6fd; }
    
    .nav-tabs-premium {
        border-bottom: 2px solid #f1f5f9;
        padding: 0 1.5rem;
    }
    .nav-tabs-premium .nav-link {
        color: var(--slate-muted);
        font-weight: 700;
        border: none;
        padding: 1.25rem 2rem;
        position: relative;
    }
    .nav-tabs-premium .nav-link.active {
        color: var(--accent-blue);
        background: transparent;
    }
    .nav-tabs-premium .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--accent-blue);
        border-radius: 3px 3px 0 0;
    }
</style>

<div class="audit-hero">
    <div class="container-fluid px-5">
        <div class="row align-items-center">
            <div class="col-md-8 d-flex align-items-center gap-4">
                <div style="width: 70px; height: 70px; background: rgba(255,255,255,0.1); border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 2rem; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                    <i class="fa fa-fingerprint"></i>
                </div>
                <div>
                    <h1 class="hero-title">Audit Trail & Security Log</h1>
                    <p class="mb-0 text-white-50 fw-bold">Pemantauan aktivitas sistem dan mutasi stok secara real-time</p>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <a href="<?= base_url('warehouse') ?>" class="btn btn-outline-light rounded-pill px-4 fw-bold border-2">
                    <i class="fa fa-arrow-left me-2"></i> KEMBALI KE DASHBOARD
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-5 audit-content-wrapper">
    <div class="audit-card">
        <ul class="nav nav-tabs nav-tabs-premium" id="auditTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="stock-tab" data-bs-toggle="tab" data-bs-target="#stock" type="button" role="tab">
                    <i class="fa fa-box-open me-2"></i> Mutasi Stok (Ledger)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="system-tab" data-bs-toggle="tab" data-bs-target="#system" type="button" role="tab">
                    <i class="fa fa-desktop me-2"></i> Aktivitas Sistem (User)
                </button>
            </li>
        </ul>
        <div class="tab-content p-0" id="auditTabContent">
            <!-- TAB: STOK -->
            <div class="tab-pane fade show active" id="stock" role="tabpanel">
                <div class="p-4 bg-light border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 fw-bold text-dark"><i class="fa fa-history text-primary me-2"></i>Riwayat Pergerakan Fisik Barang (<?= count($stock_logs) ?> Log Terakhir)</h6>
                        <button class="btn btn-sm btn-primary rounded-pill fw-bold px-3 shadow-sm" onclick="window.print()"><i class="fa fa-print me-1"></i> CETAK LOG</button>
                    </div>

                    <!-- Filter Form -->
                    <form method="GET" action="<?= base_url('warehouse/audit_log') ?>">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="small fw-bold text-muted mb-1">Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= $filters['start_date'] ?? '' ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold text-muted mb-1">Tanggal Akhir</label>
                                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= $filters['end_date'] ?? '' ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold text-muted mb-1">Pilih Material</label>
                                <select name="material_id" class="form-select form-select-sm select2">
                                    <option value="">-- Semua Material --</option>
                                    <?php foreach ($materials as $m): ?>
                                        <option value="<?= $m->material_id ?>" <?= (isset($filters['material_id']) && $filters['material_id'] == $m->material_id) ? 'selected' : '' ?>>
                                            <?= $m->bahan_nama ?> <?= $m->varian ? '('.$m->varian.')' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="small fw-bold text-muted mb-1">Pilih Gudang</label>
                                <select name="gudang_id" class="form-select form-select-sm">
                                    <option value="">-- Semua Gudang --</option>
                                    <?php foreach ($warehouses as $w): ?>
                                        <option value="<?= $w->gudang_id ?>" <?= (isset($filters['gudang_id']) && $filters['gudang_id'] == $w->gudang_id) ? 'selected' : '' ?>>
                                            <?= $w->gudang_nama ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-1 d-flex align-items-end gap-1">
                                <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fa fa-filter"></i></button>
                                <a href="<?= base_url('warehouse/audit_log') ?>" class="btn btn-sm btn-light border"><i class="fa fa-sync-alt"></i></a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-premium mb-0" id="table-stok">
                        <thead>
                            <tr>
                                <th>Waktu (Timestamp)</th>
                                <th>Aktor / Operator</th>
                                <th>Item Material</th>
                                <th>Gudang</th>
                                <th>Tipe Mutasi</th>
                                <th class="text-end">Qty In</th>
                                <th class="text-end">Qty Out</th>
                                <?php if (!empty($filters['material_id'])): ?>
                                    <th class="text-end text-primary">Saldo</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $current_saldo = isset($saldo_awal) ? $saldo_awal : 0;
                                $format_qty_global = function($val) {
                                    if (fmod((float)$val, 1) == 0) {
                                        return number_format($val, 0, ',', '.');
                                    } else {
                                        $str = number_format($val, 2, ',', '.');
                                        return rtrim(rtrim($str, '0'), ',');
                                    }
                                };
                                if (!empty($filters['material_id'])): 
                                    $unit_name = !empty($stock_logs) ? $stock_logs[0]->satuan_dasar_nama : '';
                            ?>
                            <tr class="bg-light">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-end fw-bold text-dark">SALDO AWAL</td>
                                <td class="text-end fw-bold text-primary"><?= $format_qty_global($current_saldo) ?> <?= $unit_name ?></td>
                            </tr>
                            <?php endif; ?>

                            <?php foreach($stock_logs as $sl): ?>
                            <tr>
                                <td>
                                    <span class="text-dark fw-bold"><?= date('d M Y', strtotime($sl->tanggal_mutasi)) ?></span><br>
                                    <span style="font-size: 0.75rem;"><i class="fa fa-clock me-1"></i><?= $sl->waktu_mutasi ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 30px; height: 30px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #475569; font-size: 0.7rem;">
                                            <?= strtoupper(substr($sl->created_by_name ?? 'S', 0, 2)) ?>
                                        </div>
                                        <span class="fw-bold text-dark"><?= $sl->created_by_name ?? 'System' ?></span>
                                    </div>
                                </td>
                                <td>
                                    <a href="javascript:void(0)" onclick="showMaterialDetailModal('<?= $sl->barcode ?>', '<?= $sl->gudang_id ?>')" class="text-decoration-none" title="Lihat Rekapan Stok">
                                        <strong class="text-primary"><?= $sl->bahan_nama ?></strong>
                                    </a><br>
                                    <small class="text-muted"><i class="fa fa-barcode me-1"></i><?= $sl->barcode ?></small>
                                </td>
                                <td><span class="badge bg-light text-dark border"><i class="fa fa-warehouse me-1"></i><?= $sl->gudang_nama ?? '-' ?></span></td>
                                <td>
                                    <?php 
                                        $badgeClass = 'badge-info';
                                        if (in_array($sl->jenis_mutasi, ['Pembelian', 'Inbound'])) $badgeClass = 'badge-in';
                                        if (in_array($sl->jenis_mutasi, ['Pengambilan', 'Produksi', 'Mutation'])) $badgeClass = 'badge-out';
                                    ?>
                                    <span class="badge-action <?= $badgeClass ?>"><?= $sl->jenis_mutasi ?></span>
                                </td>
                                <td class="text-end">
                                    <?php if($sl->qty_in > 0): ?>
                                        <span class="fw-bold text-success">+<?= $format_qty_global($sl->qty_in * ($sl->konversi > 0 ? $sl->konversi : 1)) ?> <?= $sl->satuan_dasar_nama ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if($sl->qty_out > 0): ?>
                                        <span class="fw-bold text-danger"><?= $format_qty_global($sl->qty_out * ($sl->konversi > 0 ? $sl->konversi : 1)) ?> <?= $sl->satuan_dasar_nama ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <?php if (!empty($filters['material_id'])): 
                                    $in_pcs = $sl->qty_in * ($sl->konversi > 0 ? $sl->konversi : 1);
                                    $out_pcs = $sl->qty_out * ($sl->konversi > 0 ? $sl->konversi : 1);
                                    $current_saldo = $current_saldo + $in_pcs - $out_pcs;
                                ?>
                                    <td class="text-end fw-bold text-primary">
                                        <?= $format_qty_global($current_saldo) ?> <?= $sl->satuan_dasar_nama ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB: SYSTEM -->
            <div class="tab-pane fade" id="system" role="tabpanel">
                <div class="p-4 bg-light border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa fa-user-shield text-primary me-2"></i>Riwayat Aktivitas & Keamanan Sistem (<?= count($activity_logs) ?> Log Terakhir)</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-premium mb-0" id="table-system">
                        <thead>
                            <tr>
                                <th>Waktu (Timestamp)</th>
                                <th>User / Karyawan</th>
                                <th>Modul</th>
                                <th>Aksi</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($activity_logs as $al): ?>
                            <tr>
                                <td>
                                    <span class="text-dark fw-bold"><?= date('d M Y', strtotime($al->timestamp)) ?></span><br>
                                    <span style="font-size: 0.75rem;"><i class="fa fa-clock me-1"></i><?= date('H:i:s', strtotime($al->timestamp)) ?></span>
                                </td>
                                <td>
                                    <strong class="text-dark"><?= $al->username ?? 'Unknown' ?></strong><br>
                                    <small class="text-muted"><?= $al->nama_lengkap ?? '' ?></small>
                                </td>
                                <td><span class="badge bg-secondary"><?= $al->module ?></span></td>
                                <td class="text-dark"><?= $al->activity ?></td>
                                <td><code class="text-primary bg-light px-2 py-1 rounded"><?= $al->ip_address ?></code></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });
    
    $('#table-stok').DataTable({
        "order": [],
        "pageLength": 25,
        "language": {
            "search": "Cari Jejak Stok:",
            "lengthMenu": "_MENU_ baris"
        }
    });
    $('#table-system').DataTable({
        "order": [],
        "pageLength": 25,
        "language": {
            "search": "Cari Log Sistem:"
        }
    });
});
</script>
<?php $this->load->view('admin/warehouse/components/v_modal_inventory_detail'); ?>
