<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    :root {
        --adm-primary: #2c3e50;
        --adm-accent: #34495e;
        --adm-highlight: #3498db;
        --adm-danger: #e74c3c;
        --adm-success: #2ecc71;
        --adm-warning: #f1c40f;
        --adm-dark: #1a252f;
    }

    .app-content {
        padding: 1.5rem;
        background: #f4f7f6;
        min-height: 100vh;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        border-top: 4px solid var(--adm-highlight);
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }

    .stat-card.active-users {
        border-top-color: var(--adm-success);
    }

    .stat-card.most-active {
        border-top-color: var(--adm-primary);
    }

    .stat-card h3 {
        font-size: 1.5rem;
        font-weight: 800;
        margin: 0;
        color: var(--adm-primary);
    }

    .stat-card p {
        font-size: 0.75rem;
        color: #7f8c8d;
        font-weight: 700;
        text-transform: uppercase;
        margin: 4px 0 0;
        letter-spacing: 0.5px;
    }

    .control-panel {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        margin-bottom: 1.5rem;
    }

    .log-timeline {
        position: relative;
        padding-left: 2.5rem;
        margin-top: 0.5rem;
    }

    .log-timeline::before {
        content: '';
        position: absolute;
        left: 0.85rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, var(--adm-highlight) 0%, #e2e8f0 100%);
        border-radius: 10px;
    }

    .log-item {
        position: relative;
        background: white;
        border-radius: 10px;
        padding: 0.75rem 1.25rem;
        margin-bottom: 0.75rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        border: 1px solid #edf2f7;
        transition: all 0.2s ease;
    }

    .log-item:hover {
        border-color: var(--adm-highlight);
        background: #f8fafc;
    }

    .log-dot {
        position: absolute;
        left: -2.05rem;
        top: 1.4rem;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: white;
        border: 3px solid var(--adm-highlight);
        box-shadow: 0 0 8px rgba(52, 152, 219, 0.2);
        z-index: 2;
    }

    .log-time {
        font-size: 0.7rem;
        font-weight: 800;
        color: var(--adm-highlight);
        margin-bottom: 0.2rem;
        display: block;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        object-fit: cover;
        margin-right: 12px;
        border: 1.5px solid #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .module-badge {
        font-size: 9px;
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .bg-soft-blue {
        background: #e0f2fe;
        color: #0369a1;
    }

    .bg-soft-green {
        background: #dcfce7;
        color: #15803d;
    }

    .bg-soft-purple {
        background: #f3e8ff;
        color: #7e22ce;
    }

    .bg-soft-orange {
        background: #ffedd5;
        color: #9a3412;
    }

    .action-text {
        font-weight: 700;
        color: var(--adm-primary);
        font-size: 0.85rem;
    }

    .ref-id {
        font-family: 'Monaco', 'Consolas', monospace;
        background: #f1f5f9;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.7rem;
        color: #64748b;
        font-weight: 700;
    }

    .filter-input {
        background: #f8fafc !important;
        border: 2px solid #edf2f7 !important;
        border-radius: 10px !important;
        padding: 0.6rem 1rem !important;
        font-weight: 600;
        transition: all 0.2s;
    }

    .filter-input:focus {
        border-color: var(--adm-highlight) !important;
        background: white !important;
    }
</style>

<div class="app-content mt-n4 pb-5">
    <div class="container-fluid">
        <!-- Header -->
        <div class="row align-items-center mb-4 pt-2">
            <div class="col-md-7 d-flex align-items-center gap-3">
                <a href="<?= base_url('hr') ?>" class="btn btn-white d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px; padding: 0; border-radius: 10px; border: 1px solid #e2e8f0; background: white; color: #334155;" title="Kembali ke Dashboard HR">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h4 class="fw-900 text-dark mb-1"><i class="fas fa-history text-primary me-2"></i>LOG AKTIVITAS KARYAWAN
                    </h4>
                    <p class="text-muted fw-bold mb-0">Pemantauan real-time operasional staf ERP TGM</p>
                </div>
            </div>
            <div class="col-md-5 text-md-end">
                <span class="badge bg-white text-dark shadow-sm px-4 py-2 mt-2 mt-md-0 rounded-pill border fw-800">
                    <i class="fas fa-calendar-day text-primary me-2"></i><?= date('d F Y', strtotime($tanggal)) ?>
                </span>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <h3><?= number_format($stats['total_activities']) ?></h3>
                    <p>Total Aktivitas</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card active-users">
                    <h3><?= number_format($stats['unique_users']) ?></h3>
                    <p>Staf Aktif</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card most-active">
                    <h3><?= $stats['most_active']->username ?? '-' ?></h3>
                    <p>Kontributor Terbanyak (<?= $stats['most_active']->activity_count ?? 0 ?> aksi)</p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-12">
                <!-- Filter Section -->
                <div class="control-panel mb-4">
                    <form action="<?= site_url('karyawan/logs') ?>" method="GET" class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <label class="form-label small fw-900 text-muted opacity-75 mb-1 text-uppercase">Pilih
                                Tanggal Audit</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i
                                        class="fas fa-search-timestamp"></i></span>
                                <input type="date" name="tanggal" class="form-control filter-input"
                                    value="<?= $tanggal ?>" onchange="this.form.submit()">
                            </div>
                        </div>
                        <div class="col-md-8 text-md-end pt-md-4">
                            <button type="button" onclick="window.location.reload()"
                                class="btn btn-primary fw-900 px-4 rounded-pill shadow-sm">
                                <i class="fas fa-sync-alt me-2"></i>PERBARUI DATA
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Timeline Container -->
                <div class="control-panel">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="fw-900 text-dark mb-0 text-uppercase"><i
                                class="fas fa-stream text-primary me-2"></i>Aliran Aktivitas Sistem</h6>
                        <?php if (count($logs) >= 500): ?>
                            <span class="badge bg-soft-orange rounded-pill px-3 fw-800" style="font-size: 0.65rem;">Menampilkan 500 Terbaru</span>
                        <?php else: ?>
                            <span class="badge bg-soft-blue rounded-pill px-3 fw-800">Audit Trail</span>
                        <?php endif; ?>
                    </div>

                    <div class="log-timeline">
                        <?php if (empty($logs)): ?>
                            <div class="text-center py-5">
                                <img src="https://illustrations.popsy.co/slate/communication.svg" style="width: 180px;"
                                    class="mb-4 opacity-75">
                                <h5 class="fw-900 text-dark">Sunyi Senyap...</h5>
                                <p class="text-muted fw-bold">Belum ada aktivitas terekam untuk tanggal ini.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                                <div class="log-item">
                                    <div class="log-dot"></div>
                                    <div class="d-flex align-items-start">
                                        <img src="<?= $log->foto ? base_url('assets/images/karyawan/' . $log->foto) : base_url('assets/images/user.png') ?>"
                                            class="user-avatar"
                                            loading="lazy"
                                            onerror="this.onerror=null; this.src='<?= base_url('assets/images/user.png') ?>'">
                                        <div class="flex-grow-1">
                                            <span class="log-time"><?= date('H:i:s', strtotime($log->timestamp)) ?></span>
                                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                                <div>
                                                    <span
                                                        class="fw-900 text-dark fs-6"><?= $log->nama_lengkap ?: $log->username ?></span>
                                                    <span class="mx-2 text-muted opacity-50">•</span>
                                                    <span
                                                        class="action-text text-uppercase small"><?= str_replace('_', ' ', $log->action) ?></span>
                                                </div>
                                                <div class="mt-2 mt-sm-0">
                                                    <?php
                                                    $module_class = 'bg-soft-blue';
                                                    $mod = strtolower($log->module);
                                                    if ($mod == 'gudang' || $mod == 'stok')
                                                        $module_class = 'bg-soft-orange';
                                                    elseif ($mod == 'kasir' || $mod == 'pembayaran')
                                                        $module_class = 'bg-soft-green';
                                                    elseif ($mod == 'deskprint' || $mod == 'produksi')
                                                        $module_class = 'bg-soft-purple';
                                                    ?>
                                                    <span class="module-badge <?= $module_class ?>"><?= $log->module ?></span>
                                                    <span class="ref-id ms-2">ID: <?= $log->reference_id ?></span>
                                                </div>
                                            </div>
                                            <?php if ($log->divisi_nama): ?>
                                                <div class="small text-muted fw-bold mt-1">
                                                    <i class="fas fa-briefcase me-1 opacity-50"></i> <?= $log->divisi_nama ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>