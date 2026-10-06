<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">

<style>
    :root {
        --tgm-primary: #2563eb;
        --tgm-primary-soft: #eff6ff;
        --slate-50: #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --slate-300: #cbd5e1;
        --slate-400: #94a3b8;
        --slate-500: #64748b;
        --slate-600: #475569;
        --slate-700: #334155;
        --slate-800: #1e293b;
        --slate-900: #0f172a;
        --emerald: #10b981;
        --rose: #f43f5e;
        --amber: #f59e0b;
        --sky: #0ea5e9;
    }

    .app-content { background-color: #f8fafc; padding: 2rem; min-height: 100vh; }
    
    /* Typography & Utilities */
    .fw-800 { font-weight: 800; }
    .fw-900 { font-weight: 900; }
    .ls-n1 { letter-spacing: -1px; }
    .ls-1 { letter-spacing: 1px; }
    .font-mono {
        font-family: 'JetBrains Mono', monospace !important;
        font-size: 0.75rem;
    }
    .text-primary-tgm { color: var(--tgm-primary); }
    .text-emerald { color: var(--emerald) !important; }
    .text-rose { color: var(--rose) !important; }
    .text-amber { color: var(--amber) !important; }
    .text-info { color: var(--sky) !important; }
    .bg-primary-tgm { background: var(--tgm-primary); }
    .btn-primary-tgm { background: var(--tgm-primary); color: white; border: none; font-weight: 800; border-radius: 12px; padding: 10px 24px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .btn-primary-tgm:hover { background: #1d4ed8; color: white; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3); }
    .btn-white { background: white; color: var(--slate-700); border: 1px solid var(--slate-200); font-weight: 800; border-radius: 12px; padding: 10px 24px; transition: all 0.3s; }
    .btn-white:hover { background: var(--slate-50); color: var(--tgm-primary); border-color: var(--tgm-primary); transform: translateY(-1px); }

    /* Clean Card System */
    .clean-card { background: white; border: 1px solid var(--slate-200); border-radius: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: all 0.3s; }
    .clean-card:hover { border-color: var(--tgm-primary); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }

    /* Stat Box Grid */
    .stats-minimal { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
    .stat-box { padding: 1.5rem; border-radius: 20px; border: 1px solid var(--slate-200); background: white; transition: all 0.3s; position: relative; overflow: hidden; }
    .stat-box::after { content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 4px; background: var(--slate-200); transition: all 0.3s; }
    .stat-box:hover { transform: translateY(-5px); border-color: var(--tgm-primary); }
    .stat-box:hover::after { background: var(--tgm-primary); }
    .stat-box.success:hover::after { background: var(--emerald); }
    .stat-box.warning:hover::after { background: var(--amber); }
    .stat-box.info:hover::after { background: var(--sky); }
    .stat-box.danger:hover::after { background: var(--rose); }

    /* Table System */
    .table-simple { width: 100%; border-collapse: separate; border-spacing: 0; }
    .table-simple thead th { background: var(--slate-50); color: var(--slate-500); font-weight: 800; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; padding: 1.2rem; border-bottom: 2px solid var(--slate-100); }
    .table-simple tbody td { padding: 1.2rem; border-bottom: 1px solid var(--slate-100); color: var(--slate-700); vertical-align: middle; }
    .table-simple tbody tr:hover td { background-color: var(--slate-50); }

    /* Soft Badges */
    .badge-soft { font-weight: 900; font-size: 0.65rem; padding: 6px 14px; border-radius: 8px; letter-spacing: 0.5px; text-transform: uppercase; }
    .bg-emerald { background-color: #d1fae5; color: #065f46; }
    .bg-rose { background-color: #fee2e2; color: #991b1b; }

    .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 8px; }

    /* Collapsible Alert */
    .late-alert { border-radius: 20px; border: none; background: #fffbeb; border-left: 6px solid var(--amber); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }

    @media print {
        .no-print { display: none !important; }
        .app-content { padding: 0; background: white; }
        .clean-card { border: none; box-shadow: none; }
    }
</style>
<?php $this->load->view('layout_admin/header_modul'); ?>
<!-- <div class="container"> -->
    <!-- Header -->
    

    <!-- Premium Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-5 no-print">
        <div class="d-flex align-items-center gap-4">
            <a href="<?= base_url('hr') ?>" class="btn-white shadow-sm d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; padding: 0; border-radius: 16px;" title="Kembali ke Dashboard">
                <i class="bi bi-arrow-left fs-4" style="margin-left: -2px;"></i>
            </a>
            <div>
                <div class="small text-primary-tgm fw-900 ls-1 text-uppercase mb-1">Live Monitoring</div>
                <h1 class="fw-900 mb-0 ls-n1 text-dark">Kehadiran <span class="text-primary-tgm">Karyawan</span></h1>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn-white shadow-sm" data-bs-toggle="modal" data-bs-target="#modalGuideAbsensi">
                <i class="bi bi-journal-text me-2"></i> PANDUAN FITUR
            </button>
            <div class="dropdown">
                <button class="btn-primary-tgm shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell me-2"></i> BROADCAST REMINDER
                </button>
                <ul class="dropdown-menu shadow">
                    <li><a class="dropdown-item" href="<?= base_url('cron_notif/absen_masuk') ?>" target="_blank"><i class="bi bi-box-arrow-in-right me-2 text-primary-tgm"></i> Reminder Absen Masuk</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('cron_notif/absen_pulang') ?>" target="_blank"><i class="bi bi-box-arrow-right me-2 text-rose"></i> Reminder Absen Pulang</a></li>
                </ul>
            </div>
            <a href="<?= base_url('absensi/input_manual') ?>" class="btn-primary-tgm shadow-sm">
                <i class="bi bi-keyboard me-2"></i> INPUT MANUAL
            </a>
        </div>
    </div>

    <!-- Statistics Overview Grid -->
    <div class="stats-minimal no-print">
        <div class="stat-box">
            <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.6rem;">Total Karyawan</div>
            <div class="h2 fw-900 font-mono text-dark mb-0 mt-2"><?= number_format($stats['total']) ?></div>
            <div class="small fw-700 text-muted mt-1 opacity-50">TOTAL SDM</div>
        </div>
        <div class="stat-box success">
            <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.6rem;">Hadir</div>
            <div class="h2 fw-900 font-mono text-emerald mb-0 mt-2"><?= number_format($stats['hadir']) ?></div>
            <div class="small fw-700 text-emerald mt-1">PRESENSI MASUK</div>
        </div>
        <div class="stat-box warning">
            <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.6rem;">Izin / Sakit</div>
            <div class="h2 fw-900 font-mono text-warning mb-0 mt-2"><?= number_format($stats['izin'] + $stats['sakit']) ?></div>
            <div class="small fw-700 text-warning mt-1">DENGAN KETERANGAN</div>
        </div>
        <div class="stat-box info">
            <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.6rem;">Cuti</div>
            <div class="h2 fw-900 font-mono text-info mb-0 mt-2"><?= number_format($stats['cuti']) ?></div>
            <div class="small fw-700 text-info mt-1">IZIN CUTI</div>
        </div>
        <div class="stat-box danger">
            <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.6rem;">Alpha</div>
            <div class="h2 fw-900 font-mono text-rose mb-0 mt-2"><?= number_format($stats['alpha']) ?></div>
            <div class="small fw-700 text-rose mt-1">TANPA KETERANGAN</div>
        </div>
        <div class="stat-box <?= $stats['terlambat'] > 0 ? 'danger' : '' ?> cursor-pointer" data-bs-toggle="collapse" data-bs-target="#collapseLate">
            <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.6rem;">Terlambat</div>
            <div class="h2 fw-900 font-mono <?= $stats['terlambat'] > 0 ? 'text-rose' : 'text-slate-400' ?> mb-0 mt-2"><?= number_format($stats['terlambat']) ?></div>
            <div class="small fw-700 <?= $stats['terlambat'] > 0 ? 'text-rose' : 'text-slate-400' ?> mt-1">TOTAL TERLAMBAT</div>
        </div>
    </div>

    <!-- Late Employees Alert -->
    <?php if (!empty($late_employees_today)): ?>
            <div class="late-alert mb-4 p-4 animate__animated animate__fadeInDown show">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary-tgm text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                            style="width: 48px; height: 48px;">
                            <i class="bi bi-clock-history fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-900 mb-0 text-dark ls-n1">Karyawan Terlambat Hari Ini</h5>
                            <div class="small fw-700 text-muted ls-1">TERDETEKSI <b class="text-rose"><?= count($late_employees_today) ?> KARYAWAN</b> MELEBIHI TOLERANSI</div>
                        </div>
                    </div>
                    <button class="btn btn-primary-tgm btn-sm px-4 rounded-pill shadow-sm" type="button"
                        data-bs-toggle="collapse" data-bs-target="#collapseLate">
                        <i class="bi bi-list-stars me-2"></i> LIHAT DETAIL
                    </button>
                </div>
                <div class="collapse mt-4" id="collapseLate">
                    <div class="row g-3">
                        <?php foreach ($late_employees_today as $le): ?>
                                <div class="col-6 col-md-3">
                                    <div class="bg-white p-3 rounded-4 border border-warning border-opacity-25 d-flex align-items-center gap-3 shadow-sm">
                                        <img src="<?= $le->foto ? base_url('assets/images/karyawan/' . $le->foto) : base_url('assets/images/user.png') ?>"
                                            class="rounded-pill border shadow-sm" style="width: 40px; height: 40px; object-fit: cover;" loading="lazy">
                                        <div class="overflow-hidden">
                                            <div class="fw-900 small text-dark text-truncate" title="<?= $le->nama_lengkap ?>">
                                                <?= strtoupper($le->nama_lengkap) ?>
                                            </div>
                                            <div class="text-rose font-mono fw-900" style="font-size: 0.75rem;">
                                                <i class="bi bi-clock me-1"></i> <?= date('H:i', strtotime($le->jam_masuk)) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
    <?php endif; ?>

    <!-- Master Filter & Table workspace -->
    <div class="clean-card shadow-sm border-0 overflow-hidden">
        <div class="p-0">
            <!-- Filter Workspace -->
            <div class="p-4 bg-slate-100 border-bottom">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="fw-900 mb-0 text-dark ls-1"><i class="bi bi-funnel-fill me-2 text-primary-tgm"></i>WORKSPACE FILTER ABSENSI</h6>
                    <?php if ($tanggal != date('Y-m-d') || !empty($status) || !empty($karyawan_id)): ?>
                            <a href="<?= base_url('absensi') ?>"
                                class="btn btn-white btn-sm px-3 rounded-pill">
                                <i class="bi bi-x-circle me-1 text-rose"></i> RESET FILTER
                            </a>
                    <?php endif; ?>
                </div>
                <form method="GET" action="<?= base_url('absensi') ?>" class="row g-3">
                    <div class="col-md-3">
                        <label class="fw-900 small text-muted text-uppercase mb-2 ls-1">Periode Tanggal</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-0" style="border-radius: 12px 0 0 12px;"><i class="bi bi-calendar3"></i></span>
                            <input type="date" class="form-control border-0 fw-900" name="tanggal" value="<?= $tanggal ?>" style="border-radius: 0 12px 12px 0;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="fw-900 small text-muted text-uppercase mb-2 ls-1">Status Kehadiran</label>
                        <select class="form-select select2-status border-0 fw-900" name="status">
                            <option value="">-- SEMUA STATUS --</option>
                            <optgroup label="KEHADIRAN">
                                <option value="Hadir" <?= $status == 'Hadir' ? 'selected' : '' ?>>HADIR (ON-TIME)</option>
                                <option value="Terlambat" <?= $status == 'Terlambat' ? 'selected' : '' ?>>TERLAMBAT</option>
                                <option value="Alpha" <?= $status == 'Alpha' ? 'selected' : '' ?>>ALPHA (TANPA KET)</option>
                            </optgroup>
                            <optgroup label="IZIN & SAKIT">
                                <option value="Izin" <?= $status == 'Izin' ? 'selected' : '' ?>>IZIN</option>
                                <option value="Sakit" <?= $status == 'Sakit' ? 'selected' : '' ?>>SAKIT</option>
                            </optgroup>
                            <optgroup label="CUTI">
                                <option value="Cuti" <?= $status == 'Cuti' ? 'selected' : '' ?>>CUTI TAHUNAN</option>
                                <option value="Cuti Sakit" <?= $status == 'Cuti Sakit' ? 'selected' : '' ?>>CUTI SAKIT</option>
                                <option value="Cuti Keluarga" <?= $status == 'Cuti Keluarga' ? 'selected' : '' ?>>CUTI KELUARGA</option>
                                <option value="Cuti Melahirkan" <?= $status == 'Cuti Melahirkan' ? 'selected' : '' ?>>CUTI MELAHIRKAN</option>
                                <option value="Cuti Menikah" <?= $status == 'Cuti Menikah' ? 'selected' : '' ?>>CUTI MENIKAH</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="fw-900 small text-muted text-uppercase mb-2 ls-1">Cari Karyawan</label>
                        <select class="form-select select2-karyawan border-0" name="karyawan_id">
                            <option value="">-- SEMUA KARYAWAN --</option>
                            <?php foreach ($karyawan_list as $karyawan): ?>
                                    <option value="<?= $karyawan->karyawan_id ?>" <?= (string)$karyawan_id === (string)$karyawan->karyawan_id ? 'selected' : '' ?>>
                                        <?= strtoupper($karyawan->nama_lengkap) ?> (<?= $karyawan->nik ?>)
                                    </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary-tgm w-100 py-2 shadow-sm">
                            <i class="bi bi-search me-2"></i> TAMPILKAN DATA
                        </button>
                    </div>
                </form>
            </div>

            <!-- Dashboard Actions -->
            <div class="p-4 bg-white border-bottom no-print">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="<?= base_url('absensi/export_excel?tanggal=' . $tanggal) ?>"
                        class="btn btn-white btn-sm px-3 rounded-pill border">
                        <i class="bi bi-file-earmark-excel me-2 text-emerald"></i> EXCEL
                    </a>
                    <a href="<?= base_url('absensi/export_pdf?tanggal=' . $tanggal) ?>"
                        class="btn btn-white btn-sm px-3 rounded-pill border">
                        <i class="bi bi-file-earmark-pdf me-2 text-rose"></i> PDF
                    </a>

                    <button type="button" class="btn-white btn-sm px-4 rounded-pill border" onclick="openCollageModal()">
                        <i class="bi bi-images me-2 text-primary-tgm"></i> KOLASE FOTO
                    </button>
                    <button type="button" class="btn-white btn-sm px-3 rounded-pill border" onclick="location.reload()">
                        <i class="bi bi-arrow-clockwise me-2"></i> REFRESH
                    </button>

                    <a href="<?= base_url('absensi/rekap_absen_bulanan') ?>" class="btn-white btn-sm px-4 rounded-pill border ms-auto">
                        <i class="bi bi-bar-chart-line me-2 text-primary-tgm"></i> LAPORAN BULANAN
                    </a>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table-simple">
                    <thead>
                        <tr>
                            <th width="50" class="text-center">#</th>
                            <th>DATA KARYAWAN</th>
                            <th>JAM MASUK</th>
                            <th>JAM PULANG</th>
                            <th class="text-center">STATUS</th>
                            <!-- <th class="text-center">LEMBUR (H)</th> -->
                            <th class="text-center">VERIFIKASI MAPS</th>
                            <th>CATATAN</th>
                            <th>Device / Agent</th>
                            <th class="text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($absensi)): ?>
                                <?php $no = 1;
                                foreach ($absensi as $absen): ?>
                                        <tr>
                                            <td class="text-center font-mono small text-muted"><?= $no++ ?></td>
                                            <td>
                                                <div class="h6 fw-900 mb-0 ls-n1"><?= strtoupper($absen->nama_lengkap) ?></div>
                                                <div class="small fw-700 text-muted ls-1" style="font-size: 0.65rem;">NIK: <?= $absen->nik ?></div>
                                                <?php if (!empty($absen->nama_perusahaan)): ?>
                                                        <div class="badge-soft bg-slate-100 text-slate-500 border border-slate-200 mt-1 d-inline-block" style="font-size: 0.6rem;">
                                                            <i class="bi bi-building me-1"></i> <?= $absen->nama_perusahaan ?>
                                                        </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <?php
                                                    $is_special_status = empty($absen->status) || in_array($absen->status, ['Izin', 'Sakit', 'Alpha']) || (strpos($absen->status, 'Cuti') !== false);
                                                    if (!$is_special_status && $absen->foto_masuk): ?>
                                                            <div class="position-relative">
                                                                <img src="<?= base_url($absen->foto_masuk) ?>"
                                                                    class="rounded-pill border shadow-sm cursor-pointer"
                                                                    style="width: 38px; height: 38px; object-fit: cover;" loading="lazy"
                                                                    title="Klik untuk perbesar"
                                                                    onclick="previewSelfie('<?= base_url($absen->foto_masuk) ?>', '<?= $absen->nama_lengkap ?> - MASUK')">
                                                                <div class="position-absolute bottom-0 end-0 bg-emerald rounded-circle border border-white" style="width: 10px; height: 10px;"></div>
                                                            </div>
                                                    <?php endif; ?>
                                                    <div class="fw-900 font-mono">
                                                        <?php if (!$is_special_status && $absen->jam_masuk): ?>
                                                                <?php $is_late = strtotime($absen->jam_masuk) > strtotime('08:00:59'); ?>
                                                                <span class="<?= $is_late ? 'text-danger fw-bolder' : 'text-emerald' ?>" <?= $is_late ? 'style="color: #ff0000 !important;"' : '' ?>>
                                                                    <?= date('H:i', strtotime($absen->jam_masuk)) ?>
                                                                    <?php if ($is_late): ?>
                                                                        <div style="font-size: 0.65rem; letter-spacing: 0.5px; color: #ff0000 !important; margin-top: 3px;" class="fw-900">(TERLAMBAT)</div>
                                                                    <?php endif; ?>
                                                                </span>
                                                        <?php else: ?>
                                                                <span class="text-slate-300">--:--</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <?php if (!$is_special_status && $absen->foto_pulang): ?>
                                                            <div class="position-relative">
                                                                <img src="<?= base_url($absen->foto_pulang) ?>"
                                                                    class="rounded-pill border shadow-sm cursor-pointer"
                                                                    style="width: 38px; height: 38px; object-fit: cover;" loading="lazy"
                                                                    title="Klik untuk perbesar"
                                                                    onclick="previewSelfie('<?= base_url($absen->foto_pulang) ?>', '<?= $absen->nama_lengkap ?> - PULANG')">
                                                                <div class="position-absolute bottom-0 end-0 bg-primary-tgm rounded-circle border border-white" style="width: 10px; height: 10px;"></div>
                                                            </div>
                                                    <?php endif; ?>
                                                    <div class="fw-900 font-mono text-dark">
                                                        <?= (!$is_special_status && $absen->jam_pulang) ? date('H:i', strtotime($absen->jam_pulang)) : '<span class="text-slate-300">--:--</span>' ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                $displayStatus = $absen->status;
                                                $badgeClass = 'bg-slate-100 text-slate-500';
                                                
                                                // If status is empty, it's an implicit Alpha BUT check for Cuti/Izin notes first
                                                if (empty($displayStatus)) {
                                                    if (!empty($absen->keterangan) && strpos(strtolower($absen->keterangan), 'cuti') !== false) {
                                                        $displayStatus = 'Cuti'; 
                                                    } elseif (!empty($absen->keterangan) && strpos(strtolower($absen->keterangan), 'izin') !== false) {
                                                        $displayStatus = 'Izin';
                                                    } elseif (!empty($absen->keterangan) && strpos(strtolower($absen->keterangan), 'sakit') !== false) {
                                                        $displayStatus = 'Sakit';
                                                    } else {
                                                        $displayStatus = 'Alpha';
                                                    }
                                                }

                                                if ($displayStatus == 'Hadir') $badgeClass = 'bg-soft-success bg-emerald';
                                                if ($displayStatus == 'Izin') $badgeClass = 'bg-warning text-dark';
                                                if ($displayStatus == 'Sakit' || $displayStatus == 'Cuti Sakit') $badgeClass = 'bg-info text-dark';
                                                if ($displayStatus == 'Alpha') $badgeClass = 'bg-rose text-white';
                                                if (strpos($displayStatus, 'Cuti') !== false && $displayStatus != 'Cuti Sakit') $badgeClass = 'bg-primary-tgm text-white';
                                                ?>
                                                <span class="badge-soft <?= $badgeClass ?> d-inline-block">
                                                    <?= $displayStatus ?>
                                                </span>
                                            </td>
                                            <!-- <td class="text-center">
                                                <?php
                                                $lembur_hours = 0;
                                                if (!$is_special_status && $absen->jam_pulang) {
                                                    $day_of_week = date('N', strtotime($absen->tanggal)); 
                                                    if ($day_of_week >= 1 && $day_of_week <= 5) {
                                                        $pulang_time = strtotime($absen->jam_pulang);
                                                        $cutoff_time = strtotime('16:00:00');
                                                        if ($pulang_time > $cutoff_time) {
                                                            $diff_seconds = $pulang_time - $cutoff_time;
                                                            $lembur_hours = round($diff_seconds / 3600, 1);
                                                        }
                                                    }
                                                }
                                                echo $lembur_hours > 0 ? '<span class="fw-900 font-mono text-primary-tgm">+'.$lembur_hours.'</span>' : '<span class="text-slate-300 font-mono">0.0</span>';
                                                ?>
                                            </td> -->
                                            <td class="text-center">
                                                <?php if (!empty($absen->lokasi_map_url)): ?>
                                                        <a href="<?= $absen->lokasi_map_url ?>" target="_blank"
                                                            class="btn btn-white btn-sm py-1 px-3 mb-1" style="font-size: 0.65rem; border-radius: 8px;">
                                                            <i class="bi bi-geo-alt-fill me-1 text-primary-tgm"></i> VIEW MAPS
                                                        </a>
                                                        <br>
                                                        <?php
                                                        $distance = $absen->jarak_meter;
                                                        $limit = !empty($absen->radius_meter) ? $absen->radius_meter : (isset($radius_kantor) ? $radius_kantor : 100);
                                                        $color = ($distance > $limit) ? 'text-rose fw-900 border-rose' : 'text-emerald fw-900';
                                                        ?>
                                                        <div class="small fw-800 font-mono <?= $color ?>" style="font-size: 0.7rem;">
                                                            <?= number_format($distance, 0) ?> M <span class="opacity-50 mx-1">/</span> MAX <?= $limit ?>M
                                                        </div>
                                                <?php else: ?>
                                                        <span class="text-slate-300 font-mono small">NO SIGNAL</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="small fw-800 text-muted ls-1">
                                                    <?php
                                                    if (!$is_special_status && $absen->jam_masuk && strtotime($absen->jam_masuk) > strtotime('08:00:59')) {
                                                        echo '<span class="badge mb-1" style="font-size:0.6rem; background-color: #ff0000 !important; color: white;">LATE ALERT</span><br>';
                                                    }
                                                    echo $absen->keterangan ? $absen->keterangan : '<span class="opacity-25 italic">Tanpa catatan</span>';
                                                    ?>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="small fw-700 text-dark mb-1 d-flex align-items-center justify-content-center gap-1">
                                                    <?php if(!empty($absen->device_type)): ?>
                                                        <i class="bi <?= (strpos(strtolower($absen->device_type), 'android') !== false) || (strpos(strtolower($absen->device_type), 'ios') !== false) || (strpos(strtolower($absen->device_type), 'mobile') !== false) ? 'bi-phone' : 'bi-laptop' ?> text-primary-tgm" style="font-size: 0.8rem;"></i>
                                                        <?= $absen->device_type ?>
                                                    <?php else: ?>
                                                        <span class="text-muted italic small opacity-50">Unknown</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="font-mono text-muted overflow-hidden text-truncate mx-auto" 
                                                     style="font-size: 0.6rem; max-width: 140px;" 
                                                     title="<?= htmlspecialchars($absen->user_agent) ?>">
                                                    <?= $absen->user_agent ?: '-' ?>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <!-- <?php if ($this->data['is_developer'] || $this->session->userdata('is_impersonating') || in_array($this->session->userdata('level'), [1, 17, 21])): ?>
                                                            <?php if (!empty($absen->user_id)): ?>
                                                                    <a href="<?= base_url('pengaturan/force_login/' . $absen->user_id) ?>"
                                                                        class="btn btn-white btn-sm border-0" title="Login Sebagai User Ini"
                                                                        onclick="return confirm('Pindah sesi sebagai <?= $absen->nama_lengkap ?>?')">
                                                                        <i class="bi bi-incognito text-primary-tgm"></i>
                                                                    </a>
                                                            <?php endif; ?>
                                                    <?php endif; ?> -->

                                                    <button class="btn btn-white btn-sm border-0"
                                                        data-item='<?= htmlspecialchars(json_encode($absen), ENT_QUOTES, 'UTF-8') ?>'
                                                        onclick="openEditModal(JSON.parse(this.getAttribute('data-item')))"
                                                        title="Edit">
                                                        <i class="bi bi-pencil-square text-warning"></i>
                                                    </button>
                                                    <a href="<?= base_url('absensi/delete/' . $absen->id) ?>"
                                                        class="btn btn-white btn-sm border-0" title="Hapus"
                                                        onclick="return confirm('Hapus data absensi ini?')">
                                                        <i class="bi bi-trash3 text-rose"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                <?php endforeach; ?>
                        <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="py-5">
                                            <i class="bi bi-calendar-x fs-1 text-slate-300 mb-3 d-block"></i>
                                            <h6 class="fw-900 text-slate-400 ls-1">TIDAK ADA DATA ABSENSI</h6>
                                            <div class="small text-slate-300 fw-700">Silahkan pilih tanggal atau filter lain.</div>
                                        </div>
                                    </td>
                                </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
</div>
<!-- Edit Absensi Modal -->
<div class="modal fade" id="editAbsensiModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?= base_url('absensi/update') ?>" method="post" class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-primary-tgm text-white p-4">
                <h5 class="modal-title fw-900 ls-n1"><i class="bi bi-pencil-square me-2"></i>KOREKSI DATA ABSENSI</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-slate-50">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="karyawan_id" id="edit_karyawan_id">

                <div class="mb-3">
                    <label class="fw-900 text-muted small text-uppercase mb-2 ls-1">Nama Karyawan</label>
                    <input type="text" class="form-control bg-white border-0 fw-900 text-dark px-3" id="edit_nama_lengkap" readonly style="border-radius: 12px;">
                </div>

                <div class="mb-3">
                    <label class="fw-900 text-muted small text-uppercase mb-2 ls-1">Tanggal Record</label>
                    <input type="date" name="tanggal" id="edit_tanggal" class="form-control bg-white border-0 fw-900 text-dark px-3" required readonly style="border-radius: 12px;">
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="fw-900 text-muted small text-uppercase mb-2 ls-1">Jam Masuk</label>
                        <input type="time" name="jam_masuk" id="edit_jam_masuk" class="form-control border-0 fw-900 font-mono text-dark px-3" style="border-radius: 12px; height: 50px;">
                    </div>
                    <div class="col-md-6">
                        <label class="fw-900 text-muted small text-uppercase mb-2 ls-1">Jam Pulang</label>
                        <input type="time" name="jam_pulang" id="edit_jam_pulang" class="form-control border-0 fw-900 font-mono text-dark px-3" style="border-radius: 12px; height: 50px;">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="fw-900 text-muted small text-uppercase mb-2 ls-1">Status Absensi</label>
                    <select name="status" id="edit_status" class="form-select border-0 fw-900 px-3" required style="border-radius: 12px; height: 50px;">
                        <option value="Hadir">HADIR</option>
                        <option value="Izin">IZIN</option>
                        <option value="Sakit">SAKIT</option>
                        <option value="Cuti">CUTI TAHUNAN</option>
                        <option value="Cuti Menikah">CUTI MENIKAH</option>
                        <option value="Cuti Duka">CUTI DUKA</option>
                        <option value="Cuti Keluarga Sakit">CUTI KELUARGA SAKIT</option>
                        <option value="Cuti Melahirkan Suami">CUTI ISTRI MELAHIRKAN</option>
                        <option value="Cuti Melahirkan">CUTI MELAHIRKAN (90 HARI)</option>
                        <option value="Alpha">ALFA (TANPA KET)</option>
                    </select>
                </div>

                <div class="mb-0">
                    <label class="fw-900 text-muted small text-uppercase mb-2 ls-1">Keterangan Koreksi</label>
                    <textarea name="keterangan" id="edit_keterangan" class="form-control border-0 fw-800 px-3 py-3" rows="3" placeholder="Alasan perubahan data..." style="border-radius: 12px;"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-white">
                <button type="button" class="btn btn-slate-100 px-4 fw-900 rounded-pill" data-bs-dismiss="modal">BATAL</button>
                <button type="submit" class="btn btn-primary-tgm px-5 fw-900 rounded-pill shadow-sm ls-1">SIMPAN PERUBAHAN</button>
            </div>
        </form>
    </div>
</div>

<!-- Attendance Collage Modal -->
<div class="modal fade" id="collageModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-primary-tgm text-white border-0 p-4">
                <h5 class="modal-title fw-900 ls-n1"><i class="bi bi-images me-2"></i>KOLASE FOTO KEHADIRAN</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-slate-50">
                <div class="row g-4 justify-content-center">
                    <!-- Dinamis via JS -->
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-white">
                <button type="button" class="btn btn-slate-100 px-5 fw-900 rounded-pill shadow-sm" data-bs-dismiss="modal">TUTUP GALERI</button>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-backdrop {
        z-index: 1050 !important;
    }

    .modal {
        z-index: 1060 !important;
    }

    .cursor-pointer {
        cursor: pointer;
    }
</style>

<script>
    let myCollageModal = null;

    function openEditModal(data) {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_karyawan_id').value = data.karyawan_id;
        document.getElementById('edit_nama_lengkap').value = data.nama_lengkap;
        document.getElementById('edit_tanggal').value = data.tanggal;
        document.getElementById('edit_jam_masuk').value = data.jam_masuk ? data.jam_masuk.substring(0, 5) : '';
        document.getElementById('edit_jam_pulang').value = data.jam_pulang ? data.jam_pulang.substring(0, 5) : '';
        document.getElementById('edit_status').value = data.status;
        document.getElementById('edit_keterangan').value = data.keterangan || '';

        new bootstrap.Modal(document.getElementById('editAbsensiModal')).show();
    }

    function previewSelfie(url, title) {
        Swal.fire({
            title: title,
            imageUrl: url,
            imageWidth: 400,
            imageAlt: 'Selfie Attendance',
            showCloseButton: true,
            showConfirmButton: false,
            borderRadius: '20px'
        });
    }

    function openCollageModal() {
        const collageBody = document.querySelector('#collageModal .modal-body .row');
        if (!collageBody) return;
        collageBody.innerHTML = '';

        let photoCount = 0;
        const rows = document.querySelectorAll('.table-simple tbody tr');

        rows.forEach(row => {
            const namaEl = row.querySelector('td:nth-child(2) .fw-900');
            if (!namaEl) return;
            const nama = namaEl.innerText.trim();

            // Foto Masuk (Kolom 3)
            const imgIn = row.querySelector('td:nth-child(3) img');
            const jamIn = row.querySelector('td:nth-child(3) .fw-900')?.innerText?.trim() || '--:--';
            if (imgIn) {
                photoCount++;
                addPhotoItem(collageBody, imgIn.src, nama, jamIn, 'MASUK', 'bg-success');
            }

            // Foto Pulang (Kolom 4)
            const imgOut = row.querySelector('td:nth-child(4) img');
            const jamOut = row.querySelector('td:nth-child(4) .fw-900')?.innerText?.trim() || '--:--';
            if (imgOut) {
                photoCount++;
                addPhotoItem(collageBody, imgOut.src, nama, jamOut, 'PULANG', 'bg-warning text-dark');
            }
        });

        if (photoCount === 0) {
            collageBody.innerHTML = `
                <div class="col-12 py-5 text-center text-muted">
                    <i class="fa fa-camera-retro fa-3x mb-3 d-block opacity-25"></i>
                    <h5>Belum ada foto selfie yang tersedia</h5>
                    <p class="small">Data kolase mengikuti hasil filter pada tabel.</p>
                </div>`;
        }

        if (!myCollageModal) {
            myCollageModal = new bootstrap.Modal(document.getElementById('collageModal'));
        }
        myCollageModal.show();
    }

    function addPhotoItem(container, src, name, time, label, badgeClass) {
        const div = document.createElement('div');
        div.className = 'col-6 col-sm-4 col-md-3 col-lg-2 mb-4';
        div.innerHTML = `
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative" style="transition: all 0.3s; border-radius: 20px !important;">
                <div class="position-absolute top-0 end-0 m-2" style="z-index:2;">
                    <span class="badge-soft ${badgeClass} shadow-sm" style="font-size:0.6rem; padding: 4px 10px; border-radius: 8px;">${label}</span>
                </div>
                <div style="height: 180px; overflow: hidden; background: #000;">
                    <img src="${src}" class="w-100 h-100 cursor-pointer" 
                         style="object-fit: cover; transition: transform 0.5s;" loading="lazy"
                         onmouseover="this.style.transform='scale(1.15)'"
                         onmouseout="this.style.transform='scale(1)'"
                         onclick="previewSelfie('${src}', '${name} - ${label}')">
                </div>
                <div class="card-body p-3 text-center bg-white border-top border-slate-100">
                    <div class="fw-900 small text-truncate ls-n1" style="font-size:0.75rem;" title="${name}">${name.toUpperCase()}</div>
                    <div class="text-muted font-mono" style="font-size: 0.65rem;">${time}</div>
                </div>
            </div>
        `;
        container.appendChild(div);
    }

    $(document).ready(function () {
        if ($('.select2-karyawan').length) {
            $('.select2-karyawan').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }
        if ($('.select2-status').length) {
            $('.select2-status').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }
    });
</script>

<!-- Modal Panduan Absensi -->
<div class="modal fade" id="modalGuideAbsensi" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-slate-900 text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary-tgm text-white rounded-pill d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="bi bi-fingerprint fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-900 mb-0 ls-n1">PANDUAN <span class="text-primary-tgm">MONITORING ABSENSI</span></h5>
                        <p class="small mb-0 opacity-75 fw-800 ls-1">Sistem Geofencing & Audit Visual Kehadiran</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-3 bg-slate-100 border-end">
                        <div class="nav flex-column nav-pills p-3" id="v-pills-tab-abs" role="tablist">
                            <button class="nav-link active text-start py-3 px-4 fw-900 ls-1 mb-2 border-0" data-bs-toggle="pill" data-bs-target="#abs-stats" type="button">
                                <i class="bi bi-grid-fill me-2"></i> RINGKASAN
                            </button>
                            <button class="nav-link text-start py-3 px-4 fw-900 ls-1 mb-2 border-0" data-bs-toggle="pill" data-bs-target="#abs-visual" type="button">
                                <i class="bi bi-camera-fill me-2"></i> AUDIT VISUAL
                            </button>
                            <button class="nav-link text-start py-3 px-4 fw-900 ls-1 mb-2 border-0" data-bs-toggle="pill" data-bs-target="#abs-geo" type="button">
                                <i class="bi bi-geo-alt-fill me-2"></i> GEOFENCING
                            </button>
                            <button class="nav-link text-start py-3 px-4 fw-900 ls-1 mb-2 border-0" data-bs-toggle="pill" data-bs-target="#abs-logic" type="button">
                                <i class="bi bi-calculator-fill me-2"></i> ATURAN & POINT
                            </button>
                            <button class="nav-link text-start py-3 px-4 fw-900 ls-1 mb-2 border-0" data-bs-toggle="pill" data-bs-target="#abs-report" type="button">
                                <i class="bi bi-file-earmark-pdf-fill me-2"></i> PELAPORAN / PDF
                            </button>
                        </div>
                    </div>
                    <div class="col-md-9 border-start">
                        <div class="tab-content p-5" id="v-pills-tabContent-abs">
                            
                            <!-- Stats -->
                            <div class="tab-pane fade show active" id="abs-stats">
                                <h4 class="fw-900 mb-4 ls-n1 text-dark">MEMAHAMI DASHBOARD MONITORING</h4>
                                <div class="clean-card p-4 mb-4">
                                    <div class="d-flex gap-3 align-items-start mb-3">
                                        <i class="bi bi-info-circle-fill text-primary-tgm fs-3"></i>
                                        <div>
                                            <h6 class="fw-900 mb-1 ls-1">DYNAMIC REAL-TIME COUNTER</h6>
                                            <p class="small text-muted fw-800">Widget statistik di bagian atas memberikan gambaran cepat status harian. Box <b>TERLAMBAT</b> akan berpendar merah jika terdeteksi data masuk di luar batas toleransi.</p>
                                        </div>
                                    </div>
                                    <div class="p-3 bg-slate-50 rounded-3 border">
                                        <p class="small fw-800 text-muted mb-0"><i class="bi bi-lightbulb-fill text-warning me-2"></i>Klik box statistik untuk memicu filter cepat atau detail otomatis.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Visual -->
                            <div class="tab-pane fade" id="abs-visual">
                                <h4 class="fw-900 mb-4 ls-n1 text-dark">VALIDASI SELFIE (VISUAL AUDIT)</h4>
                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <div class="clean-card p-4 h-100">
                                            <i class="bi bi-zoom-in text-primary-tgm fs-2 mb-3 d-block"></i>
                                            <h6 class="fw-900">PREVIEW CEPAT</h6>
                                            <p class="small text-muted fw-800">Cukup klik pada foto bulat di tabel untuk melihat bukti selfie masuk/pulang dalam ukuran penuh.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="clean-card p-4 h-100 text-white bg-primary-tgm">
                                            <i class="bi bi-images fs-2 mb-3 d-block"></i>
                                            <h6 class="fw-900">KOLASE FOTO</h6>
                                            <p class="small opacity-75 fw-800">Gunakan fitur kolase untuk memantau integritas lingkungan kerja seluruh tim dalam satu layar galeri tunggal.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Geo -->
                            <div class="tab-pane fade" id="abs-geo">
                                <h4 class="fw-900 mb-4 ls-n1 text-dark">GEOFENCING & GPS SECURITY</h4>
                                <div class="p-4 rounded-4 border bg-white mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="badge-soft bg-emerald py-2 px-3 fw-900">SAFE RADIUS</span>
                                        <i class="bi bi-shield-check text-emerald fs-4"></i>
                                    </div>
                                    <p class="small text-muted fw-800">Sistem akan menandai jarak (meter) dari koordinat kantor. Jika indikator berwarna <b class="text-rose">MERAH</b>, berarti karyawan melakukan absen jauh dari lokasi kerja yang ditentukan.</p>
                                </div>
                                <div class="clean-card p-4">
                                    <h6 class="fw-900 mb-3"><i class="bi bi-geo-alt-fill me-2 text-rose"></i>INTEGRASI GOOGLE MAPS</h6>
                                    <p class="small text-muted fw-800 mb-0">Klik tombol <b>"VIEW MAPS"</b> pada baris tabel untuk memvalidasi posisi pastinya jika terjadi anomali lokasi.</p>
                                </div>
                            </div>

                            <!-- Logic -->
                            <div class="tab-pane fade" id="abs-logic">
                                <h4 class="fw-900 mb-4 ls-n1 text-dark">ATURAN & PERHITUNGAN OTOMATIS</h4>
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item px-0 py-3 bg-transparent">
                                        <h6 class="fw-900 text-dark mb-1">LEMBUR WAJIB</h6>
                                        <p class="small text-muted fw-800 mb-0">Dihitung otomatis jika jam pulang > 16:00 (Senin-Jumat). Tampilan <b>"+2.0"</b> menandakan penambahan jam lembur.</p>
                                    </div>
                                    <div class="list-group-item px-0 py-3 bg-transparent">
                                        <h6 class="fw-900 text-rose mb-1">LATE ALERT</h6>
                                        <p class="small text-muted fw-800 mb-0">Keterlambatan dihitung dari jam 08:00 WIB. Badge merah akan muncul otomatis di kolom catatan sebagai penanda evaluasi.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Reporting -->
                            <div class="tab-pane fade" id="abs-report">
                                <h4 class="fw-900 mb-4 ls-n1 text-dark">SISTEM PELAPORAN (E-REPORT)</h4>
                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <div class="bg-white p-4 rounded-4 border">
                                            <div class="badge-soft bg-emerald mb-3 d-inline-block">A4 OPTIMIZED</div>
                                            <h6 class="fw-900">LAYOUT KOMPAK</h6>
                                            <p class="small text-muted fw-800">Laporan PDF telah dioptimalkan agar muat dalam **1 Lembar A4** (kapasitas hingga 80 karyawan) dengan desain yang tetap elegan.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="bg-white p-4 rounded-4 border">
                                            <div class="badge-soft bg-primary-tgm text-white mb-3 d-inline-block">SMART FILTER</div>
                                            <h6 class="fw-900">ANTI-DUMMY DATA</h6>
                                            <p class="small text-muted fw-800">Sistem secara otomatis membuang akun terminal/dummy dari laporan agar angka statistik kehadiran 100% akurat.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="clean-card p-4">
                                    <h6 class="fw-900 mb-2"><i class="bi bi-check2-circle text-emerald me-2"></i>DILENGKAPI RINGKASAN & VALIDASI</h6>
                                    <ul class="small fw-800 text-muted ps-3 mb-0">
                                        <li class="mb-2">Header menyertakan ringkasan Persentase Efisiensi & Total Kehadiran.</li>
                                        <li class="mb-2">Penanda validasi GEOFENCING (√ OK) menyatu dalam tabel.</li>
                                        <li>Tersedia kolom tanda tangan manajemen untuk keperluan arsip fisik.</li>
                                    </ul>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-white">
                <button type="button" class="btn btn-primary-tgm w-100 py-3 rounded-pill fw-900 ls-1 shadow-sm" data-bs-dismiss="modal">MENGERTI & TUTUP</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Reuse style from dashboard if needed, or add specific for attendance guide */
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