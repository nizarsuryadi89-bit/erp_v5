<style>
    /* Ultra-Minimalist Monthly Recap Design */
    :root {
        --tgm-primary: #2563eb;
        --tgm-primary-dark: #1d4ed8;
        --tgm-rose: #e11d48;
        --tgm-emerald: #059669;
        --tgm-slate-50: #f8fafc;
        --tgm-slate-100: #f1f5f9;
        --tgm-slate-200: #e2e8f0;
        --tgm-slate-700: #334155;
        --tgm-slate-900: #0f172a;
    }

    .text-primary-tgm { color: var(--tgm-primary) !important; }
    .bg-primary-tgm { background-color: var(--tgm-primary) !important; }
    .btn-primary-tgm { background-color: var(--tgm-primary); color: white; border: none; }
    .btn-primary-tgm:hover { background-color: var(--tgm-primary-dark); color: white; }

    .clean-card {
        background: white;
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
    }

    .stats-minimal {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-box {
        padding: 1.25rem;
        border-top: 3px solid var(--tgm-slate-900);
        transition: transform 0.2s, box-shadow 0.2s;
        background: white;
    }

    .stat-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
    }

    .stat-box.primary { border-color: var(--tgm-primary); }
    .stat-box.success { border-color: var(--tgm-emerald); }
    .stat-box.warning { border-color: #f59e0b; }
    .stat-box.danger { border-color: var(--tgm-rose); }
    .stat-box.info { border-color: #06b6d4; }
    .stat-box.secondary { border-color: var(--tgm-slate-200); }

    .table-simple thead th {
        background: #fdfdfd;
        color: var(--tgm-slate-700);
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        padding: 15px 12px;
        border-top: none;
        border-bottom: 2px solid var(--tgm-slate-100);
    }

    .table-simple tbody td {
        padding: 12px 12px;
        vertical-align: middle;
        color: var(--tgm-slate-900);
        border-bottom: 1px solid var(--tgm-slate-100);
        font-size: 0.95rem;
    }

    .font-mono { font-family: 'JetBrains Mono', 'Fira Code', monospace !important; }
    .bg-slate-100 { background-color: #f1f5f9 !important; }

    .badge-soft {
        font-size: 0.7rem;
        padding: 5px 12px;
        font-weight: 800;
        border-radius: 5px;
        text-transform: uppercase;
    }

    @media print {
        @page { size: A4 landscape; margin: 1cm; }
        .no-print { display: none !important; }
        .clean-card { border: none !important; box-shadow: none !important; }
        .table-simple { width: 100% !important; font-size: 9pt !important; }
        .table-simple th, .table-simple td { border: 1px solid #ddd !important; padding: 6px !important; }
    }
</style>

<div class="app-content">
    <?php $this->load->view('layout_admin/header_modul'); ?>

    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-end mb-4 no-print">
            <div>
                <h3 class="fw-900 mb-0 ls-1">REKAPITULASI <span class="text-primary-tgm">ABSENSI BULANAN</span></h3>
                <p class="small text-muted mb-0">Analisis kinerja kehadiran, predikat, dan rincian harian staff.</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" data-bs-toggle="modal" data-bs-target="#modalGuideRekap" class="btn btn-white border rounded-pill px-4 fw-900 small shadow-sm text-dark">
                    <i class="bi bi-info-circle me-2 text-primary-tgm"></i> PANDUAN FITUR
                </button>
                <a href="<?= base_url('absensi') ?>" class="btn btn-white border rounded-pill px-4 fw-900 small shadow-sm text-dark">
                    <i class="bi bi-arrow-left me-2"></i> KEMBALI
                </a>
            </div>
        </div>

        <!-- Statistics Grid -->
        <div class="stats-minimal no-print">
            <div class="clean-card stat-box secondary">
                <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.65rem;">Total Karyawan</div>
                <div class="h3 fw-900 font-mono text-dark mb-0 mt-1"><?= number_format($stats['total_karyawan']) ?></div>
            </div>
            <div class="clean-card stat-box success">
                <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.65rem;">Total Hadir</div>
                <div class="h3 fw-900 font-mono text-emerald mb-0 mt-1"><?= number_format($stats['total_hadir']) ?></div>
            </div>
            <div class="clean-card stat-box warning">
                <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.65rem;">Terlambat</div>
                <div class="h3 fw-900 font-mono text-warning mb-0 mt-1"><?= number_format($stats['total_terlambat']) ?></div>
            </div>
            <div class="clean-card stat-box info">
                <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.65rem;">Sakit/Izin/Cuti</div>
                <div class="h3 fw-900 font-mono text-info mb-0 mt-1"><?= number_format($stats['total_sakit'] + $stats['total_izin'] + $stats['total_cuti']) ?></div>
            </div>
            <div class="clean-card stat-box danger">
                <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.65rem;">Total Alpha</div>
                <div class="h3 fw-900 font-mono text-rose mb-0 mt-1"><?= number_format($stats['total_alpha']) ?></div>
            </div>
        </div>

        <div class="clean-card mb-4 no-print overflow-hidden">
            <div class="p-4 bg-white border-bottom">
                <h6 class="fw-900 text-dark mb-0 ls-1"><i class="bi bi-funnel-fill me-2 text-primary-tgm"></i>PENYARINGAN DATA ABSENSI</h6>
            </div>
            <div class="p-4 bg-slate-100">
                <form id="searchForm" action="<?= base_url('absensi/rekap_absen_bulanan') ?>" method="GET" class="row gx-2 gy-3">
                    <div class="col-md-3">
                        <label class="fw-900 text-muted small text-uppercase mb-2">Nama Karyawan</label>
                        <input type="text" class="form-control border-0 fw-bold px-3 py-2" name="nama" value="<?= $nama ?>" placeholder="Cari Nama...">
                    </div>
                    <div class="col-md-2">
                        <label class="fw-900 text-muted small text-uppercase mb-2">Periode</label>
                        <input type="month" class="form-control border-0 fw-bold px-3 py-2" name="bulan" value="<?= $bulan ?>"
                            onchange="updateDatesFromMonth(this.value)">
                    </div>
                    <div class="col-md-2">
                        <label class="fw-900 text-muted small text-uppercase mb-2">Jabatan</label>
                        <select class="form-select border-0 fw-bold px-3 py-2" name="jabatan">
                            <option value="">Semua Jabatan</option>
                            <?php foreach ($jabatan_list as $jabatan_item): ?>
                                    <option value="<?= $jabatan_item->nama_jabatan ?>" <?= $jabatan == $jabatan_item->nama_jabatan ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($jabatan_item->nama_jabatan) ?>
                                    </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="fw-900 text-muted small text-uppercase mb-2">Divisi</label>
                        <select class="form-select border-0 fw-bold px-3 py-2" name="divisi">
                            <option value="">Semua Divisi</option>
                            <?php foreach ($divisi_list as $divisi_item): ?>
                                    <option value="<?= $divisi_item->divisi_nama ?>" <?= $divisi == $divisi_item->divisi_nama ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($divisi_item->divisi_nama) ?>
                                    </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="fw-900 text-muted small text-uppercase mb-2">Filter Tanggal</label>
                        <div class="input-group">
                            <input type="date" class="form-control border-0 fw-bold px-2 py-2" name="start_date" id="start_date" value="<?= $start_date ?>">
                            <span class="input-group-text border-0 bg-white fw-bold">-</span>
                            <input type="date" class="form-control border-0 fw-bold px-2 py-2" name="end_date" id="end_date" value="<?= $end_date ?>">
                        </div>
                    </div>
                    <div class="col-12 mt-3 text-end">
                        <button type="submit" class="btn btn-primary-tgm rounded-pill fw-900 small px-5 py-2 shadow-sm">
                            <i class="bi bi-search me-2"></i> FILTER DATA REKAPITULASI
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="clean-card overflow-hidden">
            <div class="p-4 bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-900 text-dark mb-0 ls-1"><i class="bi bi-table me-2 text-primary-tgm"></i>DATABASE REKAPITULASI KERJA</h6>
                <div class="d-flex gap-2 no-print">
                    <a href="<?= base_url('absensi/export_excel_rekap?start_date=' . $start_date . '&end_date=' . $end_date) ?>"
                        class="btn btn-white border rounded-pill px-3 fw-900 small shadow-sm text-dark">
                        <i class="bi bi-file-earmark-excel-fill text-success me-2"></i> EXCEL
                    </a>
                    <button onclick="window.print()" class="btn btn-white border rounded-pill px-3 fw-900 small shadow-sm text-dark">
                        <i class="bi bi-printer-fill text-muted me-2"></i> CETAK A4
                    </button>
                    <a href="<?= base_url('absensi/rekapitulasi?start_date=' . $start_date . '&end_date=' . $end_date) ?>"
                        class="btn btn-primary-tgm rounded-pill px-3 fw-900 small shadow-sm">
                        <i class="bi bi-grid-3x3-gap-fill me-2"></i> GRID REKAP
                    </a>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-simple mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">No</th>
                            <th class="text-center">Profil</th>
                            <th>Staff Karyawan</th>
                            <th>Jabatan</th>
                            <th class="text-center">Hadir</th>
                            <th class="text-center">Telat</th>
                            <th class="text-center">S/I/C</th>
                            <th class="text-center">Alpha</th>
                            <th class="text-center">Lembur Net</th>
                            <th class="text-center">Poin %</th>
                            <th class="text-center">Predikat</th>
                            <th class="text-center pe-4 no-print">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rekap_data)):
                            $no = 1;
                            foreach ($rekap_data as $row): ?>
                                        <tr>
                                            <td class="ps-4 font-mono text-muted small"><?= $no++ ?></td>
                                            <td class="text-center">
                                                <div class="avatar-box mx-auto" style="width: 45px; height: 45px; overflow: hidden; border-radius: 50%; border: 2px solid var(--tgm-slate-100);">
                                                    <img src="<?= base_url('assets/images/karyawan/' . ($row->foto ? $row->foto : 'default.png')) ?>"
                                                        alt="Foto" style="width: 100%; height: 100%; object-fit: cover;">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-900 text-dark small ls-0"><?= strtoupper(htmlspecialchars($row->nama_lengkap)) ?></div>
                                                <div class="text-muted font-mono" style="font-size: 0.75rem;"><?= htmlspecialchars($row->nik) ?></div>
                                            </td>
                                            <td class="small fw-700 text-muted"><?= htmlspecialchars($row->jabatan) ?></td>
                                            <td class="text-center font-mono fw-900 text-emerald"><?= $row->total_hadir ?></td>
                                            <td class="text-center font-mono fw-900 text-warning">
                                                <?= $row->total_terlambat ?>x
                                                <?php if (isset($row->total_late_minutes) && $row->total_late_minutes > 0): ?>
                                                    <br><span class="text-danger small" style="font-size: 0.7rem; letter-spacing: -0.5px;">(-<?= $row->total_late_minutes ?>m)</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center font-mono fw-800 text-info"><?= $row->total_sakit + $row->total_izin + $row->total_cuti ?></td>
                                            <td class="text-center font-mono fw-900 text-rose <?= $row->total_alpha > 0 ? 'bg-rose bg-opacity-10 rounded text-rose px-2 py-1' : '' ?>"><?= $row->total_alpha ?></td>
                                            <td class="text-center font-mono fw-900 text-primary-tgm"><?= $row->lembur_netto_jam > 0 ? '+'.$row->lembur_netto_jam.' J' : '-' ?></td>
                                            <td class="text-center font-mono fw-900 text-primary-tgm" style="font-size: 1.1rem;"><?= $row->point_kinerja ?>%</td>
                                            <td class="text-center">
                                                <?php
                                                $badge_class = 'bg-emerald text-emerald';
                                                if ($row->point_kinerja < 75)
                                                    $badge_class = 'bg-warning text-dark';
                                                if ($row->point_kinerja < 65)
                                                    $badge_class = 'bg-rose text-rose';
                                                ?>
                                                <span class="badge-soft <?= $badge_class ?> bg-opacity-10 px-3"><?= strtoupper($row->predikat) ?></span>
                                            </td>
                                            <td class="text-center pe-4 no-print">
                                                <a href="<?= base_url('absensi/rekap/' . $row->karyawan_id . '?bulan=' . $bulan) ?>"
                                                    class="btn btn-slate-100 btn-sm rounded-pill px-3" title="Raport Individu">
                                                    <i class="bi bi-file-earmark-person-fill text-primary-tgm"></i>
                                                </a>
                                            </td>
                                        </tr>
                                <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <i class="bi bi-info-circle fa-2x mb-3 d-block opacity-25"></i>
                                        Tidak ada data absensi untuk periode ini.
                                    </td>
                                </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function updateDatesFromMonth(monthVal) {
        if (!monthVal) return;
        const [year, month] = monthVal.split('-');
        const firstDay = `${year}-${month}-01`;
        const lastDay = new Date(year, month, 0).getDate();
        const lastDayFormatted = `${year}-${month}-${String(lastDay).padStart(2, '0')}`;

        document.getElementById('start_date').value = firstDay;
        document.getElementById('end_date').value = lastDayFormatted;
    }
</script>

<!-- Modal Panduan Rekap Bulanan -->
<div class="modal fade" id="modalGuideRekap" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="bg-primary-tgm p-4 text-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-primary-tgm rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                        <i class="bi bi-book-half fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-900 ls-1 mb-0">PANDUAN REKAP BULANAN</h5>
                        <p class="small mb-0 opacity-75">SOP Analisis Kehadiran & Kedisiplinan Staff</p>
                    </div>
                </div>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="bg-slate-50 p-4 rounded-4 h-100 border border-primary-tgm border-opacity-10">
                            <h6 class="fw-900 text-primary-tgm mb-3 small text-uppercase"><i class="bi bi-graph-up-arrow me-2"></i>INDIKATOR UTAMA</h6>
                            <ul class="small text-muted ps-3 lh-lg mb-0">
                                <li><b>Poin Kinerja:</b> Rasio kehadiran harian staff terhadap total hari kerja efektif.</li>
                                <li><b>S/I/C (Sakit/Izin/Cuti):</b> Data gabungan dari absensi yang telah disetujui (Approved).</li>
                                <li><b>Alpha:</b> Hari tanpa keterangan, memicu pemotongan tajam pada skor kedisiplinan.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-slate-50 p-4 rounded-4 h-100 border border-emerald border-opacity-10">
                            <h6 class="fw-900 text-emerald mb-3 small text-uppercase"><i class="bi bi-printer me-2"></i>PELAPORAN & EKSPOR</h6>
                            <ul class="small text-muted ps-3 lh-lg mb-0">
                                <li><b>Excel Export:</b> Untuk pengolahan data atau perhitungan bonus di luar sistem ERP.</li>
                                <li><b>Cetak A4:</b> Gunakan untuk arsip fisik. Tampilan akan otomatis teroptimasi untuk kertas print.</li>
                                <li><b>Raport Individu:</b> Klik ikon dokumen untuk melihat rincian absen harian per masing-masing staff.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-primary-tgm w-100 rounded-pill fw-900 small py-3" data-bs-dismiss="modal">Selesai Membaca Panduan</button>
            </div>
        </div>
    </div>
</div>
</div>