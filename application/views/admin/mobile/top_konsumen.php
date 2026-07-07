<!-- Mobile Top Konsumen View - Consistent Dark Theme -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<link rel="stylesheet" href="<?= base_url('assets/css/tgm-top-konsumen-mobile.css') ?>">
<div class="mobile-body">

    <!-- Header -->
    <div class="page-header">
        <h5><i class="bi bi-trophy-fill me-2"></i>Top Konsumen</h5>
        <small>Monitoring konsumen terbaik</small>
    </div>

    <!-- Content -->
    <div class="content-container">

        <!-- Top 1 Konsumen -->
        <?php if (!empty($tampil_data)): ?>
            <div class="top1-card">
                <div class="top1-avatar"><i class="bi bi-crown-fill"></i></div>
                <div class="top1-name"><?= htmlspecialchars($tampil_data[0]->konsumen_nama) ?></div>
                <div class="top1-stats">
                    <div class="top1-stat">
                        <div class="number"><?= $tampil_data[0]->jumlah_transaksi ?></div>
                        <div class="label">Transaksi</div>
                    </div>
                    <div class="top1-stat">
                        <div class="number">Rp <?= number_format($tampil_data[0]->total_transaksi / 1000000, 1) ?>jt</div>
                        <div class="label">Total</div>
                    </div>
                    <div class="top1-stat">
                        <div class="number"><?= floor($tampil_data[0]->total_transaksi / 250000) ?></div>
                        <div class="label">Point</div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Chart -->
        <div class="stat-card">
            <div class="stat-card-header">
                <i class="bi bi-bar-chart text-primary"></i> Grafik Pembelian
            </div>
            <div class="stat-card-body">
                <div class="chart-container">
                    <canvas id="chartTopKonsumen"></canvas>
                </div>
            </div>
        </div>

        <!-- Konsumen List -->
        <div class="stat-card">
            <div class="stat-card-header">
                <i class="bi bi-people text-success"></i> Daftar Konsumen (<?= count($tampil_data) ?>)
            </div>
            <div class="stat-card-body">
                <?php
                $no = 1;
                foreach ($tampil_data as $dt):
                    $rankClass = $no == 1 ? 'rank-1' : ($no == 2 ? 'rank-2' : ($no == 3 ? 'rank-3' : 'rank-default'));
                    ?>
                    <div class="konsumen-item">
                        <span class="konsumen-rank <?= $rankClass ?>"><?= $no ?></span>
                        <div class="konsumen-info">
                            <div class="konsumen-name"><?= htmlspecialchars($dt->konsumen_nama) ?></div>
                            <div class="konsumen-phone"><?= $dt->konsumen_nohp ?></div>
                        </div>
                        <div class="konsumen-stats">
                            <div class="konsumen-total">Rp <?= number_format($dt->total_transaksi, 0, ',', '.') ?></div>
                            <div class="konsumen-trx"><?= $dt->jumlah_transaksi ?> trx</div>
                        </div>
                    </div>
                    <?php
                    $no++;
                endforeach;
                ?>
            </div>
        </div>

        <!-- Summary -->
        <div class="stat-card">
            <div class="stat-card-header">
                <i class="bi bi-info-circle text-info"></i> Ringkasan
            </div>
            <div class="stat-card-body">
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="number"><?= count($tampil_data) ?></div>
                        <div class="label">Total Konsumen</div>
                    </div>
                    <div class="summary-item">
                        <?php
                        $totalTrx = 0;
                        foreach ($tampil_data as $dt) {
                            $totalTrx += $dt->jumlah_transaksi;
                        }
                        ?>
                        <div class="number"><?= number_format($totalTrx) ?></div>
                        <div class="label">Total Transaksi</div>
                    </div>
                    <div class="summary-item">
                        <?php
                        $totalNilai = 0;
                        foreach ($tampil_data as $dt) {
                            $totalNilai += $dt->total_transaksi;
                        }
                        ?>
                        <div class="number">Rp <?= number_format($totalNilai / 1000000, 1) ?>jt</div>
                        <div class="label">Total Nilai</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <a href="<?= base_url('mobile') ?>" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>

    </div>

    <script>
        <?php
        $chartLabels = array();
        $chartData = array();
        foreach ($tampil_data as $dt) {
            $chartLabels[] = $dt->konsumen_nama;
            $chartData[] = (int) $dt->total_transaksi;
        }
        ?>

        var ctx = document.getElementById('chartTopKonsumen').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chartLabels) ?>,
                datasets: [{
                    label: 'Total',
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
                    x: {
                        ticks: {
                            font: { size: 9 },
                            maxRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: { size: 9 },
                            callback: function (v) { return 'Rp ' + (v / 1000000).toFixed(1) + 'jt'; }
                        }
                    }
                }
            }
        });
    </script>
</div>