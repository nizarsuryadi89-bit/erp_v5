<?php
/**
 * Sistem Monitoring Lembur Karyawan - Integrated Dashboard Style
 * Displays hrd overtime logs and approval management.
 */
$tanggal_selected = isset($tanggal) ? $tanggal : date('Y-m-d');

// Calculations for stats
$total = 0;
$pending = 0;
$approved = 0;
$rejected = 0;

foreach ($karyawan as $row) {
    $total++;
    if ($row->status == 'menunggu' || strtolower($row->status) == 'pending') {
        $pending++;
    }
    elseif ($row->status == 'disetujui') {
        $approved++;
    }
    elseif ($row->status == 'ditolak') {
        $rejected++;
    }
}

// Helper to format decimal hours to "J" and "M"
if (!function_exists('format_durasi_lembur')) {
    function format_durasi_lembur($hours)
    {
        if ($hours <= 0)
            return '-';
        $h = floor($hours);
        $m = round(($hours - $h) * 60);
        if ($m == 60) {
            $h++;
            $m = 0;
        }

        if ($h == 0)
            return $m . " M";
        if ($m == 0)
            return $h . " J";
        return $h . " J " . $m . " M";
    }
}
?>

<!-- <link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css')?>"> -->
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> -->

<style>
    /* Ultra-Minimalist Overtime Design */
    :root {
        --tgm-indigo: #4f46e5;
        --tgm-rose: #e11d48;
        --tgm-emerald: #10b981;
        --tgm-slate-50: #f8fafc;
        --tgm-slate-100: #f1f5f9;
        --tgm-slate-200: #e2e8f0;
        --tgm-slate-700: #334155;
        --tgm-slate-900: #0f172a;
    }

    body {
        background-color: #f8fafc;
        font-family: 'Inter', sans-serif !important;
    }

    .clean-card {
        background: white;
        border: 1px solid var(--tgm-slate-200);
        border-radius: 0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .stats-minimal {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-box {
        padding: 1.25rem;
        border-start: 4px solid var(--tgm-slate-900);
    }

    .stat-box.pending {
        border-color: #f59e0b;
    }

    .stat-box.approved {
        border-color: var(--tgm-emerald);
    }

    .stat-box.rejected {
        border-color: var(--tgm-rose);
    }

    .table-simple thead th {
        background: var(--tgm-slate-50);
        color: var(--tgm-slate-700);
        text-transform: uppercase;
        font-size: 0.65rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        padding: 15px 12px;
        border: none;
    }

    .table-simple tbody td {
        padding: 15px 12px;
        vertical-align: middle;
        color: var(--tgm-slate-900);
        border-bottom: 1px solid var(--tgm-slate-100);
        font-size: 0.8rem;
    }

    .employee-info-group {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .avatar-mini {
        width: 32px;
        height: 32px;
        background: var(--tgm-slate-100);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.7rem;
        color: var(--tgm-slate-700);
    }

    .status-pill-clean {
        font-size: 0.65rem;
        padding: 4px 12px;
        font-weight: 900;
        letter-spacing: 0.02em;
        border-radius: 50px;
        text-transform: uppercase;
    }

    .text-late-warning {
        color: var(--tgm-rose);
        font-weight: 800;
    }

    .penalty-badge {
        background: var(--tgm-rose);
        color: white;
        font-size: 0.6rem;
        padding: 1px 6px;
        border-radius: 4px;
        font-weight: 900;
        margin-left: 4px;
        vertical-align: middle;
    }

    /* .font-mono {
        font-family: 'JetBrains Mono', monospace !important;
        font-size: 0.8rem;
    } */

    .fw-900 {
        font-weight: 900;
    }

    .ls-1 {
        letter-spacing: 1px;
    }

    .btn-action-mini {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        border: 1px solid var(--tgm-slate-200);
        background: white;
        color: var(--tgm-slate-700);
        transition: all 0.2s;
    }

    .btn-action-mini:hover {
        background: var(--tgm-slate-900);
        color: white;
        border-color: var(--tgm-slate-900);
    }

    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>

<div class="app-content">
    <?php $this->load->view('layout_admin/header_modul'); ?>

    <div class="container-fluid pt-3">
        <!-- Header Title & Back Button -->
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="<?= base_url('hr')?>"
                class="btn btn-white d-flex align-items-center justify-content-center shadow-sm"
                style="width: 42px; height: 42px; padding: 0; border-radius: 10px; border: 1px solid var(--tgm-slate-200); background: white; color: var(--tgm-slate-700);"
                title="Kembali ke Dashboard HR">
                <i class="bi bi-arrow-left fs-5"></i>
            </a>
            <div>
                <h4 class="fw-900 mb-0 ls-1 text-dark">MONITORING <span class="text-indigo">LEMBUR (SPL)</span></h4>
                <p class="small text-muted mb-0">Manajemen pengajuan dan approval lembur karyawan</p>
            </div>
        </div>

        <!-- Dashboard Statistics Minimal -->
        <div class="stats-minimal">
            <div class="clean-card stat-box">
                <div class="small fw-900 text-muted text-uppercase ls-1">Total Pengajuan</div>
                <div class="h3 fw-900 font-mono text-dark mb-0">
                    <?= $total?>
                </div>
            </div>
            <div class="clean-card stat-box pending">
                <div class="small fw-900 text-muted text-uppercase ls-1">Menunggu Approval</div>
                <div class="h3 fw-900 font-mono text-warning mb-0">
                    <?= $pending?>
                </div>
            </div>
            <div class="clean-card stat-box approved">
                <div class="small fw-900 text-muted text-uppercase ls-1">Disetujui</div>
                <div class="h3 fw-900 font-mono text-emerald mb-0">
                    <?= $approved?>
                </div>
            </div>
            <div class="clean-card stat-box rejected">
                <div class="small fw-900 text-muted text-uppercase ls-1">Ditolak</div>
                <div class="h3 fw-900 font-mono text-rose mb-0">
                    <?= $rejected?>
                </div>
            </div>
        </div>

        <!-- Filters & Action Bar Minimal -->
        <div class="clean-card p-4 mb-4 no-print border">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <form action="" method="get">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted small fw-bold">PERIODE</span>
                            <input type="date" name="tanggal" class="form-control border-start-0 ps-0 fw-bold"
                                value="<?= $tanggal_selected?>" onchange="this.form.submit()">
                        </div>
                    </form>
                </div>
                <div class="col-md-9 text-end">
                    <div class="d-flex gap-2 justify-content-end">
                        <button class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal"
                            data-bs-target="#modalSopLembur">
                            <i class="bi bi-shield-lock me-2"></i> SOP LEMBUR
                        </button>
                        <button class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal"
                            data-bs-target="#modalTambahLembur">
                            <i class="bi bi-plus-lg me-2"></i> TAMBAH LEMBUR
                        </button>
                        <button onclick="window.print()"
                            class="btn btn-outline-dark rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-printer me-2"></i> CETAK
                        </button>
                        <a href="<?= base_url('karyawan/rekap_lembur_bulanan')?>"
                            class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-calendar-check me-2"></i> REKAP BULANAN
                        </a>
                        <a href="<?= base_url('karyawan/pengaturan_lembur')?>"
                            class="btn btn-outline-primary rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-gear me-2"></i> SETTING KEBIJAKAN
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div id="notif-area"></div>

        <!-- Main Log Table Minimal -->
        <div class="clean-card overflow-hidden">
            <div class="p-3 bg-white border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-900 text-dark mb-0 ls-1"><i class="bi bi-list-check me-2"></i>LOG PELAKSANAAN LEMBUR</h6>
                <div class="input-group" style="max-width: 300px;">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                    <input type="text" id="tableSearch" class="form-control form-control-sm border-0 bg-light"
                        placeholder="Search...">
                </div>
            </div>
            <div class="table-responsive">
                <table id="tableLembur" class="table table-simple mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">KARYAWAN</th>
                            <th>Lembur Pagi</th>
                            <th>Lembur Wajib</th>
                            <th>Lembur Tambahan</th>
                            <th class="text-center">Total</th>
                            <th class="text-end">NOMINAL</th>
                            <th class="text-end" style="color: #f97316;">UANG MAKAN</th>
                            <th>KEGIATAN / TUGAS</th>
                            <th class="text-center">BUKTI</th>
                            <th>STATUS</th>
                            <th class="text-center pe-4 no-print">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($karyawan as $row):
    $status_class = 'warning';
    $status_text = 'WAITING';
    if ($row->status == 'disetujui') {
        $status_class = 'success';
        $status_text = 'APPROVED';
    }
    elseif ($row->status == 'ditolak') {
        $status_class = 'danger';
        $status_text = 'REJECTED';
    }

    $initials = substr($row->nama_lengkap, 0, 1) . substr(explode(' ', $row->nama_lengkap)[count(explode(' ', $row->nama_lengkap)) - 1], 0, 1);
?>
                        <tr>
                            <td class="ps-4">
                                <div class="employee-info-group">
                                    <div class="avatar-mini">
                                        <?= strtoupper($initials)?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0 d-flex align-items-center gap-1">
                                            <?= strtoupper($row->nama_lengkap)?>
                                            <?php
    $jam_masuk = $row->jam_masuk;
    if ($jam_masuk && $jam_masuk != '00:00:00') {
        $time_val = strtotime($jam_masuk);
        $limit_red = strtotime(date('Y-m-d', $time_val) . ' 08:01:00');
        $limit_penalty = strtotime(date('Y-m-d', $time_val) . ' 08:11:00');

        if ($time_val >= $limit_penalty) {
            echo '<span class="penalty-badge" title="Potongan 1 Jam Lembur (Telat >= 11 Menit)">-1J</span>';
        }
    }
?>
                                        </div>
                                        <div class="x-small text-muted" style="font-size: 0.65rem;">
                                            <span
                                                class="<?=(isset($time_val) && $time_val >= $limit_red) ? 'text-late-warning' : ''?>">
                                                Masuk:
                                                <?= substr($row->jam_masuk, 0, 5)?>
                                            </span>
                                            • NIK:
                                            <?= $row->nik?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-start">
                                <?php if ($row->jam_pagi > 0 || !empty($row->all_pagi_ranges)): ?>
                                <?php
        // Use set to avoid duplicates
        $p_ranges = array_unique($row->all_pagi_ranges ?: []);
        foreach ($p_ranges as $pr):
?>
                                <div class="fw-bold font-mono text-indigo mb-0" style="font-size: 0.75rem;">
                                    SPL:
                                    <?= $pr?>
                                </div>
                                <?php
        endforeach; ?>

                                <?php if (empty($p_ranges) && $row->jam_pagi > 0): ?>
                                <div class="fw-bold font-mono text-indigo mb-0" style="font-size: 0.75rem;">
                                    Pagi:
                                    <?= $row->jam_mulai_pagi?> -
                                    <?= $row->jam_selesai_pagi?>
                                </div>
                                <?php
        endif; ?>

                                <div class="x-small font-mono text-muted" style="font-size: 0.65rem;">
                                    Absen:
                                    <?= substr($row->jam_masuk, 0, 5)?>
                                </div>
                                <div class="x-small fw-900 text-muted" style="font-size: 0.65rem;">
                                    <?= format_durasi_lembur($row->jam_pagi)?>
                                </div>
                                <?php
    else: ?>
                                <span class="text-muted opacity-50">-</span>
                                <?php
    endif; ?>
                            </td>
                            <td class="text-start">
                                <?php if ($row->jam_wajib > 0 || !empty($row->all_wajib_ranges)): ?>
                                <?php
        $w_ranges = array_unique($row->all_wajib_ranges ?: []);
        foreach ($w_ranges as $wr):
            $label = (strpos($wr, '16:') === 0 || strpos($wr, '15:') === 0) ? 'Tap' : 'SPL';
?>
                                <div class="fw-bold font-mono text-dark mb-0" style="font-size: 0.75rem;">
                                    <?= $label?>:
                                    <?= $wr?>
                                </div>
                                <?php
        endforeach; ?>

                                <?php if (empty($w_ranges) && $row->jam_wajib > 0): ?>
                                <div class="fw-bold font-mono text-dark mb-0" style="font-size: 0.75rem;">
                                    Wajib:
                                    <?= $row->jam_mulai_wajib?> -
                                    <?= $row->jam_selesai_wajib?>
                                </div>
                                <?php
        endif; ?>

                                <div class="x-small fw-900 text-muted" style="font-size: 0.65rem;">
                                    <?= format_durasi_lembur($row->jam_wajib)?>
                                </div>
                                <?php
    else: ?>
                                <span class="text-muted opacity-50">-</span>
                                <?php
    endif; ?>
                            </td>
                            <td class="text-start">
                                <?php if ($row->jam_kerjaan > 0 || !empty($row->all_kerjaan_ranges)): ?>
                                <?php
        $k_ranges = array_unique($row->all_kerjaan_ranges ?: []);
        foreach ($k_ranges as $kr):
?>
                                <div class="fw-bold font-mono text-warning mb-0" style="font-size: 0.75rem;">
                                    SPL:
                                    <?= $kr?>
                                </div>
                                <?php
        endforeach; ?>

                                <?php if (empty($k_ranges) && $row->jam_kerjaan > 0): ?>
                                <div class="fw-bold font-mono text-warning mb-0" style="font-size: 0.75rem;">
                                    SPL:
                                    <?= $row->jam_mulai_kerjaan?> -
                                    <?= $row->jam_selesai_kerjaan?>
                                </div>
                                <?php
        endif; ?>

                                <div class="x-small font-mono text-muted" style="font-size: 0.65rem;">
                                    Absen:
                                    <?= substr($row->jam_pulang, 0, 5)?>
                                </div>
                                <div class="x-small fw-900 text-muted" style="font-size: 0.65rem;">
                                    <?= format_durasi_lembur($row->jam_kerjaan)?>
                                </div>
                                <?php
    else: ?>
                                <span class="text-muted opacity-50">-</span>
                                <?php
    endif; ?>
                            </td>
                            <td class="text-center">

                                <?= format_durasi_lembur($row->jam_efektif)?>

                            </td>
                            <td class="text-end ">
                                <?= isset($row->total_lembur) ? number_format($row->total_lembur, 0, ',', '.') : '-'?>
                            </td>
                            <td class="text-end fw-bold font-mono text-orange">
                                <?=(isset($row->biaya_makan) && $row->biaya_makan > 0) ? number_format($row->biaya_makan, 0, ',', '.') : '-'?>
                            </td>
                            <td>
                                <div class="text-wrap small text-muted" style="max-width: 250px;">
                                    <?= $row->kegiatan?>
                                </div>
                            </td>
                            <td class="text-center">
                                <?php if (!empty($row->foto_kegiatan)): ?>
                                <a href="javascript:void(0)" class="view-foto btn-action-mini m-auto"
                                    data-foto="<?= base_url('assets/images/lembur/' . $row->foto_kegiatan)?>">
                                    <i class="bi bi-image"></i>
                                </a>
                                <?php
    else: ?>
                                <i class="bi bi-camera-off text-muted opacity-25"></i>
                                <?php
    endif; ?>
                            </td>
                            <td>
                                <span
                                    class="status-pill-clean bg-<?= $status_class?> bg-opacity-10 text-<?= $status_class?>">
                                    <?= $status_text?>
                                </span>
                            </td>
                            <td class="text-center pe-4 no-print">
                                <div class="d-flex justify-content-center gap-2">
                                    <?php if (!empty($row->id)): ?>
                                    <div class="dropdown">
                                        <button class="btn-action-mini" data-bs-toggle="dropdown"><i
                                                class="bi bi-check2-circle"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                            <li><a class="dropdown-item fw-bold text-success btn-update-status"
                                                    href="javascript:void(0)" data-id="<?= $row->id?>"
                                                    data-karyawan-id="<?= $row->karyawan_id?>"
                                                    data-tanggal="<?= $row->tanggal?>"
                                                    data-status="disetujui">SETUJUI</a></li>
                                            <li><a class="dropdown-item fw-bold text-danger btn-update-status"
                                                    href="javascript:void(0)" data-id="<?= $row->id?>"
                                                    data-karyawan-id="<?= $row->karyawan_id?>"
                                                    data-tanggal="<?= $row->tanggal?>" data-status="ditolak">TOLAK</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <button class="btn-action-mini text-primary btn-kelola-lembur"
                                        data-karyawan-id="<?= $row->karyawan_id?>" data-tanggal="<?= $row->tanggal?>"
                                        data-nama="<?= htmlspecialchars($row->nama_lengkap, ENT_QUOTES)?>" title="Kelola / Edit Lembur Pagi & Sore">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <?php
    else: ?>
                                    <span class="text-muted opacity-50 small">-</span>
                                    <?php
    endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php
endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick HR Links -->

    </div>
</div>

<!-- Modal Tambah Lembur -->
<div class="modal fade" id="modalTambahLembur" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content shadow-lg border-0" style="border-radius: 0;">
            <div class="modal-header bg-dark text-white border-0" style="border-radius: 0;">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>TAMBAH DATA LEMBUR</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="<?= base_url('karyawan/aksi_simpan_lembur')?>" method="post">
                <div class="modal-body p-4 bg-light">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small text-uppercase mb-2">Pilih Karyawan</label>
                        <select name="karyawan_id" class="form-select shadow-sm" style="border-radius: 0;" required>
                            <option value="">-- Pilih Karyawan --</option>
                            <?php foreach ($listkaryawan as $emp): ?>
                            <option value="<?= $emp->karyawan_id?>">
                                <?= strtoupper($emp->nama_lengkap)?>
                                (
                                <?= $emp->nik?>)
                            </option>
                            <?php
endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark small text-uppercase mb-2">Tanggal
                                Pelaksanaan</label>
                            <input type="date" name="tanggal" class="form-control shadow-sm" style="border-radius: 0;"
                                value="<?= date('Y-m-d')?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark small text-uppercase mb-2">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control shadow-sm" style="border-radius: 0;"
                                required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark small text-uppercase mb-2">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control shadow-sm"
                                style="border-radius: 0;" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small text-uppercase mb-2">Keterangan Tugas /
                            Kegiatan</label>
                        <textarea name="kegiatan" class="form-control shadow-sm" rows="3" style="border-radius: 0;"
                            placeholder="Uraikan detail pekerjaan lembur..." required></textarea>
                    </div>
                    <div>
                        <label class="form-label fw-bold text-dark small text-uppercase mb-2">Honor Lembur
                            (Opsional)</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted small fw-bold">RP</span>
                            <input type="number" name="honor" class="form-control border-start-0"
                                style="border-radius: 0;" placeholder="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-0 p-3 shadow-sm">
                    <button type="button" class="btn btn-outline-dark fw-bold px-4 text-uppercase"
                        data-bs-dismiss="modal" style="border-radius: 0; font-size: 12px;">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm text-uppercase"
                        style="border-radius: 0; font-size: 12px;">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Kelola Lembur Pagi / Sore -->
<div class="modal fade" id="modalKelolaLembur" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0" style="border-radius: 0;">
            <div class="modal-header bg-dark text-white border-0" style="border-radius: 0;">
                <div>
                    <h5 class="modal-title fw-bold mb-0"><i class="bi bi-gear-fill me-2"></i>KELOLA DETAIL LEMBUR</h5>
                    <small class="text-white-50" id="kelolaInfoTitle">Loading...</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-light">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="tableKelolaLembur">
                        <thead class="bg-white">
                            <tr>
                                <th class="text-secondary small fw-bold">JENIS</th>
                                <th class="text-secondary small fw-bold">WAKTU</th>
                                <th class="text-secondary small fw-bold">KEGIATAN</th>
                                <th class="text-secondary small fw-bold text-center">FOTO</th>
                                <th class="text-secondary small fw-bold text-center">STATUS</th>
                                <th class="text-secondary small fw-bold text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
                <div class="p-3 alert text-muted small mb-0 rounded-0 border-0 border-top">
                    *Ubah jam dan klik <i class="bi bi-save text-primary"></i> untuk menyimpan perubahan, atau klik <i
                        class="bi bi-trash text-danger"></i> untuk menghapus sesi lembur (Misal: Hapus lembur pagi jika
                    salah tekan). Data yang dihapus tidak memengaruhi lembur sesi lain.
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" />
<script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function () {
        const tableLembur = $('#tableLembur').DataTable({
            responsive: true,
            language: {
                "emptyTable": "Tidak ada data yang tersedia pada tabel ini",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                "infoThousands": ".",
                "lengthMenu": "Tampilkan _MENU_ entri",
                "loadingRecords": "Sedang memuat...",
                "processing": "Sedang memproses...",
                "search": "Cari:",
                "zeroRecords": "Tidak ditemukan data yang sesuai",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            },
            dom: 'rtip',
            pageLength: 25,
            ordering: false
        });

        $('#tableSearch').on('keyup', function () {
            tableLembur.search(this.value).draw();
        });

        $('.btn-update-status').click(function () {
            const id = $(this).data('id');
            const karyawan_id = $(this).data('karyawan-id');
            const tanggal = $(this).data('tanggal');
            const status = $(this).data('status');
            const statusText = status === 'disetujui' ? 'SETUJUI' : 'TOLAK';
            const icon = status === 'disetujui' ? 'success' : 'warning';
            const color = status === 'disetujui' ? '#27ae60' : '#e74c3c';

            Swal.fire({
                title: 'Konfirmasi Approval',
                text: "Apakah Anda yakin ingin " + statusText + " pengajuan lembur ini?",
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: color,
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'YA, KONFIRMASI',
                cancelButtonText: 'BATAL'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('karyawan/update_status_ajax')?>',
                        type: 'POST',
                        data: { id: id, status: status, karyawan_id: karyawan_id, tanggal: tanggal },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Status lembur berhasil diperbarui.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function () {
                            Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                        }
                    });
                }
            });
        });

    // KELOLA LEMBUR (Edit Time / Hapus Specifik)
    $(document).on('click', '.btn-kelola-lembur', function () {
        const karyawan_id = $(this).data('karyawan-id');
        const tanggal = $(this).data('tanggal');
        const nama = $(this).data('nama');

        $('#kelolaInfoTitle').text(nama + ' - ' + tanggal);
        const tbody = $('#tableKelolaLembur tbody');
        tbody.html('<tr><td colspan="6" class="text-center py-4"><i class="bi bi-hourglass-split fa-spin fs-4"></i></td></tr>');

        $('#modalKelolaLembur').modal('show');

        $.ajax({
            url: '<?= base_url('karyawan/get_lembur_detail_ajax')?>',
            type: 'POST',
            data: { karyawan_id: karyawan_id, tanggal: tanggal },
            dataType: 'json',
            success: function (res) {
                if (res.success && res.data.length > 0) {
                    tbody.empty();
                    res.data.forEach(item => {
                        let waktuText = (item.jam_mulai < '12:00:00') ? '<span class="badge bg-info bg-opacity-10 text-info fw-bold">PAGI</span>' : '<span class="badge bg-warning bg-opacity-10 text-warning fw-bold">SORE/MALAM</span>';

                        let statusBadge = '';
                        if (item.status === 'disetujui') statusBadge = '<span class="badge bg-success bg-opacity-10 text-success x-small fw-bold">APPROVED</span>';
                        else if (item.status === 'ditolak') statusBadge = '<span class="badge bg-danger bg-opacity-10 text-danger x-small fw-bold">REJECTED</span>';
                        else statusBadge = '<span class="badge bg-warning bg-opacity-10 text-warning x-small fw-bold">WAITING</span>';

                        let fotoHtml = item.foto_kegiatan
                            ? `<a href="<?= base_url('assets/images/lembur/')?>${item.foto_kegiatan}" target="_blank">
                                 <img src="<?= base_url('assets/images/lembur/')?>${item.foto_kegiatan}" style="width:32px; height:32px; object-fit:cover; border-radius:4px;">
                               </a>`
                            : '<i class="bi bi-camera-off text-muted opacity-25"></i>';

                        tbody.append(`
                                <tr id="row_lembur_${item.id}">
                                    <td class="align-middle">${waktuText}</td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center gap-1">
                                            <input type="time" class="form-control form-control-sm border-0 bg-white shadow-sm fw-bold time-update input-mulai" data-id="${item.id}" value="${item.jam_mulai.substring(0, 5)}" style="border-radius:0; width:85px;">
                                            <span>-</span>
                                            <input type="time" class="form-control form-control-sm border-0 bg-white shadow-sm fw-bold time-update input-selesai" data-id="${item.id}" value="${item.jam_selesai.substring(0, 5)}" style="border-radius:0; width:85px;">
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="small text-muted text-truncate" style="max-width:120px;" title="${item.kegiatan || '-'}">${item.kegiatan || '-'}</div>
                                    </td>
                                    <td class="align-middle text-center">${fotoHtml}</td>
                                    <td class="align-middle text-center">${statusBadge}</td>
                                    <td class="align-middle text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button class="btn btn-sm btn-white text-success btn-individual-status" data-id="${item.id}" data-status="disetujui" title="Setujui Bagian Ini Saja"><i class="bi bi-check-lg"></i></button>
                                            <button class="btn btn-sm btn-white text-danger btn-individual-status" data-id="${item.id}" data-status="ditolak" title="Tolak Bagian Ini Saja"><i class="bi bi-x-lg"></i></button>
                                            <button class="btn btn-sm btn-light text-primary btn-save-time" data-id="${item.id}" title="Simpan Waktu"><i class="bi bi-save"></i></button>
                                            <button class="btn btn-sm btn-light text-danger btn-delete-lembur-spesifik" data-id="${item.id}" title="Hapus"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            `);
                    });
                } else {
                    tbody.html('<tr><td colspan="6" class="text-center text-muted py-4">Sesi lembur tidak ditemukan atau sudah dihapus.</td></tr>');
                }
            }
            });
        });

    $(document).on('click', '.btn-individual-status', function () {
        const id = $(this).data('id');
        const status = $(this).data('status');

        $.ajax({
            url: '<?= base_url('karyawan/update_status_ajax')?>',
            type: 'POST',
            data: { id: id, status: status },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'Status Diperbarui', toast: true, position: 'top-end', timer: 1500, showConfirmButton: false });
                    // Re-trigger detail refresh without closing modal
                    $('.btn-kelola-lembur[data-karyawan-id]:focus').click();
                    // Or just re-fetch manually
                }
            }
        });
    });

    $(document).on('click', '.btn-save-time', function () {
        const id = $(this).data('id');
        const tr = $('#row_lembur_' + id);
        const jam_mulai = tr.find('.input-mulai').val();
        const jam_selesai = tr.find('.input-selesai').val();

        if (!jam_mulai || !jam_selesai) return Swal.fire('Error', 'Jam harus diisi!', 'error');

        const btn = $(this);
        const oh = btn.html();
        btn.html('<i class="bi bi-arrow-repeat fa-spin"></i>').prop('disabled', true);

        $.ajax({
            url: '<?= base_url('karyawan/update_lembur_time_ajax')?>',
            type: 'POST',
            data: { id: id, jam_mulai: jam_mulai, jam_selesai: jam_selesai },
            dataType: 'json',
            success: function (res) {
                btn.html(oh).prop('disabled', false);
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'Tersimpan', toast: true, position: 'top-end', timer: 1500, showConfirmButton: false });
                }
            }
            });
        });

    $(document).on('click', '.btn-delete-lembur-spesifik', function () {
        const id = $(this).data('id');
        const tr = $('#row_lembur_' + id);

        Swal.fire({
            title: 'Hapus Sesi Lembur Ini?',
            text: "Bagian lembur ini akan terhapus namun lembur lainnya pada hari ini akan tetap aman.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            confirmButtonText: 'YA, HAPUS',
            cancelButtonText: 'BATAL'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('karyawan/hapus_lembur_ajax')?>',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            tr.fadeOut(300, function () { $(this).remove(); });
                            Swal.fire({ icon: 'success', title: 'Terhapus!', toast: true, position: 'top-end', timer: 1500, showConfirmButton: false });
                        } else {
                            Swal.fire('Gagal', response.message || 'Terjadi kesalahan.', 'error');
                        }
                    }
                });
            }
        });
    });

    // Refreshes the page when the modal closes, if something has been changed
    $('#modalKelolaLembur').on('hidden.bs.modal', function () {
        location.reload();
    });
    });

    $(document).on('click', '.view-foto', function () {
        const url = $(this).data('foto');
        $('#imgPreview').attr('src', url);
        $('#modalPreviewFoto').modal('show');
    });
</script>

<!-- Modal Preview Foto -->
<div class="modal fade" id="modalPreviewFoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0" style="border-radius: 0;">
            <div class="modal-header bg-dark text-white border-0" style="border-radius: 0;">
                <h5 class="modal-title fw-bold"><i class="bi bi-image me-2"></i>FOTO KEGIATAN LEMBUR</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 text-center bg-light">
                <img id="imgPreview" src="" class="img-fluid w-100" style="max-height: 80vh; object-fit: contain;">
            </div>
            <div class="modal-footer bg-white border-0 p-3 shadow-sm">
                <button type="button" class="btn btn-outline-dark fw-bold px-4 text-uppercase w-100"
                    data-bs-dismiss="modal" style="border-radius: 0; font-size: 12px;">TUTUP PREVIEW</button>
            </div>
        </div>
    </div>
</div>

<!-- Modern Overtime SOP -->
<div class="modal fade" id="modalSopLembur" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-0 overflow-hidden">
            <div class="modal-header bg-dark text-white p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-3"
                        style="width: 40px; height: 40px;">
                        <i class="bi bi-shield-check text-dark fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-900 ls-1 mb-0 uppercase">Standard Operating Procedure</h5>
                        <p class="small text-muted mb-0 opacity-75">Sistem Perhitungan Durasi Lembur Terpadu</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-5 bg-light border-end p-4">
                        <h6 class="fw-900 text-dark mb-4 ls-1">KEBIJAKAN JAM EFEKTIF</h6>
                        <ul class="list-unstyled small text-muted lh-lg">
                            <li class="mb-4 d-flex">
                                <i class="bi bi-clock-history text-indigo me-2 mt-1"></i>
                                <div>
                                    <span class="fw-bold text-dark d-block">Sistem Tabel Dinamis</span>
                                    <span>Perhitungan Jam Efektif (1x, 1.5x, 2x) merujuk pada pengaturan di <a
                                            href="<?= base_url('karyawan/pengaturan_lembur')?>"
                                            class="fw-bold text-primary">Kebijakan Lembur</a> secara otomatis.</span>
                                </div>
                            </li>
                            <li class="mb-4 d-flex">
                                <i class="bi bi-sliders text-indigo me-2 mt-1"></i>
                                <div>
                                    <span class="fw-bold text-dark d-block">Jam Pagi Ikuti Kebijakan</span>
                                    <span>Nilai lembur pagi mengikuti sesi yang cocok di <a
                                            href="<?= base_url('karyawan/pengaturan_lembur')?>"
                                            class="fw-bold text-primary">Kebijakan Lembur</a>, tanpa potongan manual di luar tabel sesi.</span>
                                </div>
                            </li>
                            <li class="mb-4 d-flex">
                                <i class="bi bi-lightning-charge text-warning me-2 mt-1"></i>
                                <div>
                                    <span class="fw-bold text-dark d-block">Pencocokan Cerdas</span>
                                    <span>Sistem otomatis mencari kecocokan Jam Mulai &amp; Jam Selesai SPL dari
                                        *database* untuk memberikan tarif yang paling sesuai.</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-7 p-4">
                        <div class="mb-4">
                            <h6 class="fw-900 text-primary mb-3 ls-1 mt-2">ALUR VERIFIKASI ADMIN</h6>
                            <div class="timeline-sop small ps-3 border-start border-2 border-secondary">
                                <div class="mb-3 position-relative">
                                    <div class="fw-bold text-dark">1. Cek Kesesuaian Jam</div>
                                    <p class="text-muted mb-0">Pastikan jam SPL masuk akal. Jika ada kesalahan, Anda
                                        bisa menggunakan fitur "Kelola/Edit Lembur Pagi &amp; Sore" (ikon pensil).</p>
                                </div>
                                <div class="mb-3 position-relative">
                                    <div class="fw-bold text-dark">2. Validasi Bukti & Kegiatan</div>
                                    <p class="text-muted mb-0">Pastikan foto kegiatan merpresentasikan pekerjaan saat
                                        shift lembur berjalan.</p>
                                </div>
                                <div class="mb-0 position-relative">
                                    <div class="fw-bold text-dark">3. Sinkronisasi Payroll</div>
                                    <p class="text-muted mb-0">Setelah status **APPROVE**, nilai jam efektif &amp; uang
                                        makan akan langsung ditarik ke rekapitulasi slip gaji bulanan.</p>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info border-0 rounded-0 d-flex align-items-center mb-0">
                            <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                            <div class="small fw-bold">Jika Sistem Error, pastikan Anda telah mendefinisikan blok jam
                                pelaksanaan tersebut di Menu Pengaturan Lembur.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-white">
                <button type="button" class="btn btn-dark w-100 rounded-0 fw-900 py-3 shadow"
                    data-bs-dismiss="modal">SAYA MENGERTI SOP INI</button>
            </div>
        </div>
    </div>
</div>

<style>
    .timeline-sop>div:before {
        content: "";
        position: absolute;
        left: -21px;
        top: 5px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #3498db;
        border: 2px solid white;
    }
</style>
