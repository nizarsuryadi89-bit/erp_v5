<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    /* ==========================================================================
    PREMIUM DESIGN SYSTEM (LEGGER THEME v2) - DATABASE BACKUP REFACTORED
    ========================================================================== */
    :root {
        --primary-gradient: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        --success-gradient: linear-gradient(135deg, #059669 0%, #10b981 100%);
        --warning-gradient: linear-gradient(135deg, #d97706 0%, #fbbf24 100%);
        --danger-gradient: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        --premium-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .report-section-card {
        background: white;
        border-radius: 24px;
        border: none;
        box-shadow: var(--premium-shadow);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .section-header {
        background: var(--primary-gradient);
        padding: 1.25rem 1.75rem;
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
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Action Cards */
    .action-card-p {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        border: 2px solid #f1f5f9;
        transition: all 0.3s;
        text-align: center;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }

    .action-card-p:hover {
        border-color: #4361ee;
        transform: translateY(-5px);
        box-shadow: var(--premium-shadow);
    }

    .action-icon-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 5px;
    }

    /* Table List Scrollable */
    .table-selection-grid {
        max-height: 400px;
        overflow-y: auto;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
    }

    /* Form Label */
    .form-label-premium {
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 1px;
        margin-bottom: 0.5rem;
        display: block;
    }

    /* Backup History Item */
    .backup-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .backup-item:hover {
        background: #f8fafc;
    }

    .backup-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #ecfdf5;
        color: #059669;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-icon-p {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        border: none;
        margin-left: 5px;
    }

    .btn-dl {
        background: #e0f2fe;
        color: #0369a1;
    }

    .btn-rm {
        background: #fee2e2;
        color: #b91c1c;
    }

    .btn-dl:hover {
        background: #bae6fd;
        transform: scale(1.1);
    }

    .btn-rm:hover {
        background: #fecaca;
        transform: scale(1.1);
    }

    @keyframes slideUpFade {
        from {
            opacity: 0;
            transform: translateY(15px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-up {
        animation: slideUpFade 0.5s ease-out forwards;
    }

    .profile-card {
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 1rem;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        height: 100%;
    }

    .profile-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 0.35rem 0.75rem;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.6px;
    }

    .group-block {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #fff;
        padding: 1rem;
        margin-bottom: 1rem;
    }
</style>

<div class="app-content mt-2">
    <div class="container-fluid">
        <!-- Flash Messages -->
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 animate-up" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-3 fs-4"></i>
                    <div>
                        <div class="fw-bold">Berhasil!</div>
                        <div class="small"><?= $this->session->flashdata('success') ?></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 animate-up" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                    <div>
                        <div class="fw-bold">Gagal!</div>
                        <div class="small"><?= $this->session->flashdata('error') ?></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Quick Backup & Maintenance Options -->
        <div class="row g-3 mb-4 animate-up">
            <!-- 1. Full Backup -->
            <div class="col-md-4 col-xl-2">
                <a href="<?= base_url('backup/all') ?>" class="text-decoration-none">
                    <div class="action-card-p shadow-sm border-0 bg-white">
                        <div class="action-icon-circle bg-dark text-white shadow-sm">
                            <i class="fas fa-database"></i>
                        </div>
                        <h6 class="fw-800 text-dark mb-1" style="font-size: 0.75rem;">Full Backup</h6>
                        <p class="small text-muted mb-0" style="font-size: 0.6rem;">Seluruh DB (SQL)</p>
                    </div>
                </a>
            </div>
            
            <!-- 2. Operational Daily -->
            <div class="col-md-4 col-xl-2">
                <a href="<?= base_url('backup/backup_operasional_harian') ?>" class="text-decoration-none">
                    <div class="action-card-p shadow-sm border-primary-subtle" style="background: #f0f9ff;">
                        <div class="action-icon-circle bg-primary text-white shadow">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h6 class="fw-800 text-dark mb-1" style="font-size: 0.75rem;">Operasional</h6>
                        <p class="small text-muted mb-0" style="font-size: 0.6rem;">Backup harian ERP</p>
                    </div>
                </a>
            </div>

            <!-- 3. Static Core -->
            <div class="col-md-4 col-xl-2">
                <a href="<?= base_url('backup/backup_master_periodik') ?>" class="text-decoration-none">
                    <div class="action-card-p shadow-sm border-info-subtle" style="background: #f0fdfa;">
                        <div class="action-icon-circle bg-info text-white shadow">
                            <i class="fas fa-cog"></i>
                        </div>
                        <h6 class="fw-800 text-dark mb-1" style="font-size: 0.75rem;">Master Periodik</h6>
                        <p class="small text-muted mb-0" style="font-size: 0.6rem;">Setup & referensi</p>
                    </div>
                </a>
            </div>

            <!-- 4. Offline Mode -->
            <div class="col-md-4 col-xl-2">
                <a href="<?= base_url('backup/backup_transaksi') ?>" class="text-decoration-none">
                    <div class="action-card-p shadow-sm border-warning-subtle" style="background: #fffbeb;">
                        <div class="action-icon-circle bg-warning text-white shadow">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <h6 class="fw-800 text-dark mb-1" style="font-size: 0.75rem;">Offline Mode</h6>
                        <p class="small text-muted mb-0" style="font-size: 0.6rem;">Transaksi & Stok</p>
                    </div>
                </a>
            </div>

            <!-- 5. Daily Save -->
            <div class="col-md-4 col-xl-2">
                <a href="<?= base_url('backup/daily') ?>" class="text-decoration-none">
                    <div class="action-card-p shadow-sm bg-white">
                        <div class="action-icon-circle bg-success-subtle text-success">
                            <i class="fas fa-history"></i>
                        </div>
                        <h6 class="fw-800 text-dark mb-1" style="font-size: 0.75rem;">Daily Save</h6>
                        <p class="small text-muted mb-0" style="font-size: 0.6rem;">Simpan di Server</p>
                    </div>
                </a>
            </div>

            <!-- 6. Cleanup -->
            <div class="col-md-4 col-xl-2">
                <a href="<?= base_url('backup/cleanup') ?>" class="text-decoration-none"
                    onclick="return confirm('Optimasi DB akan membersihkan log lama. Lanjutkan?')">
                    <div class="action-card-p shadow-sm border-secondary-subtle" style="background: #f8fafc;">
                        <div class="action-icon-circle bg-secondary text-white shadow">
                            <i class="fas fa-broom"></i>
                        </div>
                        <h6 class="fw-800 text-dark mb-1" style="font-size: 0.75rem;">Cleanup</h6>
                        <p class="small text-muted mb-0" style="font-size: 0.6rem;">Optimasi DB</p>
                    </div>
                </a>
            </div>
        </div>

        <div class="report-section-card animate-up">
            <div class="section-header">
                <h5><i class="fas fa-layer-group"></i> ERP Backup Matrix</h5>
            </div>
            <div class="p-4">
                <div class="row g-3">
                    <?php foreach (['operational_daily', 'master_periodic'] as $profileKey): ?>
                        <?php if (!empty($backup_profiles[$profileKey])): ?>
                            <?php $profile = $backup_profiles[$profileKey]; ?>
                            <div class="col-lg-6">
                                <div class="profile-card">
                                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                        <div>
                                            <div class="fw-800 text-dark mb-1"><?= $profile['title'] ?></div>
                                            <div class="small text-muted"><?= $profile['description'] ?></div>
                                        </div>
                                        <span class="profile-badge <?= $profileKey === 'operational_daily' ? 'bg-primary-subtle text-primary' : 'bg-info-subtle text-info' ?>">
                                            <i class="fas <?= $profile['mode'] === 'save' ? 'fa-server' : 'fa-download' ?>"></i>
                                            <?= $profile['mode'] === 'save' ? 'Simpan Server' : 'Download Lokal' ?>
                                        </span>
                                    </div>

                                    <div class="small text-muted mb-2">Tabel terdeteksi: <strong class="text-dark"><?= $profile['table_count'] ?></strong></div>
                                    <div class="small text-dark mb-3" style="line-height: 1.7;">
                                        <?= empty($profile['tables']) ? 'Belum ada tabel yang cocok di database saat ini.' : implode(', ', $profile['tables']) ?>
                                    </div>

                                    <a href="<?= base_url('backup/' . ($profileKey === 'operational_daily' ? 'backup_operasional_harian' : 'backup_master_periodik')) ?>"
                                        class="btn btn-sm <?= $profileKey === 'operational_daily' ? 'btn-primary' : 'btn-info text-white' ?> rounded-pill px-3">
                                        <i class="fas <?= $profileKey === 'operational_daily' ? 'fa-bolt' : 'fa-cog' ?> me-1"></i>
                                        Jalankan <?= $profile['title'] ?>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left: Partial Backup Form -->
            <div class="col-lg-7 animate-up">
                <div class="report-section-card h-100">
                    <form method="post" action="<?= base_url('backup/selected') ?>">
                        <div class="section-header">
                            <h5><i class="fas fa-filter"></i> Partial Data Archiver</h5>
                            <button type="submit"
                                class="btn btn-sm btn-white text-dark rounded-pill px-4 fw-800 shadow-sm border-0">
                                <i class="fas fa-download me-1 text-primary"></i> Backup Selected
                            </button>
                        </div>
                        <div class="p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label-premium mb-0">Tabel Database yang Dipilih</label>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-light border fw-bold"
                                        id="selectAll">Semua</button>
                                    <button type="button" class="btn btn-sm btn-light border fw-bold"
                                        id="clearAll">Kosongkan</button>
                                </div>
                            </div>

                            <div class="table-selection-grid">
                                <?php foreach ($table_groups as $groupKey => $group): ?>
                                    <?php if (empty($group['tables'])) continue; ?>
                                    <div class="group-block">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <div class="fw-800 text-dark" style="font-size: 0.8rem;"><?= $group['title'] ?></div>
                                                <div class="small text-muted"><?= $group['description'] ?></div>
                                            </div>
                                            <button type="button"
                                                class="btn btn-sm btn-light border fw-bold select-group"
                                                data-group="<?= $groupKey ?>">
                                                Pilih Grup
                                            </button>
                                        </div>
                                        <div class="row g-0">
                                            <?php foreach ($group['tables'] as $tbl): ?>
                                                <div class="col-md-6">
                                                    <div class="table-item-row"
                                                        style="display: flex; align-items: center; padding: 6px 12px;">
                                                        <input class="form-check-input table-checkbox group-<?= $groupKey ?>"
                                                            style="width: 18px; height: 18px; margin-right: 12px; border-radius: 4px;"
                                                            type="checkbox" name="tables[]" value="<?= $tbl ?>"
                                                            id="tbl_<?= md5($tbl) ?>">
                                                        <label class="small fw-bold text-dark cursor-pointer"
                                                            for="tbl_<?= md5($tbl) ?>"><?= $tbl ?></label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="mt-3 p-3 bg-light rounded-4 small text-muted">
                                <i class="fas fa-info-circle me-1"></i> Profil ERP di atas memisahkan tabel yang berubah harian dengan tabel master yang berubah periodik. Backup manual tetap bisa dipakai untuk kasus khusus.
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right: Restore & History -->
            <div class="col-lg-5 animate-up" style="animation-delay: 0.1s">
                <!-- Restore Section -->
                <div class="report-section-card mb-4" style="border: 2px dashed #fcd34d;">
                    <div class="section-header" style="background: var(--warning-gradient)">
                        <h5><i class="fas fa-undo-alt"></i> Database Restore</h5>
                    </div>
                    <div class="p-4">
                        <div
                            class="p-3 bg-warning bg-opacity-10 border border-warning-subtle rounded-3 mb-4 text-warning-emphasis small">
                            <i class="fas fa-exclamation-triangle me-2"></i> <strong>Critical:</strong> Proses restore
                            akan menimpa data yang ada saat ini. Pastikan file backup valid.
                        </div>
                        <form method="post" action="<?= base_url('backup/restore') ?>" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label-premium">Upload File Backup (.sql, .zip, .gz)</label>
                                <input type="file" class="form-control" name="restore_file" required accept=".sql,.zip,.gz,.gzip"
                                    style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px;">
                            </div>
                            <button type="submit" class="btn btn-warning w-100 rounded-pill fw-800 text-white shadow-sm"
                                onclick="return confirm('Restore data tidak dapat dibatalkan. Lanjutkan?')">
                                <i class="fas fa-cloud-upload-alt me-1"></i> Jalankan Restore Data
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Backup Vault -->
                <div class="report-section-card">
                    <div class="section-header" style="background: var(--success-gradient)">
                        <h5><i class="fas fa-vault"></i> Secure Server Vault</h5>
                    </div>
                    <div>
                        <?php if (empty($existing_backups)): ?>
                            <div class="p-5 text-center text-muted">
                                <i class="fas fa-inbox fs-1 mb-3 opacity-25"></i>
                                <div class="fw-bold">Belum ada arsip di server</div>
                                <div class="small">Path: uploads/backup</div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($existing_backups as $b): ?>
                                <div class="backup-item">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="backup-icon"><i class="fas fa-file-zipper"></i></div>
                                        <div>
                                            <div class="fw-800 text-dark" style="font-size: 0.8rem;"><?= $b['name'] ?></div>
                                            <div class="text-muted" style="font-size: 0.65rem;">
                                                <i class="fas fa-weight-hanging"></i> <?= number_format($b['size'] / 1024, 2) ?>
                                                KB ·
                                                <i class="fas fa-calendar-alt ms-1"></i> <?= date('d M Y, H:i', $b['mtime']) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex">
                                        <a href="<?= base_url('backup/download_stored/' . rawurlencode($b['name'])) ?>"
                                            class="btn-icon-p btn-dl" title="Download ke Lokal">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <a href="<?= base_url('backup/delete/' . rawurlencode($b['name'])) ?>"
                                            class="btn-icon-p btn-rm"
                                            onclick="return confirm('Hapus file backup ini dari server?')"
                                            title="Hapus Permanen">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
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

<script>
    $(document).ready(function () {
        $('#selectAll').click(function () {
            $('.table-checkbox').prop('checked', true);
        });
        $('#clearAll').click(function () {
            $('.table-checkbox').prop('checked', false);
        });
        $('.select-group').click(function () {
            const group = $(this).data('group');
            $('.group-' + group).prop('checked', true);
        });
    });
</script>
