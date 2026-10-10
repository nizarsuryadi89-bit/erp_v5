<!-- Page Header -->
<div class="custom-page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5><i class="bi bi-graph-up-arrow me-2 text-warning"></i>Kinerja Bulanan</h5>
            <p>Bulan: <strong><?= date('F Y', strtotime($bulan)) ?></strong></p>
        </div>
        <div class="dropdown">
            <button class="btn btn-sm btn-dark rounded-pill px-3" type="button" onclick="$('#monthFilter').click()">
                <i class="bi bi-calendar-event me-1"></i> Filter
            </button>
            <input type="month" id="monthFilter" class="d-none" value="<?= $bulan ?>"
                onchange="window.location.href='<?= base_url('mobile/kinerja?bulan=') ?>' + this.value">
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="content-container">

    <!-- Hero Performance Score -->
    <div class="stat-card border-0 text-white mb-4 shadow"
        style="background: linear-gradient(135deg, #4f46e5, #7c3aed); border-radius: 25px;">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
                <h2 class="mb-0 fw-bold"><?= $skor_kinerja_personal ?? 0 ?></h2>
                <small class="opacity-75">Nilai Kinerja Berjalan</small>
            </div>
            <div class="bg-white bg-opacity-20 rounded-pill p-3">
                <i class="bi bi-award-fill fs-2 text-warning"></i>
            </div>
        </div>
        <div class="mt-3">
            <div class="progress" style="height: 8px; background: rgba(255,255,255,0.2); border-radius: 10px;">
                <div class="progress-bar bg-warning shadow-sm"
                    style="width: <?= min(($skor_kinerja_personal ?? 0), 100) ?>%"></div>
            </div>
            <div class="d-flex justify-content-between mt-2 small opacity-75">
                <span>Base: 100</span>
                <span>Target: 100</span>
            </div>
        </div>
    </div>

    <!-- Stats Grid (Attendance Based) -->
    <div class="row g-2 mb-4">
        <div class="col-4">
            <div class="stat-card p-2 text-center border-0 shadow-sm" style="border-radius: 18px;">
                <span class="d-block h5 fw-bold text-success mb-0"><?= $total_hadir ?? 0 ?></span>
                <span class="text-muted d-block" style="font-size: 0.65rem; font-weight: 700;">KEHADIRAN</span>
            </div>
        </div>
        <div class="col-4">
            <div class="stat-card p-2 text-center border-0 shadow-sm" style="border-radius: 18px;">
                <span class="d-block h5 fw-bold text-warning mb-0"><?= $total_telat ?? 0 ?></span>
                <span class="text-muted d-block" style="font-size: 0.65rem; font-weight: 700;">TERLAMBAT</span>
            </div>
        </div>
        <div class="col-4">
            <div class="stat-card p-2 text-center border-0 shadow-sm" style="border-radius: 18px;">
                <span class="d-block h5 fw-bold text-danger mb-0"><?= $total_alpha ?? 0 ?></span>
                <span class="text-muted d-block" style="font-size: 0.65rem; font-weight: 700;">ALPHA</span>
            </div>
        </div>
    </div>

    <!-- Scoring Breakdown -->
    <div class="section-header">
        <div class="section-title"><i class="bi bi-list-stars me-2 text-primary"></i>Rincian Skor</div>
    </div>
    <div class="stat-card mb-4 border-0 shadow-sm" style="border-radius: 20px; padding: 1.25rem;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center">
                <div class="bg-success bg-opacity-10 p-2 rounded-circle me-3">
                    <i class="bi bi-plus-circle text-success"></i>
                </div>
                <span class="small fw-bold">Skor Awal</span>
            </div>
            <span class="badge bg-success bg-opacity-10 text-success rounded-pill">+100</span>
        </div>

        <?php if ($total_alpha > 0): ?>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 p-2 rounded-circle me-3">
                        <i class="bi bi-dash-circle text-danger"></i>
                    </div>
                    <span class="small fw-bold">Alpha (<?= $total_alpha ?>)</span>
                </div>
                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">-<?= $total_alpha * 5 ?></span>
            </div>
        <?php endif; ?>

        <?php if ($total_telat > 0): ?>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-circle me-3">
                        <i class="bi bi-dash-circle text-warning"></i>
                    </div>
                    <span class="small fw-bold">Terlambat (<?= $total_telat ?>)</span>
                </div>
                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill">-<?= $total_telat * 1 ?></span>
            </div>
        <?php endif; ?>

        <?php if ($performance && $performance->total_pulang_cepat > 0): ?>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 p-2 rounded-circle me-3">
                        <i class="bi bi-dash-circle text-danger"></i>
                    </div>
                    <span class="small fw-bold">Pulang Cepat (<?= $performance->total_pulang_cepat ?>)</span>
                </div>
                <span
                    class="badge bg-danger bg-opacity-10 text-danger rounded-pill">-<?= $performance->total_pulang_cepat * 3 ?></span>
            </div>
        <?php endif; ?>

        <?php if ($total_izin_sakit > 0): ?>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 p-2 rounded-circle me-3">
                        <i class="bi bi-dash-circle text-info"></i>
                    </div>
                    <span class="small fw-bold">Izin/Sakit (<?= $total_izin_sakit ?>)</span>
                </div>
                <span class="badge bg-info bg-opacity-10 text-info rounded-pill">-<?= $total_izin_sakit * 1 ?></span>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3">
                    <i class="bi bi-rocket-takeoff text-primary"></i>
                </div>
                <span class="small fw-bold">SPK Selesai (Produktivitas)</span>
            </div>
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill"><?= $performance->total_spk ?? 0 ?> Jobs</span>
        </div>

        <div class="border-top mt-3 pt-3 d-flex justify-content-between align-items-center">
            <span class="fw-bold">Total Nilai</span>
            <span class="h5 fw-bold text-primary mb-0"><?= $skor_kinerja_personal ?></span>
        </div>
    </div>

    <!-- Activity Log Chart (Secondary Insight) -->
    <div class="section-header">
        <div class="section-title"><i class="bi bi-activity me-2 text-info"></i>Grafik Log Aktivitas</div>
    </div>
    <div class="stat-card mb-4 border-0 shadow-sm" style="padding: 1.25rem; border-radius: 20px;">
        <div style="height: 180px; position: relative;">
            <canvas id="chartKinerja"></canvas>
        </div>
        <p class="text-muted x-small text-center mt-3 mb-0">Data berdasarkan 30 hari aktivitas terakhir</p>
    </div>

</div>

<!-- Chart.js and Initialization -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    <?php if (!empty($chart_data)): ?>
        const ctx = document.getElementById('chartKinerja').getContext('2d');
        const dailyLabels = <?= json_encode(array_map(function ($x) {
            return date('d M', strtotime($x->tanggal));
        }, $chart_data)) ?>;
        const dailyData = <?= json_encode(array_map(function ($x) {
            return (int) $x->total_aktivitas;
        }, $chart_data)) ?>;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Aktivitas',
                    data: dailyData,
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderColor: '#4f46e5',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { backgroundColor: '#1e293b', padding: 10, titleFont: { size: 10 }, bodyFont: { size: 12 } } },
                scales: {
                    y: { beginAtZero: true, ticks: { display: false }, grid: { display: false } },
                    x: { ticks: { font: { size: 10 }, color: '#94a3b8' }, grid: { display: false } }
                }
            }
        });
    <?php endif; ?>
</script>