<?php
/**
 * Sistem Monitoring Absensi Karyawan - Integrated Dashboard Style
 * Displays hrd attendance logs and grouped monthly performance recap.
 */
$tanggal_selected = isset($tanggal) ? $tanggal : date('Y-m-d');
$bulan_selected = isset($bulan) ? $bulan : date('Y-m');

// Initial stats for daily view
$jumlah_hadir = 0;
$jumlah_izin = 0;
$jumlah_sakit = 0;
$jumlah_alpa = 0;

foreach ($absensi as $row) {
    if (strtolower($row->status) == 'hadir')
        $jumlah_hadir++;
    elseif (strtolower($row->status) == 'izin')
        $jumlah_izin++;
    elseif (strtolower($row->status) == 'sakit')
        $jumlah_sakit++;
    else
        $jumlah_alpa++;
}

// Haversine Distance Helper
if (!function_exists('hitung_jarak')) {
    function hitung_jarak($lat1, $lon1, $lat2, $lon2) {
        $earth_radius = 6371000; // in meters
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * asin(sqrt($a));
        $d = $earth_radius * $c;
        
        return $d;
    }
}

$CI =& get_instance();
$comp = $CI->db->get_where('tbl_perusahaan', ['id_perusahaan' => 1])->row();
$comp_lat = $comp ? $comp->latitude : -6.23674577;
$comp_lon = $comp ? $comp->longitude : 106.99821922;
?>

<link rel="stylesheet" href="<?= base_url('assets/css/costume-tgm.css') ?>">

<link rel="stylesheet" href="<?= base_url('assets/css/tgm-absen-karyawan.css') ?>">

<div class="app-content">
    <?php $this->load->view('layout_admin/header_modul'); ?>

    <div class="container-fluid">
        <!-- Dashboard Statistics -->
        <div class="stats-overview">
            <div class="stat-card info">
                <div class="stat-info">
                    <div class="stat-icon info"><i class="fas fa-users"></i></div>
                    <h3><?= count($absensi) ?></h3>
                    <p>Total Absensi Hari Ini</p>
                </div>
            </div>
            <div class="stat-card hadir">
                <div class="stat-info">
                    <div class="stat-icon hadir"><i class="fas fa-check"></i></div>
                    <h3><?= $jumlah_hadir ?></h3>
                    <p>Pegawai Hadir</p>
                </div>
            </div>
            <div class="stat-card izin">
                <div class="stat-info">
                    <div class="stat-icon izin"><i class="fas fa-id-card"></i></div>
                    <h3><?= $jumlah_izin + $jumlah_sakit ?></h3>
                    <p>Izin / Sakit</p>
                </div>
            </div>
            <div class="stat-card alpa">
                <div class="stat-info">
                    <div class="stat-icon alpa"><i class="fas fa-times"></i></div>
                    <h3><?= $jumlah_alpa ?></h3>
                    <p>Mangkir / Lainnya</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="report-section-card filters no-print">
            <div class="card-body p-4">
                <form action="" method="GET" class="row align-items-center g-3">
                    <div class="col-md-5">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-light p-3 border text-primary">
                                <i class="fa fa-calendar-check fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Monitoring Kehadiran</h6>
                                <p class="text-muted small mb-0">Tampilkan data absensi berdasarkan tanggal spesifik.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white" style="border-radius: 0;"><i
                                    class="fa fa-calendar-alt text-muted"></i></span>
                            <input type="date" name="tanggal" class="form-control fw-bold"
                                value="<?= $tanggal_selected ?>" onchange="this.form.submit()"
                                style="height: 50px; border-radius: 0;">
                        </div>
                    </div>
                    <div class="col-md-3 text-end">
                        <button type="button" onclick="window.print()" class="btn btn-outline-dark px-4"
                            style="height: 50px; border-radius: 0;">
                            <i class="fas fa-print me-2"></i> CETAK
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <!-- Grouped Summary Recap (Monthly) - AS REQUESTED BY USER -->
            <div class="col-12">
                <div class="report-section-card">
                    <div class="section-header">
                        <h5><i class="fas fa-users-cog me-2"></i>Rekap Absensi Bulanan (Grouping Karyawan)</h5>
                        <div class="no-print">
                            <span
                                class="badge bg-white text-dark py-2 px-3 rounded-0 fw-bold"><?= date('F Y', strtotime($bulan_selected)) ?></span>
                        </div>
                    </div>
                    <div class="section-body p-0">
                        <div class="p-3 border-bottom no-print">
                            <div class="input-group" style="max-width: 400px;">
                                <span class="input-group-text bg-light border-0"
                                    style="border-radius: 0; border-left: 4px solid #f39c12 !important;"><i
                                        class="fa fa-search text-muted"></i></span>
                                <input type="text" id="summarySearch"
                                    class="form-control form-control-sm border-0 bg-light"
                                    placeholder="Cari di rekap bulanan..." style="border-radius: 0;">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="tableSummary">
                                <thead>
                                    <tr>
                                        <th>Nama Karyawan</th>
                                        <th>Divisi / Jabatan</th>
                                        <th class="text-center">Hadir</th>
                                        <th class="text-center">Izin</th>
                                        <th class="text-center">Sakit</th>
                                        <th class="text-center">Cuti</th>
                                        <th class="text-center">Alpha</th>
                                        <th class="text-center">Telat</th>
                                        <th class="text-center">Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rekap_bulanan as $r):
                                        $working_days = 25; // Estimate or get from model
                                        $pct = ($r->total_hadir / $working_days) * 100;
                                        $pct_color = $pct >= 90 ? 'success' : ($pct >= 75 ? 'warning' : 'danger');
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <?php if (!empty($r->foto) && file_exists(FCPATH . 'assets/images/karyawan/' . $r->foto)): ?>
                                                        <img src="<?= base_url('assets/images/karyawan/' . $r->foto) ?>" alt=""
                                                            class="employee-avatar">
                                                    <?php else: ?>
                                                        <div class="bg-light d-flex align-items-center justify-content-center text-muted"
                                                            style="width: 38px; height: 38px;">
                                                            <i class="fas fa-user"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="fw-bold text-dark"><?= $r->nama_lengkap ?></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-muted small fw-bold text-uppercase"><?= $r->divisi ?></div>
                                                <div class="small opacity-75"><?= $r->jabatan ?></div>
                                            </td>
                                            <td class="text-center fw-bold text-success"><?= $r->total_hadir ?></td>
                                            <td class="text-center"><?= $r->total_izin ?></td>
                                            <td class="text-center"><?= $r->total_sakit ?></td>
                                            <td class="text-center"><?= $r->total_cuti ?></td>
                                            <td class="text-center text-danger fw-bold"><?= $r->total_alpha ?></td>
                                            <td class="text-center text-warning fw-bold"><?= $r->total_terlambat ?></td>
                                            <td class="text-center">
                                                <div class="progress" style="height: 6px; width: 60px; margin: 0 auto 4px;">
                                                    <div class="progress-bar bg-<?= $pct_color ?>"
                                                        style="width: <?= min(100, $pct) ?>%"></div>
                                                </div>
                                                <small
                                                    class="fw-bold text-<?= $pct_color ?>"><?= number_format(min(100, $pct), 0) ?>%</small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Attendance Details -->
            <div class="col-lg-12">
                <div class="report-section-card">
                    <div class="section-header">
                        <h5><i class="fas fa-list-ul me-2"></i>Log Absensi Harian: <?= date_indo($tanggal_selected) ?>
                        </h5>
                        <div class="no-print d-flex gap-2">
                            <span
                                class="badge bg-white text-dark py-2 px-3 rounded-0 fw-bold">ABS-<?= date('Ymd', strtotime($tanggal_selected)) ?></span>
                        </div>
                    </div>
                    <div class="section-body p-0">
                        <div class="p-3 border-bottom no-print">
                            <div class="input-group" style="max-width: 400px;">
                                <span class="input-group-text bg-light border-0"
                                    style="border-radius: 0; border-left: 4px solid #3498db !important;"><i
                                        class="fa fa-search text-muted"></i></span>
                                <input type="text" id="dailySearch"
                                    class="form-control form-control-sm border-0 bg-light"
                                    placeholder="Cari nama di log harian..." style="border-radius: 0;">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="tableDaily">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="50">No</th>
                                        <th>Karyawan</th>
                                        <th class="text-center">Jam Masuk</th>
                                        <th class="text-center">Jam Pulang</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Lokasi</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($absensi)): ?>
                                        <?php $no = 1;
                                        foreach ($absensi as $row): ?>
                                            <tr>
                                                <td class="text-center text-muted"><?= $no++ ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="fw-bold text-dark"><?= $row->nama_lengkap ?></div>
                                                    </div>
                                                </td>
                                                <td class="text-center fw-bold text-primary"><?= $row->jam_masuk ?: '--:--' ?>
                                                </td>
                                                <td class="text-center fw-bold text-muted"><?= $row->jam_pulang ?: '--:--' ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php
                                                    $sc = 'secondary';
                                                    if (strtolower($row->status) == 'hadir')
                                                        $sc = 'success';
                                                    elseif (strtolower($row->status) == 'izin')
                                                        $sc = 'warning';
                                                    elseif (strtolower($row->status) == 'sakit')
                                                        $sc = 'info';
                                                    elseif (strtolower($row->status) == 'alpa' || strtolower($row->status) == 'alpha')
                                                        $sc = 'danger';
                                                    ?>
                                                    <span class="badge status-pill bg-<?= $sc ?>">
                                                        <?= strtoupper($row->status) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <?php if (!empty($row->lokasi_absen)): ?>
                                                        <?php
                                                        $loc_url = '#';
                                                        $jarak_text = '';
                                                        if (preg_match('/Lat:\s*([-\d\.]+),\s*Long:\s*([-\d\.]+)/i', $row->lokasi_absen, $matches)) {
                                                            $loc_url = "https://www.google.com/maps/search/?api=1&query=" . $matches[1] . "," . $matches[2];
                                                            $jarak = hitung_jarak($comp_lat, $comp_lon, $matches[1], $matches[2]);
                                                            $jarak_text = '<div class="small mt-1 text-muted" style="font-size: 0.75rem;">' . number_format($jarak, 0, ',', '.') . ' m</div>';
                                                        } else {
                                                            $loc_url = "https://www.google.com/maps/search/?api=1&query=" . urlencode($row->lokasi_absen);
                                                        }
                                                        ?>
                                                        <a href="<?= $loc_url ?>" target="_blank" class="badge bg-info text-decoration-none">
                                                            <i class="fas fa-map-marker-alt"></i> Peta
                                                        </a>
                                                        <?= $jarak_text ?>
                                                    <?php else: ?>
                                                        <span class="text-muted small">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><small class="text-muted"><?= $row->keterangan ?: '-' ?></small></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick HR Links -->
        <h5 class="fw-bold mb-3 mt-4 text-dark no-print"><i class="fas fa-link text-primary me-2"></i>LINK HRD TERKAIT
        </h5>
        <div class="row g-4 mb-5 no-print">
            <div class="col-6 col-md-3">
                <a href="<?= base_url('karyawan/karyawan') ?>" class="nav-card">
                    <i class="fas fa-id-card nav-icon"></i>
                    <div class="nav-title">Data Master</div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="<?= base_url('karyawan/penggajian') ?>" class="nav-card">
                    <i class="fas fa-file-invoice-dollar nav-icon"></i>
                    <div class="nav-title">Sistem Payroll</div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="<?= base_url('karyawan/penilaian_kinerja') ?>" class="nav-card">
                    <i class="fas fa-star nav-icon"></i>
                    <div class="nav-title">Evaluasi Kinerja</div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="<?= base_url('laporan/dashboard_terpadu') ?>" class="nav-card">
                    <i class="fas fa-chart-line nav-icon"></i>
                    <div class="nav-title">Dashboard Utama</div>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        const tableSummary = $('#tableSummary').DataTable({
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
            pageLength: 10
        });

        const tableDaily = $('#tableDaily').DataTable({
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
            pageLength: 25
        });

        $('#summarySearch').on('keyup', function () {
            tableSummary.search(this.value).draw();
        });

        $('#dailySearch').on('keyup', function () {
            tableDaily.search(this.value).draw();
        });
    });
</script>