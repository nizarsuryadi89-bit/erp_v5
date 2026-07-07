<style>
    /* Ultra-Minimalist Performance Design */
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

    .text-primary-tgm {
        color: var(--tgm-primary) !important;
    }

    .bg-primary-tgm {
        background-color: var(--tgm-primary) !important;
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
        background: white;
    }

    .stat-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
    }

    .stat-box.primary {
        border-color: var(--tgm-primary);
    }

    .stat-box.success {
        border-color: var(--tgm-emerald);
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
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        padding: 15px 12px;
        border-top: none;
        border-bottom: 2px solid var(--tgm-slate-100);
    }

    .table-simple tbody td {
        padding: 15px 12px;
        vertical-align: middle;
        color: var(--tgm-slate-900);
        border-bottom: 1px solid var(--tgm-slate-100);
        font-size: 0.9rem;
    }

    .badge-soft {
        font-size: 0.7rem;
        padding: 5px 12px;
        font-weight: 800;
        border-radius: 5px;
        text-transform: uppercase;
    }

    .bg-slate-100 {
        background-color: #f1f5f9 !important;
    }

    .font-mono {
        font-family: 'JetBrains Mono', 'Fira Code', monospace !important;
    }
</style>

<div class="app-content">
    <?php $this->load->view('layout_admin/header_modul'); ?>

    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div class="d-flex align-items-center gap-3">
                <a href="<?= base_url('hr') ?>"
                    class="btn btn-white d-flex align-items-center justify-content-center shadow-sm"
                    style="width: 42px; height: 42px; padding: 0; border-radius: 10px; border: 1px solid var(--tgm-slate-200); background: white; color: var(--tgm-slate-700);"
                    title="Kembali ke Dashboard HR">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h3 class="fw-900 mb-0 ls-1">PENILAIAN <span class="text-primary-tgm">KINERJA</span></h3>
                    <p class="small text-muted mb-0">Analisis disiplin, kehadiran, dan skor performa staff harian.</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" data-bs-toggle="modal" data-bs-target="#modalSOPKinerja"
                    class="btn btn-white border rounded-pill px-4 fw-900 small shadow-sm">
                    <i class="bi bi-journal-text me-2 text-primary-tgm"></i> PANDUAN SOP
                </button>
                <a href="<?= base_url('karyawan/export_kinerja_excel?bulan=' . $bulan) ?>"
                    class="btn btn-white border rounded-pill px-4 fw-900 small shadow-sm">
                    <i class="bi bi-file-earmark-excel me-2 text-success"></i> EXCEL
                </a>
                <a href="<?= base_url('absensi/rekap?bulan=' . $bulan) ?>"
                    class="btn btn-primary-tgm rounded-pill px-4 fw-900 small shadow-sm">
                    <i class="bi bi-calendar-check me-2"></i> REKAP ABSENSI
                </a>
            </div>
        </div>

        <!-- Statistics Grid -->
        <div class="stats-minimal">
            <div class="clean-card stat-box primary">
                <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.75rem; opacity: 0.8;">
                    Karyawan Aktif</div>
                <div class="h3 fw-900 font-mono text-dark mb-0 mt-1">
                    <?= number_format($stats['total_karyawan_aktif']) ?> <small class="fw-bold text-muted ls-0"
                        style="font-size: 0.8rem;">STAFF</small>
                </div>
            </div>
            <div class="clean-card stat-box success">
                <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.75rem; opacity: 0.8;">Total
                    Terlambat</div>
                <div class="h3 fw-900 font-mono text-emerald mb-0 mt-1"><?= number_format($stats['total_telat']) ?>
                    <small class="fw-bold text-muted ls-0" style="font-size: 0.8rem;">KALI</small>
                </div>
            </div>
            <div class="clean-card stat-box warning">
                <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.75rem; opacity: 0.8;">Avg
                    Disiplin</div>
                <div class="h3 fw-900 font-mono text-warning mb-0 mt-1"><?= $stats['rata_rata_skor'] ?> <small
                        class="fw-bold text-muted ls-0" style="font-size: 0.8rem;">PTS</small></div>
            </div>
            <div class="clean-card stat-box danger">
                <div class="fw-800 text-muted text-uppercase ls-1 small" style="font-size: 0.75rem; opacity: 0.8;">Skor
                    Tertinggi</div>
                <div class="h3 fw-900 font-mono text-rose mb-0 mt-1"><?= $stats['top_score'] ?> <small
                        class="fw-bold text-muted ls-0" style="font-size: 0.8rem;">PTS</small></div>
            </div>
        </div>

        <!-- Filters -->
        <div class="clean-card mb-4 overflow-hidden">
            <div class="p-4 bg-white">
                <form method="GET" action="<?= base_url('karyawan/penilaian_kinerja') ?>"
                    class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span
                                class="input-group-text bg-slate-100 border-end-0 text-dark small fw-900 px-3">PERIODE</span>
                            <input type="month" class="form-control border-start-0 ps-0 fw-bold" name="bulan"
                                value="<?= $bulan ?>" onchange="this.form.submit()">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span
                                class="input-group-text bg-slate-100 border-end-0 text-dark small fw-900 px-3">JABATAN</span>
                            <select class="form-select border-start-0 ps-0 fw-bold select2-filter" name="jabatan_id"
                                onchange="this.form.submit()">
                                <option value="">Semua Jabatan</option>
                                <?php foreach ($jabatan_list as $jabatan): ?>
                                    <option value="<?= $jabatan->jabatan_id ?>" <?= $jabatan_id == $jabatan->jabatan_id ? 'selected' : '' ?>>
                                        <?= $jabatan->nama_jabatan ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span
                                class="input-group-text bg-slate-100 border-end-0 text-dark small fw-900 px-3">KARYAWAN</span>
                            <select class="form-select border-start-0 ps-0 fw-bold select2-filter" name="karyawan_id"
                                onchange="this.form.submit()">
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
                        <button type="submit" class="btn btn-primary-tgm w-100 rounded-pill fw-900 small py-2">
                            <i class="bi bi-filter me-2"></i> FILTER DATA
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Main Table -->
        <div class="clean-card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-simple mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3" width="5%">NO</th>
                            <th width="20%">KARYAWAN</th>
                            <th class="text-center">S</th>
                            <th class="text-center">I</th>
                            <th class="text-center">C</th>
                            <th class="text-center">TL</th>
                            <th class="text-center">PC</th>
                            <th class="text-center text-rose">A</th>
                            <th class="text-center">SPK</th>
                            <th class="text-center">SKOR</th>
                            <th class="text-center">STATUS</th>
                            <th class="text-center pe-3">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($performance_data)): ?>
                            <?php $no = 1;
                            foreach ($performance_data as $perf): ?>
                                <tr>
                                    <td class="ps-3 font-mono text-muted small"><?= $no++ ?></td>
                                    <td>
                                        <div class="fw-bold text-dark small"><?= strtoupper($perf->nama_lengkap) ?></div>
                                        <div class="text-muted" style="font-size: 0.75rem;"><?= $perf->nama_jabatan ?></div>
                                    </td>
                                    <td class="text-center font-mono small"><?= $perf->total_sakit ?></td>
                                    <td class="text-center font-mono small"><?= $perf->total_izin ?></td>
                                    <td class="text-center font-mono small"><?= $perf->total_cuti ?></td>
                                    <td
                                        class="text-center font-mono <?= $perf->total_telat > 0 ? 'text-rose fw-bold' : 'text-muted opacity-50' ?>">
                                        <?= $perf->total_telat ?>
                                    </td>
                                    <td
                                        class="text-center font-mono <?= $perf->total_pulang_cepat > 0 ? 'text-rose fw-bold' : 'text-muted opacity-50' ?>">
                                        <?= $perf->total_pulang_cepat ?>
                                    </td>
                                    <td
                                        class="text-center font-mono <?= $perf->total_alpha > 0 ? 'text-rose fw-900' : 'text-muted opacity-50' ?>">
                                        <?= $perf->total_alpha ?>
                                    </td>
                                    <td class="text-center font-mono fw-bold text-dark">
                                        <?= $perf->total_spk ?>
                                    </td>
                                    <td class="text-center font-mono fw-900 text-primary-tgm" style="font-size: 1rem;">
                                        <?= number_format($perf->skor_kinerja) ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $status_label = '';
                                        $badge_class = '';
                                        $score = $perf->skor_kinerja;
                                        if ($score >= 95) {
                                            $status_label = 'Sangat Disiplin';
                                            $badge_class = 'bg-emerald text-emerald';
                                        } elseif ($score >= 85) {
                                            $status_label = 'Disiplin';
                                            $badge_class = 'bg-primary-tgm text-white';
                                        } elseif ($score >= 75) {
                                            $status_label = 'Cukup Disiplin';
                                            $badge_class = 'bg-warning text-dark';
                                        } else {
                                            $status_label = 'Kurang Disiplin';
                                            $badge_class = 'bg-rose text-rose';
                                        }
                                        ?>
                                        <span
                                            class="badge-soft <?= $badge_class ?> bg-opacity-10 px-3"><?= $status_label ?></span>
                                    </td>
                                    <td class="text-center pe-3">
                                        <div class="d-flex justify-content-center gap-1">
                                            <!-- <a href="<?= base_url('absensi/detail/' . $perf->karyawan_id . '?bulan=' . $bulan) ?>"
                                                class="btn btn-slate-100 btn-sm rounded-pill px-3" title="Detail Absensi">
                                                <i class="bi bi-calendar3 text-primary-tgm"></i>
                                            </a> -->
                                            <button type="button"
                                                class="btn btn-slate-100 btn-sm rounded-pill px-3 btn-print-modal"
                                                data-url="<?= base_url('karyawan/cetak_kinerja/' . $perf->karyawan_id . '?bulan=' . $bulan) ?>">
                                                <i class="bi bi-printer-fill text-dark"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar-x fa-3x mb-3 d-block opacity-25"></i>
                                    <h6 class="fw-900">Belum ada data absensi</h6>
                                    <p class="small">Coba ubah periode atau filter pencarian Anda.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-3 bg-slate-50 border-top text-end">
                <small class="fw-bold text-muted text-uppercase ls-1" style="font-size: 0.65rem;">Total Record:
                    <?= count($performance_data) ?> Karyawan</small>
            </div>
        </div>
    </div>
</div>

<!-- Modal Print Preview -->
<div class="modal fade" id="modalPrintKinerja" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="bg-primary-tgm p-4 text-white d-flex justify-content-between align-items-center">
                <h5 class="modal-title fw-900 ls-1 mb-0"><i class="bi bi-printer-fill me-2"></i>PREVIEW CETAK KINERJA
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-light">
                <div class="d-flex justify-content-center py-4">
                    <div class="shadow-lg bg-white overflow-hidden" style="width: 850px; min-height: 600px;">
                        <iframe id="printFrame" src="" style="width: 100%; height: 800px; border: none;"></iframe>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-slate-100 rounded-pill px-4 fw-900 small"
                    data-bs-dismiss="modal">TUTUP WINDOW</button>
                <button type="button" class="btn btn-primary-tgm rounded-pill px-5 fw-900 small shadow-primary-tgm"
                    onclick="printReport()">
                    <i class="bi bi-printer me-2"></i> CETAK LAPORAN SEKARANG
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.btn-print-modal', function () {
        const url = $(this).data('url');
        $('#printFrame').attr('src', url);
        $('#modalPrintKinerja').modal('show');
    });

    function printReport() {
        const frame = document.getElementById('printFrame');
        if (frame.contentWindow) {
            frame.contentWindow.print();
        }
    }
</script>

<!-- SOP Guide Modal -->
<div class="modal fade" id="modalSOPKinerja" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="bg-primary-tgm p-4 text-white position-relative">
                <div class="position-relative z-index-1">
                    <h5 class="modal-title fw-900 ls-1 mb-0"><i class="bi bi-info-circle-fill me-2"></i>PANDUAN SOP
                        PENILAIAN KINERJA</h5>
                    <p class="small mb-0 opacity-75 mt-1">Standar Operasional Prosedur Analisis Kedisiplinan - TGM ERP
                    </p>
                </div>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-4"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="bg-slate-50 p-4 rounded-4 h-100 border border-primary-tgm border-opacity-10">
                            <h6 class="fw-900 text-primary-tgm mb-3"><i class="bi bi-calculator me-2"></i>LOGIKA SKORING
                            </h6>
                            <ul class="small text-muted mb-0 ps-3 lh-lg">
                                <li>Skor dasar dimulai dari <b>100 Poin</b>.</li>
                                <li><b>Sakit/Izin (S/I):</b> Potong 1-2 Poin (Kebijakan).</li>
                                <li><b>Terlambat (TL):</b> Potongan kumulatif per menit.</li>
                                <li><b>Alpha (A):</b> Potongan signifikan (10% per hari).</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-slate-50 p-4 rounded-4 h-100 border border-emerald border-opacity-10">
                            <h6 class="fw-900 text-emerald mb-3"><i class="bi bi-award me-2"></i>KATEGORI STATUS</h6>
                            <ul class="small text-muted mb-0 ps-3 lh-lg">
                                <li><b>Sangat Disiplin:</b> Skor 95 - 100.</li>
                                <li><b>Disiplin:</b> Skor 85 - 94.</li>
                                <li><b>Cukup Disiplin:</b> Skor 75 - 84.</li>
                                <li><b>Kurang Disiplin:</b> Skor di bawah 75.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="bg-primary-tgm bg-opacity-10 p-4 rounded-4">
                            <h6 class="fw-900 text-primary-tgm mb-2 small"><i
                                    class="bi bi-printer-fill me-2"></i>PROSEDUR PELAPORAN</h6>
                            <p class="small text-dark fw-bold mb-0">
                                Gunakan tombol <b>Ikon Printer</b> untuk mencetak Rapor Kinerja individu. Laporan ini
                                bersifat rahasia dan dapat dilampirkan dalam pengajuan bonus kinerja bulanan pada modul
                                Payroll.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-primary-tgm w-100 rounded-pill fw-900 small py-3"
                    data-bs-dismiss="modal">SAYA MENGERTI & SIAP MENGANALISIS</button>
            </div>
        </div>
    </div>
</div>