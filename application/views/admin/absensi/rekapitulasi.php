<?php
// Get Holidays for this range
$holidays = $this->db->select('tanggal, nama_libur')
    ->where('tanggal >=', $start_date)
    ->where('tanggal <=', $end_date)
    ->get('hari_libur')
    ->result_array();
$holiday_map = array_column($holidays, 'nama_libur', 'tanggal');

$jam_kerja = $this->db->get('tbl_jam_kerja')->result_array();
$jam_kerja_map = array_column($jam_kerja, 'jam_masuk', 'hari');
$daysIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
?>

<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">
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
    .fw-800 { font-weight: 800; }
    .fw-900 { font-weight: 900; }
    .ls-n1 { letter-spacing: -1px; }
    .ls-1 { letter-spacing: 1px; }
    .font-mono { font-family: 'JetBrains Mono', monospace; }
    .text-primary-tgm { color: var(--tgm-primary); }

    /* Clean Card System */
    .clean-card { background: white; border: 1px solid var(--slate-200); border-radius: 20px; overflow: hidden; }

    /* Table Matrix Refinement */
    .table-rekap { font-size: 0.75rem; border-collapse: separate; border-spacing: 0; }
    .table-rekap th { background: var(--slate-50); color: var(--slate-500); font-weight: 900; text-transform: uppercase; letter-spacing: 1px; padding: 1rem 0.5rem; border-bottom: 2px solid var(--slate-100); text-align: center; }
    .table-rekap td { padding: 0.5rem; border-bottom: 1px solid var(--slate-100); border-right: 1px solid var(--slate-100); vertical-align: middle; transition: all 0.2s; }

    /* Sticky Columns */
    .sticky-col { position: sticky; background-color: white !important; z-index: 10; border-right: 2px solid var(--slate-200) !important; }
    .sticky-col-no { left: 0; min-width: 45px; }
    .sticky-col-name { left: 45px; min-width: 200px; box-shadow: 4px 0 10px -5px rgba(0,0,0,0.1); }
    tr:hover td.sticky-col { background-color: var(--slate-50) !important; }

    /* Status Cells */
    .status-cell { font-weight: 900; color: white; display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; border-radius: 4px; }
    .date-col { min-width: 38px; text-align: center; position: relative; font-family: 'JetBrains Mono'; }
    .date-col:hover { background-color: var(--slate-100); }

    .status-h { background-color: #dcfce7 !important; color: #166534 !important; } /* Hadir */
    .status-s { background-color: #ecfeff !important; color: #155e75 !important; } /* Sakit */
    .status-i { background-color: #fffbeb !important; color: #92400e !important; } /* Izin */
    .status-c { background-color: #f0f9ff !important; color: #075985 !important; } /* Cuti */
    .status-a { background-color: #fef2f2 !important; color: #991b1b !important; } /* Alpha */
    .status-l { background-color: #fdf2f8 !important; color: #86198f !important; } /* Libur */
    .status-m { background-color: #f1f5f9 !important; color: #475569 !important; border: 1px dashed var(--slate-300); } /* Minggu */
    
    .status-late { border-bottom: 3px solid var(--rose) !important; }

    .photo-indicator { font-size: 0.6rem; position: absolute; bottom: 1px; right: 2px; color: var(--tgm-primary); opacity: 0.6; }

    /* Summary Columns */
    .summary-col { background: var(--slate-50) !important; font-weight: 900; text-align: center; border-left: 1px solid var(--slate-200); }
    .summary-h { color: var(--emerald); }
    .summary-t { color: var(--rose); }

    /* Buttons */
    .btn-primary-tgm { background: var(--tgm-primary); color: white; border: none; font-weight: 800; border-radius: 12px; padding: 8px 20px; transition: all 0.3s; }
    .btn-primary-tgm:hover { background: #1d4ed8; color: white; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2); }
    .btn-white { background: white; color: var(--slate-700); border: 1px solid var(--slate-200); font-weight: 800; border-radius: 12px; padding: 8px 20px; transition: all 0.3s; }
    .btn-white:hover { background: var(--slate-50); border-color: var(--tgm-primary); color: var(--tgm-primary); }

    .table-responsive { max-height: calc(100vh - 350px); overflow-y: auto; scrollbar-width: thin; }

    @media print {
        .no-print { display: none !important; }
        .app-content { padding: 0; }
        .sticky-col { position: static !important; }
    }
</style>


<!-- Header -->
<?php $this->load->view('layout_admin/header_modul'); ?>

<div class="app-content">
    <!-- Premium Header -->
    <div class="d-flex justify-content-between align-items-center mb-5 no-print">
        <div>
            <div class="small text-primary-tgm fw-900 ls-1 text-uppercase mb-1">Kehadiran Matriks</div>
            <h1 class="fw-900 mb-0 ls-n1 text-dark">Rekapitulasi <span class="text-primary-tgm">Bulanan</span></h1>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn-white shadow-sm" data-bs-toggle="modal" data-bs-target="#modalGuideRekapMatriks">
                <i class="bi bi-journal-text me-2"></i> PANDUAN MODUL
            </button>
            <a href="<?= base_url('absensi/export_rekapitulasi_excel?' . $_SERVER['QUERY_STRING']) ?>" class="btn-primary-tgm shadow-sm">
                <i class="bi bi-file-earmark-excel me-2"></i> EXPORT EXCEL
            </a>
        </div>
    </div>

    <div class="clean-card shadow-sm no-print border-0 mb-4">
        <div class="p-4 bg-slate-100 border-bottom">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h6 class="fw-900 mb-0 text-dark ls-1"><i class="bi bi-funnel-fill me-2 text-primary-tgm"></i>INTEGRATED FILTER WORKSPACE</h6>
                <div class="small fw-800 text-muted ls-1">RANGE: <b class="text-dark"><?= date('d M', strtotime($start_date)) ?> - <?= date('d M Y', strtotime($end_date)) ?></b></div>
            </div>
            <form action="<?= base_url('absensi/rekapitulasi') ?>" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="fw-900 small text-muted text-uppercase mb-2 ls-1">Dari Tanggal</label>
                    <input type="date" class="form-control border-0 fw-800 px-3 py-2" name="start_date" value="<?= $start_date ?>" style="border-radius: 10px;">
                </div>
                <div class="col-md-3">
                    <label class="fw-900 small text-muted text-uppercase mb-2 ls-1">Sampai Tanggal</label>
                    <input type="date" class="form-control border-0 fw-800 px-3 py-2" name="end_date" value="<?= $end_date ?>" style="border-radius: 10px;">
                </div>
                <div class="col-md-2">
                    <label class="fw-900 small text-muted text-uppercase mb-2 ls-1">Filter Jabatan</label>
                    <select class="form-select border-0 fw-800 px-3 py-2" name="jabatan" style="border-radius: 10px;">
                        <option value="">Semua Jabatan</option>
                        <?php foreach ($jabatan_list as $j): ?>
                                <option value="<?= $j->nama_jabatan ?>" <?= $jabatan == $j->nama_jabatan ? 'selected' : '' ?>>
                                    <?= $j->nama_jabatan ?>
                                </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="fw-900 small text-muted text-uppercase mb-2 ls-1">Filter Divisi</label>
                    <select class="form-select border-0 fw-800 px-3 py-2" name="divisi" style="border-radius: 10px;">
                        <option value="">Semua Divisi</option>
                        <?php foreach ($divisi_list as $d): ?>
                                <option value="<?= $d->divisi_nama ?>" <?= $divisi == $d->divisi_nama ? 'selected' : '' ?>>
                                    <?= $d->divisi_nama ?>
                                </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary-tgm w-100 py-2 shadow-sm">
                        <i class="bi bi-lightning-charge-fill me-2"></i> PROSES DATA
                    </button>
                </div>
            </form>
        </div>

        <!-- Legend -->
        <div class="p-3 bg-white border-bottom no-print">
            <div class="d-flex flex-wrap gap-4 px-2">
                <div class="d-flex align-items-center gap-2"><span class="badge-soft bg-emerald px-2 py-1 fw-900" style="min-width:25px">H</span> <small class="text-muted fw-800 ls-1">HADIR</small></div>
                <div class="d-flex align-items-center gap-2"><span class="badge-soft bg-sky px-2 py-1 fw-900" style="min-width:25px">S</span> <small class="text-muted fw-800 ls-1">SAKIT</small></div>
                <div class="d-flex align-items-center gap-2"><span class="badge-soft bg-amber px-2 py-1 fw-900" style="min-width:25px">I</span> <small class="text-muted fw-800 ls-1">IZIN</small></div>
                <div class="d-flex align-items-center gap-2"><span class="badge-soft bg-primary-tgm text-white px-2 py-1 fw-900" style="min-width:25px">C</span> <small class="text-muted fw-800 ls-1">CUTI</small></div>
                <div class="d-flex align-items-center gap-2"><span class="badge-soft bg-rose text-white px-2 py-1 fw-900" style="min-width:25px">A</span> <small class="text-muted fw-800 ls-1">ALPHA</small></div>
                <div class="d-flex align-items-center gap-2"><span class="badge-soft bg-slate-200 text-slate-500 px-2 py-1 fw-900" style="min-width:25px">M</span> <small class="text-muted fw-800 ls-1">MINGGU / LIBUR</small></div>
                <div class="d-flex align-items-center gap-2 text-rose"><span class="border-bottom border-3 border-rose fw-900">H</span> <small class="fw-800 ls-1">TERLAMBAT</small></div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-rekap align-middle">
                <thead>
                    <tr>
                        <th class="sticky-col sticky-col-no text-center">NO</th>
                        <th class="sticky-col sticky-col-name text-start">KARYAWAN & JABATAN</th>
                        <?php foreach ($dates as $date):
                            $dayName = $daysIndo[date('w', strtotime($date))];
                            $isSunday = (date('w', strtotime($date)) == 0);
                            $isHoliday = isset($holiday_map[$date]);
                            ?>
                                <th class="date-col <?= $isSunday || $isHoliday ? 'bg-slate-50' : '' ?>">
                                    <div class="small opacity-50"><?= substr($dayName, 0, 1) ?></div>
                                    <div class="fw-900 fs-6"><?= date('d', strtotime($date)) ?></div>
                                </th>
                        <?php endforeach; ?>
                        <th class="summary-col summary-h">H</th>
                        <th class="summary-col text-info">S</th>
                        <th class="summary-col text-warning">I</th>
                        <th class="summary-col text-primary">C</th>
                        <th class="summary-col text-danger border-rose">A</th>
                        <th class="summary-col summary-t">T</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    foreach ($employees as $emp):
                        $total_h = 0;
                        $total_s = 0;
                        $total_i = 0;
                        $total_c = 0;
                        $total_a = 0;
                        $total_t = 0;
                        ?>
                            <tr>
                                <td class="sticky-col sticky-col-no text-center font-mono">
                                    <?= str_pad($no++, 2, '0', STR_PAD_LEFT) ?>
                                </td>
                                <td class="sticky-col sticky-col-name">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="overflow-hidden">
                                            <div class="fw-900 text-dark ls-n1 text-truncate" style="max-width:140px"><?= strtoupper($emp->nama_lengkap) ?></div>
                                            <div class="small fw-700 text-muted ls-1" style="font-size:0.6rem"><?= strtoupper($emp->jabatan) ?></div>
                                        </div>
                                        <a href="<?= base_url('absensi/rekap/' . $emp->karyawan_id . '?start_date=' . $start_date . '&end_date=' . $end_date) ?>"
                                            class="btn btn-white btn-sm p-1 shadow-sm border-0 bg-slate-100" title="Detail Raport">
                                            <i class="bi bi-box-arrow-in-up-right text-primary-tgm px-1"></i>
                                        </a>
                                    </div>
                                </td>
                                <?php foreach ($dates as $date):
                                    $att = isset($attendance_map[$emp->karyawan_id][$date]) ? $attendance_map[$emp->karyawan_id][$date] : null;
                                    $isSunday = (date('w', strtotime($date)) == 0);
                                    $isHoliday = isset($holiday_map[$date]);
                                    $status_char = '';
                                    $class = '';
                                    $title = date('d M Y', strtotime($date));

                                    if ($att) {
                                        $status = $att->status;
                                        if ($status == 'Hadir') {
                                            $status_char = 'H';
                                            $class = 'status-h';
                                            $total_h++;
                                            $hariIndo = $daysIndo[date('w', strtotime($date))];
                                            $jamMasukStd = isset($jam_kerja_map[$hariIndo]) ? $jam_kerja_map[$hariIndo] : '08:00:00';
                                            $limitLate = date('H:i:s', strtotime($jamMasukStd . ' + 15 minutes'));
                                            if ($att->jam_masuk > $limitLate) {
                                                $class .= ' status-late';
                                                $total_t++;
                                            }
                                        } elseif ($status == 'Sakit') {
                                            $status_char = 'S';
                                            $class = 'status-s';
                                            $total_s++;
                                        } elseif ($status == 'Izin') {
                                            $status_char = 'I';
                                            $class = 'status-i';
                                            $total_i++;
                                        } elseif (stripos($status, 'Cuti') !== false) {
                                            $status_char = 'C';
                                            $class = 'status-c';
                                            $total_c++;
                                        } elseif ($status == 'Alpha') {
                                            $status_char = 'A';
                                            $class = 'status-a';
                                            $total_a++;
                                        }
                                    } else {
                                        if ($isSunday) {
                                            $status_char = 'M';
                                            $class = 'status-m';
                                        } elseif ($isHoliday) {
                                            $status_char = 'L';
                                            $class = 'status-l';
                                        } else {
                                            if ($date < date('Y-m-d')) {
                                                $status_char = 'A';
                                                $class = 'status-a';
                                                $total_a++;
                                            }
                                        }
                                    }
                                    ?>
                                        <td class="date-col <?= $class ?>">
                                            <?= $status_char ?>
                                            <?php if ($att && ($att->foto_masuk || $att->foto_pulang)): ?>
                                                    <i class="bi bi-camera-fill photo-indicator"></i>
                                            <?php endif; ?>
                                        </td>
                                <?php endforeach; ?>
                                <td class="summary-col summary-h"><?= $total_h ?></td>
                                <td class="summary-col text-info"><?= $total_s ?></td>
                                <td class="summary-col text-warning text-opacity-75"><?= $total_i ?></td>
                                <td class="summary-col text-primary"><?= $total_c ?></td>
                                <td class="summary-col text-rose"><?= $total_a ?></td>
                                <td class="summary-col text-rose fw-900 border-start border-rose border-opacity-25" style="background:#fff1f2 !important"><?= $total_t ?></td>
                            </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Panduan Rekapitulasi Matriks -->
<div class="modal fade" id="modalGuideRekapMatriks" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-primary-tgm text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-primary-tgm rounded-pill d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="bi bi-grid-3x3 fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-900 mb-0 ls-n1">PANDUAN <span class="opacity-75">REKAP MATRIKS</span></h5>
                        <p class="small mb-0 opacity-75 fw-800 ls-1">Monitor Kehadiran Harian Massal</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-slate-50">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="clean-card p-4 h-100">
                            <h6 class="fw-900 text-dark mb-3 ls-1 border-bottom pb-2">SIMBOL KEHADIRAN</h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2 small fw-800"><span class="badge-soft bg-emerald px-2">H</span> : Hadir Normal (Check-In Valid)</li>
                                <li class="mb-2 small fw-800"><span class="badge-soft bg-rose text-white px-2">A</span> : Alpha (Tanpa Keterangan)</li>
                                <li class="mb-2 small fw-800"><span class="badge-soft bg-slate-200 text-muted px-2">M</span> : Libur / Minggu (Non-Efektif)</li>
                                <li class="small fw-800 text-rose"><span class="border-bottom border-2 border-rose">H</span> : Hadir tapi <b>Terlambat</b></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="clean-card p-4 h-100">
                            <h6 class="fw-900 text-dark mb-3 ls-1 border-bottom pb-2">FITUR UTAMA</h6>
                            <p class="small text-muted fw-800 mb-3">Gunakan <b>Sticky Column</b> untuk menggeser tabel ke samping tanpa kehilangan nama karyawan.</p>
                            <div class="p-3 bg-slate-100 rounded-3 text-center">
                                <i class="bi bi-camera-fill text-primary-tgm me-2"></i>
                                <span class="small fw-900 ls-1">ADA BUKTI FOTO</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="clean-card p-4">
                            <h6 class="fw-900 text-dark mb-1 ls-1">LOGIKA REKAPITULASI</h6>
                            <p class="small text-muted fw-800 mb-0 ls-1">Sistem otomatis menghitung rekap di kolom kanan (H, S, I, C, A, T). <b class="text-rose">T</b> menunjukkan total frekuensi terlambat dalam periode tersebut.</p>
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

</div>
</div>
</div>
</div>