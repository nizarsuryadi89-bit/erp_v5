<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>
    .kpi-card {
        transition: transform 0.2s ease-in-out;
    }
    .kpi-card:hover {
        transform: translateY(-5px);
    }
    .timeline-item {
        position: relative;
        padding-left: 2rem;
        margin-bottom: 1.5rem;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 6px;
        top: 0;
        bottom: -1.5rem;
        width: 2px;
        background: #e2e8f0;
    }
    .timeline-item:last-child::before {
        display: none;
    }
    .timeline-dot {
        position: absolute;
        left: 0;
        top: 0;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #3b82f6;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #3b82f6;
    }
</style>

<!-- PAGE HEADER -->
<div class="page-header d-print-none mb-3">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle fw-bold text-muted text-uppercase">Owner Dashboard</div>
                <h2 class="page-title text-dark fw-bolder">
                    <span class="badge bg-purple text-white me-2 shadow-sm" style="font-size:.85rem;">👑 OWNER</span>
                    Monitoring Kinerja & Aktivitas Karyawan
                </h2>
                <p class="text-muted small mb-0 mt-1">
                    Pantau produktivitas, aktivitas harian, dan log sistem karyawan secara terpusat.
                </p>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <form method="GET" class="d-flex align-items-center">
                    <label class="me-2 fw-semibold text-muted small">Mulai:</label>
                    <input type="date" name="date_start" class="form-control form-control-sm me-2" value="<?= $date_start ?>">
                    <label class="me-2 fw-semibold text-muted small">Selesai:</label>
                    <input type="date" name="date_end" class="form-control form-control-sm me-2" value="<?= $date_end ?>">
                    <button type="submit" class="btn btn-primary btn-sm fw-bold text-white shadow-sm">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-fluid">
        <div class="card border-0 shadow-sm">
            <!-- Tabs -->
            <ul class="nav nav-tabs nav-fill" data-bs-toggle="tabs">
                <li class="nav-item">
                    <a href="#tabs-kpi" class="nav-link active fw-bold text-dark" data-bs-toggle="tab">
                        <i class="fas fa-chart-line me-2 text-primary"></i> 1. KPI & Leaderboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#tabs-timeline" class="nav-link fw-bold text-dark" data-bs-toggle="tab">
                        <i class="fas fa-stream me-2 text-info"></i> 2. Live Activity Timeline
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#tabs-audit" class="nav-link fw-bold text-dark" data-bs-toggle="tab">
                        <i class="fas fa-shield-alt me-2 text-danger"></i> 3. Audit & Security Logs
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#tabs-wip" class="nav-link fw-bold text-dark" data-bs-toggle="tab">
                        <i class="fas fa-industry me-2 text-warning"></i> 4. Data WIP Produksi
                    </a>
                </li>
            </ul>
            
            <!-- Tab Content -->
            <div class="card-body">
                <div class="tab-content">
                    
                    <!-- TAB 1: KPI & Leaderboard -->
                    <div class="tab-pane active show" id="tabs-kpi">
                        <div class="row row-cards">
                            
                            <!-- Top Kasir / CS -->
                            <div class="col-md-6">
                                <div class="card border border-light h-100">
                                    <div class="card-header bg-white border-bottom-0 py-3">
                                        <h3 class="card-title text-dark fw-bold mb-0">
                                            <span class="me-2 fs-3">🏆</span> Top Performer: Kasir / CS
                                        </h3>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-vcenter table-hover table-sm mb-0">
                                            <thead class="table-light text-muted small text-uppercase fw-bold">
                                                <tr>
                                                    <th class="w-1">No</th>
                                                    <th>Nama Karyawan</th>
                                                    <th class="text-center">Total SPK</th>
                                                    <th class="text-end">Omset Cetak</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(empty($top_kasir)): ?>
                                                    <tr><td colspan="4" class="text-center text-muted py-3">Belum ada data untuk periode ini</td></tr>
                                                <?php else: ?>
                                                    <?php $no=1; foreach($top_kasir as $k): ?>
                                                    <tr>
                                                        <td><?= $no++ ?></td>
                                                        <td class="fw-semibold text-dark">
                                                            <?= $k->nama_lengkap ?: $k->username ?>
                                                            <?= ($no==2) ? '<span class="badge bg-warning text-dark ms-2">👑 #1</span>' : '' ?>
                                                        </td>
                                                        <td class="text-center"><span class="badge bg-primary text-white fw-bold"><?= $k->total_spk ?> SPK</span></td>
                                                        <td class="text-end fw-bold text-success">Rp <?= number_format($k->total_omset, 0, ',', '.') ?></td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Top Produksi -->
                            <div class="col-md-6">
                                <div class="card border border-light h-100">
                                    <div class="card-header bg-white border-bottom-0 py-3">
                                        <h3 class="card-title text-dark fw-bold mb-0">
                                            <span class="me-2 fs-3">🥇</span> Top Performer: Produksi
                                        </h3>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-vcenter table-hover table-sm mb-0">
                                            <thead class="table-light text-muted small text-uppercase fw-bold">
                                                <tr>
                                                    <th class="w-1">No</th>
                                                    <th>Nama Operator</th>
                                                    <th class="text-center">Batch / SPK Selesai</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(empty($top_produksi)): ?>
                                                    <tr><td colspan="3" class="text-center text-muted py-3">Belum ada data produksi selesai bulan ini</td></tr>
                                                <?php else: ?>
                                                    <?php $no=1; foreach($top_produksi as $p): ?>
                                                    <tr>
                                                        <td><?= $no++ ?></td>
                                                        <td class="fw-semibold text-dark">
                                                            <?= $p->nama_lengkap ?: $p->username ?>
                                                            <?= ($no==2) ? '<span class="badge bg-warning text-dark ms-2">👑 #1</span>' : '' ?>
                                                        </td>
                                                        <td class="text-center"><span class="badge bg-success text-white fw-bold"><?= $p->total_diselesaikan ?> Selesai</span></td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Kehadiran / Status Online -->
                            <div class="col-12 mt-4">
                                <div class="card border border-light">
                                    <div class="card-header bg-white py-3 border-bottom-0">
                                        <h3 class="card-title text-dark fw-bold mb-0">
                                            <span class="me-2 fs-3">🟢</span> Kehadiran & Status Login Hari Ini
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <?php foreach($online_status as $os): ?>
                                                <div class="col-6 col-md-4 col-lg-3">
                                                    <div class="card kpi-card shadow-sm border-0 bg-light">
                                                        <div class="card-body p-3 d-flex align-items-center">
                                                            <?php if($os->status == 'Online Today'): ?>
                                                                <span class="avatar bg-success text-white fw-bold me-3">ON</span>
                                                            <?php else: ?>
                                                                <span class="avatar bg-secondary text-white fw-bold me-3">OFF</span>
                                                            <?php endif; ?>
                                                            <div>
                                                                <div class="fw-bold text-dark"><?= $os->nama_lengkap ?: $os->username ?></div>
                                                                <div class="text-muted small">Terakhir: <?= $os->last_login ? date('d M, H:i', strtotime($os->last_login)) : 'Belum Pernah' ?></div>
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

                    <!-- TAB 2: Live Activity Timeline -->
                    <div class="tab-pane" id="tabs-timeline">
                        <div class="card border border-light border-0 shadow-none">
                            <div class="card-header bg-white border-0 py-3">
                                <h3 class="card-title text-dark fw-bold mb-0">
                                    <span class="me-2 fs-3">⏱️</span> Live Activity Feed (50 Aktivitas Terakhir)
                                </h3>
                            </div>
                            <div class="card-body">
                                <?php if(empty($timeline)): ?>
                                    <div class="alert alert-info border-0 shadow-sm bg-info-lt text-info fw-bold">
                                        <i class="fas fa-info-circle me-2"></i> Belum ada aktivitas yang terekam hari ini.
                                    </div>
                                <?php else: ?>
                                    <div class="ps-3 pt-2">
                                        <?php foreach($timeline as $t): ?>
                                            <div class="timeline-item">
                                                <div class="timeline-dot bg-blue"></div>
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <div class="fw-bold text-dark"><?= $t->nama_lengkap ?: $t->username ?></div>
                                                    <span class="text-muted small fw-semibold"><?= date('d M Y, H:i:s', strtotime($t->created_at)) ?></span>
                                                </div>
                                                <div class="text-secondary">
                                                    <span class="badge bg-light text-muted border px-2 py-1 me-2"><?= $t->action_type ?></span>
                                                    <?= $t->description ?> 
                                                    <?php if($t->reference_id): ?>
                                                        <span class="text-primary fw-bold">(Ref: <?= $t->reference_id ?>)</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: Audit & Security Logs -->
                    <div class="tab-pane" id="tabs-audit">
                         <div class="alert alert-danger border-0 shadow-sm bg-danger-lt text-danger fw-bold mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i> Ini adalah log khusus tindakan sensitif seperti Hapus Transaksi, Ubah Harga, atau Batal Pesanan.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter table-hover table-sm mb-0">
                                <thead class="table-light text-muted small text-uppercase fw-bold">
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Karyawan</th>
                                        <th>Tindakan (Action)</th>
                                        <th>Detail Keterangan</th>
                                        <th>IP Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($audit_logs)): ?>
                                        <tr><td colspan="5" class="text-center text-muted py-4">Aman! Tidak ada log aktivitas mencurigakan.</td></tr>
                                    <?php else: ?>
                                        <?php foreach($audit_logs as $log): ?>
                                        <tr>
                                            <td class="text-nowrap text-muted"><?= date('d M Y H:i', strtotime($log->created_at)) ?></td>
                                            <td class="fw-semibold text-dark"><?= $log->nama_lengkap ?: $log->username ?></td>
                                            <td><span class="badge bg-danger-lt fw-bold border border-danger"><?= $log->action_type ?></span></td>
                                            <td class="text-secondary"><?= $log->description ?> (Ref: <?= $log->reference_id ?>)</td>
                                            <td class="text-muted small"><?= $log->ip_address ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 4: Data WIP Produksi -->
                    <div class="tab-pane" id="tabs-wip">
                        <div class="alert alert-info border-0 shadow-sm bg-info-lt text-info fw-bold mb-3">
                            <i class="fas fa-info-circle me-2"></i> Menampilkan antrean WIP produksi aktual berdasarkan rentang tanggal yang dipilih.
                        </div>
                        <div class="ratio ratio-16x9">
                            <iframe src="<?= site_url('wip/produksi?iframe=1&date_start=' . $date_start . '&date_end=' . $date_end) ?>" class="border-0 w-100 rounded shadow-sm" style="min-height: 800px;"></iframe>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
