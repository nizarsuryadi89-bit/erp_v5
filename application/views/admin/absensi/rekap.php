<?php
$daysIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
?>
<style>
    /* Ultra-Minimalist Individual Report Design */
    :root {
        --tgm-primary: #2563eb;
        --tgm-primary-dark: #1d4ed8;
        --tgm-rose: #e11d48;
        --tgm-emerald: #059669;
        --tgm-slate-50: #f8fafc;
        --tgm-slate-100: #f1f5f9;
        --tgm-slate-200: #e2e8f0;
        --tgm-slate-700: #475569;
        --tgm-slate-900: #0f172a;
    }

    body {
        background-color: #f8fafc;
        color: var(--tgm-slate-900);
    }

    .clean-card {
        background: white;
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
    }

    .report-header {
        background: white;
        padding: 1.5rem 2rem;
        border-bottom: 2px solid var(--tgm-slate-100);
        margin-bottom: 2rem;
    }

    .text-primary-tgm {
        color: var(--tgm-primary) !important;
    }

    .bg-primary-tgm {
        background-color: var(--tgm-primary) !important;
    }

    .text-rose {
        color: var(--tgm-rose) !important;
    }

    .bg-rose {
        background-color: var(--tgm-rose) !important;
    }

    .text-emerald {
        color: var(--tgm-emerald) !important;
    }

    .bg-emerald {
        background-color: var(--tgm-emerald) !important;
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

    /* Employee Profile Reinvented */
    .emp-profile-header {
        background: var(--tgm-slate-900);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 2rem;
        position: relative;
        overflow: hidden;
    }

    .emp-profile-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: linear-gradient(135deg, var(--tgm-primary) 0%, transparent 100%);
        opacity: 0.15;
    }

    .emp-avatar {
        width: 100px;
        height: 100px;
        border-radius: 20px;
        object-fit: cover;
        border: 4px solid rgba(255, 255, 255, 0.1);
        position: relative;
        z-index: 2;
    }

    .metric-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-box {
        padding: 1.5rem;
        background: white;
        border-radius: 12px;
        border: 1px solid var(--tgm-slate-100);
        border-top: 4px solid var(--tgm-slate-200);
        transition: transform 0.2s;
    }

    .stat-box:hover {
        transform: translateY(-3px);
    }

    .stat-box.success {
        border-top-color: var(--tgm-emerald);
    }

    .stat-box.warning {
        border-top-color: #f59e0b;
    }

    .stat-box.info {
        border-top-color: #06b6d4;
    }

    .stat-box.danger {
        border-top-color: var(--tgm-rose);
    }

    .stat-box.primary {
        border-top-color: var(--tgm-primary);
    }

    .table-simple thead th {
        background: var(--tgm-slate-50);
        color: var(--tgm-slate-700);
        text-transform: uppercase;
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        padding: 12px 15px;
        border-bottom: 2px solid var(--tgm-slate-100);
    }

    .table-simple tbody td {
        padding: 15px;
        border-bottom: 1px solid var(--tgm-slate-100);
        font-size: 0.9rem;
    }

    .font-mono {
        font-family: 'JetBrains Mono', 'Fira Code', monospace !important;
    }

    .ls-1 {
        letter-spacing: 0.05em;
    }

    .fw-900 {
        font-weight: 900;
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 1cm;
        }

        .no-print {
            display: none !important;
        }

        .emp-profile-header {
            background: white !important;
            color: black !important;
            border: 2px solid #eee !important;
            box-shadow: none !important;
        }

        .emp-avatar {
            border: 2px solid #ddd !important;
        }

        .table-simple th,
        .table-simple td {
            border: 1px solid #ddd !important;
            padding: 8px !important;
        }

        .signature-section {
            display: flex !important;
            margin-top: 50px;
        }
    }

    .signature-section {
        display: none;
        justify-content: space-between;
        margin-top: 3rem;
    }

    .sig-box {
        text-align: center;
        width: 220px;
    }

    .sig-line {
        margin-top: 4rem;
        border-bottom: 2px solid var(--tgm-slate-900);
        padding-bottom: 5px;
        font-weight: 800;
        text-transform: uppercase;
    }
</style>

<div class="app-content container-fluid px-4 px-lg-5">
    <!-- Header Section -->
    <div class="report-header clean-card d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-900 mb-0 ls-1">RAPORT <span class="text-primary-tgm">KEHADIRAN KERJA</span></h3>
            <div class="d-flex align-items-center gap-2 text-muted mt-1">
                <span class="badge bg-primary-tgm rounded-pill px-3"><?= $perusahaan ?></span>
                <span class="small fw-800 text-uppercase ls-1"><i class="bi bi-calendar3 me-2"></i>
                    PERIODE: <?= date('d M Y', strtotime($start_date)) ?> —
                    <?= date('d M Y', strtotime($end_date)) ?></span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 no-print">
            <button type="button" class="btn btn-white border rounded-pill px-4 fw-900 small shadow-sm text-dark"
                data-bs-toggle="modal" data-bs-target="#modalGuideRekapIndividu">
                <i class="bi bi-info-circle me-1 text-primary-tgm"></i> PANDUAN
            </button>
            <a href="javascript:history.back()"
                class="btn btn-white border rounded-pill px-4 fw-900 small shadow-sm text-dark">
                <i class="bi bi-arrow-left me-1"></i> KEMBALI
            </a>
            <?php if (isset($karyawan)): ?>
                <a href="<?= base_url('absensi/cetak_rekap/' . $karyawan->karyawan_id . '?start_date=' . $start_date . '&end_date=' . $end_date) ?>"
                    target="_blank" class="btn btn-primary-tgm rounded-pill px-4 fw-900 small shadow-sm">
                    <i class="bi bi-printer-fill me-1"></i> CETAK RAPORT
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filter Box -->
    <div class="clean-card p-4 mb-4 no-print bg-slate-100 border-0">
        <form method="GET" action="<?= current_url() ?>" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="fw-900 text-muted small text-uppercase mb-2">Dari Tanggal</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-0"><i
                            class="bi bi-calendar-event text-muted"></i></span>
                    <input type="date" class="form-control border-0 fw-bold px-3" name="start_date"
                        value="<?= $start_date ?>">
                </div>
            </div>
            <div class="col-md-4">
                <label class="fw-900 text-muted small text-uppercase mb-2">Sampai Tanggal</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-0"><i
                            class="bi bi-calendar-check text-muted"></i></span>
                    <input type="date" class="form-control border-0 fw-bold px-3" name="end_date"
                        value="<?= $end_date ?>">
                </div>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary-tgm w-100 fw-900 rounded-pill py-2 shadow-sm">
                    <i class="bi bi-arrow-repeat me-2"></i> PERBARUI LAPORAN
                </button>
            </div>
        </form>
    </div>

    <?php if (isset($karyawan)): ?>
        <!-- Premium Employee Profile Header -->
        <div class="emp-profile-header">
            <div class="emp-avatar-wrapper position-relative">
                <img src="<?= base_url('assets/images/karyawan/' . ($karyawan->foto ? $karyawan->foto : 'default.png')) ?>"
                    class="emp-avatar shadow">
                <?php
                $badgeColor = 'bg-emerald text-white';
                if ($predicate == 'Cukup')
                    $badgeColor = 'bg-warning text-dark';
                if ($predicate == 'Kurang' || $predicate == 'Sangat Buruk')
                    $badgeColor = 'bg-rose text-white';
                ?>
                <div class="position-absolute bottom-0 end-0 translate-middle-y me-n2">
                    <span
                        class="badge rounded-pill <?= $badgeColor ?> px-3 py-2 fw-900 border border-white border-2 small shadow-sm"><?= strtoupper($predicate) ?></span>
                </div>
            </div>
            <div class="flex-grow-1">
                <div class="small text-white-50 text-uppercase fw-900 ls-1 mb-1" style="font-size: 0.65rem;">Arsip Data
                    Karyawan</div>
                <h1 class="fw-900 mb-2 ls-n1"><?= $karyawan->nama_lengkap ?></h1>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="badge bg-white bg-opacity-10 text-white rounded-pill px-3 py-2 small fw-800 ls-1"
                        style="font-size: 0.7rem;"><i class="bi bi-person-badge me-2"></i> <?= $karyawan->nik ?></span>
                    <span class="badge bg-white bg-opacity-10 text-white rounded-pill px-3 py-2 small fw-800 ls-1"
                        style="font-size: 0.7rem;"><i class="bi bi-briefcase me-2"></i>
                        <?= $karyawan->nama_jabatan ?></span>
                    <span class="badge bg-white bg-opacity-10 text-white rounded-pill px-3 py-2 small fw-800 ls-1"
                        style="font-size: 0.7rem;"><i class="bi bi-diagram-3 me-2"></i> <?= $karyawan->divisi_nama ?></span>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($stats)): ?>
        <!-- Quick Stats Metric Grid -->
        <div class="metric-grid no-print">
            <div class="clean-card stat-box success">
                <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.6rem;">Total Hadir</div>
                <div class="h2 fw-900 font-mono text-emerald mb-0 mt-1"><?= $stats['hadir'] ?> <small
                        style="font-size: 50%;">HARI</small></div>
            </div>
            <div class="clean-card stat-box warning">
                <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.6rem;">Terlambat</div>
                <div class="h2 fw-900 font-mono text-warning mb-0 mt-1"><?= $stats['telat'] ?? 0 ?> <small
                        style="font-size: 50%;">X</small></div>
            </div>
            <div class="clean-card stat-box info">
                <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.6rem;">Izin / Sakit / Cuti
                </div>
                <div class="h2 fw-900 font-mono text-info mb-0 mt-1">
                    <?= ($stats['sakit'] ?? 0) + ($stats['izin'] ?? 0) + ($stats['cuti'] ?? 0) ?>
                    <small style="font-size: 50%;">HARI</small>
                </div>
            </div>
            <div class="clean-card stat-box danger">
                <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.6rem;">Alfa / Tanpa Ket</div>
                <div class="h2 fw-900 font-mono text-rose mb-0 mt-1"><?= $stats['alpha'] ?? 0 ?> <small
                        style="font-size: 50%;">HARI</small></div>
            </div>
            <div class="clean-card stat-box primary">
                <?php if (isset($karyawan)): ?>
                    <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.6rem;">Indeks Disiplin</div>
                    <div class="h2 fw-900 font-mono text-primary-tgm mb-0 mt-1"><?= number_format($score ?? 0, 1) ?>%</div>
                <?php else: ?>
                    <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.6rem;">Total Staff</div>
                    <div class="h2 fw-900 font-mono text-primary-tgm mb-0 mt-1"><?= $stats['total'] ?? 0 ?> <small
                            style="font-size: 50%;">PERSONEL</small></div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="clean-card overflow-hidden">
        <?php if (isset($karyawan) && isset($dates_range)): ?>
            <div class="p-4 bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-900 text-dark mb-0 ls-1"><i class="bi bi-clock-history me-2 text-primary-tgm"></i>LOG DETAIL
                    KEHADIRAN HARIAN</h6>
                <div class="small fw-800 text-muted d-none d-md-block ls-1">TOTAL ENTRY: <?= count($dates_range ?? []) ?>
                    HARI</div>
            </div>
            <div class="table-responsive">
                <table class="table table-simple mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4 text-center" width="60">NO</th>
                            <th>TANGGAL & HARI</th>
                            <th class="text-center">JAM MASUK</th>
                            <th class="text-center">JAM PULANG</th>
                            <th class="text-center">LEMBUR PAGI</th>
                            <th class="text-center">LEMBUR WAJIB + TAMBAHAN</th>
                            <th class="text-center">TOTAL LEMBUR</th>
                            <th class="text-center no-print">STATUS LEMBUR</th>
                            <!-- <th class="no-print">KETERANGAN</th> -->
                            <th class="text-center no-print">VERIFIKASI</th>
                            <th class="pe-4 no-print text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_pagi    = 0;
                        $total_wajib   = 0;
                        $total_kerjaan = 0;
                        $total_wt      = 0; // akumulasi display: SPL jika ada, wajib jika tidak
                        $total_all     = 0;
                        $iter = 1;

                        // Total Count Logic (Izin, Sakit, Cuti)
                        $count_is = ($stats['sakit'] ?? 0) + ($stats['izin'] ?? 0) + ($stats['cuti'] ?? 0);

                        foreach ($dates_range as $date):
                            $att = isset($att_map[$date]) ? $att_map[$date] : null;
                            $dayIndo = $daysIndo[date('w', strtotime($date))];
                            $isSaturday = (date('w', strtotime($date)) == 6);
                            $isSunday = (date('w', strtotime($date)) == 0);
                            $isHoliday = isset($holiday_map[$date]);
                            $day_spls = isset($lembur_map[$date]) ? $lembur_map[$date] : [];

                            $calc    = isset($lembur_calc[$date]) ? $lembur_calc[$date] : ['pagi' => 0, 'wajib' => 0, 'kerjaan' => 0, 'total' => 0];
                            $p_hours = round($calc['pagi'], 2);
                            $w_hours = round($calc['wajib'], 2);
                            $k_hours = round($calc['kerjaan'], 2);

                            // Hitung total: wajib + SPL + pagi (semua dijumlah)
                            $t_hours = $p_hours + $w_hours + $k_hours;

                            $total_pagi    += $p_hours;
                            $total_wajib   += $w_hours;
                            $total_kerjaan += $k_hours;
                            $total_wt      += ($w_hours + $k_hours);
                            $total_all     += $t_hours;

                            $tr_class = '';
                            if ($isSunday)
                                $tr_class = 'bg-rose bg-opacity-10';
                            else if ($isSaturday)
                                $tr_class = 'bg-slate-50';
                            ?>
                            <tr class="<?= $tr_class ?>">
                                <td class="text-center font-mono text-muted small"><?= $iter++ ?></td>
                                <td>
                                    <div class="fw-900 text-dark small ls-0"><?= strtoupper(date('d M Y', strtotime($date))) ?>
                                    </div>
                                    <div class="small fw-800 text-muted ls-1"><?= strtoupper($dayIndo) ?></div>
                                </td>
                                <?php if ($isHoliday): ?>
                                    <td colspan="2" class="text-center">
                                        <span
                                            class="badge-soft bg-rose text-rose bg-opacity-10 px-3 py-2 border border-rose border-opacity-25 w-100">
                                            <i class="bi bi-gift-fill me-2"></i> LIBUR: <?= $holiday_map[$date] ?>
                                        </span>
                                    </td>
                                <?php elseif ($isSunday): ?>
                                    <td colspan="2" class="text-center">
                                        <span
                                            class="badge-soft bg-rose text-rose bg-opacity-10 px-3 py-2 border border-rose border-opacity-25 w-100">
                                            <i class="bi bi-calendar-x-fill me-2"></i> LIBUR HARI MINGGU
                                        </span>
                                    </td>
                                <?php elseif ($att && $att->status && $att->status != 'Hadir'): ?>
                                    <td colspan="2" class="text-center">
                                        <?php
                                        $s_class = 'bg-slate-200 text-slate-700';
                                        if ($att->status == 'Izin' || strpos($att->status, 'Cuti') !== false)
                                            $s_class = 'bg-warning text-dark';
                                        elseif ($att->status == 'Sakit')
                                            $s_class = 'bg-info text-info';
                                        elseif ($att->status == 'Alpha')
                                            $s_class = 'bg-rose text-rose';
                                        ?>
                                        <span
                                            class="badge-soft <?= $s_class ?> bg-opacity-10 px-4 py-2 w-100 fw-900 border border-current border-opacity-10">
                                            <?= strtoupper($att->status) ?>
                                        </span>
                                    </td>
                                <?php else: ?>
                                    <td>
                                        <?php if ($att && $att->jam_masuk && $att->jam_masuk != '00:00:00'): ?>
                                            <?php
                                            $check_jk = isset($jk_map[$dayIndo]) ? $jk_map[$dayIndo]->jam_masuk : '08:00:00';
                                            $is_late = strtotime($att->jam_masuk) > strtotime($check_jk . ' +1 minute');
                                            ?>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="status-dot <?= $is_late ? 'bg-rose' : 'bg-emerald' ?>"
                                                    style="width: 8px; height: 8px; border-radius: 50%;"></div>
                                                <span
                                                    class="fw-900 font-mono small <?= $is_late ? 'text-rose' : 'text-dark' ?>"><?= substr($att->jam_masuk, 0, 5) ?></span>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted opacity-25">--:--</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($att && $att->jam_pulang && $att->jam_pulang != '00:00:00'): ?>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="status-dot bg-primary-tgm"
                                                    style="width: 8px; height: 8px; border-radius: 50%;"></div>
                                                <span
                                                    class="fw-900 font-mono small text-dark"><?= substr($att->jam_pulang, 0, 5) ?></span>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted opacity-25">--:--</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                                <td class="text-center fw-900 font-mono text-dark">
                                    <?= $p_hours > 0 ? str_replace('.', ',', (string) $p_hours) . ' <small class="fw-800">JAM</small>' : '<span class="opacity-25">-</span>' ?>
                                </td>
                                <td class="text-center fw-900 font-mono text-dark">
                                    <?php
                                    // Tampilkan wajib dan SPL keduanya jika ada
                                    $has_wajib = $w_hours > 0;
                                    $has_spl   = $k_hours > 0;
                                    if (!$has_wajib && !$has_spl):
                                    ?>
                                        <span class="opacity-25">-</span>
                                    <?php else: ?>
                                        <?php if ($has_wajib): ?>
                                            <span class="d-block"><?= str_replace('.', ',', (string)$w_hours) ?> <small class="fw-800">JAM</small></span>
                                            <span class="badge badge-soft bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25" style="font-size:.6rem;">WAJIB</span>
                                        <?php endif; ?>
                                        <?php if ($has_spl): ?>
                                            <?php if ($has_wajib): ?><div style="margin-top:3px;"></div><?php endif; ?>
                                            <span class="d-block"><?= str_replace('.', ',', (string)$k_hours) ?> <small class="fw-800">JAM</small></span>
                                            <span class="badge badge-soft bg-primary-tgm bg-opacity-10 text-primary-tgm border border-primary-tgm border-opacity-25" style="font-size:.6rem;">SPL</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center fw-900 font-mono text-primary-tgm">
                                    <?= $t_hours > 0 ? str_replace('.', ',', (string) $t_hours) . ' <small class="fw-800">JAM</small>' : '<span class="opacity-25">-</span>' ?>
                                </td>
                                <td class="text-center no-print">
                                    <?php if (!empty($day_spls)): ?>
                                        <?php
                                        $all_approved = true;
                                        $any_waiting = false;
                                        foreach ($day_spls as $s) {
                                            if ($s->status != 'disetujui')
                                                $all_approved = false;
                                            if ($s->status == 'pending' || empty($s->status))
                                                $any_waiting = true;
                                        }

                                        $b_class = 'btn-slate-100 text-muted';
                                        $icon = 'bi-gear';
                                        if ($all_approved) {
                                            $b_class = 'btn-emerald bg-opacity-10 text-emerald border-emerald border-opacity-25';
                                            $icon = 'bi-check-circle-fill';
                                        } elseif ($any_waiting) {
                                            $b_class = 'btn-warning bg-opacity-10 text-warning border-warning border-opacity-25';
                                            $icon = 'bi-clock-history';
                                        }
                                        ?>
                                        <button type="button" class="btn btn-sm rounded-pill px-3 btn-kelola-lembur <?= $b_class ?>"
                                            data-karyawan-id="<?= $karyawan->karyawan_id ?>" data-tanggal="<?= $date ?>"
                                            data-nama="<?= $karyawan->nama_lengkap ?>">
                                            <i class="bi <?= $icon ?> me-1"></i> KELOLA
                                        </button>
                                    <?php else: ?>
                                        <span class="opacity-25 small italic">Tanpa SPL</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center no-print">
                                    <?php
                                    $photos = [];
                                    if ($att && $att->foto_masuk)
                                        $photos[] = ['label' => 'ABSEN MASUK', 'url' => base_url($att->foto_masuk), 'info' => substr($att->jam_masuk, 0, 5)];
                                    if ($att && $att->foto_pulang)
                                        $photos[] = ['label' => 'ABSEN PULANG', 'url' => base_url($att->foto_pulang), 'info' => substr($att->jam_pulang, 0, 5)];
                                    foreach ($day_spls as $spl) {
                                        if ($spl->foto_kegiatan)
                                            $photos[] = ['label' => 'KEGIATAN LEMBUR', 'url' => base_url('assets/images/lembur/' . $spl->foto_kegiatan), 'info' => substr($spl->jam_mulai, 0, 5) . ' - ' . substr($spl->jam_selesai, 0, 5)];
                                    }
                                    ?>
                                    <?php if (!empty($photos)): ?>
                                        <button type="button"
                                            class="btn btn-sm btn-white border rounded-pill px-3 btn-view-all-photos shadow-sm"
                                            data-photos='<?= json_encode($photos) ?>'
                                            data-date="<?= strtoupper(date('d M Y', strtotime($date))) ?>"
                                            data-nama="<?= $karyawan->nama_lengkap ?>">
                                            <i class="bi bi-images text-primary-tgm me-1"></i> <?= count($photos) ?> FOTO
                                        </button>
                                    <?php else: ?>
                                        <i class="bi bi-camera text-muted opacity-25 small" title="Tidak ada foto"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="no-print text-center">
                                    <button type="button" class="btn btn-slate-100 btn-sm rounded-pill px-3 edit-attendance"
                                        data-id="<?= $att ? $att->id : '' ?>" data-karyawan-id="<?= $karyawan->karyawan_id ?>"
                                        data-tanggal="<?= $date ?>" data-status="<?= $att ? $att->status : '' ?>"
                                        data-masuk="<?= $att ? substr($att->jam_masuk, 0, 5) : '' ?>"
                                        data-pulang="<?= $att ? substr($att->jam_pulang, 0, 5) : '' ?>"
                                        data-ket="<?= $att ? $att->keterangan : '' ?>">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="bg-primary-tgm text-white">
                            <td colspan="4" class="text-end pe-4 py-3 fw-900 text-uppercase ls-1">AKUMULASI LEMBUR :</td>
                            <td class="text-center py-3 fw-900 font-mono">
                                <?= str_replace('.', ',', (string) round($total_pagi, 2)) ?> JAM
                            </td>
                            <td class="text-center py-3 fw-900 font-mono">
                                <?php if ($total_wajib > 0): ?>
                                    <span class="d-block"><?= str_replace('.', ',', (string) round($total_wajib, 2)) ?> <small class="fw-800">JAM</small></span>
                                    <span class="badge badge-soft bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25" style="font-size:.6rem;">WAJIB</span>
                                <?php endif; ?>
                                <?php if ($total_kerjaan > 0): ?>
                                    <?php if ($total_wajib > 0): ?><div style="margin-top:3px;"></div><?php endif; ?>
                                    <span class="d-block"><?= str_replace('.', ',', (string) round($total_kerjaan, 2)) ?> <small class="fw-800">JAM</small></span>
                                    <span class="badge badge-soft bg-white bg-opacity-10 text-white border border-white border-opacity-25" style="font-size:.6rem;">SPL</span>
                                <?php endif; ?>
                                <?php if ($total_wajib == 0 && $total_kerjaan == 0): ?>
                                    <span class="opacity-25">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center py-3 fw-900 font-mono">
                                <?= str_replace('.', ',', (string) round($total_all, 2)) ?> JAM
                            </td>
                            <td class="no-print p-2" colspan="3">
                                <button type="button" id="btnSyncPayroll" onclick="syncToPayroll()"
                                    class="btn btn-white text-primary-tgm fw-900 small w-100 rounded-pill shadow-sm py-2">
                                    <i class="bi bi-arrow-repeat me-1"></i> SYNC TO PAYROLL
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        <?php elseif (isset($rekap) && !empty($rekap)): ?>
            <div class="p-4 bg-white border-bottom">
                <h6 class="fw-900 text-dark mb-0 ls-1"><i class="bi bi-clock-history me-2 text-primary-tgm"></i>RIWAYAT
                    ABSENSI TERKINI</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-simple mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4 text-center" width="60">NO</th>
                            <th>TANGGAL & HARI</th>
                            <th>JAM MASUK</th>
                            <th>JAM PULANG</th>
                            <th class="text-center">STATUS</th>
                            <th class="pe-4">KETERANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($rekap as $r): ?>
                            <tr>
                                <td class="ps-4 text-center font-mono text-muted small"><?= $no++ ?></td>
                                <td>
                                    <div class="fw-900 text-dark small ls-0">
                                        <?= strtoupper(date('d M Y', strtotime($r->tanggal))) ?>
                                    </div>
                                    <div class="small fw-800 text-muted ls-1">
                                        <?= strtoupper($daysIndo[date('w', strtotime($r->tanggal))]) ?>
                                    </div>
                                </td>
                                <td class="font-mono fw-900 small"><?= $r->jam_masuk ? substr($r->jam_masuk, 0, 5) : '--:--' ?>
                                </td>
                                <td class="font-mono fw-900 small">
                                    <?= $r->jam_pulang ? substr($r->jam_pulang, 0, 5) : '--:--' ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $s_class = 'bg-slate-200 text-slate-700';
                                    if ($r->status == 'Hadir')
                                        $s_class = 'bg-emerald text-emerald';
                                    elseif ($r->status == 'Izin' || $r->status == 'Cuti')
                                        $s_class = 'bg-warning text-dark';
                                    elseif ($r->status == 'Sakit')
                                        $s_class = 'bg-info text-info';
                                    elseif ($r->status == 'Alpha')
                                        $s_class = 'bg-rose text-rose';
                                    ?>
                                    <span
                                        class="badge-soft <?= $s_class ?> bg-opacity-10 px-3 fw-900 border border-current border-opacity-10"><?= strtoupper($r->status) ?></span>
                                </td>
                                <td class="small text-muted italic pe-4"><?= $r->keterangan ?: '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif (isset($rekap_all) && !empty($rekap_all)): ?>
            <div class="p-4 bg-white border-bottom">
                <h6 class="fw-900 text-dark mb-0 ls-1"><i class="bi bi-people-fill me-2 text-primary-tgm"></i>RINGKASAN
                    KEHADIRAN KARYAWAN</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-simple mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4 text-center" width="60">NO</th>
                            <th>NAMA & NIK</th>
                            <th>JABATAN</th>
                            <th class="text-center">HADIR</th>
                            <th class="text-center">I/S</th>
                            <th class="text-center">ALFA</th>
                            <th>PERSENTASE</th>
                            <th class="pe-4 text-center">OPSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($rekap_all as $r): ?>
                            <tr>
                                <td class="ps-4 text-center font-mono text-muted small"><?= $no++ ?></td>
                                <td>
                                    <div class="fw-900 text-dark small ls-0"><?= strtoupper($r->nama_lengkap) ?></div>
                                    <div class="text-muted font-mono" style="font-size: 0.75rem;"><?= $r->nik ?></div>
                                </td>
                                <td class="small fw-800 text-muted"><?= $r->jabatan ?></td>
                                <td class="text-center font-mono fw-900 text-emerald"><?= $r->total_hadir ?></td>
                                <td class="text-center font-mono fw-900 text-warning">
                                    <?= $r->total_izin + $r->total_sakit + $r->total_cuti ?>
                                </td>
                                <td class="text-center font-mono fw-900 text-rose"><?= $r->total_alpha ?></td>
                                <td>
                                    <?php
                                    $total_att = $r->total_hadir + $r->total_izin + $r->total_sakit + $r->total_alpha;
                                    $percent = $total_att > 0 ? round(($r->total_hadir / $total_att) * 100, 1) : 0;
                                    $p_color = 'bg-emerald';
                                    if ($percent < 80)
                                        $p_color = 'bg-warning';
                                    if ($percent < 50)
                                        $p_color = 'bg-rose';
                                    ?>
                                    <div class="d-flex align-items-center gap-2" style="width: 140px;">
                                        <div class="progress flex-grow-1"
                                            style="height: 6px; border-radius: 10px; background: #f1f5f9;">
                                            <div class="progress-bar <?= $p_color ?>" style="width: <?= $percent ?>%"></div>
                                        </div>
                                        <span class="small fw-900 font-mono"><?= $percent ?>%</span>
                                    </div>
                                </td>
                                <td class="pe-4 text-center">
                                    <button type="button" onclick="viewReportKaryawan(<?= $r->karyawan_id ?>)"
                                        class="btn btn-slate-100 btn-sm rounded-pill px-3">
                                        <i class="bi bi-graph-up text-primary-tgm"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted opacity-25 d-block mb-3"></i>
                <h6 class="fw-900 text-muted ls-1">DATA TIDAK DITEMUKAN</h6>
                <p class="small text-muted">Sesuaikan periode filter atau hubungi Administrator.</p>
            </div>
        <?php endif; ?>
    </div>


    <!-- Official Print Footer (Signatures) -->
    <div class="signature-section ">
        <div class="sig-box">
            <p class="mb-0 fw-900 small text-uppercase">Dibuat Oleh,</p>
            <div class="sig-line">Admin Absensi</div>
            <div class="small fw-800 text-muted mt-1 ls-1">Staf HRD & Payroll</div>
        </div>
        <div class="sig-box text-center align-self-end pb-3">
            <p class="mb-0 font-mono small text-muted italic ls-1"><?= strtoupper(date('d F Y')) ?></p>
        </div>
        <div class="sig-box">
            <p class="mb-0 fw-900 small text-uppercase">Diketahui Oleh,</p>
            <div class="sig-line">Manager / Owner</div>
            <div class="small fw-800 text-muted mt-1 ls-1">Pimpinan Perusahaan</div>
        </div>
    </div>
</div>

<!-- Report Karyawan Modal -->
<div class="modal fade" id="reportKaryawanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-900 ls-n1">REPORT <span class="text-primary-tgm">SCORING DISIPLIN</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="reportKaryawanBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary-tgm" role="status"></div>
                    <p class="mt-3 fw-800 text-muted small ls-1">MENGAMBIL DATA DATABASE...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function viewReportKaryawan(id) {
        const modal = new bootstrap.Modal(document.getElementById('reportKaryawanModal'));
        modal.show();

        $('#reportKaryawanBody').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary-tgm" role="status"></div>
            <p class="mt-3 fw-800 text-muted small ls-1">MENGAMBIL DATA DATABASE...</p>
        </div>
    `);

        $.ajax({
            url: '<?= base_url('karyawan/get_karyawan_report_json/') ?>' + id,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (!response.success) {
                    $('#reportKaryawanBody').html('<div class="alert alert-danger px-4 py-3 border-0 rounded-4 fw-800">' + response.message + '</div>');
                    return;
                }
                const data = response.data;
                const stats = data.stats;

                let alertColor = 'bg-emerald';
                let catColor = 'text-emerald';
                if (data.score < 65) { alertColor = 'bg-rose'; catColor = 'text-rose'; }
                else if (data.score < 75) { alertColor = 'bg-warning'; catColor = 'text-warning'; }
                else if (data.score < 85) { alertColor = 'bg-info'; catColor = 'text-info'; }

                let html = `
                <div class="text-center mb-4">
                    <h2 class="fw-900 mb-0 ls-n1">${data.nama_lengkap}</h2>
                    <div class="small fw-800 text-muted ls-1">LAPORAN DISIPLIN TAHUN ${data.tahun}</div>
                </div>

                <div class="row g-2 mb-4 text-center font-mono">
                    <div class="col">
                        <div class="p-3 bg-slate-50 border rounded-4">
                            <h3 class="fw-900 text-emerald mb-0">${stats.hadir}</h3>
                            <small class="text-muted fw-800 ls-1">HADIR</small>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-3 bg-slate-50 border rounded-4">
                            <h3 class="fw-900 text-warning mb-0">${stats.telat}</h3>
                            <small class="text-muted fw-800 ls-1">TELAT</small>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-3 bg-slate-50 border rounded-4">
                            <h3 class="fw-900 text-info mb-0">${stats.sakit + stats.izin + stats.cuti}</h3>
                            <small class="text-muted fw-800 ls-1">I/S/C</small>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-3 bg-slate-50 border rounded-4">
                            <h3 class="fw-900 text-rose mb-0">${stats.alpha}</h3>
                            <small class="text-muted fw-800 ls-1">ALFA</small>
                        </div>
                    </div>
                </div>

                <div class="${alertColor} bg-opacity-10 rounded-4 text-center py-5 mb-0 border border-current border-opacity-10">
                    <h6 class="fw-900 text-uppercase ls-2 opacity-50 mb-3">INDEKS DISIPLIN AKHIR</h6>
                    <div class="display-2 fw-900 mb-0 ${catColor}">${data.score}%</div>
                    <h4 class="fw-900 mb-0 ${catColor} ls-1">${data.category.toUpperCase()}</h4>
                </div>
                
                <div class="text-center mt-3 small text-muted fw-800 ls-1 opacity-50">
                    <i class="bi bi-info-circle me-1"></i> PENALTI: TELAT (2%), ALPHA (5%), IZIN/SAKIT (1%)
                </div>
            `;
                $('#reportKaryawanBody').html(html);
            }
        });
    }
</script>

<!-- Modal Panduan Raport Individu -->
<div class="modal fade" id="modalGuideRekapIndividu" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-primary-tgm text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-primary-tgm rounded-pill d-flex align-items-center justify-content-center"
                        style="width: 45px; height: 45px;">
                        <i class="bi bi-journal-text fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-900 mb-0 ls-n1">PANDUAN <span class="opacity-75">RAPORT
                                KEHADIRAN</span></h5>
                        <p class="small mb-0 opacity-75 fw-800 ls-1">Penjelasan Indikator & Sistem Penilaian</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-slate-50">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="clean-card p-4 h-100">
                            <h6 class="fw-900 text-dark mb-3 ls-1 border-bottom pb-2">INDIKATOR UTAMA</h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-3 d-flex gap-2">
                                    <i class="bi bi-check-circle-fill text-emerald"></i>
                                    <span class="small text-muted fw-800"><b class="text-dark">Sangat Baik / Baik:</b>
                                        Kehadiran konsisten dengan keterlambatan minimal.</span>
                                </li>
                                <li class="mb-3 d-flex gap-2">
                                    <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                                    <span class="small text-muted fw-800"><b class="text-dark">Cukup:</b> Terdapat
                                        beberapa catatan keterlambatan atau izin.</span>
                                </li>
                                <li class="d-flex gap-2">
                                    <i class="bi bi-x-circle-fill text-rose"></i>
                                    <span class="small text-muted fw-800"><b class="text-dark">Kurang / Buruk:</b>
                                        Akumulasi Alpha atau keterlambatan masif.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="clean-card p-4 h-100">
                            <h6 class="fw-900 text-dark mb-3 ls-1 border-bottom pb-2">SISTEM SKORING</h6>
                            <div class="p-3 bg-slate-100 rounded-3 text-center mb-3">
                                <code class="fw-900 text-primary-tgm small">100% - (Penalty Terlambat/Izin/Alpha)</code>
                            </div>
                            <p class="small text-muted fw-800 mb-0">Total nilai akan menentukan predikat disiplin
                                karyawan sebagai bahan evaluasi manajemen bulanan.</p>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="clean-card p-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="fw-900 text-dark mb-1 ls-1">PENALTI POINT</h6>
                                    <p class="small text-muted fw-800 mb-0 ls-1">Keterlambatan (-2%), Alpha (-5%),
                                        Izin/Sakit (-1%) per hari.</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <span class="badge-soft bg-primary-tgm text-primary-tgm px-3 py-2 fw-900">STANDAR
                                        HRD 2024</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-white">
                <button type="button" class="btn btn-primary-tgm w-100 py-3 rounded-pill fw-900 ls-1 shadow-sm"
                    data-bs-dismiss="modal">MENGERTI & TUTUP</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal Edit Absensi -->
<div class="modal fade" id="modalEditAbsensi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-primary-tgm text-white p-4">
                <h5 class="modal-title fw-900 ls-n1"><i class="bi bi-pencil-square me-2"></i>EDIT ABSENSI MANUAL</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="<?= base_url('absensi/update_attendance_manual') ?>" method="POST">
                <div class="modal-body p-4 bg-slate-50">
                    <input type="hidden" name="id" id="edit_id">
                    <input type="hidden" name="karyawan_id" id="edit_karyawan_id">
                    <input type="hidden" name="tanggal" id="edit_tanggal">

                    <div class="mb-3">
                        <label class="fw-900 text-muted small text-uppercase mb-2 ls-1">Tanggal Absensi</label>
                        <input type="text" class="form-control bg-white border-0 fw-900 text-dark px-3"
                            id="display_tanggal" readonly disabled style="border-radius: 12px;">
                    </div>

                    <div class="mb-3">
                        <label class="fw-900 text-muted small text-uppercase mb-2 ls-1">Status Kehadiran</label>
                        <select class="form-select border-0 fw-900 px-3" name="status" id="edit_status" required
                            style="border-radius: 12px; height: 50px;">
                            <option value="Hadir">HADIR</option>
                            <option value="Sakit">SAKIT</option>
                            <option value="Izin">IZIN</option>
                            <option value="Alpha">ALFA (TANPA KET)</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="fw-900 text-muted small text-uppercase mb-2 ls-1">Jam Masuk</label>
                            <input type="time" class="form-control border-0 fw-900 font-mono text-dark px-3"
                                name="jam_masuk" id="edit_jam_masuk" style="border-radius: 12px; height: 50px;">
                        </div>
                        <div class="col-6">
                            <label class="fw-900 text-muted small text-uppercase mb-2 ls-1">Jam Pulang</label>
                            <input type="time" class="form-control border-0 fw-900 font-mono text-dark px-3"
                                name="jam_pulang" id="edit_jam_pulang" style="border-radius: 12px; height: 50px;">
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="fw-900 text-muted small text-uppercase mb-2 ls-1">Keterangan / Catatan</label>
                        <textarea class="form-control border-0 fw-800 px-3 py-3" name="keterangan" id="edit_keterangan"
                            rows="3" placeholder="Alasan koreksi data..." style="border-radius: 12px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-white">
                    <button type="button" class="btn btn-slate-100 px-4 fw-900 rounded-pill"
                        data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-primary-tgm px-5 fw-900 rounded-pill shadow-sm ls-1">SIMPAN
                        PERUBAHAN</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    document.querySelectorAll('.edit-attendance').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const kId = this.getAttribute('data-karyawan-id');
            const tgl = this.getAttribute('data-tanggal');
            const status = this.getAttribute('data-status');
            const masuk = this.getAttribute('data-masuk');
            const pulang = this.getAttribute('data-pulang');
            const ket = this.getAttribute('data-ket');

            document.getElementById('edit_id').value = id;
            document.getElementById('edit_karyawan_id').value = kId;
            document.getElementById('edit_tanggal').value = tgl;
            document.getElementById('display_tanggal').value = tgl;
            document.getElementById('edit_status').value = status || 'Hadir';
            document.getElementById('edit_jam_masuk').value = (masuk && masuk !== '00:00') ? masuk : '';
            document.getElementById('edit_jam_pulang').value = (pulang && pulang !== '00:00') ? pulang : '';
            document.getElementById('edit_keterangan').value = ket || '';

            new bootstrap.Modal(document.getElementById('modalEditAbsensi')).show();
        });
    });

    // KELOLA LEMBUR (MODAL AJAX)
    $(document).on('click', '.btn-kelola-lembur', function () {
        const karyawan_id = $(this).data('karyawan-id');
        const tanggal = $(this).data('tanggal');
        const nama = $(this).data('nama');

        $('#kelolaInfoTitle').text(nama + ' - ' + tanggal);
        const tbody = $('#tableKelolaLembur tbody');
        tbody.html('<tr><td colspan="6" class="text-center py-4"><i class="bi bi-hourglass-split fa-spin fs-4 text-primary-tgm"></i></td></tr>');

        $('#modalKelolaLembur').modal('show');

        $.ajax({
            url: '<?= base_url('karyawan/get_lembur_detail_ajax') ?>',
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
                            ? `<a href="<?= base_url('assets/images/lembur/') ?>${item.foto_kegiatan}" target="_blank">
                                 <img src="<?= base_url('assets/images/lembur/') ?>${item.foto_kegiatan}" style="width:32px; height:32px; object-fit:cover; border-radius:4px;">
                               </a>`
                            : '<i class="bi bi-camera-off text-muted opacity-25"></i>';

                        tbody.append(`
                                <tr id="row_lembur_${item.id}">
                                    <td class="align-middle small fw-bold">${waktuText}</td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center gap-1">
                                            <input type="time" class="form-control form-control-sm border-0 bg-white shadow-sm fw-bold time-update input-mulai" data-id="${item.id}" value="${item.jam_mulai.substring(0, 5)}" style="width:85px;">
                                            <span>-</span>
                                            <input type="time" class="form-control form-control-sm border-0 bg-white shadow-sm fw-bold time-update input-selesai" data-id="${item.id}" value="${item.jam_selesai.substring(0, 5)}" style="width:85px;">
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="small text-muted text-truncate" style="max-width:120px;" title="${item.kegiatan || '-'}">${item.kegiatan || '-'}</div>
                                    </td>
                                    <td class="align-middle text-center">${fotoHtml}</td>
                                    <td class="align-middle text-center">${statusBadge}</td>
                                    <td class="align-middle text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button class="btn btn-sm btn-white text-success border btn-individual-status" data-id="${item.id}" data-status="disetujui" title="Setujui"><i class="bi bi-check-lg"></i></button>
                                            <button class="btn btn-sm btn-white text-danger border btn-individual-status" data-id="${item.id}" data-status="ditolak" title="Tolak"><i class="bi bi-x-lg"></i></button>
                                            <button class="btn btn-sm btn-light text-primary border btn-save-time" data-id="${item.id}" title="Simpan Waktu"><i class="bi bi-save"></i></button>
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
            url: '<?= base_url('karyawan/update_status_ajax') ?>',
            type: 'POST',
            data: { id: id, status: status },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'Status Diperbarui', toast: true, position: 'top-end', timer: 1500, showConfirmButton: false });
                    // Refresh data in modal
                    $('.btn-kelola-lembur:focus').click();
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

        $.ajax({
            url: '<?= base_url('karyawan/update_lembur_time_ajax') ?>',
            type: 'POST',
            data: { id: id, jam_mulai: jam_mulai, jam_selesai: jam_selesai },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'Waktu Tersimpan', toast: true, position: 'top-end', timer: 1500, showConfirmButton: false });
                }
            }
        });
    });

    $(document).on('click', '.btn-view-all-photos', function () {
        const photos = $(this).data('photos');
        const date = $(this).data('date');
        const nama = $(this).data('nama');

        $('#photoModalTitle').text(nama + ' - ' + date);
        const container = $('#photoContainer');
        container.empty();

        photos.forEach(p => {
            container.append(`
                <div class="col-md-6 mb-3">
                    <div class="clean-card overflow-hidden h-100 shadow-sm border-0">
                        <div class="bg-primary-tgm bg-opacity-10 px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                            <span class="fw-900 text-primary-tgm small ls-1">${p.label}</span>
                            <span class="badge bg-white text-primary-tgm font-mono small">${p.info}</span>
                        </div>
                        <div class="p-0 text-center bg-dark" style="height: 300px;">
                            <img src="${p.url}" class="w-100 h-100" style="object-fit: contain; cursor: pointer;" onclick="window.open(this.src)">
                        </div>
                    </div>
                </div>
            `);
        });

        $('#modalVerifikasiFoto').modal('show');
    });

    function syncToPayroll() {
        const btn = $('#btnSyncPayroll');
        const originalHtml = btn.html();

        Swal.fire({
            title: 'Konfirmasi Sinkronisasi',
            text: "Data kehadiran dan lembur periode ini akan dikunci dan dikirim ke sistem payroll. Lanjutkan?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'YA, SINKRONKAN',
            cancelButtonText: 'BATAL'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.html('<i class="bi bi-arrow-repeat fa-spin"></i> MEMPROSES...').prop('disabled', true);

                $.ajax({
                    url: '<?= base_url('absensi/sync_to_payroll') ?>',
                    type: 'POST',
                    data: {
                        karyawan_id: '<?= $karyawan->karyawan_id ?>',
                        start_date: '<?= $start_date ?>',
                        end_date: '<?= $end_date ?>',
                        bulan: '<?= $bulan ?>',
                        total_pagi: '<?= $total_pagi ?>',
                        total_wajib: '<?= $total_wajib ?>',
                        total_kerjaan: '<?= $total_kerjaan ?>',
                        total_lembur: '<?= $total_all ?>',
                        biaya_makan: '<?= isset($biaya_makan_lembur_total) ? $biaya_makan_lembur_total : 0 ?>',
                        total_hadir: '<?= $stats['hadir'] ?? 0 ?>',
                        total_izin: '<?= $stats['izin'] ?? 0 ?>',
                        total_sakit: '<?= $stats['sakit'] ?? 0 ?>',
                        total_alpha: '<?= $stats['alpha'] ?? 0 ?>',
                        total_terlambat: '<?= $stats['terlambat'] ?? 0 ?>'
                    },
                    dataType: 'json',
                    success: function (res) {
                        btn.html(originalHtml).prop('disabled', false);
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Data berhasil disinkronkan ke sistem payroll.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('Gagal', res.message || 'Terjadi kesalahan.', 'error');
                        }
                    },
                    error: function () {
                        btn.html(originalHtml).prop('disabled', false);
                        Swal.fire('Error', 'Gagal menghubungi server.', 'error');
                    }
                });
            }
        });
    }
</script>

<!-- Modal Kelola Lembur -->
<div class="modal fade" id="modalKelolaLembur" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-primary-tgm text-white p-4">
                <h5 class="modal-title fw-900 ls-n1"><i class="bi bi-gear-fill me-2"></i>MANAJEMEN PERSETUJUAN LEMBUR
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="bg-white px-4 py-2 border-bottom">
                <span class="small fw-900 text-primary-tgm ls-1" id="kelolaInfoTitle">-</span>
            </div>
            <div class="modal-body p-0 bg-light">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="tableKelolaLembur">
                        <thead class="bg-white">
                            <tr>
                                <th class="text-secondary small fw-bold">SESI</th>
                                <th class="text-secondary small fw-bold">JAM LEMBUR</th>
                                <th class="text-secondary small fw-bold">KEGIATAN</th>
                                <th class="text-secondary small fw-bold text-center">FOTO</th>
                                <th class="text-secondary small fw-bold text-center">STATUS</th>
                                <th class="text-secondary small fw-bold text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-white">
                <button type="button" class="btn btn-slate-100 rounded-pill px-4 fw-900 small"
                    data-bs-dismiss="modal">TUTUP</button>
                <button type="button" onclick="location.reload()"
                    class="btn btn-primary-tgm rounded-pill px-4 fw-900 small shadow-sm">SELESAI & REFRESH DATA</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Verifikasi Foto -->
<div class="modal fade" id="modalVerifikasiFoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-primary-tgm text-white p-4">
                <h5 class="modal-title fw-900 ls-n1"><i class="bi bi-camera-fill me-2"></i>VERIFIKASI FOTO KEGIATAN</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="bg-white px-4 py-2 border-bottom">
                <span class="small fw-900 text-primary-tgm ls-1" id="photoModalTitle">-</span>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="row" id="photoContainer" style="max-height: 70vh; overflow-y: auto;">
                    <!-- Photos will be loaded here -->
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-white">
                <button type="button" class="btn btn-primary-tgm rounded-pill px-5 fw-900 small"
                    data-bs-dismiss="modal">SELESAI VERIFIKASI</button>
            </div>
        </div>
    </div>
</div>