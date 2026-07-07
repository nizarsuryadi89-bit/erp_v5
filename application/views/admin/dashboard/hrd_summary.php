<?php $this->load->view('layout_admin/header_modul'); ?>

<style>
    :root {
        --hr-bg: #f8fafc;
        --hr-card-bg: #ffffff;
        --hr-border: #e2e8f0;
        --hr-primary: #3b82f6;
        --hr-primary-soft: #eff6ff;
        --hr-text-dark: #1e293b;
        --hr-text-muted: #64748b;
        --hr-success: #10b981;
        --hr-success-soft: #ecfdf5;
        --hr-warning: #f59e0b;
        --hr-warning-soft: #fffbeb;
        --hr-danger: #ef4444;
        --hr-danger-soft: #fef2f2;
    }

    .hr-minimal-container {
        font-family: 'JetBrains Mono', monospace !important;
        background-color: var(--hr-bg);
        min-height: 100vh;
        color: var(--hr-text-dark);
    }

    .clean-card {
        background: var(--hr-card-bg);
        border: 1px solid var(--hr-border);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }

    .clean-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .stat-badge {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-badge.primary {
        background: var(--hr-primary-soft);
        color: var(--hr-primary);
    }

    .stat-badge.success {
        background: var(--hr-success-soft);
        color: var(--hr-success);
    }

    .stat-badge.warning {
        background: var(--hr-warning-soft);
        color: var(--hr-warning);
    }

    .stat-badge.danger {
        background: var(--hr-danger-soft);
        color: var(--hr-danger);
    }

    .hr-shortcut-box {
        background: white;
        border: 1px solid var(--hr-border);
        border-radius: 12px;
        padding: 1.25rem;
        text-decoration: none !important;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.2s;
        height: 100%;
    }

    .hr-shortcut-box:hover {
        background: var(--hr-primary-soft);
        border-color: var(--hr-primary);
        transform: translateY(-2px);
    }

    .hr-shortcut-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        background: #f1f5f9;
        color: var(--hr-primary);
        transition: all 0.2s;
    }

    .hr-shortcut-box:hover .hr-shortcut-icon {
        background: var(--hr-primary);
        color: white;
    }

    .minimal-table th {
        background: #f8fafc;
        text-transform: uppercase;
        font-size: 0.65rem;
        font-weight: 800;
        letter-spacing: 1px;
        color: var(--hr-text-muted);
        border-top: none;
    }

    .minimal-table td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
        font-size: 0.875rem;
    }

    .avatar-sm {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        object-fit: cover;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: var(--hr-primary);
    }

    .section-label {
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--hr-text-muted);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 1.5rem;
        display: block;
    }

    .hc-analytics-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 8px;
        border: 1px solid var(--hr-border);
        font-size: 0.8rem;
        font-weight: 600;
        background: white;
    }

    @keyframes pulse-blue {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
        }

        70% {
            transform: scale(1.05);
            box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
        }

        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
        }
    }

    .pulse-blue {
        animation: pulse-blue 2s infinite;
    }

    /* SOP Checklist Styles */
    .sop-item {
        padding: 10px 12px;
        border-radius: 8px;
        transition: all 0.2s;
        border: 1px solid transparent;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
    }

    .sop-item:hover {
        background: var(--hr-primary-soft);
        border-color: var(--hr-primary);
    }

    .sop-check {
        width: 18px;
        height: 18px;
        border: 2px solid var(--hr-border);
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        background: white;
    }

    .sop-item.completed {
        opacity: 0.6;
    }

    .sop-item.completed .sop-check {
        background: var(--hr-success);
        border-color: var(--hr-success);
        color: white;
    }

    .sop-item.completed .sop-text {
        text-decoration: line-through;
    }
</style>

<div class="hr-minimal-container mt-n4 pb-5">
    <div class="container-fluid pt-4">

        <!-- Minimalist Header -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h3 class="fw-bold mb-1 text-primary-emphasis">RINGKASAN HR</h3>
                <p class="text-muted small mb-0 fw-medium"><?= $this->session->userdata('nama_perusahaan') ?> •
                    <?= date('d F Y') ?>
                </p>
            </div>
            <div class="d-flex gap-3 align-items-center">
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal"
                    data-bs-target="#modalGuideHR">
                    <i class="fas fa-book-open me-2"></i> PANDUAN PENGGUNAAN
                </button>
                <div class="text-end border-start ps-3">
                    <div class="h4 fw-bold mb-0"><?= date('H:i') ?></div>
                    <div class="small text-muted fw-bold text-uppercase"
                        style="font-size: 0.65rem; letter-spacing: 1px;">Waktu Operasional</div>
                </div>
            </div>
        </div>

        <!-- HR Shortcuts: The core of utility -->
        <div class="mb-5">
            <span class="section-label">Shortcut Utama HR</span>
            <div class="row g-3">
                <div class="col-md-3">
                    <a href="<?= site_url('absensi') ?>" class="hr-shortcut-box">
                        <div class="hr-shortcut-icon text-primary"><i class="fas fa-user-check"></i></div>
                        <div>
                            <div class="fw-bold text-dark">Absensi Harian</div>
                            <small class="text-muted">Cek kehadiran hari ini</small>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?= site_url('absensi/rekap_absen_bulanan') ?>" class="hr-shortcut-box">
                        <div class="hr-shortcut-icon text-indigo" style="color: #6366f1;"><i
                                class="fas fa-calendar-alt"></i></div>
                        <div>
                            <div class="fw-bold text-dark">Rekapan Bulanan</div>
                            <small class="text-muted">Laporan utuh 1 bulan</small>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?= site_url('karyawan/penggajian') ?>" class="hr-shortcut-box">
                        <div class="hr-shortcut-icon text-success"><i class="fas fa-money-check-alt"></i></div>
                        <div>
                            <div class="fw-bold text-dark">Pusat Payroll</div>
                            <small class="text-muted">Gaji & sinkronisasi</small>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?= site_url('karyawan') ?>" class="hr-shortcut-box">
                        <div class="hr-shortcut-icon text-info"><i class="fas fa-users-cog"></i></div>
                        <div>
                            <div class="fw-bold text-dark">Data Karyawan</div>
                            <small class="text-muted">Master database staf</small>
                        </div>
                    </a>
                </div>
                <!-- Row 2 -->
                <div class="col-md-3">
                    <a href="<?= site_url('karyawan/rekap_lembur') ?>" class="hr-shortcut-box">
                        <div class="hr-shortcut-icon text-warning"><i class="fas fa-history"></i></div>
                        <div>
                            <div class="fw-bold text-dark">Lemburan</div>
                            <small class="text-muted">Cek lembur harian</small>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?= site_url('absensi/approval_cuti') ?>" class="hr-shortcut-box">
                        <div class="hr-shortcut-icon text-rose" style="color: #e11d48;"><i
                                class="fas fa-umbrella-beach"></i></div>
                        <div>
                            <div class="fw-bold text-dark">Cuti & Izin</div>
                            <small class="text-muted">Persetujuan absen</small>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?= site_url('karyawan/pengaturan_lembur') ?>" class="hr-shortcut-box">
                        <div class="hr-shortcut-icon text-dark"><i class="fas fa-cogs"></i></div>
                        <div>
                            <div class="fw-bold text-dark">Atur Kebijakan</div>
                            <small class="text-muted">Shift & jam lembur</small>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?= site_url('karyawan/laporan_payroll_bulanan') ?>" class="hr-shortcut-box">
                        <div class="hr-shortcut-icon text-purple" style="color: #a855f7;"><i
                                class="fas fa-file-invoice-dollar"></i></div>
                        <div>
                            <div class="fw-bold text-dark">Laporan Gaji</div>
                            <small class="text-muted">Arsip payroll bulanan</small>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?= site_url('karyawan/leger_hrd') ?>" class="hr-shortcut-box"
                        style="border: 1px solid #fbbf24; background: #fffbeb;">
                        <div class="hr-shortcut-icon" style="color: #d97706;"><i class="fas fa-star"></i></div>
                        <div>
                            <div class="fw-bold text-dark">Master Leger HRD</div>
                            <small class="text-muted">Aggregator all-in-one</small>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?= site_url('karyawan/logs') ?>" class="hr-shortcut-box">
                        <div class="hr-shortcut-icon text-secondary"><i class="fas fa-shield-alt"></i></div>
                        <div>
                            <div class="fw-bold text-dark">Internal Audit</div>
                            <small class="text-muted">Keamanan data HR</small>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?= site_url('pengaturan/user_account') ?>" class="hr-shortcut-box position-relative">
                        <div class="position-absolute top-0 start-100 translate-middle">
                            <span class="badge rounded-pill bg-danger pulse-blue"
                                style="font-size: 0.65rem; box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); animation: pulse-red 2s infinite;">NEW</span>
                        </div>
                        <div class="hr-shortcut-icon text-danger"><i class="fas fa-user-shield"></i></div>
                        <div>
                            <div class="fw-bold text-dark">Pengguna Sistem <span class="badge bg-danger ms-1"
                                    style="font-size:0.55rem;">NEW</span></div>
                            <small class="text-muted">Hak akses & sandi</small>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?= site_url('absensi/sisa_cuti') ?>" class="hr-shortcut-box">
                        <div class="hr-shortcut-icon" style="color: #0d9488;"><i class="fas fa-chart-pie"></i></div>
                        <div>
                            <div class="fw-bold text-dark">Sisa Cuti Karyawan</div>
                            <small class="text-muted">Pantau kuota cuti</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Modul Keuangan (Finance Integration for HR) -->
        <div class="mb-5">
            <span class="section-label">Modul Keuangan </span>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="clean-card p-4 h-100 border-start border-4 border-success">
                        <span class="stat-badge success mb-3"><i class="fas fa-arrow-down me-1"></i>Pemasukan</span>
                        <div class="d-flex flex-column gap-3">
                            <div>
                                <small class="text-muted d-block fw-bold text-uppercase"
                                    style="font-size: 0.65rem;">Harian (Hari Ini)</small>
                                <h4 class="fw-bold text-success mb-0">Rp
                                    <?= number_format($pemasukan_hariini ?? 0, 0, ',', '.') ?>
                                </h4>
                            </div>
                            <div>
                                <small class="text-muted d-block fw-bold text-uppercase"
                                    style="font-size: 0.65rem;">Bulanan (Bulan Ini)</small>
                                <h4 class="fw-bold text-success mb-0">Rp
                                    <?= number_format($pemasukan_bulanini ?? 0, 0, ',', '.') ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="clean-card p-4 h-100 border-start border-4 border-danger">
                        <span class="stat-badge danger mb-3"><i class="fas fa-arrow-up me-1"></i>Pengeluaran</span>
                        <div class="d-flex flex-column gap-3">
                            <div>
                                <small class="text-muted d-block fw-bold text-uppercase"
                                    style="font-size: 0.65rem;">Harian (Hari Ini)</small>
                                <h4 class="fw-bold text-danger mb-0">Rp
                                    <?= number_format($pengeluaran_hariini ?? 0, 0, ',', '.') ?>
                                </h4>
                            </div>
                            <div>
                                <small class="text-muted d-block fw-bold text-uppercase"
                                    style="font-size: 0.65rem;">Bulanan (Bulan Ini)</small>
                                <h4 class="fw-bold text-danger mb-0">Rp
                                    <?= number_format($pengeluaran_bulanini ?? 0, 0, ',', '.') ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="clean-card p-4 h-100 border-start border-4 border-warning d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="stat-badge warning m-0"><i
                                    class="fas fa-file-invoice-dollar me-1"></i>Informasi Hutang</span>
                        </div>
                        <div
                            class="flex-grow-1 d-flex flex-column mt-2 text-center align-items-center justify-content-center p-3 bg-light rounded bg-opacity-50">
                            <small class="text-muted d-block fw-bold mb-2 text-uppercase"
                                style="font-size: 0.70rem;">Total Hutang ke Supplier</small>
                            <h3 class="fw-bold text-warning mb-3" style="color: #d97706 !important;">Rp
                                <?= number_format($total_hutang ?? 0, 0, ',', '.') ?>
                            </h3>

                            <div class="d-flex gap-2 w-100 px-2 mt-auto">
                                <a href="<?= site_url('laporan/hutang') ?>"
                                    class="btn btn-sm btn-outline-warning fw-bold w-50" style="font-size: 0.75rem;"><i
                                        class="fas fa-hand-holding-usd me-1"></i> Data Hutang</a>
                                <a href="<?= site_url('pengaturan/supplier') ?>"
                                    class="btn btn-sm btn-warning text-white fw-bold w-50"
                                    style="font-size: 0.75rem;"><i class="fas fa-truck-loading me-1"></i> Supplier</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval Command Center: Real-time action items -->
        <div class="mb-5">
            <span class="section-label">Pusat Persetujuan & Tugas Payroll</span>
            <div class="row g-3">
                <!-- NEW: Pending Payroll Sync (Integration Alert) -->
                <div class="col-md-3">
                    <div
                        class="clean-card p-3 d-flex align-items-center justify-content-between h-100 border-start border-4 border-primary">
                        <div class="d-flex align-items-center gap-3">
                            <div class="hr-shortcut-icon pulse-blue"
                                style="background: var(--hr-primary-soft); color: var(--hr-primary); width: 40px; height: 40px;">
                                <i class="fas fa-sync-alt"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark small">Pending Sync Payroll</div>
                                <small class="text-muted x-small">Absensi belum terkunci</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge rounded-pill bg-primary text-white fw-bold px-3 py-1"
                                style="font-size: 0.75rem;">
                                <?= isset($pending_sync_count) ? $pending_sync_count : '0' ?> Staf
                            </span>
                            <div class="mt-1">
                                <a href="<?= site_url('karyawan/penggajian') ?>"
                                    class="text-primary x-small fw-bold text-decoration-none">Sinkronkan <i
                                        class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="clean-card p-3 d-flex align-items-center justify-content-between h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="hr-shortcut-icon"
                                style="background: var(--hr-warning-soft); color: var(--hr-warning); width: 40px; height: 40px;">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark small">Persetujuan Izin</div>
                                <small class="text-muted x-small">Cuti/Sakit pending</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge rounded-pill bg-warning text-dark fw-bold px-3 py-1"
                                style="font-size: 0.75rem;">
                                <?= number_format($pending_izin_count) ?> Izin
                            </span>
                            <div class="mt-1">
                                <a href="<?= site_url('absensi/approval_cuti') ?>"
                                    class="text-primary x-small fw-bold text-decoration-none">Kelola <i
                                        class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="clean-card p-3 d-flex align-items-center justify-content-between h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="hr-shortcut-icon"
                                style="background: var(--hr-danger-soft); color: var(--hr-danger); width: 40px; height: 40px;">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark small">Pelunasan Slip</div>
                                <small class="text-muted x-small">Gaji belum terbayar</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge rounded-pill bg-danger text-white fw-bold px-3 py-1"
                                style="font-size: 0.75rem;">
                                <?= number_format($pending_payroll_count) ?> Slip
                            </span>
                            <div class="mt-1">
                                <a href="<?= site_url('karyawan/penggajian') ?>"
                                    class="text-danger x-small fw-bold text-decoration-none">Proses <i
                                        class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div
                        class="clean-card p-3 d-flex align-items-center justify-content-between h-100 <?= ($pending_kontrak_count > 0) ? 'border-start border-4 border-success pulse-blue' : '' ?>">
                        <div class="d-flex align-items-center gap-3">
                            <div class="hr-shortcut-icon"
                                style="background: var(--hr-success-soft); color: var(--hr-success); width: 40px; height: 40px;">
                                <i class="fas fa-file-signature"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark small">Perpanjangan Kontrak</div>
                                <small class="text-muted x-small">Pengajuan kontrak baru</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge rounded-pill bg-success text-white fw-bold px-3 py-1"
                                style="font-size: 0.75rem;">
                                <?= number_format($pending_kontrak_count) ?> Ajuan
                            </span>
                            <div class="mt-1">
                                <a href="#expiring_contracts_table"
                                    class="text-success x-small fw-bold text-decoration-none"
                                    onclick="document.getElementById('expiring_contracts_table').scrollIntoView({behavior: 'smooth'})">Lihat
                                    <i class="fas fa-chevron-down"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overdue Contracts Alert -->
        <?php
        $overdue_contracts = array_filter($expiring_contracts ?? [], function ($ec) {
            return new DateTime($ec->akhir_kontrak) < new DateTime(date('Y-m-d'));
        });
        ?>
        <?php if (!empty($overdue_contracts)): ?>
            <div class="clean-card p-3 mb-4 border-danger pulse-blue"
                style="background: #fef2f2; border-left: 5px solid #ef4444; box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); animation: pulse-red 2s infinite;">
                <style>
                    @keyframes pulse-red {
                        0% {
                            transform: scale(1);
                            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
                        }

                        70% {
                            transform: scale(1.02);
                            box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
                        }

                        100% {
                            transform: scale(1);
                            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
                        }
                    }
                </style>
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px;">
                        <i class="fas fa-exclamation-octagon"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-danger">KONTRAK KEDALUWARSA!</h6>
                        <p class="small mb-0 text-muted">Terdapat <b><?= count($overdue_contracts) ?> karyawan</b> yang masa
                            kontraknya <b>sudah lewat</b> dan belum ada keputusan perpanjangan.</p>
                    </div>
                    <a href="#expiring_contracts_table" class="btn btn-sm btn-danger ms-auto fw-bold"
                        onclick="document.getElementById('expiring_contracts_table').scrollIntoView({behavior: 'smooth'})">
                        TINDAK LANJUTI
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Late Employees Alert: Dynamic warning -->
        <?php if (!empty($late_employees_today)): ?>
            <div class="clean-card p-3 mb-5 border-warning" style="background: #fffbeb; border-left: 5px solid #f59e0b;">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-warning-emphasis">KARYAWAN KESIANGAN HARI INI</h6>
                        <p class="small mb-0 text-muted">Terdapat <b><?= count($late_employees_today) ?> karyawan</b> yang
                            terlambat masuk melebihi batas toleransi 15 menit.</p>
                    </div>
                    <button class="btn btn-sm btn-warning ms-auto fw-bold" type="button" data-bs-toggle="collapse"
                        data-bs-target="#lateList">
                        LIHAT DATA
                    </button>
                </div>
                <div class="collapse" id="lateList">
                    <div class="row g-2 mt-3">
                        <?php foreach ($late_employees_today as $le): ?>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center p-2 bg-white rounded border border-warning-subtle">
                                    <img src="<?= $le->foto ? base_url('assets/images/karyawan/' . $le->foto) : base_url('assets/images/user.png') ?>"
                                        class="avatar-sm me-2" alt="">
                                    <div>
                                        <div class="fw-bold" style="font-size: 0.75rem;"><?= $le->nama_lengkap ?></div>
                                        <div class="text-danger fw-bold" style="font-size: 0.7rem;">Masuk:
                                            <?= date('H:i', strtotime($le->jam_masuk)) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Key Metrics: Clean stats -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="clean-card p-4">
                    <span class="stat-badge primary mb-3">Total Karyawan</span>
                    <div class="d-flex justify-content-between align-items-end">
                        <h2 class="fw-bold mb-0"><?= number_format($total_karyawan) ?></h2>
                        <i class="fas fa-users text-light fs-1"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="clean-card p-4">
                    <span class="stat-badge success mb-3">Hadir Hari Ini</span>
                    <div class="d-flex justify-content-between align-items-end">
                        <h2 class="fw-bold mb-0"><?= number_format($absensi_stats['hadir']) ?></h2>
                        <span class="small fw-bold text-success"><i class="fas fa-heartbeat"></i>
                            <?= $attendance_health ?>%</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="clean-card p-4">
                    <span class="stat-badge warning mb-3">Izin/Sakit</span>
                    <div class="d-flex justify-content-between align-items-end">
                        <h2 class="fw-bold mb-0"><?= number_format($absensi_stats['izin']) ?></h2>
                        <i class="fas fa-hospital-user text-light fs-1"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="clean-card p-4">
                    <span class="stat-badge danger mb-3">Kedisiplinan</span>
                    <div class="d-flex justify-content-between align-items-end">
                        <h2 class="fw-bold mb-0"><?= number_format($absensi_stats['telat'] + $absensi_stats['alpha']) ?>
                        </h2>
                        <small class="text-muted fw-bold">Perlu perhatian</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column: Talent Stream -->
            <div class="col-lg-8">
                <!-- EXPIRING CONTRACTS ALERT -->
                <?php if (!empty($expiring_contracts)): ?>
                    <div id="expiring_contracts_table" class="clean-card p-4 mb-4 border-danger"
                        style="background: #fef2f2; border-left: 5px solid #ef4444;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 text-danger"><i class="fas fa-file-signature me-2"></i>PERINGATAN HABIS
                                KONTRAK (SISA < 30 HARI)</h6>
                                    <span class="badge bg-danger rounded-pill"><?= count($expiring_contracts) ?>
                                        Karyawan</span>
                        </div>
                        <p class="small text-danger fw-bold mb-3">Karyawan dengan status Kontrak/Magang berikut akan segera
                            habis masa kontraknya. Segera evaluasi kelanjutan kontrak mereka.
                        </p>
                        <div class="table-responsive">
                            <table class="table minimal-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Karyawan</th>
                                        <th>Status Saat Ini</th>
                                        <th class="text-end">Estimasi Perpanjangan</th>
                                        <th class="text-center">Pengajuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($expiring_contracts as $ec): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-3"
                                                        style="width: 30px; height: 30px; background: white;">
                                                        <?php if ($ec->foto): ?>
                                                            <img src="<?= base_url('assets/images/karyawan/' . $ec->foto) ?>"
                                                                class="avatar-sm" style="width: 30px; height: 30px;" alt="">
                                                        <?php else: ?>
                                                            <?= substr($ec->nama_lengkap, 0, 1) ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold" style="font-size: 0.8rem;"><?= $ec->nama_lengkap ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-soft-warning text-warning fw-bold border border-warning"
                                                    style="font-size: 0.65rem;"><?= $ec->status_karyawan ?></span>
                                            </td>
                                            <td class="text-end">
                                                <span class="text-danger fw-bold small">
                                                    <?php
                                                    $akhir_kontrak = date('d M Y', strtotime($ec->akhir_kontrak));
                                                    $today = new DateTime(date('Y-m-d'));
                                                    $end_date = new DateTime($ec->akhir_kontrak);
                                                    $diff = $today->diff($end_date);

                                                    if ($end_date < $today) {
                                                        echo $akhir_kontrak . ' <br><span class="badge bg-danger mt-1">Berakhir ' . $diff->days . ' hari lalu</span>';
                                                    } elseif ($diff->days == 0) {
                                                        echo $akhir_kontrak . ' <br><span class="badge bg-warning text-dark mt-1">Berakhir Hari Ini</span>';
                                                    } else {
                                                        echo $akhir_kontrak . ' <br><span class="badge bg-warning text-dark border border-warning mt-1">Sisa ' . $diff->days . ' hari</span>';
                                                    }
                                                    ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <?php if (!empty($ec->pengajuan_id) && $ec->pengajuan_status === 'Pending'): ?>
                                                    <div class="small fw-bold text-primary mb-1"><?= $ec->durasi_perpanjangan ?>
                                                        Bulan</div>
                                                    <button type="button" class="btn btn-sm btn-success fw-bold p-1 px-2 text-white"
                                                        style="font-size: 0.65rem;"
                                                        onclick="approveKontrak(<?= $ec->pengajuan_id ?>)"><i
                                                            class="fas fa-check"></i> Approve</button>
                                                <?php else: ?>
                                                    <span class="text-muted small">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="clean-card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="fw-bold mb-0">ONBOARDING TERBARU</h6>
                        <a href="<?= site_url('karyawan') ?>"
                            class="text-primary small fw-bold text-decoration-none">Lihat Semua</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table minimal-table">
                            <thead>
                                <tr>
                                    <th>Karyawan</th>
                                    <th>Jabatan</th>
                                    <th>Divisi/Cabang</th>
                                    <th class="text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_employees)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted small fw-bold">Belum ada
                                            aktivitas terbaru.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recent_employees as $emp): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-3">
                                                        <?php if ($emp->foto): ?>
                                                            <img src="<?= base_url('assets/images/karyawan/' . $emp->foto) ?>"
                                                                class="avatar-sm" alt="">
                                                        <?php else: ?>
                                                            <?= substr($emp->nama_lengkap, 0, 1) ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold"><?= $emp->nama_lengkap ?></div>
                                                        <small class="text-muted">NIK: <?= $emp->nik ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold"><?= $emp->nama_jabatan ?></div>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <span class="badge bg-light text-dark fw-bold border"
                                                        style="font-size: 0.65rem;"><?= $emp->divisi_nama ?></span>
                                                    <span class="badge bg-light text-primary fw-bold border"
                                                        style="font-size: 0.65rem;"><?= $emp->branch_name ?></span>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <span
                                                    class="badge rounded-pill <?= $emp->status_karyawan == 'Tetap' ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning' ?> fw-bold px-3 py-1">
                                                    <?= $emp->status_karyawan ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- SYSTEM AUDIT TRAIL (Moved from Admin) -->
                <div class="clean-card p-4 mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="fw-bold mb-0 text-dark-blue"><i
                                class="fas fa-history text-primary me-2"></i>AKTIVITAS SISTEM TERKINI</h6>
                        <span class="badge bg-soft-blue text-primary fw-bold rounded-pill"
                            style="font-size: 0.6rem;">ADMIN AUDIT</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table minimal-table mb-0">
                            <thead>
                                <tr class="text-muted small">
                                    <th>WAKTU</th>
                                    <th>USER</th>
                                    <th>AKSI</th>
                                    <th class="text-end">MODUL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_logs)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small fw-bold">Tidak ada
                                            aktivitas sistem tercatat.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recent_logs as $log): ?>
                                        <tr>
                                            <td class="text-muted fw-bold" style="font-size: 0.75rem;">
                                                <?= date('H:i d/m', strtotime($log->timestamp)) ?>
                                            </td>
                                            <td>
                                                <div class="fw-bold small"><?= $log->username ?></div>
                                            </td>
                                            <td><span
                                                    class="small fw-800 text-dark"><?= str_replace('_', ' ', $log->action) ?></span>
                                            </td>
                                            <td class="text-end"><span class="badge bg-light text-primary fw-bold"
                                                    style="font-size: 0.6rem;"><?= $log->module ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-center">
                        <a href="<?= site_url('karyawan/logs') ?>"
                            class="btn btn-sm btn-light fw-800 px-4 rounded-pill shadow-sm">PANTAU SEMUA AKTIVITAS</a>
                    </div>
                </div>
            </div>

            <!-- Right Column: HC Insights -->
            <div class="col-lg-4">
                <!-- Daily SOP Checklist -->
                <div class="clean-card p-4 mb-4 border-start border-4 border-primary"
                    style="background: linear-gradient(to right, #f0f7ff, #ffffff);">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 text-primary-emphasis"><i class="fas fa-tasks me-2"></i> RUTINITAS
                            HARIAN HR</h6>
                        <span class="badge bg-primary rounded-pill x-small" style="font-size: 0.6rem;">SOP</span>
                    </div>

                    <div class="d-flex flex-column gap-1">
                        <div class="sop-item">
                            <div class="sop-check"><i class="fas fa-check small"></i></div>
                            <span class="sop-text small fw-bold text-dark">1. Cek Absensi Karyawan</span>
                        </div>
                        <div class="sop-item">
                            <div class="sop-check"><i class="fas fa-check small"></i></div>
                            <span class="sop-text small fw-bold text-dark">2. Cek Pengajuan Cuti & Izin</span>
                        </div>
                        <div class="sop-item">
                            <div class="sop-check"><i class="fas fa-check small"></i></div>
                            <span class="sop-text small fw-bold text-dark">3. Cek Lembur Karyawan</span>
                        </div>
                        <div class="sop-item">
                            <div class="sop-check"><i class="fas fa-check small"></i></div>
                            <span class="sop-text small fw-bold text-dark">4. Cek Gaji/Payroll Karyawan</span>
                        </div>
                        <div class="sop-item">
                            <div class="sop-check"><i class="fas fa-check small"></i></div>
                            <span class="sop-text small fw-bold text-dark">5. Cek KPI & Kinerja Staf</span>
                        </div>
                        <div class="sop-item">
                            <div class="sop-check"><i class="fas fa-check small"></i></div>
                            <span class="sop-text small fw-bold text-dark">6. Cek Dokumen (KTP/KK/Lainnya)</span>
                        </div>
                        <div class="sop-item">
                            <div class="sop-check"><i class="fas fa-check small"></i></div>
                            <span class="sop-text small fw-bold text-dark">7. Cek Masa Kontrak Karyawan</span>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-top">
                        <p class="x-small text-muted mb-0 italic">* Checklist ini akan tereset otomatis setiap hari
                            untuk memastikan kedisiplinan operasional.</p>
                    </div>
                </div>

                <!-- Gender & Division Stats -->
                <div class="clean-card p-4 mb-4">
                    <h6 class="fw-bold mb-4">DIVERSITAS & DISTRIBUSI</h6>
                    <div class="row g-3">
                        <div class="col-6 text-center">
                            <div style="position: relative; height: 150px; width: 100%;">
                                <canvas id="genderChart"></canvas>
                            </div>
                            <p class="small text-muted fw-bold mt-2 mb-0">Rasio Gender</p>
                        </div>
                        <div class="col-6 text-center">
                            <div style="position: relative; height: 150px; width: 100%;">
                                <canvas id="divisionChart"></canvas>
                            </div>
                            <p class="small text-muted fw-bold mt-2 mb-0">Demografi Divisi</p>
                        </div>
                    </div>
                </div>

                <!-- Status Karyawan Stats -->
                <div class="clean-card p-4 mb-4">
                    <h6 class="fw-bold mb-3">STATUS KEPEGAWAIAN (RETENSI)</h6>
                    <div class="d-flex align-items-center">
                        <div style="position: relative; height: 120px; width: 120px;">
                            <canvas id="statusChart"></canvas>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <?php if (!empty($status_stats) && is_array($status_stats)):
                                foreach ($status_stats as $ss): ?>
                                    <div class="d-flex justify-content-between small fw-bold mb-2">
                                        <span><?= $ss->status_karyawan ?></span>
                                        <span><?= $ss->count ?> org</span>
                                    </div>
                                <?php endforeach; endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Culture: Birthdays & Anniversaries Combined -->
                <div class="clean-card p-4 mb-4">
                    <h6 class="fw-bold mb-4">KALENDER BUDAYA</h6>

                    <?php if (empty($birthdays) && empty($anniversaries)): ?>
                        <p class="text-muted small fw-bold mb-0">Tidak ada agenda mendatang.</p>
                    <?php else: ?>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($birthdays as $bd): ?>
                                <li class="d-flex align-items-center gap-3 mb-3">
                                    <div class="avatar-sm" style="font-size: 0.75rem;">🎂</div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold small"><?= $bd->nama_lengkap ?></div>
                                        <small class="text-danger fw-bold">Ulang Tahun •
                                            <?= date('d M', strtotime($bd->tanggal_lahir)) ?></small>
                                    </div>
                                </li>
                            <?php endforeach; ?>

                            <?php foreach ($anniversaries as $year): ?>
                                <?php $yrs = date('Y') - date('Y', strtotime($year->tanggal_masuk)); ?>
                                <li class="d-flex align-items-center gap-3 mb-3">
                                    <div class="avatar-sm" style="font-size: 0.75rem;">🌟</div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold small"><?= $year->nama_lengkap ?></div>
                                        <small class="text-primary fw-bold">Join •
                                            <?= $yrs > 0 ? $yrs . ' Thn Anniversary' : 'Karyawan Baru' ?></small>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <!-- Live Attendance Feed -->
                <div class="clean-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="fw-bold mb-0 text-success"><i class="fas fa-satellite-dish me-2"></i>AKTIVITAS
                            LANGSUNG</h6>
                        <span class="badge bg-soft-success text-success fw-bold rounded-pill"
                            style="font-size: 0.6rem;">REALTIME</span>
                    </div>

                    <?php if (empty($attendance_feed)): ?>
                        <div class="text-center py-4">
                            <p class="text-muted small fw-bold mb-0">Menunggu absensi masuk...</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($attendance_feed as $log): ?>
                            <div
                                class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom border-light last-child-border-0">
                                <div class="avatar-sm"
                                    style="background: <?= $log->status == 'Hadir' ? 'var(--hr-success-soft)' : 'var(--hr-warning-soft)' ?>;">
                                    <?php if ($log->foto): ?>
                                        <img src="<?= base_url('assets/images/karyawan/' . $log->foto) ?>" class="avatar-sm" alt="">
                                    <?php else: ?>
                                        <?= substr($log->nama_lengkap, 0, 1) ?>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold small"><?= $log->nama_lengkap ?></span>
                                        <span class="text-muted"
                                            style="font-size: 0.7rem;"><?= date('H:i', strtotime($log->jam_masuk)) ?></span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-<?= $log->status == 'Hadir' ? 'success' : 'warning' ?> fw-bold"
                                            style="font-size: 0.7rem; text-transform: uppercase;">
                                            <?= $log->status ?>
                                        </span>
                                        <?php if ($log->jam_pulang): ?>
                                            <span class="text-muted fw-bold" style="font-size: 0.65rem;">• Pulang:
                                                <?= date('H:i', strtotime($log->jam_pulang)) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Daftar Pencarian Cepat Karyawan (Mini Table) -->
        <div class="clean-card p-4 mt-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-bold mb-1 text-dark"><i class="fas fa-users text-primary me-2"></i>Daftar Karyawan
                        Aktif</h5>
                    <p class="text-muted small mb-0">Pencarian cepat dan status staf real-time</p>
                </div>
                <a href="<?= site_url('karyawan') ?>"
                    class="btn btn-primary btn-sm rounded-pill fw-bold px-4 shadow-sm">
                    Modul Penuh <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover minimal-table w-100" id="tableMiniKaryawan">
                    <thead>
                        <tr>
                            <th width="30%">PROFIL</th>
                            <th>NIK</th>
                            <th>JABATAN & DIVISI</th>
                            <th>KONTAK</th>
                            <th class="text-end">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($semua_karyawan)):
                            foreach ($semua_karyawan as $k): ?>
                                <tr style="cursor: pointer;"
                                    onclick="window.location.href='<?= site_url('karyawan/edit_karyawan/' . $k->karyawan_id) ?>'">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <?php if ($k->foto): ?>
                                                    <img src="<?= base_url('assets/images/karyawan/' . $k->foto) ?>"
                                                        class="avatar-sm" alt="">
                                                <?php else: ?>
                                                    <?= substr($k->nama_lengkap, 0, 1) ?>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?= $k->nama_lengkap ?></div>
                                                <?php if ($k->jenis_kelamin == 'L' || $k->jenis_kelamin == 'Laki-laki'): ?>
                                                    <small class="text-muted"><i class="fas fa-mars text-info me-1"></i>
                                                        Pria</small>
                                                <?php else: ?>
                                                    <small class="text-muted"><i class="fas fa-venus text-danger me-1"></i>
                                                        Wanita</small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border"><?= $k->nik ?: '-' ?></span></td>
                                    <td>
                                        <div class="fw-bold text-primary" style="font-size: 0.8rem;">
                                            <?= $k->nama_jabatan ?: 'Staff' ?>
                                        </div>
                                        <div class="text-muted" style="font-size: 0.7rem;">
                                            <?= $k->divisi_nama ?: '-' ?> • <?= $k->branch_name ?: '-' ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($k->telepon): ?>
                                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $k->telepon) ?>" target="_blank"
                                                class="text-success text-decoration-none fw-bold" style="font-size: 0.75rem;"
                                                onclick="event.stopPropagation();">
                                                <i class="fab fa-whatsapp me-1"></i> <?= $k->telepon ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end align-middle">
                                        <?php
                                        $bg_status = 'secondary';
                                        if (in_array($k->status_karyawan, ['Aktif', 'Tetap']))
                                            $bg_status = 'success';
                                        elseif (in_array($k->status_karyawan, ['Kontrak', 'Probation']))
                                            $bg_status = 'warning';
                                        elseif (in_array($k->status_karyawan, ['Non-Aktif', 'Resign']))
                                            $bg_status = 'danger';
                                        ?>
                                        <span
                                            class="badge rounded-pill fw-bold px-3 py-1 bg-soft-<?= $bg_status ?> text-<?= $bg_status ?>">
                                            <?= $k->status_karyawan ?: 'Tidak Diketahui' ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- jQuery DataTables init for Mini Table -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // SOP Checklist Persistence
    document.addEventListener('DOMContentLoaded', function () {
        const sopItems = document.querySelectorAll('.sop-item');

        // Load state
        sopItems.forEach((item, index) => {
            const isCompleted = localStorage.getItem('sop_task_' + index) === 'true';
            if (isCompleted) item.classList.add('completed');

            item.addEventListener('click', function () {
                this.classList.toggle('completed');
                localStorage.setItem('sop_task_' + index, this.classList.contains('completed'));
            });
        });

        // Reset daily? (Optional: Reset if date changed)
        const lastSopDate = localStorage.getItem('sop_last_date');
        const today = new Date().toDateString();
        if (lastSopDate !== today) {
            sopItems.forEach((item, index) => {
                item.classList.remove('completed');
                localStorage.removeItem('sop_task_' + index);
            });
            localStorage.setItem('sop_last_date', today);
        }
    });

    if (typeof jQuery !== 'undefined') {
        $(document).ready(function () {
            if ($.fn.DataTable) {
                $('#tableMiniKaryawan').DataTable({
                    "pageLength": 10,
                    "lengthChange": false,
                    "language": {
                        "search": "Cari Karyawan:",
                        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ Karyawan",
                        "emptyTable": "Data Karyawan Tidak Ditemukan",
                        "zeroRecords": "Tidak ada data karyawan yang cocok",
                        "paginate": {
                            "first": "Awal",
                            "last": "Akhir",
                            "next": "Lanjut",
                            "previous": "Kembali"
                        }
                    }
                });
            }
        });
    }
</script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Gender Chart
        const genderCtx = document.getElementById('genderChart');
        if (genderCtx) {
            new Chart(genderCtx, {
                type: 'doughnut',
                data: {
                    labels: [
                        <?php if (!empty($gender_stats) && is_array($gender_stats)):
                            foreach ($gender_stats as $gs): ?>
                                            '<?= ($gs->jenis_kelamin == 'L' || $gs->jenis_kelamin == 'Laki-laki') ? 'Pria' : 'Wanita' ?>',
                            <?php endforeach; endif; ?>
                    ],
                    datasets: [{
                        data: [
                            <?php if (!empty($gender_stats) && is_array($gender_stats)):
                                foreach ($gender_stats as $gs): ?>
                                                <?= $gs->count ?>,
                                <?php endforeach; endif; ?>
                        ],
                        backgroundColor: ['#3b82f6', '#ef4444', '#10b981'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: { legend: { display: false } }
                }
            });
        }

        // Division Chart
        const divCtx = document.getElementById('divisionChart');
        if (divCtx) {
            new Chart(divCtx, {
                type: 'doughnut',
                data: {
                    labels: [
                        <?php if (!empty($division_stats) && is_array($division_stats)):
                            foreach (array_slice($division_stats, 0, 5) as $ds): ?>
                                        '<?= $ds->divisi_nama ?>',
                            <?php endforeach; endif; ?>
                    ],
                    datasets: [{
                        data: [
                            <?php if (!empty($division_stats) && is_array($division_stats)):
                                foreach (array_slice($division_stats, 0, 5) as $ds): ?>
                                                <?= $ds->count ?>,
                                <?php endforeach; endif; ?>
                        ],
                        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: { legend: { display: false } }
                }
            });
        }

        // Status Chart
        const statCtx = document.getElementById('statusChart');
        if (statCtx) {
            new Chart(statCtx, {
                type: 'pie',
                data: {
                    labels: [
                        <?php if (!empty($status_stats) && is_array($status_stats)):
                            foreach ($status_stats as $ss): ?>
                                        '<?= $ss->status_karyawan ?>',
                            <?php endforeach; endif; ?>
                    ],
                    datasets: [{
                        data: [
                            <?php if (!empty($status_stats) && is_array($status_stats)):
                                foreach ($status_stats as $ss): ?>
                                                <?= $ss->count ?>,
                                <?php endforeach; endif; ?>
                        ],
                        backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });
        }
    });
</script>

<style>
    .last-child-border-0:last-child {
        border-bottom: none !important;
        padding-bottom: 0 !important;
        margin-bottom: 0 !important;
    }

    /* Modal Guide Specific */
    .guide-step {
        border-left: 3px solid var(--hr-primary);
        padding-left: 1.5rem;
        position: relative;
    }

    .guide-step::before {
        content: '';
        position: absolute;
        left: -8px;
        top: 0;
        width: 13px;
        height: 13px;
        border-radius: 50%;
        background: var(--hr-primary);
    }
</style>

<!-- Modal Panduan HR -->
<div class="modal fade" id="modalGuideHR" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0 p-4"
                style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 45px; height: 45px;">
                        <i class="fas fa-book-reader fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">Panduan Dashboard Human Capital</h5>
                        <p class="text-white-50 small mb-0">Cara mengoptimalkan penggunaan dashboard ringkasan HR</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-3 bg-light border-end">
                        <div class="nav flex-column nav-pills p-3" id="v-pills-tab" role="tablist">
                            <button class="nav-link active text-start py-3 px-4 fw-bold mb-2 border-0"
                                data-bs-toggle="pill" data-bs-target="#guide-intro" type="button">
                                <i class="fas fa-star me-2"></i> Pengantar
                            </button>
                            <button class="nav-link text-start py-3 px-4 fw-bold mb-2 border-0" data-bs-toggle="pill"
                                data-bs-target="#guide-metrics" type="button">
                                <i class="fas fa-chart-line me-2"></i> Metrik & KPI
                            </button>
                            <button class="nav-link text-start py-3 px-4 fw-bold mb-2 border-0" data-bs-toggle="pill"
                                data-bs-target="#guide-attendance" type="button">
                                <i class="fas fa-user-clock me-2"></i> Monitoring Absensi
                            </button>
                            <button class="nav-link text-start py-3 px-4 fw-bold mb-2 border-0" data-bs-toggle="pill"
                                data-bs-target="#guide-culture" type="button">
                                <i class="fas fa-heart me-2"></i> Budaya & Onboarding
                            </button>
                            <button class="nav-link text-start py-3 px-4 fw-bold mb-2 border-0" data-bs-toggle="pill"
                                data-bs-target="#guide-audit" type="button">
                                <i class="fas fa-shield-alt me-2"></i> Audit & Keamanan
                            </button>
                        </div>
                    </div>
                    <div class="col-md-9 border-start">
                        <div class="tab-content p-5" id="v-pills-tabContent">

                            <!-- Intro -->
                            <div class="tab-pane fade show active" id="guide-intro">
                                <h4 class="fw-bold mb-4 text-dark">Selamat Datang di HC Dashboard</h4>
                                <p class="text-muted mb-4">Dashboard ini dirancang untuk memberikan visibilitas penuh
                                    terhadap kesehatan operasional SDM perusahaan secara real-time. Anda dapat memantau
                                    kedisiplinan, ketersediaan tenaga kerja, hingga log aktivitas dalam satu layar.</p>

                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 border bg-primary bg-opacity-10 h-100">
                                            <h6 class="fw-bold text-primary mb-2"><i class="fas fa-bolt me-2"></i>
                                                Efisiensi Shortcut</h6>
                                            <p class="small text-muted mb-0">Akses langsung ke pusat payroll, rekap
                                                absensi, dan data karyawan tanpa melalui menu navigasi yang panjang.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 border bg-success bg-opacity-10 h-100">
                                            <h6 class="fw-bold text-success mb-2"><i class="fas fa-sync-alt me-2"></i>
                                                Update Real-time</h6>
                                            <p class="small text-muted mb-0">Informasi kehadiran dan aktivitas admin
                                                diperbarui secara otomatis setiap kali halaman dimuat ulang.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Metrics -->
                            <div class="tab-pane fade" id="guide-metrics">
                                <h4 class="fw-bold mb-4">Memahami Angka (KPI)</h4>
                                <div class="d-flex flex-column gap-4">
                                    <div class="guide-step">
                                        <div class="fw-bold mb-1">Attendance Health (%)</div>
                                        <p class="small text-muted">Persentase kehadiran karyawan hari ini dihitung dari
                                            jumlah (Hadir / Total Karyawan Aktif). Target ideal adalah di atas 95%.</p>
                                    </div>
                                    <div class="guide-step">
                                        <div class="fw-bold mb-1">Metrik Kedisiplinan</div>
                                        <p class="small text-muted">Akumulasi dari karyawan yang Terlambat (melebihi
                                            toleransi) dan Alpha (tanpa keterangan). Semakin besar angka ini, semakin
                                            mendesak perlunya evaluasi departemen.</p>
                                    </div>
                                    <div class="guide-step">
                                        <div class="fw-bold mb-1">Distribusi Divisi</div>
                                        <p class="small text-muted">Grafik di sisi kanan menunjukkan sebaran beban kerja
                                            antar departemen. Gunakan ini untuk menganalisis kebutuhan rekrutmen atau
                                            efisiensi struktural.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Attendance -->
                            <div class="tab-pane fade" id="guide-attendance">
                                <h4 class="fw-bold mb-4">Monitoring Absensi Aktif</h4>
                                <div class="alert alert-warning border-0 p-4 rounded-4 mb-4">
                                    <h6 class="fw-bold mb-2"><i class="fas fa-exclamation-triangle me-2"></i> Alert
                                        Karyawan Kesiangan</h6>
                                    <p class="small mb-0">Bagian ini hanya akan muncul jika terdapat karyawan yang
                                        terlambat masuk melewati batas toleransi perusahaan (15 menit). Segera tindak
                                        lanjuti jika angka ini meningkat di hari yang sama.</p>
                                </div>
                                <h6 class="fw-bold mb-3">Live Attendance Feed</h6>
                                <p class="text-muted small mb-4">Feed di kanan dashboard menunjukkan aktivitas absensi
                                    terkini. Anda bisa melihat siapa yang baru saja melakukan scan masuk atau scan
                                    pulang, lengkap dengan jam transaksinya.</p>
                                <div class="p-3 border rounded-3 bg-light">
                                    <div class="small fw-bold text-uppercase text-muted ls-1 mb-2">Tips Pro</div>
                                    <p class="small mb-0 italic text-muted">"Gunakan filter shortcut Rekap Absensi untuk
                                        melihat riwayat ketidakhadiran yang lebih detail per individu."</p>
                                </div>
                            </div>

                            <!-- Culture -->
                            <div class="tab-pane fade" id="guide-culture">
                                <h4 class="fw-bold mb-4">Budaya Org. & Onboarding</h4>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="card h-100 border-0 shadow-sm bg-light">
                                            <div class="card-body">
                                                <h6 class="fw-bold">📅 Kalender Budaya</h6>
                                                <p class="small text-muted">Jangan lewatkan ulang tahun atau anniversary
                                                    bergabungnya karyawan. Mengucapkan selamat atau memberikan apresiasi
                                                    kecil dapat meningkatkan loyalitas tim.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card h-100 border-0 shadow-sm bg-light">
                                            <div class="card-body">
                                                <h6 class="fw-bold">🚀 Onboarding Terbaru</h6>
                                                <p class="small text-muted">Daftar ini menunjukkan 'darah baru' di
                                                    perusahaan. Pastikan mereka sudah mendapatkan induksi yang tepat dan
                                                    akses sistem yang sesuai.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Audit -->
                            <div class="tab-pane fade" id="guide-audit">
                                <h4 class="fw-bold mb-4">Audit Trail & Transparansi</h4>
                                <p class="text-muted mb-4">Keamanan data karyawan sangat kritikal. Modul **Aktivitas
                                    Sistem Terkini** memungkinkan Anda memantau setiap perubahan yang dilakukan oleh
                                    Admin atau User lain di sistem.</p>
                                <ul class="list-group list-group-flush border-top">
                                    <li class="list-group-item bg-transparent py-3 px-0 border-bottom">
                                        <div class="fw-bold small text-primary">USER & MODUL</div>
                                        <div class="small text-muted">Mengetahui siapa yang mengakses dan apa yang
                                            diubah.</div>
                                    </li>
                                    <li class="list-group-item bg-transparent py-3 px-0 border-bottom">
                                        <div class="fw-bold small text-primary">TIMESTAMP</div>
                                        <div class="small text-muted">Melacak waktu kejadian untuk investigasi jika
                                            terjadi kesalahan input atau manipulasi data.</div>
                                    </li>
                                </ul>
                                <div class="mt-4 p-4 rounded-4"
                                    style="background: #fef2f2; border: 1px dashed #ef4444;">
                                    <p class="small text-danger mb-0 fw-bold"><i class="fas fa-info-circle me-2"></i>
                                        Gunakan tombol "PANTAU SEMUA" untuk melihat riwayat log yang lebih komprehensif.
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-light">
                <button type="button" class="btn btn-secondary px-5 fw-bold rounded-pill"
                    data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script>
    function approveKontrak(pengajuan_id) {
        Swal.fire({
            title: 'Konfirmasi Persetujuan',
            text: 'Anda yakin ingin menyetujui perpanjangan kontrak ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?= site_url("hr/approve_kontrak") ?>',
                    type: 'POST',
                    data: {
                        pengajuan_id: pengajuan_id,
                        '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            Swal.fire('Berhasil!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error!', 'Terjadi kesalahan sistem', 'error');
                    }
                });
            }
        });
    }
</script>