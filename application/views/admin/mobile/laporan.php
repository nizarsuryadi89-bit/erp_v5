<!-- Mobile Laporan View - Consistent Dark Theme -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

<style>
    :root {
        --primary: #1a1a2e;
        --secondary: #16213e;
    }

    * {
        -webkit-tap-highlight-color: transparent;
        box-sizing: border-box;
    }

    body {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        min-height: 100vh;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        margin: 0;
        padding: 0;
    }

    .page-header {
        padding: 1.25rem 1rem;
        color: #fff;
        text-align: center;
    }

    .page-header h5 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .page-header .name {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-top: 0.25rem;
    }

    .page-header .email {
        font-size: 0.75rem;
        opacity: 0.7;
    }

    .content-container {
        background: #f5f5f5;
        border-radius: 24px 24px 0 0;
        min-height: calc(100vh - 120px);
        padding: 1.25rem;
        padding-bottom: 2rem;
    }

    .stat-card {
        background: #fff;
        border-radius: 14px;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .stat-card-header {
        padding: 0.6rem 0.875rem;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.8rem;
        font-weight: 600;
        color: #333;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        background: #fafafa;
    }

    .stat-card-body {
        padding: 0.875rem;
    }

    /* Filter */
    .filter-row {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 0.5rem;
        align-items: end;
    }

    .filter-label {
        font-size: 0.7rem;
        color: #888;
        margin-bottom: 0.25rem;
    }

    .filter-select {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 0.8rem;
        background: #fff;
    }

    .btn-filter {
        padding: 0.5rem 0.75rem;
        background: var(--primary);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.5rem;
    }

    .stat-box {
        text-align: center;
        padding: 0.6rem 0.25rem;
        border-radius: 10px;
    }

    .stat-box .number {
        font-size: 1rem;
        font-weight: 700;
    }

    .stat-box .label {
        font-size: 0.55rem;
        font-weight: 500;
    }

    .stat-green {
        background: #dcfce7;
        color: #16a34a;
    }

    .stat-red {
        background: #fee2e2;
        color: #dc2626;
    }

    .stat-yellow {
        background: #fef3c7;
        color: #d97706;
    }

    .stat-blue {
        background: #dbeafe;
        color: #2563eb;
    }

    /* Absensi Item */
    .absen-item {
        display: flex;
        align-items: center;
        padding: 0.6rem 0;
        border-bottom: 1px solid #f5f5f5;
        gap: 0.5rem;
    }

    .absen-item:last-child {
        border-bottom: none;
    }

    .absen-date {
        min-width: 40px;
        text-align: center;
    }

    .absen-date .day {
        font-size: 1rem;
        font-weight: 700;
        color: var(--primary);
    }

    .absen-date .month {
        font-size: 0.6rem;
        color: #888;
    }

    .absen-info {
        flex: 1;
    }

    .absen-time {
        font-size: 0.75rem;
        color: #333;
    }

    .absen-hours {
        font-size: 0.65rem;
        color: #888;
    }

    .absen-late {
        font-size: 0.6rem;
        color: #d97706;
    }

    .absen-status {
        padding: 0.2rem 0.4rem;
        border-radius: 6px;
        font-size: 0.65rem;
        font-weight: 600;
    }

    .status-hadir {
        background: #dcfce7;
        color: #16a34a;
    }

    .status-absen {
        background: #fee2e2;
        color: #dc2626;
    }

    /* Lembur Item */
    .lembur-item {
        padding: 0.6rem 0;
        border-bottom: 1px solid #f5f5f5;
    }

    .lembur-item:last-child {
        border-bottom: none;
    }

    .lembur-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.25rem;
    }

    .lembur-date {
        font-size: 0.8rem;
        font-weight: 600;
        color: #333;
    }

    .lembur-status {
        font-size: 0.7rem;
    }

    .lembur-time {
        font-size: 0.75rem;
        color: #666;
    }

    .lembur-kegiatan {
        font-size: 0.7rem;
        color: #888;
    }

    /* Summary */
    .summary-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
    }

    .summary-item {
        text-align: center;
        padding: 0.75rem 0.5rem;
        background: #f9fafb;
        border-radius: 10px;
    }

    .summary-item .number {
        font-size: 1rem;
        font-weight: 700;
        color: var(--primary);
    }

    .summary-item .label {
        font-size: 0.65rem;
        color: #888;
    }

    /* Buttons */
    .btn-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
    }

    .btn-export {
        padding: 0.6rem;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 600;
        background: #fff;
        color: #333;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
    }

    .btn-back {
        width: 100%;
        padding: 0.8rem;
        background: #f3f4f6;
        color: #333;
        border: none;
        border-radius: 12px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        margin-top: 0.5rem;
    }

    .empty-state {
        text-align: center;
        padding: 1.5rem;
        color: #888;
        font-size: 0.85rem;
    }
</style>

<?php
$bulan_filter = $this->input->get('bulan') ? $this->input->get('bulan') : date('m');
$tahun_filter = $this->input->get('tahun') ? $this->input->get('tahun') : date('Y');
$bulan_array = array(
    '01' => 'Jan',
    '02' => 'Feb',
    '03' => 'Mar',
    '04' => 'Apr',
    '05' => 'Mei',
    '06' => 'Jun',
    '07' => 'Jul',
    '08' => 'Agu',
    '09' => 'Sep',
    '10' => 'Okt',
    '11' => 'Nov',
    '12' => 'Des'
);
$bulan_full = array(
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember'
);

// Calculate stats
$total_hari_kerja = cal_days_in_month(CAL_GREGORIAN, $bulan_filter, $tahun_filter);
$total_hadir = 0;
$total_terlambat = 0;
$total_jam_kerja = 0;

if (!empty($absensi)) {
    foreach ($absensi as $row) {
        if (!empty($row['jam_masuk'])) {
            $total_hadir++;
            if (!empty($row['jam_pulang'])) {
                $masuk = strtotime($row['jam_masuk']);
                $pulang = strtotime($row['jam_pulang']);
                $total_jam_kerja += ($pulang - $masuk) / 3600;
            }
            if (strtotime($row['jam_masuk']) > strtotime('08:00:00')) {
                $total_terlambat++;
            }
        }
    }
}
$persentase = $total_hari_kerja > 0 ? round(($total_hadir / $total_hari_kerja) * 100, 0) : 0;
?>

<!-- Header -->
<div class="page-header">
    <h5><i class="bi bi-file-text me-2"></i>Laporan</h5>
    <div class="name"><?= $karyawan_terpilih['nama_lengkap'] ?></div>
    <div class="email"><?= $karyawan_terpilih['email'] ?></div>
</div>

<!-- Content -->
<div class="content-container">

    <!-- Filter -->
    <div class="stat-card">
        <div class="stat-card-header">
            <i class="bi bi-funnel text-primary"></i> Filter Periode
        </div>
        <div class="stat-card-body">
            <form method="GET" action="<?= base_url('mobile/laporan') ?>">
                <div class="filter-row">
                    <div>
                        <div class="filter-label">Bulan</div>
                        <select name="bulan" class="filter-select">
                            <?php foreach ($bulan_full as $key => $value): ?>
                                <option value="<?= $key ?>" <?= $bulan_filter == $key ? 'selected' : '' ?>><?= $value ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <div class="filter-label">Tahun</div>
                        <select name="tahun" class="filter-select">
                            <?php for ($i = date('Y'); $i >= date('Y') - 3; $i--): ?>
                                <option value="<?= $i ?>" <?= $tahun_filter == $i ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn-filter"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats -->
    <div class="stat-card">
        <div class="stat-card-header">
            <i class="bi bi-bar-chart text-success"></i> Statistik <?= $bulan_full[$bulan_filter] ?>
        </div>
        <div class="stat-card-body">
            <div class="stats-grid">
                <div class="stat-box stat-green">
                    <div class="number"><?= $total_hadir ?></div>
                    <div class="label">Hadir</div>
                </div>
                <div class="stat-box stat-red">
                    <div class="number"><?= $total_hari_kerja - $total_hadir ?></div>
                    <div class="label">Absen</div>
                </div>
                <div class="stat-box stat-yellow">
                    <div class="number"><?= $total_terlambat ?></div>
                    <div class="label">Terlambat</div>
                </div>
                <div class="stat-box stat-blue">
                    <div class="number"><?= $persentase ?>%</div>
                    <div class="label">Kehadiran</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Absensi List -->
    <div class="stat-card">
        <div class="stat-card-header">
            <i class="bi bi-calendar-check text-info"></i> Riwayat Absensi
        </div>
        <div class="stat-card-body">
            <?php if (empty($absensi)): ?>
                <div class="empty-state">Belum ada data absensi bulan ini</div>
            <?php else: ?>
                <?php foreach ($absensi as $row):
                    $jam_kerja = '-';
                    $late = '';
                    if (!empty($row['jam_masuk']) && !empty($row['jam_pulang'])) {
                        $selisih = strtotime($row['jam_pulang']) - strtotime($row['jam_masuk']);
                        $jam_kerja = floor($selisih / 3600) . 'j ' . floor(($selisih % 3600) / 60) . 'm';
                    }
                    if (!empty($row['jam_masuk']) && strtotime($row['jam_masuk']) > strtotime('08:00:00')) {
                        $late = 'Terlambat ' . floor((strtotime($row['jam_masuk']) - strtotime('08:00:00')) / 60) . ' menit';
                    }
                    ?>
                    <div class="absen-item">
                        <div class="absen-date">
                            <div class="day"><?= date('d', strtotime($row['tanggal'])) ?></div>
                            <div class="month"><?= $bulan_array[date('m', strtotime($row['tanggal']))] ?></div>
                        </div>
                        <div class="absen-info">
                            <div class="absen-time">
                                <?= $row['jam_masuk'] ? $row['jam_masuk'] : '-' ?> -
                                <?= $row['jam_pulang'] ? $row['jam_pulang'] : '-' ?>
                            </div>
                            <div class="absen-hours">Jam kerja: <?= $jam_kerja ?></div>
                            <?php if ($late): ?>
                                <div class="absen-late"><?= $late ?></div><?php endif; ?>
                        </div>
                        <span class="absen-status <?= !empty($row['jam_masuk']) ? 'status-hadir' : 'status-absen' ?>">
                            <?= !empty($row['jam_masuk']) ? 'Hadir' : 'Absen' ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Jam Kerja Summary -->
    <div class="stat-card">
        <div class="stat-card-header">
            <i class="bi bi-clock text-warning"></i> Jam Kerja
        </div>
        <div class="stat-card-body">
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="number"><?= round($total_jam_kerja, 1) ?></div>
                    <div class="label">Total Jam</div>
                </div>
                <div class="summary-item">
                    <div class="number"><?= $total_hadir > 0 ? round($total_jam_kerja / $total_hadir, 1) : 0 ?></div>
                    <div class="label">Rata-rata/Hari</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lembur -->
    <div class="stat-card">
        <div class="stat-card-header">
            <i class="bi bi-moon text-purple"></i> Lembur
        </div>
        <div class="stat-card-body">
            <?php if (empty($lembur)): ?>
                <div class="empty-state">Belum ada data lembur bulan ini</div>
            <?php else: ?>
                <?php foreach ($lembur as $row): ?>
                    <div class="lembur-item">
                        <div class="lembur-header">
                            <span class="lembur-date"><?= date('d M Y', strtotime($row->tanggal)) ?></span>
                            <span class="lembur-status">
                                <?php if ($row->status == 'disetujui'): ?>✅
                                <?php elseif ($row->status == 'menunggu'): ?>🕒
                                <?php elseif ($row->status == 'ditolak'): ?>❌
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="lembur-time"><?= $row->jam_mulai ?> - <?= $row->jam_selesai ?></div>
                        <div class="lembur-kegiatan"><?= $row->kegiatan ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Export -->
    <div class="stat-card">
        <div class="stat-card-header">
            <i class="bi bi-download text-success"></i> Export
        </div>
        <div class="stat-card-body">
            <div class="btn-row">
                <button class="btn-export" onclick="alert('Fitur PDF akan tersedia')">
                    <i class="bi bi-file-pdf text-danger"></i> PDF
                </button>
                <button class="btn-export" onclick="alert('Fitur Excel akan tersedia')">
                    <i class="bi bi-file-excel text-success"></i> Excel
                </button>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <a href="<?= base_url('mobile') ?>" class="btn-back">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>

</div>