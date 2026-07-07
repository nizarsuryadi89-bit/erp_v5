<style>
    /* Ultra-Minimalist Payroll Design */
    :root {
        --tgm-primary: #2563eb;
        --tgm-primary-dark: #1d4ed8;
        --tgm-rose: #e11d48;
        --tgm-emerald: #10b981;
        --tgm-slate-50: #f8fafc;
        --tgm-slate-100: #f1f5f9;
        --tgm-slate-200: #e2e8f0;
        --tgm-slate-700: #334155;
        --tgm-slate-900: #0f172a;
    }

    /* Custom Color Utilities */
    .text-primary-tgm {
        color: var(--tgm-primary) !important;
    }

    .bg-primary-tgm {
        background-color: var(--tgm-primary) !important;
    }

    .border-primary-tgm {
        border-color: var(--tgm-primary) !important;
    }

    .text-rose {
        color: #e11d48 !important;
    }

    .bg-rose {
        background-color: #e11d48 !important;
    }

    .border-rose {
        border-color: #e11d48 !important;
    }

    .text-emerald {
        color: #059669 !important;
    }

    .bg-emerald {
        background-color: #059669 !important;
    }

    .border-emerald {
        border-color: #059669 !important;
    }

    .btn-emerald {
        background-color: #059669;
        color: white;
        border: none;
    }

    .btn-emerald:hover {
        background-color: #047857;
        color: white;
    }

    .btn-primary-tgm {
        background-color: var(--tgm-primary);
        color: white;
        border: none;
    }

    .btn-primary-tgm:hover {
        background-color: var(--tgm-primary-dark);
        color: white;
    }

    .bg-slate-50 {
        background-color: #f8fafc !important;
    }

    .btn-white {
        background-color: white;
        color: var(--tgm-slate-900);
        border: 1px solid var(--tgm-slate-200);
    }

    .btn-white:hover {
        background-color: var(--tgm-slate-50);
        color: var(--tgm-primary);
        border-color: var(--tgm-primary);
    }

    /* Opacity Fixes for Custom Colors (Standard Bootstrap 5 opacity classes might not work on custom bg-* classes) */
    .bg-opacity-10 {
        --bs-bg-opacity: 0.1;
    }

    .border-opacity-25 {
        --bs-border-opacity: 0.25;
    }

    /* Manually handle combined classes if needed */
    .bg-primary-tgm.bg-opacity-10 {
        background-color: rgba(37, 99, 235, 0.1) !important;
    }

    .border-primary-tgm.border-opacity-25 {
        border-color: rgba(37, 99, 235, 0.25) !important;
    }

    .bg-emerald.bg-opacity-10 {
        background-color: rgba(16, 185, 129, 0.1) !important;
    }

    .bg-rose.bg-opacity-10 {
        background-color: rgba(225, 29, 72, 0.1) !important;
    }



    .clean-card {
        background: white;
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
    }

    .stats-minimal {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-box {
        padding: 1.5rem;
        border-top: 3px solid var(--tgm-slate-900);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
    }

    .stat-box.primary {
        border-color: var(--tgm-primary);
    }

    .stat-box.success {
        border-color: #059669;
    }

    .stat-box.warning {
        border-color: #f59e0b;
    }

    .stat-box.danger {
        border-color: var(--tgm-rose);
    }

    .table-simple thead th {
        background: #fdfdfd;
        color: var(--tgm-slate-700);
        text-transform: uppercase;
        font-size: 0.65rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        padding: 15px 10px;
        border-top: none;
        border-bottom: 2px solid var(--tgm-slate-100);
    }

    .table-simple tbody td {
        padding: 12px 8px;
        vertical-align: middle;
        color: var(--tgm-slate-900);
        border-bottom: 1px solid var(--tgm-slate-100);
        font-size: 0.75rem;
    }

    .font-mono {
        font-family: 'JetBrains Mono', monospace !important;
        font-size: 0.75rem;
    }

    .fw-900 {
        font-weight: 900;
    }

    .ls-1 {
        letter-spacing: 1px;
    }

    .btn-action-mini {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        border: 1px solid var(--tgm-slate-200);
        background: white;
        color: var(--tgm-slate-700);
        transition: all 0.2s;
        font-size: 0.75rem;
    }

    .btn-action-mini:hover {
        background: var(--tgm-slate-900);
        color: white;
        border-color: var(--tgm-slate-900);
    }

    .badge-soft {
        font-size: 0.6rem;
        padding: 3px 8px;
        font-weight: 800;
        border-radius: 50px;
        text-transform: uppercase;
    }

    .payroll-header {
        margin-top: 1.5rem;
        margin-bottom: 2rem;
    }

    @media print {
        .no-print {
            display: none !important;
        }
    }

    /* Payroll Wizard Styling */
    .payroll-wizard {
        display: flex;
        gap: 15px;
        margin-bottom: 2rem;
        background: #f8fafc;
        padding: 1rem;
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .wizard-step {
        flex: 1;
        background: white;
        padding: 1.25rem;
        border-radius: 1rem;
        border: 1px solid transparent;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .wizard-step:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        border-color: var(--tgm-primary);
    }

    .step-number {
        width: 28px;
        height: 28px;
        background: #f1f5f9;
        color: #64748b;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 0.75rem;
        margin-bottom: 0.75rem;
        transition: all 0.3s;
    }

    .wizard-step:hover .step-number {
        background: var(--tgm-primary);
        color: white;
    }

    .step-title {
        font-weight: 800;
        font-size: 0.8rem;
        color: var(--tgm-slate-900);
        margin-bottom: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .step-desc {
        font-size: 0.7rem;
        color: #64748b;
        line-height: 1.4;
    }

    .wizard-icon {
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 3rem;
        opacity: 0.05;
        transform: rotate(-15deg);
        transition: all 0.3s;
    }

    .wizard-step:hover .wizard-icon {
        opacity: 0.1;
        right: 5px;
        bottom: 5px;
    }

    @keyframes pulse-warning {
        0% {
            transform: scale(1);
            opacity: 0.8;
        }

        50% {
            transform: scale(1.1);
            opacity: 1;
        }

        100% {
            transform: scale(1);
            opacity: 0.8;
        }
    }

    .pulse-warning {
        animation: pulse-warning 2s infinite ease-in-out;
    }

    tr.row-pending {
        background-color: rgba(245, 158, 11, 0.04) !important;
        transition: all 0.3s ease;
    }

    tr.row-pending:hover {
        background-color: rgba(245, 158, 11, 0.08) !important;
    }

    tr.row-pending td:first-child {
        border-left: 4px solid #f59e0b !important;
    }
</style>

<div class="app-content">
    <!-- <div class="container-fluid"> -->
    <!-- Header -->
    <?php $this->load->view('layout_admin/header_modul'); ?>

    <!-- Header & Action Bar Minimal -->
    <!-- <div class="payroll-wizard no-print">
        <div class="wizard-step" onclick="window.location.href='<?= base_url('karyawan/karyawan') ?>'">
            <div class="step-number">1</div>
            <div class="step-title">Database Master</div>
            <div class="step-desc">Validasi Gapok, Tunjangan Aktif, & Tarif Lembur Dasar.</div>
            <i class="bi bi-person-lines-fill wizard-icon"></i>
        </div>
        <div class="wizard-step"
            onclick="window.location.href='<?= base_url('karyawan/rekap_lembur_bulanan?bulan=' . $bulan) ?>'">
            <div class="step-number">2</div>
            <div class="step-title">Verifikasi Lembur</div>
            <div class="step-desc">Review detail Lembur Pagi, Wajib, & Kerjaan per Karyawan.</div>
            <i class="bi bi-clock-history wizard-icon"></i>
        </div>
        <div class="wizard-step"
            onclick="Swal.fire('Info', 'Gunakan menu Alat (Icon Gear) pada tabel untuk input Reward/BPJS.', 'info')">
            <div class="step-number">3</div>
            <div class="step-title">Earnings Extra</div>
            <div class="step-desc">Input Reward, BPJS, & Tunjangan Kinerja Manual.</div>
            <i class="bi bi-patch-plus wizard-icon"></i>
        </div>
        <div class="wizard-step"
            onclick="Swal.fire('Info', 'Input Potongan (Terlambat/Kasbon) tersedia di menu Alat tab POTONGAN.', 'info')">
            <div class="step-number">4</div>
            <div class="step-title">Deductions</div>
            <div class="step-desc">Input Potongan Alpha, Kesiangan, Kasbon, & Pinjaman.</div>
            <i class="bi bi-calculator-fill wizard-icon"></i>
        </div>
    </div> -->
    <!-- Statistics Grid Minimal -->
    <div class="stats-minimal">
        <div class="clean-card stat-box">
            <div class="small fw-800 text-muted text-uppercase ls-1" style="font-size: 0.6rem; opacity: 0.7;">Total
                Karyawan</div>
            <div class="h3 fw-900 font-mono text-dark mb-0 mt-1"><?= number_format($salary_stats['total_employees']) ?>
                <small class="fw-bold text-muted ls-0" style="font-size: 0.65rem;">ORANG</small>
            </div>
        </div>
        <div class="clean-card stat-box primary">
            <div class="small fw-800 text-muted text-uppercase ls-1" style="font-size: 0.6rem; opacity: 0.7;">Total
                Pengeluaran Gaji</div>
            <div class="h3 fw-900 font-mono text-primary-tgm mb-0 mt-1">Rp
                <?= number_format($salary_stats['total_salary']) ?>
            </div>
        </div>
        <div class="clean-card stat-box warning">
            <div class="small fw-800 text-muted text-uppercase ls-1" style="font-size: 0.6rem; opacity: 0.7;">Kumulatif
                Lembur</div>
            <div class="h3 fw-900 font-mono text-warning mb-0 mt-1">Rp
                <?= number_format($salary_stats['total_overtime']) ?>
            </div>
        </div>
        <div class="clean-card stat-box success">
            <div class="small fw-800 text-muted text-uppercase ls-1" style="font-size: 0.6rem; opacity: 0.7;">Bonus &
                Reward</div>
            <div class="h3 fw-900 font-mono text-emerald mb-0 mt-1">Rp
                <?= number_format($salary_stats['total_performance_bonus']) ?>
            </div>
        </div>
    </div>

    <!-- Administrative Control Panel (Filter & Actions) -->
    <div class="clean-card mb-4 no-print overflow-hidden">
        <div class="p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center gap-3">
                    <a href="<?= base_url('hr') ?>"
                        class="btn btn-white d-flex align-items-center justify-content-center shadow-sm"
                        style="width: 42px; height: 42px; padding: 0; border-radius: 10px;"
                        title="Kembali ke Dashboard HR">
                        <i class="bi bi-arrow-left fs-5"></i>
                    </a>
                    <h6 class="fw-900 text-dark mb-0 ls-1"><i
                            class="bi bi-shield-check-fill me-2 text-primary-tgm"></i>CONTROL
                        PANEL PAYROLL</h6>
                </div>
                <div class="badge bg-white text-primary-tgm border border-primary-tgm border-opacity-25 px-3 py-2 font-mono"
                    style="border-radius: 50px; box-shadow: 0 2px 4px rgba(37, 99, 235, 0.05);">
                    <i class="bi bi-calendar3 me-2"></i>
                    <?= date('d M Y', strtotime($payroll_range['start'])) ?> —
                    <?= date('d M Y', strtotime($payroll_range['end'])) ?>
                </div>
            </div>

            <form method="GET" action="<?= base_url('karyawan/penggajian') ?>" class="row g-2 align-items-center">
                <div class="col-md-2">
                    <div class="input-group input-group-sm">
                        <span
                            class="input-group-text bg-slate-50 border-end-0 text-muted small fw-bold px-2">BULAN</span>
                        <input type="month" class="form-control border-start-0 ps-0 fw-bold" name="bulan"
                            value="<?= $bulan ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span
                            class="input-group-text bg-slate-50 border-end-0 text-muted small fw-bold px-3">KARYAWAN</span>
                        <select class="form-select border-start-0 ps-0 fw-bold select2" name="karyawan_id">
                            <option value="">Semua Karyawan</option>
                            <?php foreach ($karyawan_list as $karyawan): ?>
                                <option value="<?= $karyawan->karyawan_id ?>" <?= $karyawan_id == $karyawan->karyawan_id ? 'selected' : '' ?>>
                                    <?= $karyawan->nama_lengkap ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group input-group-sm">
                        <span
                            class="input-group-text bg-slate-50 border-end-0 text-muted small fw-bold px-3">CABANG</span>
                        <select class="form-select border-start-0 ps-0 fw-bold select2" name="perusahaan_id">
                            <option value="">Semua Cabang</option>
                            <?php foreach ($perusahaan_list as $pr): ?>
                                <option value="<?= $pr->id_perusahaan ?>" <?= $perusahaan_id == $pr->id_perusahaan ? 'selected' : '' ?>>
                                    <?= $pr->nama_perusahaan ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group input-group-sm">
                        <span
                            class="input-group-text bg-slate-50 border-end-0 text-muted small fw-bold px-3">JABATAN</span>
                        <select class="form-select border-start-0 ps-0 fw-bold select2" name="jabatan_id">
                            <option value="">Semua Jabatan</option>
                            <?php foreach ($jabatan_list as $jabatan): ?>
                                <option value="<?= $jabatan->jabatan_id ?>" <?= $jabatan_id == $jabatan->jabatan_id ? 'selected' : '' ?>>
                                    <?= $jabatan->nama_jabatan ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary-tgm w-100 rounded-pill fw-bold shadow-sm py-2">
                        <i class="bi bi-funnel-fill me-2"></i> TERAPKAN FILTER
                    </button>
                </div>
            </form>
        </div>

        <!-- Quick Actions Integration -->
        <div class="px-4 py-3 bg-slate-50 border-top d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-white text-dark border px-2 py-1 x-small fw-900 ls-1"
                        style="font-size: 0.6rem;">ACTION CENTER</span>
                    <div class="vr mx-1" style="height: 15px; opacity: 0.1;"></div>
                </div>

                <!-- Search Input -->
                <div class="input-group input-group-sm rounded-pill overflow-hidden shadow-sm border border-secondary border-opacity-10"
                    style="width: 280px; transition: all 0.3s ease;">
                    <span class="input-group-text bg-white border-0 ps-3">
                        <i class="bi bi-search text-primary-tgm" style="font-size: 0.75rem;"></i>
                    </span>
                    <input type="text" id="payrollSearch" class="form-control border-0 bg-white small fw-bold"
                        placeholder="Cari Nama Karyawan..." autocomplete="off">
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= base_url('karyawan/export_penggajian_pdf?bulan=' . $bulan . '&perusahaan_id=' . $perusahaan_id) ?>"
                    class="btn btn-white btn-sm rounded-pill px-3 fw-bold shadow-sm" target="_blank">
                    <i class="bi bi-filetype-pdf me-2 text-rose"></i> PDF
                </a>
                <a href="<?= base_url('karyawan/slip_gaji?bulan=' . $bulan . '&perusahaan_id=' . $perusahaan_id) ?>"
                    class="btn btn-white btn-sm rounded-pill px-3 fw-bold shadow-sm" target="_blank">
                    <i class="bi bi-printer-fill me-2 text-primary"></i> SLIP KOLEKTIF
                </a>
                <button type="button" class="btn btn-white btn-sm rounded-pill px-3 fw-bold shadow-sm"
                    data-bs-toggle="modal" data-bs-target="#modalGuidePayroll">
                    <i class="bi bi-shield-lock-fill me-2 text-emerald"></i> PANDUAN
                </button>
            </div>
        </div>
    </div>

    <style>
        .table-compact {
            font-size: 0.7rem;
        }

        .table-compact th {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
            /* Keep headers one line if possible, or wrap carefully */
            vertical-align: middle;
            padding: 4px 6px !important;
            background-color: #f8f9fa;
            color: #495057;
        }

        .table-compact td {
            vertical-align: middle;
            padding: 4px 6px !important;
        }

        .fw-bold-amount {
            font-weight: 600;
            letter-spacing: -0.3px;
        }

        .btn-link-compact {
            padding: 0 2px !important;
            font-size: 0.8rem;
        }
    </style>

    <!-- Payroll Data Table Minimal -->
    <div class="clean-card overflow-hidden">
        <?php
        $unsynced_count = 0;
        if (!empty($salary_data)) {
            foreach ($salary_data as $s) {
                if (!isset($s['is_synchronized']) || !$s['is_synchronized'])
                    $unsynced_count++;
            }
        }
        ?>
        <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-900 text-dark mb-0 ls-1"><i class="bi bi-wallet2 me-2"></i>LOG PENGGAJIAN KARYAWAN</h6>
            <?php if ($unsynced_count > 0): ?>
                <span class="badge bg-warning text-dark rounded-pill px-3 py-2 fw-900 shadow-sm" style="font-size: 0.6rem;">
                    <i class="bi bi-exclamation-circle-fill me-1"></i> <?= $unsynced_count ?> KARYAWAN PERLU SINKRONISASI
                </span>
            <?php else: ?>
                <span class="badge bg-emerald text-white rounded-pill px-3 py-2 fw-900 shadow-sm" style="font-size: 0.6rem;">
                    <i class="bi bi-check-circle-fill me-1"></i> SEMUA DATA TER-SINKRON
                </span>
            <?php endif; ?>
        </div>
        <div class="table-responsive">
            <table class="table table-simple mb-0" id="payrollTable">
                <thead>
                    <tr>
                        <th rowspan="2" class="text-center align-middle" width="40">SYNC</th>
                        <th rowspan="2" class="ps-3 align-middle" width="15%">KARYAWAN</th>
                        <th rowspan="2" class="text-center align-middle">HADIR</th>
                        <th rowspan="2" class="text-end align-middle">GAJI POKOK</th>
                        <th rowspan="2" class="text-end align-middle">TUNJ. JABATAN</th>
                        <th rowspan="2" class="text-end align-middle">TUNJ. TRANS</th>
                        <th colspan="3" class="text-center border-bottom py-1"
                            style="font-size: 0.6rem; letter-spacing: 2px;">NOMINAL LEMBUR</th>
                        <th rowspan="2" class="text-end align-middle">TOTAL LEMBUR</th>
                        <th rowspan="2" class="text-end align-middle" style="color: #f97316;">UANG MAKAN</th>
                        <th rowspan="2" class="text-end align-middle">REWARD</th>
                        <th rowspan="2" class="text-end align-middle text-rose">POTONGAN</th>
                        <th rowspan="2" class="text-end bg-light align-middle">TOTAL GAJI</th>
                        <th rowspan="2" class="text-center pe-3 align-middle">AKSI</th>
                    </tr>
                    <tr>
                        <th class="text-end text-primary py-1" style="font-size: 0.55rem; background: #fdfdfd;">PAGI
                        </th>
                        <th class="text-end text-warning py-1" style="font-size: 0.55rem; background: #fdfdfd;">WAJIB
                        </th>
                        <th class="text-end text-success py-1" style="font-size: 0.55rem; background: #fdfdfd;">KERJA
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($salary_data)): ?>
                        <?php foreach ($salary_data as $salary): ?>
                            <?php $is_pending = (!isset($salary['is_synchronized']) || !$salary['is_synchronized']); ?>
                            <tr class="<?= $is_pending ? 'row-pending' : '' ?>">
                                <td class="text-center align-middle">
                                    <?php if (isset($salary['is_synchronized']) && $salary['is_synchronized']): ?>
                                        <i class="bi bi-check-circle-fill text-primary-tgm fs-6" title="Sudah Sinkron"></i>
                                    <?php else: ?>
                                        <a href="<?= base_url('absensi/rekap/' . $salary['karyawan_id'] . '?bulan=' . $bulan) ?>"
                                            class="text-warning text-decoration-none"
                                            title="Belum Sinkron. Klik untuk Sinkronisasi">
                                            <i class="bi bi-clock-fill fs-6 pulse-warning"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td class="ps-3">
                                    <div class="fw-bold text-dark mb-0">
                                        <?= strtoupper($salary['nama_lengkap']) ?>
                                    </div>
                                    <div class="x-small text-muted" style="font-size: 0.65rem;">NIK: <?= $salary['nik'] ?> |
                                        <?= $salary['nama_jabatan'] ?> | <span
                                            class="badge bg-slate-100 text-primary-tgm p-1"><?= $salary['nama_perusahaan'] ?></span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-slate-100 text-dark border fw-bold"><?= $salary['total_hadir'] ?>
                                        H</span>
                                </td>
                                <td class="text-end font-mono">
                                    <div class="d-flex justify-content-end align-items-center gap-1">
                                        <?= number_format($salary['gaji_pokok']) ?>
                                        <button type="button" class="btn-action-mini"
                                            style="width: 18px; height: 18px; font-size: 0.6rem;"
                                            onclick="showGajiDetail('<?= $salary['nama_lengkap'] ?>', <?= $salary['gaji_pokok'] ?>, '<?= $salary['jenis_gaji'] ?>', '<?= $salary['status_karyawan'] ?>')">
                                            <i class="bi bi-info"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="text-end font-mono">
                                    <div class="d-flex justify-content-end align-items-center gap-1">
                                        <?= number_format($salary['tunjangan_jabatan']) ?>
                                        <button type="button" class="btn-action-mini"
                                            style="width: 18px; height: 18px; font-size: 0.6rem;"
                                            onclick="showJabatanDetail('<?= $salary['nama_lengkap'] ?>', '<?= $salary['nama_jabatan'] ?>', <?= $salary['tunjangan_jabatan'] ?>)">
                                            <i class="bi bi-info"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="text-end font-mono">
                                    <div class="d-flex justify-content-end align-items-center gap-1">
                                        <?php 
                                            // Hitung potensi transport jika aktif namun bernilai 0 (sinkronisasi tertunda)
                                            // Rumus: (GajiDasar * 0.05 / 26) * TotalHadir
                                            $is_harian = (isset($salary['jenis_gaji']) && $salary['jenis_gaji'] == 'Harian');
                                            $base_salary = $is_harian ? ($salary['gaji_pokok'] / ($salary['total_hadir'] ?: 1) * 26) : $salary['gaji_pokok'];
                                            $calculated_transport = round(($base_salary * 0.05 / 26) * $salary['total_hadir']);
                                            $display_transport = ($salary['transport_aktif'] == 1 && $salary['tunjangan_transportasi'] == 0) ? $calculated_transport : $salary['tunjangan_transportasi'];
                                        ?>
                                        <?= $salary['transport_aktif'] == 1 ? number_format($display_transport) : '<span class="text-muted opacity-25">-</span>' ?>
                                        <button type="button"
                                            class="btn-action-mini <?= $salary['transport_aktif'] == 1 ? 'text-info' : 'text-muted opacity-50' ?>"
                                            style="width: 18px; height: 18px; font-size: 0.6rem;"
                                            onclick="showTransportDetail('<?= $salary['karyawan_id'] ?>', '<?= $salary['nama_lengkap'] ?>', <?= $base_salary ?>, <?= $salary['total_hadir'] ?>, <?= ($display_transport > 0) ? $display_transport : $calculated_transport ?>, <?= $salary['transport_aktif'] ?>)">
                                            <i class="bi bi-gear-fill"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="text-end font-mono text-primary fw-bold">
                                    <?php if (isset($salary['detail_lembur']['pagi']) && $salary['detail_lembur']['pagi']['total'] > 0): ?>
                                        <div class="d-flex justify-content-end align-items-center gap-1">
                                            <?= number_format($salary['detail_lembur']['pagi']['total']) ?>
                                            <button type="button" class="btn-action-mini"
                                                style="width: 18px; height: 18px; font-size: 0.6rem;"
                                                onclick="showLemburDetail('Pagi', '<?= $salary['nama_lengkap'] ?>', <?= $salary['detail_lembur']['pagi']['jam_act'] ?>, <?= $salary['detail_lembur']['pagi']['jam'] ?>, <?= $salary['detail_lembur']['pagi']['rate'] ?>, <?= $salary['detail_lembur']['pagi']['total'] ?>, <?= htmlspecialchars(json_encode($salary['detail_lembur']['pagi']['records'])) ?>)">
                                                <i class="bi bi-info"></i>
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted opacity-25">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end font-mono text-warning fw-bold">
                                    <?php if (isset($salary['detail_lembur']['wajib']) && $salary['detail_lembur']['wajib']['total'] > 0): ?>
                                        <div class="d-flex justify-content-end align-items-center gap-1">
                                            <?= number_format($salary['detail_lembur']['wajib']['total']) ?>
                                            <button type="button" class="btn-action-mini"
                                                style="width: 18px; height: 18px; font-size: 0.6rem;"
                                                onclick="showLemburDetail('Wajib', '<?= $salary['nama_lengkap'] ?>', <?= $salary['detail_lembur']['wajib']['jam_act'] ?>, <?= $salary['detail_lembur']['wajib']['jam'] ?>, <?= $salary['detail_lembur']['wajib']['rate'] ?>, <?= $salary['detail_lembur']['wajib']['total'] ?>, <?= htmlspecialchars(json_encode($salary['detail_lembur']['wajib']['records'])) ?>)">
                                                <i class="bi bi-info"></i>
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted opacity-25">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end font-mono text-success fw-bold">
                                    <?php if (isset($salary['detail_lembur']['kerjaan']) && $salary['detail_lembur']['kerjaan']['total'] > 0): ?>
                                        <div class="d-flex justify-content-end align-items-center gap-1">
                                            <?= number_format($salary['detail_lembur']['kerjaan']['total']) ?>
                                            <button type="button" class="btn-action-mini"
                                                style="width: 18px; height: 18px; font-size: 0.6rem;"
                                                onclick="showLemburDetail('Kerjaan', '<?= $salary['nama_lengkap'] ?>', <?= $salary['detail_lembur']['kerjaan']['jam_act'] ?>, <?= $salary['detail_lembur']['kerjaan']['jam'] ?>, <?= $salary['detail_lembur']['kerjaan']['rate'] ?>, <?= $salary['detail_lembur']['kerjaan']['total'] ?>, <?= htmlspecialchars(json_encode($salary['detail_lembur']['kerjaan']['records'])) ?>)">
                                                <i class="bi bi-info"></i>
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted opacity-25">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end font-mono fw-bold" style="background: #fffcf0;">
                                    <div class="d-flex flex-column align-items-end">
                                        <span class="text-dark"><?= number_format($salary['uang_lembur']) ?></span>
                                        <span class="text-muted" style="font-size: 0.6rem;">
                                            <i
                                                class="bi bi-clock-history me-1"></i><?= round($salary['total_jam_lembur'], 1) ?>h
                                        </span>
                                    </div>
                                </td>
                                <td class="text-end font-mono fw-bold" style="color: #f97316;">
                                    <?= (isset($salary['biaya_makan_lembur']) && $salary['biaya_makan_lembur'] > 0) ? number_format($salary['biaya_makan_lembur']) : '-' ?>
                                </td>
                                <td class="text-end font-mono">
                                    <?php $total_bonus = $salary['reward'] + $salary['bpjs'] + $salary['tunjangan_extra'] + $salary['tunjangan_kinerja_manual'] + $salary['bonus_kinerja'] + $salary['bonus_kehadiran']; ?>
                                    <span class="text-primary-tgm"><?= number_format($total_bonus) ?></span>
                                </td>
                                <td class="text-end font-mono text-rose">
                                    <?php
                                    $total_potongan = $salary['potongan_alpha'] + $salary['kasbon'] + $salary['potongan_terlambat'] + $salary['pinjaman'];
                                    $total_alpha_days = $salary['total_alpha'] ?? 0;
                                    $total_telat_detik = $salary['total_detik_telat'] ?? 0;
                                    ?>
                                    <div class="d-flex justify-content-end align-items-center gap-1">
                                        <?= $total_potongan > 0 ? '(' . number_format($total_potongan) . ')' : '-' ?>
                                        <?php if ($total_potongan > 0): ?>
                                            <button type="button" class="btn-action-mini text-rose"
                                                style="width: 18px; height: 18px; font-size: 0.6rem;"
                                                onclick="showPotonganDetail('<?= $salary['nama_lengkap'] ?>', <?= $salary['potongan_alpha'] ?>, <?= $total_alpha_days ?>, <?= $salary['potongan_terlambat'] ?>, <?= $total_telat_detik ?>, <?= $salary['kasbon'] ?>, <?= $salary['pinjaman'] ?>, <?= $total_potongan ?>)">
                                                <i class="bi bi-info"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-end bg-light font-mono fw-900 text-primary-tgm">
                                    <div class="d-flex justify-content-end align-items-center gap-1">
                                        <?= number_format($salary['total_gaji']) ?>
                                        <button type="button" class="btn-action-mini"
                                            style="width: 18px; height: 18px; font-size: 0.6rem;"
                                            onclick="showTotalGajiDetail('<?= $salary['nama_lengkap'] ?>', <?= $salary['gaji_pokok'] ?>, <?= $salary['tunjangan_jabatan'] ?>, <?= $salary['tunjangan_transportasi'] ?>, <?= $salary['uang_lembur'] ?>, <?= $salary['biaya_makan_lembur'] ?>, <?= ($salary['bonus_kinerja'] + $salary['bonus_kehadiran'] + $salary['reward'] + $salary['tunjangan_extra'] + $salary['tunjangan_kinerja_manual']) ?>, <?= $salary['potongan_alpha'] + $salary['potongan_terlambat'] ?>, <?= $salary['kasbon'] ?>, <?= $salary['pinjaman'] ?>, <?= $salary['total_gaji'] ?>)">
                                            <i class="bi bi-info"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <button type="button" class="btn-action-mini text-warning" title="Edit Manual"
                                            onclick="showEditManual('<?= $salary['karyawan_id'] ?>', '<?= $salary['nama_lengkap'] ?>', '<?= $bulan ?>', <?= $salary['reward'] ?>, <?= $salary['bpjs'] ?>, <?= $salary['tunjangan_extra'] ?>, <?= $salary['tunjangan_kinerja_manual'] ?>, <?= $salary['kasbon'] ?>, <?= $salary['pinjaman'] ?>)">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <?php
                                        $override_data = [
                                            'karyawan_id' => $salary['karyawan_id'],
                                            'nama_lengkap' => $salary['nama_lengkap'],
                                            'bulan' => $bulan,
                                            'gaji_pokok' => $salary['gaji_pokok'],
                                            'tunjangan_jabatan' => $salary['tunjangan_jabatan'],
                                            'tunjangan_transportasi' => $salary['tunjangan_transportasi'],
                                            'bonus_kinerja' => $salary['bonus_kinerja'],
                                            'tunj_kinerja' => $salary['tunjangan_kinerja_manual'],
                                            'bonus_kehadiran' => $salary['bonus_kehadiran'],
                                            'uang_lembur' => $salary['uang_lembur'],
                                            'biaya_makan_lembur' => $salary['biaya_makan_lembur'],
                                            'reward' => $salary['reward'],
                                            'bpjs' => $salary['bpjs'],
                                            'tunjangan_extra' => $salary['tunjangan_extra'],
                                            'potongan_terlambat' => $salary['potongan_terlambat'],
                                            'potongan_alpha' => $salary['potongan_alpha'],
                                            'kasbon' => $salary['kasbon'],
                                            'pinjaman' => $salary['pinjaman'],
                                            'total_gaji' => $salary['total_gaji'],
                                            'is_manual' => $salary['is_manual'] ? true : false,
                                        ];
                                        ?>
                                        <button type="button"
                                            class="btn-action-mini <?= $salary['is_manual'] ? 'bg-danger text-white border-danger' : 'text-danger' ?>"
                                            title="Override Total Gaji"
                                            onclick="showOverrideGaji(<?= htmlspecialchars(json_encode($override_data), ENT_QUOTES, 'UTF-8') ?>)">
                                            <i class="bi bi-sliders"></i>
                                        </button>
                                        <?php if (isset($salary['is_synchronized']) && $salary['is_synchronized']): ?>
                                            <button type="button" class="btn-action-mini text-primary" title="Preview Slip"
                                                onclick="showSlip('<?= base_url('karyawan/slip_gaji_single/' . $salary['karyawan_id'] . '?bulan=' . $bulan . '&mode=preview') ?>')">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn-action-mini text-success" title="WhatsApp"
                                                onclick="sendWA('<?= $salary['telepon'] ?>', '<?= $salary['nama_lengkap'] ?>', '<?= $bulan ?>', '<?= $salary['total_gaji'] ?>', '<?= $salary['karyawan_id'] ?>', '<?= md5(trim($salary['karyawan_id']) . '-' . trim($bulan) . '-tgm_slip_secret') ?>')">
                                                <i class="bi bi-whatsapp"></i>
                                            </button>
                                        <?php else: ?>
                                            <a href="<?= base_url('absensi/rekap/' . $salary['karyawan_id'] . '?bulan=' . $bulan) ?>"
                                                class="btn-action-mini text-warning border-warning border-opacity-25"
                                                title="Buka Rekap untuk Sinkronisasi" style="background: rgba(245, 158, 11, 0.05);">
                                                <i class="bi bi-lock-fill"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="text-center py-5 text-muted small fw-bold">Belum ada data penggajian
                                periode ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="p-3 bg-white border-top no-print">
            <div class="d-flex justify-content-between align-items-center">
                <div class="small fw-900 text-muted text-uppercase ls-1" style="font-size: 0.65rem;">
                    PERIODE DATA: <span
                        class="text-primary-tgm font-mono"><?= date('F Y', strtotime($bulan . '-01')) ?></span>
                </div>
                <div class=" small fw-900 text-muted text-uppercase ls-1" style="font-size: 0.65rem;">
                    DATA TERMUAT: <span class="text-primary-tgm font-mono"><?= count($salary_data) ?> KARYAWAN</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pengaturan Lembur -->
<div class=" modal fade" id="modalLembur" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pengaturan Tarif Lembur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('karyawan/update_overtime_rates') ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tarif Lembur Kerjaan / Jam</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" name="rate_lembur_kerjaan"
                                value="<?= isset($perusahaan_data['rate_lembur_kerjaan']) ? $perusahaan_data['rate_lembur_kerjaan'] : 10000 ?>"
                                required min="0">
                        </div>
                        <div class="form-text">Untuk lembur tugas/project (input manual di menu
                            Lembur)</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tarif Lembur Wajib / Jam</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" name="rate_lembur_wajib"
                                value="<?= isset($perusahaan_data['rate_lembur_wajib']) ? $perusahaan_data['rate_lembur_wajib'] : 10000 ?>"
                                required min="0">
                        </div>
                        <div class="form-text">Untuk kelebihan jam kerja (Senin-Jumat diatas
                            16:00)</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Slip Gaji -->
<div class="modal fade" id="modalSlipGaji" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content h-100">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="fa fa-file-invoice-dollar me-2"></i>Preview Slip Gaji
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="min-height: 500px; background: #525659;">
                <iframe id="iframeSlip" style="width:100%; height:100%; min-height: 500px; border:none;"
                    src=""></iframe>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="printSlip()">
                    <i class="fa fa-print me-1"></i> Cetak Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function showSlip(url) {
        // Set loading state if needed, or just set src
        const iframe = document.getElementById('iframeSlip');
        iframe.src = url;

        // Show modal
        const myModal = new bootstrap.Modal(document.getElementById('modalSlipGaji'));
        myModal.show();

        // Reset src on close to stop audio/video if any (not likely here but good practice)
        document.getElementById('modalSlipGaji').addEventListener('hidden.bs.modal', function () {
            iframe.src = '';
        });
    }

    function printSlip() {
        const iframe = document.getElementById('iframeSlip');
        if (iframe.contentWindow) {
            iframe.contentWindow.print();
        }
    }
</script>

<!-- Modal Rumus Transport -->
<div class="modal fade" id="modalTransport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-white d-flex justify-content-between align-items-center">
                <h5 class="modal-title mb-0"><i class="fa fa-calculator me-2"></i>Detail Rumus Transport</h5>
                <div class="form-check form-switch bg-white rounded-pill px-3 py-1 d-flex align-items-center gap-2">
                    <input class="form-check-input ms-0" type="checkbox" id="toggleTransport" style="cursor: pointer;">
                    <label class="form-check-label text-dark fw-bold" for="toggleTransport"
                        style="font-size: 0.65rem; cursor: pointer;">AKTIF</label>
                </div>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Karyawan</label>
                    <h6 id="transName" class="fw-bold text-dark mb-0">-</h6>
                    <input type="hidden" id="transKaryawanId">
                </div>

                <div class="card border-0 bg-light mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Gaji Pokok</span>
                            <span id="transGaji" class="fw-bold">-</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Plafon Transport (5%)</span>
                            <span id="transPlafon" class="fw-bold text-primary">-</span>
                        </div>
                        <div class="border-top my-2"></div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Rate Transport / Hari (Plafon / 26)</span>
                            <span id="transRate" class="fw-bold">-</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Jumlah Kehadiran</span>
                            <span id="transHadir" class="fw-bold text-success">-</span>
                        </div>
                    </div>
                </div>

                <div class="text-center p-3 rounded bg-info bg-opacity-10">
                    <div class="text-muted small mb-1">Total Tunjangan Transportasi</div>
                    <h4 id="transTotal" class="fw-bold text-info mb-0">-</h4>
                </div>

                <div class="mt-4 p-3 border rounded border-dashed">
                    <small class="text-muted d-block mb-1"><i class="fa fa-info-circle me-1"></i> Rumus
                        Dasar:</small>
                    <code class="d-block text-dark"
                        style="font-size: 0.9em;">((5% x Gaji Pokok) / 26 Hari) x Kehadiran</code>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showTransportDetail(id, name, gaji, hadir, total, is_active) {
        const plafon = gaji * 0.05;
        const rate = plafon / 26;
        const calculatedTotal = Math.round(rate * hadir);

        document.getElementById('transKaryawanId').value = id;
        document.getElementById('transName').innerText = name;
        document.getElementById('transGaji').innerText = 'Rp ' + new Number(gaji).toLocaleString('id-ID');
        document.getElementById('transPlafon').innerText = 'Rp ' + new Number(plafon).toLocaleString('id-ID');
        document.getElementById('transRate').innerText = 'Rp ' + new Number(rate).toLocaleString('id-ID', { maximumFractionDigits: 0 });
        document.getElementById('transHadir').innerText = hadir + ' Hari';
        
        // Gunakan nilai yang dihitung jika total masih 0 (biasanya saat belum diaktifkan)
        const displayTotal = (total > 0) ? total : calculatedTotal;
        document.getElementById('transTotal').innerText = 'Rp ' + new Number(displayTotal).toLocaleString('id-ID');

        const toggle = document.getElementById('toggleTransport');
        toggle.checked = is_active == 1;

        const myModal = new bootstrap.Modal(document.getElementById('modalTransport'));
        myModal.show();
    }

    document.getElementById('toggleTransport').addEventListener('change', function () {
        const id = document.getElementById('transKaryawanId').value;
        const status = this.checked ? 1 : 0;

        fetch('<?= site_url('karyawan/toggle_transport_status') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'karyawan_id=' + encodeURIComponent(id) + '&status=' + status
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                        timer: 1000,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
            });
    });
    function showLemburDetail(type, name, jamRaw, jamWeighted, rate, total, records) {
        document.getElementById('lemburTypeTitle').innerText = 'Lembur ' + type;
        document.getElementById('lemburName').innerText = name;
        document.getElementById('lemburRaw').innerText = jamRaw.toFixed(2) + ' Jam';
        document.getElementById('lemburWeighted').innerText = jamWeighted.toFixed(2) + ' Jam';
        document.getElementById('lemburRate').innerText = 'Rp ' + new Number(rate).toLocaleString('id-ID');
        document.getElementById('lemburTotal').innerText = 'Rp ' + new Number(total).toLocaleString('id-ID');

        // Render records table
        const tbody = document.getElementById('lemburRecordsBody');
        tbody.innerHTML = '';
        if (records && records.length > 0) {
            records.forEach(rec => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="small">${rec.tgl}</td>
                    <td class="small">${rec.jam}</td>
                    <td class="small fw-bold">${rec.actual}h <span class="text-muted fw-normal">→ ${rec.durasi}h</span></td>
                    <td class="small text-muted">${rec.info || '-'}</td>
                `;
                tbody.appendChild(tr);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted small">Tidak ada record detail</td></tr>';
        }

        // Header styling based on type
        const header = document.querySelector('#modalLemburDetail .modal-header');
        if (type === 'Kerjaan') {
            header.classList.remove('bg-warning', 'bg-primary');
            header.classList.add('bg-success');
            document.getElementById('tierSection').classList.remove('d-none');
        } else if (type === 'Pagi') {
            header.classList.remove('bg-warning', 'bg-success');
            header.classList.add('bg-primary');
            document.getElementById('tierSection').classList.add('d-none');
        } else {
            header.classList.remove('bg-success', 'bg-primary');
            header.classList.add('bg-warning');
            document.getElementById('tierSection').classList.add('d-none');
        }

        const myModal = new bootstrap.Modal(document.getElementById('modalLemburDetail'));
        myModal.show();
    }
</script>

<!-- Modal Rumus Lembur -->
<div class="modal fade" id="modalLemburDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white">
                <h5 class="modal-title"><i class="fa fa-calculator me-2"></i>Detail <span id="lemburTypeTitle"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Karyawan</label>
                    <h6 id="lemburName" class="fw-bold text-dark mb-0">-</h6>
                </div>

                <div class="card border-0 bg-light mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Jam Aktual (Raw)</span>
                            <span id="lemburRaw" class="fw-bold">-</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Jam Bobot (Weighted)</span>
                            <span id="lemburWeighted" class="fw-bold text-primary">-</span>
                        </div>
                        <div class="border-top my-2"></div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tarif Lembur / Jam (Profil)</span>
                            <span id="lemburRate" class="fw-bold">-</span>
                        </div>
                    </div>
                </div>

                <div class="text-center p-3 rounded bg-light border border-info border-opacity-25 bg-opacity-50 mb-3">
                    <div class="text-muted small mb-1">Hasil Perhitungan (Weighted Jam x Tarif)</div>
                    <h4 id="lemburTotal" class="fw-bold text-dark mb-0">-</h4>
                </div>

                <!-- Detail Records List -->
                <div class="mb-3">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-2">Log Lembur
                        (Bulan Ini)</label>
                    <div class="table-responsive border rounded bg-white" style="max-height: 200px; overflow-y: auto;">
                        <table class="table table-sm table-borderless mb-0">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th class="py-2 small">Tgl</th>
                                    <th class="py-2 small">Jam</th>
                                    <th class="py-2 small">Aktual → Bobot</th>
                                    <th class="py-2 small">Info</th>
                                </tr>
                            </thead>
                            <tbody id="lemburRecordsBody">
                                <!-- Records injected here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="tierSection" class="mt-4 d-none">
                    <div class="p-3 border rounded border-dashed bg-light">
                        <small class="text-muted d-block mb-2 text-uppercase fw-bold" style="font-size: 0.75rem;">Aturan
                            Multiplier (Lembur Kerjaan):</small>
                        <ul class="list-unstyled mb-0 small">
                            <li class="mb-1 d-flex justify-content-between">
                                <span><i class="fa fa-sun text-warning me-1"></i> Minggu / Hari
                                    Libur</span>
                                <span class="badge bg-danger">2.0x All Day</span>
                            </li>
                            <li class="mb-1 d-flex justify-content-between">
                                <span><i class="fa fa-moon text-primary me-1"></i> Jam 18:00 - 19:00
                                    (Hari Kerja)</span>
                                <span class="badge bg-success">1.5x</span>
                            </li>
                            <li class="d-flex justify-content-between">
                                <span><i class="fa fa-bolt text-danger me-1"></i> Jam 19:00+ (Hari
                                    Kerja)</span>
                                <span class="badge bg-success">2.0x</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-3 p-2 bg-light rounded shadow-sm border-start border-4 border-info">
                    <small class="text-muted d-block"><i class="fa fa-info-circle me-1"></i>
                        Info:</small>
                    <p class="small text-muted mb-0">Jam Bobot adalah jumlah jam setelah dikalikan
                        multiplier sesuai
                        waktu pengerjaannya.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function sendWA(phone, name, bulan, totalGaji, id, token) {
        if (!phone || phone === '') {
            Swal.fire({
                icon: 'error',
                title: 'Nomor Tidak Ditemukan',
                text: 'Silakan update nomor telepon di data karyawan.'
            });
            return;
        }

        let cleanPhone = phone.replace(/\D/g, '');
        if (cleanPhone.startsWith('0')) {
            cleanPhone = '62' + cleanPhone.substr(1);
        } else if (!cleanPhone.startsWith('62')) {
            cleanPhone = '62' + cleanPhone;
        }

        const date = new Date(bulan + '-01');
        const bulanName = date.toLocaleDateString('id-ID', {
            month: 'long',
            year: 'numeric'
        });
        const formattedGaji = new Number(totalGaji).toLocaleString('id-ID');

        // Build PDF Link with Token for public access
        const pdfLink = `<?= base_url('karyawan/export_slip_single_pdf/') ?>${id}?bulan=${bulan}&token=${token}`;

        const message = `Halo *${name}*,\n\nBerikut adalah rincian gaji Anda untuk periode *${bulanName}*.\n\n*Unduh Slip Gaji (PDF):*\n${pdfLink}\n\nTerima kasih atas dedikasi Anda.\n\n_Pesan otomatis dari TGM ERP_`;
        const encodedMsg = encodeURIComponent(message);

        const waUrl = `https://wa.me/${cleanPhone}?text=${encodedMsg}`;
        window.open(waUrl, '_blank');
    }

    function showGajiDetail(name, nominal, type, status) {
        document.getElementById('gajiName').innerText = name;
        document.getElementById('gajiNominal').innerText = 'Rp ' + new Number(nominal).toLocaleString('id-ID');

        let infoText = 'Gaji Pokok Bulanan';
        if (type === 'Harian' || status === 'Kontrak') {
            infoText = 'Gaji Harian (Terakumulasi x Kehadiran)';
        }
        document.getElementById('gajiType').innerText = infoText;

        const myModal = new bootstrap.Modal(document.getElementById('modalGajiDetail'));
        myModal.show();
    }

    function showTotalGajiDetail(name, gp, tj, tt, ul, makan, bonus, pot, kasbon, pinjaman, total) {
        document.getElementById('totalName').innerText = name;
        document.getElementById('summGP').innerText = 'Rp ' + gp.toLocaleString('id-ID');
        document.getElementById('summTJ').innerText = 'Rp ' + tj.toLocaleString('id-ID');
        document.getElementById('summTT').innerText = 'Rp ' + tt.toLocaleString('id-ID');
        document.getElementById('summUL').innerText = 'Rp ' + ul.toLocaleString('id-ID');
        if (document.getElementById('summUM')) document.getElementById('summUM').innerText = 'Rp ' + makan.toLocaleString('id-ID');
        document.getElementById('summBonus').innerText = 'Rp ' + bonus.toLocaleString('id-ID');
        document.getElementById('summPot').innerText = '- Rp ' + pot.toLocaleString('id-ID');
        document.getElementById('summKasbon').innerText = '- Rp ' + kasbon.toLocaleString('id-ID');
        document.getElementById('summPinjaman').innerText = '- Rp ' + pinjaman.toLocaleString('id-ID');
        document.getElementById('summTotal').innerText = 'Rp ' + total.toLocaleString('id-ID');

        const myModal = new bootstrap.Modal(document.getElementById('modalTotalGajiDetail'));
        myModal.show();
    }

    // Initialize Select2 for Filters
    $(document).ready(function () {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    });

    function showJabatanDetail(name, jabatan, nominal) {
        document.getElementById('jabName').innerText = name;
        document.getElementById('jabTitle').innerText = jabatan;
        document.getElementById('jabNominal').innerText = 'Rp ' + new Number(nominal).toLocaleString('id-ID');

        const myModal = new bootstrap.Modal(document.getElementById('modalJabatanDetail'));
        myModal.show();
    }

    function showPotonganDetail(name, pAlpha, cAlpha, pTelat, sTelat, kasbon, pinjaman, total) {
        document.getElementById('potName').innerText = name;
        document.getElementById('detPotAlpha').innerText = 'Rp ' + pAlpha.toLocaleString('id-ID');
        document.getElementById('detAlphaDays').innerText = '(' + cAlpha + ' Hari)';
        document.getElementById('detPotTelat').innerText = 'Rp ' + pTelat.toLocaleString('id-ID');

        let jam = Math.floor(sTelat / 3600);
        let menit = Math.round((sTelat % 3600) / 60);
        document.getElementById('detTelatDur').innerText = '(' + jam + 'j ' + menit + 'm)';

        document.getElementById('detKasbon').innerText = 'Rp ' + kasbon.toLocaleString('id-ID');
        document.getElementById('detPinjaman').innerText = 'Rp ' + pinjaman.toLocaleString('id-ID');
        document.getElementById('detTotalPotongan').innerText = 'Rp ' + total.toLocaleString('id-ID');

        const myModal = new bootstrap.Modal(document.getElementById('modalPotonganDetail'));
        myModal.show();
    }

    // Real-time Search Logic
    document.getElementById('payrollSearch').addEventListener('keyup', function () {
        let keyword = this.value.toUpperCase();
        let table = document.getElementById('payrollTable');
        let rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            let row = rows[i];
            let nameCell = row.getElementsByTagName('td')[0];
            if (nameCell) {
                let nameContainer = nameCell.getElementsByClassName('fw-bold')[0];
                if (nameContainer) {
                    let nameText = nameContainer.textContent || nameContainer.innerText;
                    if (nameText.toUpperCase().indexOf(keyword) > -1) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                }
            }
        }
    });
</script>

<!-- Modal Info Tunjangan Jabatan -->
<div class="modal fade" id="modalJabatanDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title"><i class="fa fa-briefcase me-2"></i>Sumber Data Tunjangan
                    Jabatan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Karyawan</label>
                    <h6 id="jabName" class="fw-bold text-dark mb-0">-</h6>
                </div>

                <div class="card border-0 bg-light mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Jabatan</span>
                            <span id="jabTitle" class="fw-bold text-dark">-</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Nominal Tunjangan</span>
                            <span id="jabNominal" class="fw-bold h5 mb-0 text-primary">-</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-3 border rounded border-dashed bg-light">
                    <small class="text-muted d-block mb-2"><i class="fa fa-info-circle me-1"></i> Asal
                        Data:</small>
                    <p class="small text-dark mb-0">
                        Sistem memprioritaskan nominal yang diatur di <strong>Gaji & Lembur
                            (Individu)</strong>.
                        Jika tidak diatur secara khusus, maka akan menggunakan nilai default dari master
                        <strong>Jabatan</strong> (<code>tbl_jabatan</code>).
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Info Gaji -->
<div class="modal fade" id="modalGajiDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="fa fa-database me-2"></i>Sumber Data Gaji Pokok</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Karyawan</label>
                    <h6 id="gajiName" class="fw-bold text-dark mb-0">-</h6>
                </div>

                <div class="card border-0 bg-light mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block" id="gajiType">Nominal Gaji
                                    Pokok</small>
                                <span id="gajiNominal" class="fw-bold h5 mb-0 text-primary">-</span>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success">Calculated</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-3 border rounded border-dashed bg-light">
                    <small class="text-muted d-block mb-2"><i class="fa fa-info-circle me-1"></i> Asal
                        Data:</small>
                    <p class="small text-dark mb-0">
                        Data ini diambil langsung dari <strong>Profil Karyawan > Payroll
                            Settings</strong> di tabel
                        <code>tbl_karyawan</code>.
                        Ini merupakan nominal dasar yang bersifat tetap untuk individu ini.
                    </p>
                </div>

                <div class="mt-3 text-center">
                    <small class="text-muted italic">"Data gaji pokok kini dikelola secara mandiri per
                        individu."</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Rincian Total Gaji -->
<div class="modal fade" id="modalTotalGajiDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fa fa-list-ul me-2"></i>Rincian Akhir Gaji</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4 text-center">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Karyawan</label>
                    <h5 id="totalName" class="fw-bold text-dark mb-0">-</h5>
                </div>

                <div class="list-group list-group-flush mb-4">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">Gaji Pokok (+)</span>
                        <span id="summGP" class="fw-bold">-</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">Tunjangan Jabatan (+)</span>
                        <span id="summTJ" class="fw-bold">-</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">Tunjangan Transport (+)</span>
                        <span id="summTT" class="fw-bold">-</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">Total Lembur (+)</span>
                        <span id="summUL" class="fw-bold text-success">-</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">Uang Makan Lembur (+)</span>
                        <span id="summUM" class="fw-bold" style="color: #f97316;">-</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">Total Bonus (+)</span>
                        <span id="summBonus" class="fw-bold text-info">-</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">Potongan Absensi (-)</span>
                        <span id="summPot" class="fw-bold text-danger">-</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">Kasbon (-)</span>
                        <span id="summKasbon" class="fw-bold text-danger">-</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">Pinjaman / Lain (-)</span>
                        <span id="summPinjaman" class="fw-bold text-danger">-</span>
                    </div>
                </div>

                <div class="p-3 rounded bg-primary bg-opacity-10 border border-primary border-opacity-25">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-primary">TOTAL GAJI AKHIR</span>
                        <h4 id="summTotal" class="fw-bold text-primary mb-0">-</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Rincian Potongan -->
<div class="modal fade" id="modalPotonganDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-rose text-white">
                <h5 class="modal-title"><i class="fa fa-minus-circle me-2"></i>Rincian Potongan Gaji</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4 text-center">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Karyawan</label>
                    <h5 id="potName" class="fw-bold text-dark mb-0">-</h5>
                </div>

                <div class="list-group list-group-flush mb-4">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <span class="text-muted">Potongan Alpha</span>
                            <small id="detAlphaDays" class="text-muted ms-1 small"></small>
                        </div>
                        <span id="detPotAlpha" class="fw-bold text-danger">-</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <span class="text-muted">Potongan Terlambat</span>
                            <small id="detTelatDur" class="text-muted ms-1 small"></small>
                        </div>
                        <span id="detPotTelat" class="fw-bold text-danger">-</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">Kasbon</span>
                        <span id="detKasbon" class="fw-bold text-danger">-</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">Pinjaman / Lain</span>
                        <span id="detPinjaman" class="fw-bold text-danger">-</span>
                    </div>
                </div>

                <div class="p-3 rounded bg-danger bg-opacity-10 border border-danger border-opacity-25mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-danger">TOTAL POTONGAN</span>
                        <h4 id="detTotalPotongan" class="fw-bold text-danger mb-0">-</h4>
                    </div>
                </div>

                <div class="bg-slate-50 p-3 rounded-3 border border-dashed text-muted">
                    <h6 class="fw-bold text-dark mb-2" style="font-size: 0.75rem;"><i
                            class="bi bi-info-circle-fill me-1 text-primary"></i> INFO PERHITUNGAN</h6>
                    <div class="d-flex flex-column gap-2" style="font-size: 0.65rem; line-height: 1.4;">
                        <div class="d-flex gap-2">
                            <span class="fw-900 text-primary-tgm">ALPHA:</span>
                            <span>(Gaji Pokok / 26 Hari) &times; Jumlah Hari Alpha.</span>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="fw-900 text-primary-tgm">TELAT:</span>
                            <span>((Gaji Pokok / 26 / 8 Jam) &times; Jam Terlambat) + Penalti Manual.</span>
                        </div>
                        <div class="mt-1 opacity-75 italic">* Penghitungan berdasarkan standar 26 hari kerja/bulan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Edit Manual Payroll -->
<div class="modal fade" id="modalEditManual" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fa fa-edit me-2"></i>Edit Manual Payroll</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditManual">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="text-muted small text-uppercase fw-bold d-block mb-1">Karyawan</label>
                        <h6 id="editManualName" class="fw-bold text-dark mb-0">-</h6>
                        <input type="hidden" id="editManualKaryawanId" name="karyawan_id">
                        <input type="hidden" id="editManualBulan" name="bulan">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Reward (Rp)</label>
                            <input type="number" class="form-control form-control-sm" id="editReward" name="reward"
                                value="0" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">BPJS (Rp)</label>
                            <input type="number" class="form-control form-control-sm" id="editBPJS" name="bpjs"
                                value="0" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Tunjangan Extra (Rp)</label>
                            <input type="number" class="form-control form-control-sm" id="editTunjExtra"
                                name="tunjangan_extra" value="0" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Tunj. Kinerja Manual (Rp)</label>
                            <input type="number" class="form-control form-control-sm" id="editTunjKinerja"
                                name="tunjangan_kinerja_manual" value="0" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-danger">Kasbon (Rp)</label>
                            <input type="number" class="form-control form-control-sm" id="editKasbon" name="kasbon"
                                value="0" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-danger">Pinjaman / Lain (Rp)</label>
                            <input type="number" class="form-control form-control-sm" id="editPinjaman" name="pinjaman"
                                value="0" min="0">
                        </div>
                        <div class="col-12 mt-3 pt-3 border-top">
                            <div class="bg-light p-2 rounded-3 text-center">
                                <small class="text-muted italic"><i class="fa fa-info-circle me-1"></i>Komponen Kasbon &
                                    Pinjaman akan memotong total gaji karyawan secara otomatis.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-slate-100 fw-bold rounded-pill px-4"
                        data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-warning fw-bold rounded-pill px-4 shadow-sm">SIMPAN
                        PERUBAHAN</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Override Gaji Manual -->
<div class="modal fade" id="modalOverrideGaji" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle me-2"></i>Override Total Gaji</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="formOverrideGaji">
                <div class="modal-body p-4">
                    <div class="alert alert-warning border-0 rounded-3 mb-4">
                        <i class="fa fa-info-circle me-1"></i> Aksi ini akan menimpa seluruh perhitungan gaji komputer!
                        Anda dapat memaksa angka-angka berikut ini untuk ditampilkan utuh di slip gaji karyawan
                        bersangkutan.
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small text-uppercase fw-bold d-block mb-1">Karyawan</label>
                        <h6 id="overrideGajiName" class="fw-bold text-dark mb-0">-</h6>
                        <input type="hidden" id="overrideGajiKaryawanId" name="karyawan_id">
                        <input type="hidden" id="overrideGajiBulan" name="bulan">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small">Gaji Pokok</label>
                            <input type="number" class="form-control form-control-sm" id="ovGajiPokok" name="gaji_pokok"
                                value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Tunj. Jabatan</label>
                            <input type="number" class="form-control form-control-sm" id="ovTunjJabatan"
                                name="tunjangan_jabatan" value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Tunj. Transport</label>
                            <input type="number" class="form-control form-control-sm" id="ovTunjTransport"
                                name="tunjangan_transportasi" value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-success">Bonus Kinerja</label>
                            <input type="number" class="form-control form-control-sm" id="ovBonusKinerja"
                                name="bonus_kinerja" value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-success">Tunj. Kinerja</label>
                            <input type="number" class="form-control form-control-sm" id="ovTunjKinerja"
                                name="tunjangan_kinerja_manual" value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-success">Bonus Kehadiran</label>
                            <input type="number" class="form-control form-control-sm" id="ovBonusKehadiran"
                                name="bonus_kehadiran" value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-success">Uang Lembur</label>
                            <input type="number" class="form-control form-control-sm" id="ovLembur" name="uang_lembur"
                                value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-success">Biaya Makan Lmbr</label>
                            <input type="number" class="form-control form-control-sm" id="ovBiayaMakanLembur"
                                name="biaya_makan_lembur" value="0">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small text-success">Reward</label>
                            <input type="number" class="form-control form-control-sm" id="ovReward" name="reward"
                                value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-success">BPJS</label>
                            <input type="number" class="form-control form-control-sm" id="ovBpjs" name="bpjs" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-success">Tunjangan Extra</label>
                            <input type="number" class="form-control form-control-sm" id="ovTunjExtra"
                                name="tunjangan_extra" value="0">
                        </div>

                        <!-- Potongan -->
                        <div class="col-12 border-top mt-2 pt-2">
                            <span class="badge bg-danger rounded-pill px-3 py-1 mb-2">POTONGAN</span>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-danger">Potongan Terlambat</label>
                            <input type="number" class="form-control form-control-sm" id="ovPotTerlambat"
                                name="potongan_terlambat" value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-danger">Potongan Alpha</label>
                            <input type="number" class="form-control form-control-sm" id="ovPotAlpha"
                                name="potongan_alpha" value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-danger">Kasbon</label>
                            <input type="number" class="form-control form-control-sm" id="ovKasbon" name="kasbon"
                                value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-danger">Pinjaman / Lain</label>
                            <input type="number" class="form-control form-control-sm" id="ovPinjaman" name="pinjaman"
                                value="0">
                        </div>

                        <div class="col-12 mt-3 pt-3 border-top">
                            <label class="form-label fw-bold text-dark">TOTAL GAJI AKHIR (Rp) <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control form-control-lg fw-bold text-primary"
                                id="ovTotalGaji" name="total_gaji" value="0" required>
                        </div>
                        <div class="col-12 mt-2">
                            <label class="form-label small">Keterangan Khusus (Opsional)</label>
                            <textarea class="form-control" name="keterangan" id="ovKeterangan" rows="2"
                                placeholder="Alasan dilakukan override..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 d-flex justify-content-between">
                    <div>
                        <button type="button" class="btn btn-outline-danger fw-bold rounded-pill px-3 d-none"
                            id="btnResetOverride" onclick="resetOverride()"><i class="fa fa-trash me-2"></i>HAPUS
                            OVERRIDE</button>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-slate-100 fw-bold rounded-pill px-4"
                            data-bs-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-danger fw-bold rounded-pill px-4 shadow-sm">SIMPAN
                            OVERRIDE</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Biaya Makan Detail -->
<div class="modal fade" id="modalBiayaMakan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fa fa-utensils me-2"></i>Detail Biaya Makan Lembur
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold d-block mb-1">Karyawan</label>
                    <h6 id="biayaMakanName" class="fw-bold text-dark mb-0">-</h6>
                </div>

                <div class="text-center p-3 rounded bg-info bg-opacity-10">
                    <div class="text-muted small mb-1">Total Biaya Makan Lembur</div>
                    <h4 id="biayaMakanTotal" class="fw-bold text-info mb-0">-</h4>
                </div>

                <div class="mt-4 p-3 border rounded border-dashed bg-light">
                    <small class="text-muted d-block mb-1"><i class="fa fa-info-circle me-1"></i> Sumber
                        Data:</small>
                    <p class="small text-dark mb-0">
                        Data ini diambil dari kolom <code>biaya_makan</code> di tabel
                        <code>tbl_lembur</code>
                        untuk semua lembur yang sudah disetujui bulan ini.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showEditManual(karyawanId, namaLengkap, bulan, reward, bpjs, tunjExtra, tunjKinerja, kasbon, pinjaman) {
        document.getElementById('editManualName').innerText = namaLengkap;
        document.getElementById('editManualKaryawanId').value = karyawanId;
        document.getElementById('editManualBulan').value = bulan;
        document.getElementById('editReward').value = reward;
        document.getElementById('editBPJS').value = bpjs;
        document.getElementById('editTunjExtra').value = tunjExtra;
        document.getElementById('editTunjKinerja').value = tunjKinerja;
        document.getElementById('editKasbon').value = kasbon;
        document.getElementById('editPinjaman').value = pinjaman;

        const myModal = new bootstrap.Modal(document.getElementById('modalEditManual'));
        myModal.show();
    }

    function showBiayaMakanDetail(name, total) {
        document.getElementById('biayaMakanName').innerText = name;
        document.getElementById('biayaMakanTotal').innerText = 'Rp ' + new Number(total).toLocaleString('id-ID');

        const myModal = new bootstrap.Modal(document.getElementById('modalBiayaMakan'));
        myModal.show();
    }

    // Handle Edit Manual form submission
    document.getElementById('formEditManual').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('<?= base_url('karyawan/save_manual_payroll') ?>', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat menyimpan data'
                });
            });
    });

    function showOverrideGaji(data) {
        document.getElementById('overrideGajiName').innerText = data.nama_lengkap;
        document.getElementById('overrideGajiKaryawanId').value = data.karyawan_id;
        document.getElementById('overrideGajiBulan').value = data.bulan;

        document.getElementById('ovGajiPokok').value = data.gaji_pokok;
        document.getElementById('ovTunjJabatan').value = data.tunjangan_jabatan;
        document.getElementById('ovTunjTransport').value = data.tunjangan_transportasi;
        document.getElementById('ovBonusKinerja').value = data.bonus_kinerja;
        document.getElementById('ovTunjKinerja').value = data.tunj_kinerja;
        document.getElementById('ovBonusKehadiran').value = data.bonus_kehadiran;
        document.getElementById('ovLembur').value = data.uang_lembur;
        document.getElementById('ovBiayaMakanLembur').value = data.biaya_makan_lembur;
        document.getElementById('ovReward').value = data.reward;
        document.getElementById('ovBpjs').value = data.bpjs;
        document.getElementById('ovTunjExtra').value = data.tunjangan_extra;

        document.getElementById('ovPotTerlambat').value = data.potongan_terlambat;
        document.getElementById('ovPotAlpha').value = data.potongan_alpha;
        document.getElementById('ovKasbon').value = data.kasbon;
        document.getElementById('ovPinjaman').value = data.pinjaman;

        document.getElementById('ovTotalGaji').value = data.total_gaji;

        const btnReset = document.getElementById('btnResetOverride');
        if (data.is_manual) {
            btnReset.classList.remove('d-none');
        } else {
            btnReset.classList.add('d-none');
        }

        const myModal = new bootstrap.Modal(document.getElementById('modalOverrideGaji'));
        myModal.show();
    }

    document.getElementById('formOverrideGaji').addEventListener('submit', function (e) {
        e.preventDefault();

        Swal.fire({
            title: 'Konfirmasi Override Gaji',
            text: 'Data gaji ini akan ditimpa dengan angka manual yang Anda masukkan. Lanjutkan?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Paksa Ubah',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData(document.getElementById('formOverrideGaji'));
                fetch('<?= base_url('karyawan/save_gaji_manual') ?>', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Override gaji berhasil disimpan', timer: 1500, showConfirmButton: false }).then(() => location.reload());
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Gagal merubah gaji.' });
                        }
                    });
            }
        });
    });

    function resetOverride() {
        const karyawanId = document.getElementById('overrideGajiKaryawanId').value;
        const bulan = document.getElementById('overrideGajiBulan').value;

        Swal.fire({
            title: 'Hapus Override Gaji?',
            text: 'Gaji karyawan ini akan kembali mengikuti perhitungan otomatis dari sistem.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Kembalikan Otomatis',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('karyawan_id', karyawanId);
                formData.append('bulan', bulan);

                fetch('<?= base_url('karyawan/reset_gaji_manual') ?>', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Perhitungan gaji kembali normal!', timer: 1500, showConfirmButton: false }).then(() => location.reload());
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat mengembalikan gaji.' });
                        }
                    });
            }
        });
    }
</script>

<!-- Button to trigger the guide modal -->
<!-- <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalGuidePayroll">
    <i class="fas fa-question-circle me-2"></i>Panduan Payroll
</button> -->

<!-- Modal Panduan Payroll -->
<div class="modal fade" id="modalGuidePayroll" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0 p-4"
                style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 45px; height: 45px;">
                        <i class="fas fa-money-check-alt fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">Panduan Modul Penggajian
                            (Payroll)</h5>
                        <p class="text-white-50 small mb-0">Memahami komponen gaji, rumus tunjangan, dan
                            distribusi slip
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-3 bg-light border-end">
                        <div class="nav flex-column nav-pills p-3" id="v-pills-tab-pay" role="tablist">
                            <button class="nav-link active text-start py-3 px-4 fw-bold mb-2 border-0"
                                data-bs-toggle="pill" data-bs-target="#pay-components" type="button">
                                <i class="fas fa-coins me-2"></i> Komponen Gaji
                            </button>
                            <button class="nav-link text-start py-3 px-4 fw-bold mb-2 border-0" data-bs-toggle="pill"
                                data-bs-target="#pay-transport" type="button">
                                <i class="fas fa-bus me-2"></i> Rumus Transport
                            </button>
                            <button class="nav-link text-start py-3 px-4 fw-bold mb-2 border-0" data-bs-toggle="pill"
                                data-bs-target="#pay-overtime" type="button">
                                <i class="fas fa-business-time me-2"></i> Logika Lembur
                            </button>
                            <button class="nav-link text-start py-3 px-4 fw-bold mb-2 border-0" data-bs-toggle="pill"
                                data-bs-target="#pay-manual" type="button">
                                <i class="fas fa-user-edit me-2"></i> Edit Manual
                            </button>
                            <button class="nav-link text-start py-3 px-4 fw-bold mb-2 border-0" data-bs-toggle="pill"
                                data-bs-target="#pay-distribution" type="button">
                                <i class="fas fa-paper-plane me-2"></i> Distribusi Slip
                            </button>
                        </div>
                    </div>
                    <div class="col-md-9 border-start">
                        <div class="tab-content p-5" id="v-pills-tabContent-pay">

                            <!-- Components -->
                            <div class="tab-pane fade show active" id="pay-components">
                                <h4 class="fw-bold mb-4">Struktur & Lokasi Input Data</h4>
                                <p class="text-muted">Berikut adalah panduan lengkap lokasi penginputan
                                    setiap komponen
                                    gaji:</p>

                                <div class="row g-4">
                                    <div class="col-md-12">
                                        <div class="p-4 rounded-4 border bg-light">
                                            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-user-cog me-2"></i>1.
                                                Gaji Pokok & Tunjangan Tetap</h6>
                                            <p class="small text-muted mb-2">Data ini bersifat permanen
                                                untuk tiap
                                                individu. Input dilakukan di:</p>
                                            <div class="bg-white p-2 rounded border small mb-3">
                                                <i class="fas fa-arrow-right text-primary me-2"></i>
                                                <b>Menu
                                                    Karyawan</b> &gt; <b>Data Karyawan</b> &gt; Klik
                                                <b>Edit
                                                    (Pensil)</b> &gt; Tab <b>Gaji & Lembur</b>
                                            </div>
                                            <ul class="small text-muted mb-0">
                                                <li><b>Gaji Pokok:</b> Masukkan nominal per bulan (untuk
                                                    karyawan tetap)
                                                    atau per hari (untuk harian).</li>
                                                <li><b>Tunj. Jabatan:</b> Masukkan nominal tunjangan
                                                    sesuai posisi
                                                    karyawan.</li>
                                                <li><b>Tunj. Makan:</b> (Jika ada) akan dihitung
                                                    berdasarkan kehadiran.
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 border bg-light h-100">
                                            <h6 class="fw-bold text-warning mb-3"><i class="fas fa-edit me-2"></i>2.
                                                Bonus & Potongan Bulanan</h6>
                                            <p class="small text-muted mb-2">Untuk data yang berubah
                                                tiap bulan (Reward,
                                                Kasbon, BPJS, Tunj. Extra):</p>
                                            <div class="bg-white p-2 rounded border small mb-2">
                                                <i class="fas fa-mouse-pointer text-warning me-2"></i>
                                                Gunakan <b>Tombol
                                                    Edit (Kuning)</b> di kolom <b>Aksi</b> pada tabel
                                                Payroll ini.
                                            </div>
                                            <p class="small text-muted mb-0">Perubahan di sini hanya
                                                berlaku untuk
                                                periode bulan yang sedang aktif.</p>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="p-4 rounded-4 border bg-light h-100">
                                            <h6 class="fw-bold text-success mb-3"><i
                                                    class="fas fa-calculator me-2"></i>3. Komponen
                                                Otomatis</h6>
                                            <p class="small text-muted mb-3">Beberapa data tidak perlu
                                                diinput manual
                                                karena dihitung oleh sistem:</p>
                                            <ul class="small text-muted">
                                                <li><b>Tunj. Transport:</b> Otomatis (5% Gaji Pokok / 26
                                                    * Hadir).</li>
                                                <li><b>Lembur Wajib:</b> Otomatis dari sitem Absensi.
                                                </li>
                                                <li><b>Bonus Kehadiran:</b> Otomatis jika absen 100%
                                                    (Alpha = 0).</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Transport -->
                            <div class="tab-pane fade" id="pay-transport">
                                <h4 class="fw-bold mb-4">Perhitungan Tunjangan Transport</h4>
                                <div
                                    class="bg-primary bg-opacity-10 p-4 rounded-4 mb-4 border border-primary border-opacity-25">
                                    <h5 class="fw-bold text-primary mb-2">Rumus Baku:</h5>
                                    <code
                                        class="h6 d-block text-dark">((5% x Gaji Pokok) / 26 Hari) x Jumlah Hadir</code>
                                </div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item bg-transparent px-0">
                                        <div class="fw-bold small">Plafon Transport</div>
                                        <p class="small text-muted">Batas maksimal transport adalah 5%
                                            dari Gaji Pokok.
                                        </p>
                                    </li>
                                    <li class="list-group-item bg-transparent px-0">
                                        <div class="fw-bold small">Pembagi 26</div>
                                        <p class="small text-muted">Plafon dibagi 26 hari kerja untuk
                                            mendapatkan "Rate
                                            per Hari".</p>
                                    </li>
                                </ul>
                            </div>

                            <!-- Overtime -->
                            <div class="tab-pane fade" id="pay-overtime">
                                <h4 class="fw-bold mb-4">Logika Lembur (Dual System)</h4>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="card h-100 border-0 shadow-sm bg-warning bg-opacity-10">
                                            <div class="card-body">
                                                <h6 class="fw-bold"><i class="fas fa-clock me-2"></i>Lembur Wajib</h6>
                                                <p class="small text-muted">Dihitung otomatis dari
                                                    mesin/aplikasi
                                                    absensi jika pulang > 16:00 pada Senin-Jumat.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card h-100 border-0 shadow-sm bg-success bg-opacity-10">
                                            <div class="card-body">
                                                <h6 class="fw-bold"><i class="fas fa-tasks me-2"></i>Lembur Kerjaan
                                                </h6>
                                                <p class="small text-muted">Berdasarkan penugasan
                                                    spesifik
                                                    (Project/Deadline). Memiliki multiplier 1.5x hingga
                                                    2.0x tergantung
                                                    jam pengerjaan.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 p-3 border rounded-3 bg-light">
                                    <p class="small mb-0 text-muted italic text-center"><i
                                            class="fas fa-info-circle me-1"></i> Klik tombol <b>"Atur
                                            Lembur"</b> untuk
                                        mengubah tarif dasar per jam.</p>
                                </div>
                            </div>

                            <!-- Manual Edit -->
                            <div class="tab-pane fade" id="pay-manual">
                                <h4 class="fw-bold mb-4">Penyesuaian (Edit Manual)</h4>
                                <p class="text-muted">Gunakan ikon <i class="fa fa-edit text-warning"></i> di kolom Aksi
                                    untuk menyesuaikan data berikut secara manual per karyawan:</p>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <div class="badge bg-light text-dark border p-2 w-100">Reward /
                                            Bonus</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="badge bg-light text-dark border p-2 w-100">Potongan
                                            BPJS</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="badge bg-light text-dark border p-2 w-100">Tunjangan
                                            Extra</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="badge bg-light text-dark border p-2 w-100">Bonus
                                            Kinerja</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="badge bg-danger text-white border p-2 w-100 text-uppercase">
                                            Kasbon
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-warning mt-4 border-0 small">
                                    <i class="fas fa-exclamation-triangle me-2"></i> <b>PENTING:</b>
                                    Simpan perubahan
                                    untuk memperbarui Total Gaji Akhir secara instan.
                                </div>
                            </div>

                            <!-- Distribution -->
                            <div class="tab-pane fade" id="pay-distribution">
                                <h4 class="fw-bold mb-4">Distribusi & Pengiriman Slip</h4>

                                <div
                                    class="alert alert-primary border-0 rounded-4 p-3 mb-4 d-flex align-items-center gap-3">
                                    <div class="bg-white rounded-circle p-2 text-primary shadow-sm">
                                        <i class="bi bi-shield-lock-fill fs-4"></i>
                                    </div>
                                    <div>
                                        <b class="d-block">SAFETY LOCK AKTIF</b>
                                        <span class="small opacity-75">Tombol aksi hanya akan muncul setelah data
                                            <b>Disinkronkan</b> dari modul Absensi.</span>
                                    </div>
                                </div>

                                <div class="d-flex flex-column gap-3">
                                    <div class="d-flex align-items-start gap-3 p-3 border rounded-3">
                                        <div class="bg-success text-white rounded p-2"><i class="fab fa-whatsapp"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Kirim via WhatsApp</div>
                                            <p class="small text-muted mb-0">Mengirimkan rincian gaji
                                                dan <b>link
                                                    download PDF</b> langsung ke nomor WA karyawan yang
                                                terdaftar.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start gap-3 p-3 border rounded-3">
                                        <div class="bg-primary text-white rounded p-2"><i class="fas fa-file-pdf"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Export PDF / Slip Massal</div>
                                            <p class="small text-muted mb-0">Mencetak atau menyimpan
                                                semua slip gaji
                                                dalam satu file PDF untuk arsip kantor.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 p-3 rounded-3 bg-light border">
                                    <h6 class="fw-bold small mb-2"><i class="bi bi-info-circle me-2"></i>Kenapa tombol
                                        tidak muncul?</h6>
                                    <p class="small text-muted mb-0">Jika ikon <i class="bi bi-eye"></i> dan <i
                                            class="bi bi-whatsapp"></i> belum tampil, artinya data kehadiran karyawan
                                        tersebut belum Anda sinkronkan di menu <b>Rekap Absensi</b>. Ini adalah prosedur
                                        pengamanan agar tidak terjadi kesalahan kirim slip gaji.</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-light">
                <button type="button" class="btn btn-secondary px-5 fw-bold rounded-pill" data-bs-dismiss="modal">Tutup
                    Panduan</button>
            </div>
        </div>
    </div>
</div>

<style>
    .guide-step {
        border-left: 3px solid #3b82f6;
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
        background: #3b82f6;
    }
</style>