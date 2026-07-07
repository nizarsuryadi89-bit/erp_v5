<?php
$this->load->view('layout_admin/header_modul');
$bulan_selected = isset($bulan) ? $bulan : date('m');
$tahun_selected = isset($tahun) ? $tahun : date('Y');

// Helper to format decimal hours to "X jam" (Whole hours only as per SOP)
function format_duration_text($decimal_hours)
{
    if ($decimal_hours <= 0)
        return "-";
    $hours = floor($decimal_hours);

    if ($hours > 0)
        return "$hours jam";

    return "-";
}

// Calculate Summary
$total_jam = 0;
$total_pagi = 0;
$total_wajib = 0;
$total_kerjaan = 0;
$total_karyawan = 0;
$total_nominal = 0;
$total_all_makan = 0;
$unique_employees = [];

foreach ($karyawan as $row) {
    $total_jam += isset($row->total_jam_efektif) ? $row->total_jam_efektif : 0;
    $total_pagi += isset($row->jam_pagi) ? $row->jam_pagi : 0;
    $total_wajib += isset($row->jam_wajib) ? $row->jam_wajib : 0;
    $total_kerjaan += isset($row->jam_kerjaan) ? $row->jam_kerjaan : 0;
    $total_nominal += isset($row->total_rupiah) ? $row->total_rupiah : 0;
    $total_all_makan += isset($row->total_uang_makan) ? $row->total_uang_makan : 0;
    $unique_employees[$row->karyawan_id] = true;
}
$total_karyawan = count($unique_employees);
?>

<style>
    /* Ultra-Minimalist Rekap Design */
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

    .stat-box.highlight {
        border-color: var(--tgm-indigo);
    }

    .stat-box.success {
        border-color: var(--tgm-emerald);
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

    /* .font-mono {
        font-family: 'JetBrains Mono', monospace !important;
        font-size: 0.82rem;
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

    .badge-soft {
        font-size: 0.65rem;
        padding: 4px 10px;
        font-weight: 800;
        border-radius: 50px;
        text-transform: uppercase;
    }

    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>

<div class="app-content">
    <div class="container-fluid">
        <!-- Header & Stats Minimal -->
        <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
            <div class="d-flex align-items-center gap-3">
                <a href="<?= base_url('hr') ?>"
                    class="btn btn-white d-flex align-items-center justify-content-center shadow-sm"
                    style="width: 42px; height: 42px; padding: 0; border-radius: 10px; border: 1px solid var(--tgm-slate-200); background: white; color: var(--tgm-slate-700);"
                    title="Kembali ke Dashboard HR">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h3 class="fw-900 mb-0 ls-1">REKAP <span class="text-indigo">LEMBUR BULANAN</span></h3>
                    <p class="small text-muted mb-0">Akumulasi durasi dan nominal periode
                        <?= date('F Y', strtotime("$tahun-$bulan-01")) ?>.
                    </p>
                </div>
            </div>
            <div class="d-flex gap-2 no-print">
                <button class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal"
                    data-bs-target="#modalSopRekap">
                    <i class="bi bi-shield-lock me-2"></i> SOP REKAP
                </button>
                <button onclick="window.print()" class="btn btn-outline-dark rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-printer me-2"></i> CETAK LAPORAN
                </button>
            </div>
        </div>

        <div class="stats-minimal">
            <div class="clean-card stat-box">
                <div class="small fw-900 text-muted text-uppercase ls-1 x-small">Total Karyawan</div>
                <div class="h3 fw-900 text-dark mb-0"><?= $total_karyawan ?> <span class="small text-muted"
                        style="font-size: 0.9rem;">Org</span></div>
            </div>
            <div class="clean-card stat-box highlight">
                <div class="small fw-900 text-muted text-uppercase ls-1 x-small">Total Lembur Pagi</div>
                <div class="h3 fw-900 text-indigo mb-0"><?= number_format($total_pagi, 1) ?> <span
                        class="small text-muted" style="font-size: 0.9rem;">Jam</span></div>
            </div>
            <div class="clean-card stat-box highlight">
                <div class="small fw-900 text-muted text-uppercase ls-1 x-small">Total Lembur Wajib</div>
                <div class="h3 fw-900 text-indigo mb-0"><?= number_format($total_wajib, 1) ?> <span
                        class="small text-muted" style="font-size: 0.9rem;">Jam</span></div>
            </div>
            <div class="clean-card stat-box highlight">
                <div class="small fw-900 text-muted text-uppercase ls-1 x-small">Total Lembur Kerjaan</div>
                <div class="h3 fw-900 text-indigo mb-0"><?= number_format($total_kerjaan, 1) ?> <span
                        class="small text-muted" style="font-size: 0.9rem;">Jam</span></div>
            </div>
            <div class="clean-card stat-box success">
                <div class="small fw-900 text-muted text-uppercase ls-1 x-small">Anggaran Lembur</div>
                <div class="h3 fw-900 text-emerald mb-0">Rp <?= number_format($total_nominal, 0, ',', '.') ?></div>
            </div>
            <div class="clean-card stat-box success" style="border-color: #f97316;">
                <div class="small fw-900 text-muted text-uppercase ls-1 x-small">Anggaran Makan</div>
                <div class="h3 fw-900 mb-0" style="color: #f97316;">Rp
                    <?= number_format($total_all_makan, 0, ',', '.') ?></div>
            </div>
        </div>

        <!-- Filters Minimal -->
        <div class="clean-card p-4 mb-4 no-print border">
            <form action="" method="get" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted small fw-bold">BULAN</span>
                        <select name="bulan" class="form-select border-start-0 ps-0 fw-bold">
                            <?php
                            for ($i = 1; $i <= 12; $i++) {
                                $month_val = str_pad($i, 2, '0', STR_PAD_LEFT);
                                $selected = ($month_val == $bulan_selected) ? 'selected' : '';
                                echo "<option value='$month_val' $selected>" . date('F', mktime(0, 0, 0, $i, 10)) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted small fw-bold">TAHUN</span>
                        <select name="tahun" class="form-select border-start-0 ps-0 fw-bold">
                            <?php
                            $current_year = date('Y');
                            for ($i = $current_year; $i >= $current_year - 5; $i--) {
                                $selected = ($i == $tahun_selected) ? 'selected' : '';
                                echo "<option value='$i' $selected>$i</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold shadow-sm">
                        <i class="bi bi-filter me-2"></i> FILTER
                    </button>
                </div>
                <div class="col-md-3 ms-auto">
                    <input type="hidden" id="current_bulan_full" value="<?= $tahun_selected . '-' . $bulan_selected ?>">
                    <div
                        class="input-group input-group-sm rounded-pill overflow-hidden shadow-sm border border-secondary border-opacity-10">
                        <span class="input-group-text bg-white border-0 ps-3">
                            <i class="bi bi-search text-primary" style="font-size: 0.75rem;"></i>
                        </span>
                        <input type="text" id="rekapSearch" class="form-control border-0 bg-white small fw-bold"
                            placeholder="Cari Nama Karyawan..." autocomplete="off">
                    </div>
                </div>
            </form>
        </div>

        <!-- Data Table Minimal -->
        <div class="clean-card overflow-hidden">
            <div class="p-3 bg-white border-bottom">
                <h6 class="fw-900 text-dark mb-0 ls-1"><i class="bi bi-grid-3x3-gap me-2"></i>RINCIAN AKUMULASI PER
                    KARYAWAN</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-simple mb-0 align-middle" id="rekapTable">
                    <thead>
                        <tr>
                            <th class="ps-4">KARYAWAN</th>

                            <th class="text-center">DURASI PAGI</th>
                            <th class="text-center">DURASI WAJIB</th>
                            <th class="text-center">DURASI LANJUT</th>
                            <th class="text-center bg-light">TOTAL JAM</th>

                            <th class="text-end">Tarif Lembur</th>
                            <th class="text-end">NOMINAL</th>
                            <th class="text-end" style="color: #f97316;">UANG MAKAN</th>
                            <th class="text-center">KEHADIRAN</th>
                            <th class="text-center pe-4 no-print">DETAIL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($karyawan)): ?>
                            <?php foreach ($karyawan as $row):
                                $initials = substr($row->nama_lengkap, 0, 1) . substr(explode(' ', $row->nama_lengkap)[count(explode(' ', $row->nama_lengkap)) - 1], 0, 1);
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="employee-info-group">
                                            <div class="avatar-mini"><?= strtoupper($initials) ?></div>
                                            <div>
                                                <div class="fw-bold text-dark mb-0"><?= strtoupper($row->nama_lengkap) ?></div>
                                                <div class="x-small text-muted" style="font-size: 0.65rem;">NIK:
                                                    <?= $row->nik ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <?php if ($row->jam_pagi > 0): ?>
                                            <button type="button" class="btn btn-link p-0 fw-bold text-decoration-none text-indigo"
                                                style="font-size: 0.8rem;" onclick="showPagiModal('<?= $row->karyawan_id ?>')">
                                                <?= number_format($row->jam_pagi, 1) ?> Jam
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted opacity-25">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($row->jam_wajib > 0): ?>
                                            <button type="button" class="btn btn-link p-0 fw-bold text-decoration-none text-indigo"
                                                style="font-size: 0.8rem;" onclick="showWajibModal('<?= $row->karyawan_id ?>')">
                                                <?= number_format($row->jam_wajib, 1) ?> Jam
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted opacity-25">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($row->jam_kerjaan > 0): ?>
                                            <button type="button" class="btn btn-link p-0 fw-bold text-decoration-none text-indigo"
                                                style="font-size: 0.8rem;" onclick="showKerjaanModal('<?= $row->karyawan_id ?>')">
                                                <?= number_format($row->jam_kerjaan, 1) ?> Jam
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted opacity-25">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center bg-light fw-900 text-indigo">
                                        <?= number_format($row->total_jam_efektif, 1) ?> Jam
                                        <?php if (isset($row->jam_potongan) && $row->jam_potongan > 0): ?>
                                            <div class="x-small text-danger fw-bold" style="font-size: 0.65rem;"
                                                title="Potongan Telat (>= 08:11)">
                                                -<?= number_format($row->jam_potongan, 1) ?>J
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?= number_format($row->nominal_rate, 0, ',', '.') ?>
                                    </td>
                                    <td class="text-end fw-bold font-mono text-emerald">
                                        <?php if ($row->manual_uang_lembur !== null): ?>
                                            <div class="text-decoration-line-through text-muted x-small" style="font-size: 0.65rem;">
                                                <?= number_format($row->total_rupiah, 0, ',', '.') ?>
                                            </div>
                                            <span class="text-primary">Rp <?= number_format($row->manual_uang_lembur, 0, ',', '.') ?></span>
                                            <span class="badge bg-primary x-small p-1" style="font-size: 0.5rem; vertical-align: middle;">MANUAL</span>
                                        <?php else: ?>
                                            <?= number_format($row->total_rupiah, 0, ',', '.') ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end fw-bold font-mono" style="color: #f97316;">
                                        <?php if ($row->manual_biaya_makan !== null): ?>
                                            <div class="text-decoration-line-through text-muted x-small" style="font-size: 0.65rem;">
                                                <?= (isset($row->total_uang_makan) && $row->total_uang_makan > 0) ? number_format($row->total_uang_makan, 0, ',', '.') : '-' ?>
                                            </div>
                                            <span class="text-primary">Rp <?= number_format($row->manual_biaya_makan, 0, ',', '.') ?></span>
                                        <?php else: ?>
                                            <?= (isset($row->total_uang_makan) && $row->total_uang_makan > 0) ? number_format($row->total_uang_makan, 0, ',', '.') : '-' ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge bg-slate-100 text-dark border px-2 py-1 fw-bold"><?= $row->jml_hari ?>
                                            Hari</span>
                                    </td>
                                    <td class="text-center pe-4 no-print">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button type="button" class="btn-action-mini"
                                                onclick="showDetailModal('<?= $row->karyawan_id ?>')" title="Lihat Detail">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn-action-mini text-primary"
                                                onclick="openManualModal('<?= $row->karyawan_id ?>', '<?= addslashes($row->nama_lengkap) ?>', '<?= $row->total_rupiah ?>', '<?= $row->total_uang_makan ?>', '<?= $row->manual_uang_lembur ?>', '<?= $row->manual_biaya_makan ?>', '<?= addslashes($row->manual_keterangan) ?>')" title="Update Manual Payroll">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted small fw-bold">Belum ada data lembur
                                    untuk periode ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modals Section -->
<?php if (!empty($karyawan)): ?>
    <?php foreach ($karyawan as $row): ?>
        <div class="modal fade" id="modalDetail<?= $row->karyawan_id ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-0 border-0 shadow-lg">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold">Detail Lembur - <?= strtoupper($row->nama_lengkap) ?></h5>
                        <button type="button" class="btn-close" onclick="closeDetailModal('<?= $row->karyawan_id ?>')"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0" style="font-size: 0.85rem;">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        <th class="py-2 px-3">Tanggal</th>
                                        <th class="py-2 text-center">Masuk</th>
                                        <th class="py-2 text-center">Pulang</th>
                                        <th class="py-2 text-center">SPL</th>
                                        <th class="py-2 text-center">Pagi</th>
                                        <th class="py-2 text-center">Wajib</th>
                                        <th class="py-2 text-center">Kerjaan</th>
                                        <th class="py-2 text-center">Total</th>
                                        <th class="py-2 text-end px-3">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($row->details)):
                                        foreach ($row->details as $d):
                                            // Format times
                                            $in = ($d->jam_masuk != '00:00:00') ? substr($d->jam_masuk, 0, 5) : '-';
                                            $out = ($d->jam_pulang != '00:00:00') ? substr($d->jam_pulang, 0, 5) : '-';
                                            $spl_range = ($d->jam_mulai_spl != '00:00') ? substr($d->jam_mulai_spl, 0, 5) . ' - ' . substr($d->jam_selesai_spl, 0, 5) : '-';
                                            ?>
                                            <tr>
                                                <td class="px-3 fw-bold"><?= date('d/m', strtotime($d->tanggal)) ?></td>
                                                <td class="text-center"><?= $in ?></td>
                                                <td class="text-center"><?= $out ?></td>
                                                <td class="text-center text-muted"><?= $spl_range ?></td>

                                                <td class="text-center"><?= ($d->pagi > 0) ? number_format($d->pagi, 2) : '-' ?></td>
                                                <td class="text-center text-primary fw-bold">
                                                    <?php if ($d->wajib > 0): ?>
                                                        <div class="mb-0"><?= number_format($d->wajib, 1) ?> jam</div>
                                                        <div class="x-small fw-normal text-muted" style="font-size:0.7rem;">
                                                            <?= $d->wajib_start ?> - <?= $d->wajib_end ?>
                                                        </div>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center"><?= ($d->kerjaan > 0) ? number_format($d->kerjaan, 2) : '-' ?>
                                                </td>

                                                <td class="text-center fw-bold bg-light"><?= format_duration_text($d->total_efektif) ?>
                                                </td>
                                                <td class="text-end px-3"><?= number_format($d->rupiah, 0, ',', '.') ?></td>
                                            </tr>
                                            <?php
                                        endforeach;
                                    else:
                                        ?>
                                        <tr>
                                            <td colspan="9" class="text-center py-3 text-muted">Tidak ada data detail.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot class="bg-light fw-bold border-top">
                                    <tr>
                                        <td colspan="7" class="text-end py-2">TOTAL</td>
                                        <td class="text-center py-2"><?= format_duration_text($row->total_jam_efektif) ?></td>
                                        <td class="text-end px-3 py-2">Rp <?= number_format($row->total_rupiah, 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-sm btn-secondary"
                            onclick="closeDetailModal('<?= $row->karyawan_id ?>')">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Modals Detail Kerjaan Section -->
<?php if (!empty($karyawan)): ?>
    <?php foreach ($karyawan as $row): ?>
        <div class="modal fade" id="modalKerjaan<?= $row->karyawan_id ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-0 border-0 shadow-lg">
                    <div class="modal-header bg-warning bg-opacity-10">
                        <h5 class="modal-title fw-bold text-dark">Detail Lembur Kerjaan -
                            <?= strtoupper($row->nama_lengkap) ?>
                        </h5>
                        <button type="button" class="btn-close" onclick="closeKerjaanModal('<?= $row->karyawan_id ?>')"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0" style="font-size: 0.85rem;">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        <th class="py-2 px-3">Tanggal</th>
                                        <th class="py-2 text-center">SPL</th>
                                        <th class="py-2 text-center">Durasi</th>
                                        <th class="py-2 px-3">Keterangan</th>
                                        <th class="py-2 text-center">Bukti</th>
                                        <th class="py-2 text-end px-3">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $total_durasi_kerjaan = 0;
                                    $total_rupiah_kerjaan = 0;
                                    if (!empty($row->details)):
                                        foreach ($row->details as $d):
                                            if ($d->kerjaan > 0):
                                                $nominal_kerjaan = $d->kerjaan * $row->nominal_rate;
                                                $total_durasi_kerjaan += $d->kerjaan;
                                                $total_rupiah_kerjaan += $nominal_kerjaan;
                                                $spl_range = ($d->jam_mulai_spl != '00:00') ? substr($d->jam_mulai_spl, 0, 5) . ' - ' . substr($d->jam_selesai_spl, 0, 5) : '-';
                                                ?>
                                                <tr>
                                                    <td class="px-3 fw-bold">
                                                        <?= date('d/m', strtotime($d->tanggal)) ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?= $spl_range ?>
                                                    </td>
                                                    <td class="text-center fw-bold">
                                                        <?= number_format($d->kerjaan, 1) ?>
                                                    </td>
                                                    <td class="px-3 small text-muted" style="max-width: 200px; word-wrap: break-word;">
                                                        <?= !empty($d->keterangan) ? $d->keterangan : '-' ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <?php if (!empty($d->foto_kegiatan)): ?>
                                                                <a href="<?= base_url('assets/images/lembur/' . $d->foto_kegiatan) ?>"
                                                                    target="_blank" title="Foto Kegiatan">
                                                                    <img src="<?= base_url('assets/images/lembur/' . $d->foto_kegiatan) ?>"
                                                                        class="rounded border"
                                                                        style="height: 40px; width: 40px; object-fit: cover;">
                                                                </a>
                                                            <?php else: ?>
                                                                <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <td class="text-end px-3">
                                                        <?= number_format($nominal_kerjaan, 0, ',', '.') ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            endif;
                                        endforeach;
                                    endif;
                                    ?>
                                </tbody>
                                <tfoot class="bg-light fw-bold border-top">
                                    <tr>
                                        <td colspan="2" class="text-end py-2">TOTAL</td>
                                        <td class="text-center py-2">
                                            <?= number_format($total_durasi_kerjaan, 1) ?>
                                        </td>
                                        <td colspan="2"></td>
                                        <td class="text-end px-3 py-2">Rp
                                            <?= number_format($total_rupiah_kerjaan, 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- NEW: Modal Pagi -->
        <div class="modal fade" id="modalPagi<?= $row->karyawan_id ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-0 border-0 shadow-lg">
                    <div class="modal-header bg-indigo bg-opacity-10">
                        <h5 class="modal-title fw-bold text-dark">Detail Lembur Pagi -
                            <?= strtoupper($row->nama_lengkap) ?>
                        </h5>
                        <button type="button" class="btn-close" onclick="closePagiModal('<?= $row->karyawan_id ?>')"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0" style="font-size: 0.85rem;">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        <th class="py-2 px-3">Tanggal</th>
                                        <th class="py-2 text-center">Masuk</th>
                                        <th class="py-2 text-center">Durasi</th>
                                        <th class="py-2 text-end px-3">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $total_pagi_h = 0;
                                    $total_rupiah_pagi = 0;
                                    if (!empty($row->details)):
                                        foreach ($row->details as $d):
                                            if ($d->pagi > 0):
                                                $nominal_pagi = $d->pagi * $row->nominal_rate;
                                                $total_pagi_h += $d->pagi;
                                                $total_rupiah_pagi += $nominal_pagi;
                                                ?>
                                                <tr>
                                                    <td class="px-3 fw-bold"><?= date('d/m', strtotime($d->tanggal)) ?></td>
                                                    <td class="text-center"><?= substr($d->jam_masuk, 0, 5) ?></td>
                                                    <td class="text-center fw-bold"><?= number_format($d->pagi, 1) ?></td>
                                                    <td class="text-end px-3"><?= number_format($nominal_pagi, 0, ',', '.') ?></td>
                                                </tr>
                                                <?php
                                            endif;
                                        endforeach;
                                    endif;
                                    ?>
                                </tbody>
                                <tfoot class="bg-light fw-bold border-top">
                                    <tr>
                                        <td colspan="2" class="text-end py-2">TOTAL</td>
                                        <td class="text-center py-2"><?= number_format($total_pagi_h, 1) ?></td>
                                        <td class="text-end px-3 py-2">Rp <?= number_format($total_rupiah_pagi, 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- NEW: Modal Wajib -->
        <div class="modal fade" id="modalWajib<?= $row->karyawan_id ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-0 border-0 shadow-lg">
                    <div class="modal-header bg-emerald bg-opacity-10">
                        <h5 class="modal-title fw-bold text-dark">Detail Lembur Wajib -
                            <?= strtoupper($row->nama_lengkap) ?>
                        </h5>
                        <button type="button" class="btn-close" onclick="closeWajibModal('<?= $row->karyawan_id ?>')"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0" style="font-size: 0.85rem;">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        <th class="py-2 px-3">Tanggal</th>
                                        <th class="py-2 text-center">Jam Kerja</th>
                                        <th class="py-2 text-center">Durasi</th>
                                        <th class="py-2 text-end px-3">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $total_wajib_h = 0;
                                    $total_rupiah_wajib = 0;
                                    if (!empty($row->details)):
                                        foreach ($row->details as $d):
                                            if ($d->wajib > 0):
                                                $nominal_wajib = $d->wajib * $row->nominal_rate;
                                                $total_wajib_h += $d->wajib;
                                                $total_rupiah_wajib += $nominal_wajib;
                                                ?>
                                                <tr>
                                                    <td class="px-3 fw-bold"><?= date('d/m', strtotime($d->tanggal)) ?></td>
                                                    <td class="text-center small text-muted"><?= $d->wajib_start ?> - <?= $d->wajib_end ?>
                                                    </td>
                                                    <td class="text-center fw-bold"><?= number_format($d->wajib, 1) ?></td>
                                                    <td class="text-end px-3"><?= number_format($nominal_wajib, 0, ',', '.') ?></td>
                                                </tr>
                                                <?php
                                            endif;
                                        endforeach;
                                    endif;
                                    ?>
                                </tbody>
                                <tfoot class="bg-light fw-bold border-top">
                                    <tr>
                                        <td colspan="2" class="text-end py-2">TOTAL</td>
                                        <td class="text-center py-2"><?= number_format($total_wajib_h, 1) ?></td>
                                        <td class="text-end px-3 py-2">Rp <?= number_format($total_rupiah_wajib, 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Modern Rekap Overtime SOP -->
<div class="modal fade" id="modalSopRekap" tabindex="-1">
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
                        <p class="small text-muted mb-0 opacity-75">Rekonsiliasi & Penutupan Lembur Bulanan</p>
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
                                    <span class="fw-bold text-dark d-block">Lembur Pagi (06:00 - 08:00)</span>
                                    <span>Menggunakan sistem **LINEAR**. Durasi kehadiran x 1.5 (Sesuai Konfigurasi).</span>
                                </div>
                            </li>
                            <li class="mb-4 d-flex">
                                <i class="bi bi-arrow-up-right-circle text-indigo me-2 mt-1"></i>
                                <div>
                                    <span class="fw-bold text-dark d-block">Lembur Wajib & Kerjaan</span>
                                    <span>Menggunakan **Sistem Progresif**. <br>
                                    - Jam 1-2 (Wajib): Rate 1.0x<br>
                                    - Jam 3-4 (Kerjaan): Rate 1.5x<br>
                                    - Jam 5 keatas (Kerjaan): Rate 2.0x</span>
                                </div>
                            </li>
                            <li class="mb-4 d-flex">
                                <i class="bi bi-lightning-charge text-warning me-2 mt-1"></i>
                                <div>
                                    <span class="fw-bold text-dark d-block">Dynamic Threshold</span>
                                    <span>Lembur kerjaan & batas lembur wajib ditarik secara **OTOMATIS** berdasarkan
                                        pengaturan jam kerja per hari di database.</span>
                                </div>
                            </li>
                            <li class="mb-0 d-flex">
                                <i class="bi bi-shield-check text-success me-2 mt-1"></i>
                                <div>
                                    <span class="fw-bold text-dark d-block">Prioritas Data Tugas</span>
                                    <span>Jam selesai lembur kerjaan mengikuti **Waktu Tap Selesai** tugas di aplikasi
                                        mobile.</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-7 p-4">
                        <div class="mb-4">
                            <h6 class="fw-900 text-primary mb-3 ls-1 mt-2">1. AUDIT REKAP BULANAN</h6>
                            <div class="p-3 border bg-white mb-3" style="border-left: 4px solid #4f46e5 !important;">
                                <p class="small fw-bold text-dark mb-1">Pengecekan Detail Tugas</p>
                                <p class="x-small text-muted mb-0">Klik pada angka di kolom **DURASI LANJUT** untuk
                                    memverifikasi log kegiatan harian yang menyusun total jam tersebut.</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-900 text-primary mb-3 ls-1">2. LANGKAH PENUTUPAN</h6>
                            <div class="timeline-sop small ps-3 border-start border-2 border-secondary">
                                <div class="mb-3 position-relative">
                                    <div class="fw-bold text-dark">Step 1: Validasi Status</div>
                                    <p class="text-muted mb-0">Hanya data berstatus **DISUJUTUI** di Log Harian yang
                                        akan muncul di rekap ini.</p>
                                </div>
                                <div class="mb-3 position-relative">
                                    <div class="fw-bold text-dark">Step 2: Monitoring Anggaran</div>
                                    <p class="text-muted mb-0">Pantau kolom **TOTAL RUPIAH** guna memastikan total
                                        lembur per orang tetap dalam batas budget departemen.</p>
                                </div>
                                <div class="mb-0 position-relative">
                                    <div class="fw-bold text-dark">Step 3: Export & Arsip</div>
                                    <p class="text-muted mb-0">Simpan laporan ini sebagai bukti dukung pelampiran
                                        penggajian setiap bulannya.</p>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info border-0 rounded-0 d-flex align-items-center mb-0">
                            <i class="bi bi-info-circle fs-4 me-3"></i>
                            <div class="small fw-bold">PENTING: Seluruh angka di sini sudah tersinkronisasi dengan
                                aplikasi mobile dan slip gaji karyawan.</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-white">
                <button type="button" class="btn btn-dark w-100 rounded-0 fw-900 py-3 shadow"
                    data-bs-dismiss="modal">SAYA MENGERTI & DATA SUDAH SESUAI</button>
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
        background: var(--tgm-indigo);
        border: 2px solid white;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        $('.btn-update-status').click(function () {
            const id = $(this).data('id');
            const status = $(this).data('status');
            const statusText = status === 'disetujui' ? 'SETUJUI' : 'TOLAK';
            const icon = status === 'disetujui' ? 'success' : 'warning';
            const color = status === 'disetujui' ? '#27ae60' : '#e74c3c';

            Swal.fire({
                title: 'Konfirmasi Approval',
                text: "Apakah Anda yakin ingin " + statusText + " data ini?",
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: color,
                confirmButtonText: 'YA, KONFIRMASI',
                cancelButtonText: 'BATAL'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('karyawan/update_status_ajax') ?>',
                        type: 'POST',
                        data: { id: id, status: status },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                Swal.fire('Berhasil', 'Status berhasil diperbarui.', 'success').then(() => location.reload());
                            }
                        },
                        error: function () {
                            Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                        }
                    });
                }
            });
        });
    });

    // Force jQuery usage for Modals (AdminLTE/Mixed Environment Safe)
    function showDetailModal(id) {
        $('#modalDetail' + id).modal('show');
    }

    function closeDetailModal(id) {
        $('#modalDetail' + id).modal('hide');
    }

    function showKerjaanModal(id) {
        $('#modalKerjaan' + id).modal('show');
    }

    function closeKerjaanModal(id) {
        $('#modalKerjaan' + id).modal('hide');
    }

    function showPagiModal(id) {
        $('#modalPagi' + id).modal('show');
    }

    function closePagiModal(id) {
        $('#modalPagi' + id).modal('hide');
    }

    function showWajibModal(id) {
        $('#modalWajib' + id).modal('show');
    }

    function closeWajibModal(id) {
        $('#modalWajib' + id).modal('hide');
    }

    // Real-time Search Logic for Rekap Overtime
    document.getElementById('rekapSearch').addEventListener('keyup', function () {
        let keyword = this.value.toUpperCase();
        let table = document.getElementById('rekapTable');
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
<script>
    // Manual Override Logic
    function openManualModal(id, nama, sysUang, sysMakan, manUang, manMakan, ket) {
        $('#man_karyawan_id').val(id);
        $('#man_nama_karyawan').text(nama);
        $('#man_sys_uang').text(parseFloat(sysUang).toLocaleString('id-ID'));
        $('#man_sys_makan').text(parseFloat(sysMakan).toLocaleString('id-ID'));

        // Values
        $('#input_uang_lembur').val(manUang !== '' ? manUang : sysUang);
        $('#input_biaya_makan').val(manMakan !== '' ? manMakan : sysMakan);
        $('#input_keterangan').val(ket);

        $('#modalUpdateManual').modal('show');
    }

    function saveManualPayroll() {
        const data = {
            karyawan_id: $('#man_karyawan_id').val(),
            bulan: $('#current_bulan_full').val(),
            uang_lembur: $('#input_uang_lembur').val(),
            biaya_makan_lembur: $('#input_biaya_makan').val(),
            keterangan: $('#input_keterangan').val()
        };

        Swal.fire({
            title: 'Simpan Perubahan?',
            text: "Data ini akan menggantikan perhitungan sistem pada Slip Gaji.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('karyawan/save_gaji_manual') ?>',
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil', 'Data manual telah disimpan.', 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Gagal', 'Gagal menyimpan data.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                    }
                });
            }
        });
    }

    function resetManualPayroll() {
        const kid = $('#man_karyawan_id').val();
        const bln = $('#current_bulan_full').val();

        Swal.fire({
            title: 'Reset ke Sistem?',
            text: "Data manual akan dihapus dan kembali menggunakan hitungan ERP.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Reset',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('karyawan/reset_gaji_manual') ?>',
                    type: 'POST',
                    data: {
                        karyawan_id: kid,
                        bulan: bln
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil', 'Data telah direset ke sistem.', 'success').then(() => location.reload());
                        }
                    }
                });
            }
        });
    }
</script>

<!-- Modal Update Manual -->
<div class="modal fade" id="modalUpdateManual" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-0">
            <div class="modal-header bg-dark text-white p-3">
                <h5 class="modal-title fw-bold small uppercase ls-1 mb-0">UPDATE MANUAL PAYROLL (OVERRIDE)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <label class="small fw-900 text-muted ls-1 mb-2 d-block">KARYAWAN</label>
                    <div id="man_nama_karyawan" class="h5 fw-bold text-indigo mb-0"></div>
                    <input type="hidden" id="man_karyawan_id">
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="small fw-900 text-muted ls-1 mb-2 d-block">UANG LEMBUR (RP)</label>
                        <input type="number" id="input_uang_lembur" class="form-control fw-bold" placeholder="0">
                        <div class="x-small text-muted mt-1">Sistem: <span id="man_sys_uang">0</span></div>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-900 text-muted ls-1 mb-2 d-block">UANG MAKAN (RP)</label>
                        <input type="number" id="input_biaya_makan" class="form-control fw-bold" placeholder="0">
                        <div class="x-small text-muted mt-1">Sistem: <span id="man_sys_makan">0</span></div>
                    </div>
                </div>

                <div class="mb-0">
                    <label class="small fw-900 text-muted ls-1 mb-2 d-block">ALASAN / KETERANGAN</label>
                    <textarea id="input_keterangan" class="form-control small" rows="3" placeholder="Contoh: Penyesuaian selisih mesin Finger..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-light">
                <button type="button" class="btn btn-outline-danger btn-sm px-3 fw-bold" onclick="resetManualPayroll()">RESET KE SISTEM</button>
                <button type="button" class="btn btn-dark btn-sm px-4 fw-bold ms-auto" onclick="saveManualPayroll()">SIMPAN PERUBAHAN</button>
            </div>
        </div>
    </div>
</div>
