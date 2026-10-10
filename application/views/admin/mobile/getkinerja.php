<!-- Mobile Kinerja View - Consistent Dark Theme -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    :root {
        --primary: #1a1a2e;
        --secondary: #16213e;
    }

    * {
        -webkit-tap-highlight-color: transparent;
    }

    body {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        min-height: 100vh;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        margin: 0;
        padding: 0;
    }

    .page-header {
        padding: 1.5rem;
        color: #fff;
        text-align: center;
    }

    .page-header h5 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .page-header small {
        opacity: 0.7;
        font-size: 0.8rem;
    }

    .content-container {
        background: #f5f5f5;
        border-radius: 24px 24px 0 0;
        min-height: calc(100vh - 100px);
        padding: 1.5rem;
    }

    .stat-card {
        background: #fff;
        border-radius: 16px;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .stat-card-header {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.85rem;
        font-weight: 600;
        color: #333;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .stat-card-body {
        padding: 1rem;
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
    }

    .stat-item {
        text-align: center;
        padding: 0.75rem 0.5rem;
        border-radius: 12px;
    }

    .stat-item .number {
        font-size: 1.25rem;
        font-weight: 700;
    }

    .stat-item .label {
        font-size: 0.6rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .stat-green {
        background: #dcfce7;
        color: #16a34a;
    }

    .stat-blue {
        background: #dbeafe;
        color: #2563eb;
    }

    .stat-cyan {
        background: #cffafe;
        color: #0891b2;
    }

    .stat-red {
        background: #fee2e2;
        color: #dc2626;
    }

    .stat-yellow {
        background: #fef3c7;
        color: #d97706;
    }

    .stat-purple {
        background: #f3e8ff;
        color: #9333ea;
    }

    .chart-container {
        position: relative;
        height: 200px;
        width: 100%;
    }

    .activity-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        background: #f9fafb;
        border-radius: 10px;
        margin-bottom: 0.5rem;
    }

    .activity-date {
        font-size: 0.8rem;
        color: #666;
    }

    .activity-count {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--primary);
    }

    .absen-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem;
        background: #f9fafb;
        border-radius: 10px;
        margin-bottom: 0.5rem;
    }

    .absen-date {
        min-width: 45px;
        text-align: center;
    }

    .absen-date .day {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--primary);
    }

    .absen-date .month {
        font-size: 0.6rem;
        color: #888;
    }

    .absen-time {
        flex: 1;
        text-align: center;
        font-size: 0.75rem;
        color: #666;
    }

    .absen-status {
        min-width: 60px;
        text-align: right;
    }

    .badge-hadir {
        background: #dcfce7;
        color: #16a34a;
        padding: 0.25rem 0.5rem;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .badge-izin {
        background: #dbeafe;
        color: #2563eb;
        padding: 0.25rem 0.5rem;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .badge-sakit {
        background: #cffafe;
        color: #0891b2;
        padding: 0.25rem 0.5rem;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .badge-alpha {
        background: #fee2e2;
        color: #dc2626;
        padding: 0.25rem 0.5rem;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .badge-cuti {
        background: #f3e8ff;
        color: #9333ea;
        padding: 0.25rem 0.5rem;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .btn-back {
        width: 100%;
        padding: 0.875rem;
        background: #f3f4f6;
        color: #333;
        border: none;
        border-radius: 12px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .btn-back:active {
        background: #e5e7eb;
    }
</style>

<!-- Header -->
<div class="page-header">
    <h5><i class="bi bi-bar-chart-line-fill me-2"></i>Laporan Kinerja</h5>
    <small>Hai, <?= $this->session->userdata('username') ?></small>
</div>

<!-- Content -->
<div class="content-container">

    <!-- Rekap Absensi Bulan Ini -->
    <div class="stat-card">
        <div class="stat-card-header">
            <i class="bi bi-calendar-check text-primary"></i> Rekap Absensi Bulan Ini
        </div>
        <div class="stat-card-body">
            <div class="stat-grid">
                <div class="stat-item stat-green">
                    <div class="number"><?= isset($rekap_absensi['masuk']) ? $rekap_absensi['masuk'] : 0 ?></div>
                    <div class="label">Masuk</div>
                </div>
                <div class="stat-item stat-blue">
                    <div class="number"><?= isset($rekap_absensi['izin']) ? $rekap_absensi['izin'] : 0 ?></div>
                    <div class="label">Izin</div>
                </div>
                <div class="stat-item stat-cyan">
                    <div class="number"><?= isset($rekap_absensi['sakit']) ? $rekap_absensi['sakit'] : 0 ?></div>
                    <div class="label">Sakit</div>
                </div>
                <div class="stat-item stat-red">
                    <div class="number"><?= isset($rekap_absensi['alpha']) ? $rekap_absensi['alpha'] : 0 ?></div>
                    <div class="label">Alpha</div>
                </div>
                <div class="stat-item stat-purple">
                    <div class="number"><?= isset($rekap_absensi['cuti']) ? $rekap_absensi['cuti'] : 0 ?></div>
                    <div class="label">Cuti</div>
                </div>
                <div class="stat-item stat-yellow">
                    <div class="number"><?= isset($rekap_absensi['lembur']) ? $rekap_absensi['lembur'] : 0 ?></div>
                    <div class="label">Lembur</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Absensi -->
    <?php if (!empty($data_absensi)): ?>
        <div class="stat-card">
            <div class="stat-card-header">
                <i class="bi bi-clock-history text-secondary"></i> Riwayat Absensi
            </div>
            <div class="stat-card-body">
                <?php foreach ($data_absensi as $absen): ?>
                    <div class="absen-item">
                        <div class="absen-date">
                            <div class="day"><?= date('d', strtotime($absen->tanggal)) ?></div>
                            <div class="month"><?= date('M', strtotime($absen->tanggal)) ?></div>
                        </div>
                        <div class="absen-time">
                            <?php if (!empty($absen->jam_masuk)): ?>
                                <i class="bi bi-box-arrow-in-right text-success"></i>
                                <?= date('H:i', strtotime($absen->jam_masuk)) ?>
                            <?php endif; ?>
                            <?php if (!empty($absen->jam_pulang)): ?>
                                <i class="bi bi-box-arrow-left text-info ms-2"></i>
                                <?= date('H:i', strtotime($absen->jam_pulang)) ?>
                            <?php endif; ?>
                        </div>
                        <div class="absen-status">
                            <?php
                            $status = isset($absen->status) ? $absen->status : 'Hadir';
                            $badge_class = 'badge-hadir';
                            if ($status == 'Izin')
                                $badge_class = 'badge-izin';
                            elseif ($status == 'Sakit')
                                $badge_class = 'badge-sakit';
                            elseif ($status == 'Alpha')
                                $badge_class = 'badge-alpha';
                            elseif ($status == 'Cuti')
                                $badge_class = 'badge-cuti';
                            ?>
                            <span class="<?= $badge_class ?>"><?= $status ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Grafik Kinerja -->
    <div class="stat-card">
        <div class="stat-card-header">
            <i class="bi bi-graph-up text-success"></i> Grafik Aktivitas
        </div>
        <div class="stat-card-body">
            <div class="chart-container">
                <canvas id="chartKinerja"></canvas>
            </div>
        </div>
    </div>

    <!-- Skor Kinerja -->
    <?php if (!empty($skor_kinerja)): ?>
        <div class="stat-card">
            <div class="stat-card-header">
                <i class="bi bi-trophy text-warning"></i> Skor Kinerja
            </div>
            <div class="stat-card-body">
                <?php foreach ($skor_kinerja as $row): ?>
                    <div class="activity-item">
                        <span class="activity-date"><?= $row->username ?></span>
                        <span class="activity-count"><?= $row->skor_kinerja ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Aktivitas Harian -->
    <?php if (!empty($daily_summary)): ?>
        <div class="stat-card">
            <div class="stat-card-header">
                <i class="bi bi-list-check text-info"></i> Aktivitas Harian
            </div>
            <div class="stat-card-body">
                <?php foreach ($daily_summary as $row): ?>
                    <div class="activity-item">
                        <span class="activity-date"><?= date('d M Y', strtotime($row->tanggal)) ?></span>
                        <span class="activity-count"><?= $row->total_aktivitas ?> aktivitas</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Back Button -->
    <a href="<?= base_url('mobile') ?>" class="btn-back">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>

</div>

<script>
    <?php
    $chartLabels = array();
    $chartData = array();
    if (!empty($daily_summary)) {
        foreach ($daily_summary as $x) {
            $chartLabels[] = date('d M', strtotime($x->tanggal));
            $chartData[] = (int) $x->total_aktivitas;
        }
    }
    ?>

    var ctx = document.getElementById('chartKinerja').getContext('2d');
    var chartKinerja = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chartLabels) ?>,
            datasets: [{
                label: 'Aktivitas',
                data: <?= json_encode($chartData) ?>,
                backgroundColor: 'rgba(26, 26, 46, 0.7)',
                borderColor: 'rgba(26, 26, 46, 1)',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });
</script>