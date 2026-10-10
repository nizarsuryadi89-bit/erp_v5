<div class="app-content">
    <div class="container-fluid">
        <!-- Header dengan Filter -->
        <div class="dashboard-header mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="page-title">
                        <i class="fas fa-chart-line me-2"></i>Dashboard Keuangan Terpadu
                    </h2>
                    <p class="text-muted">Analisis komprehensif keuangan harian dan bulanan</p>
                </div>
                <div class="col-md-6">
                    <div class="d-flex gap-2 justify-content-end">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary" onclick="setPeriod('today')">Hari Ini</button>
                            <button type="button" class="btn btn-outline-primary" onclick="setPeriod('week')">Minggu Ini</button>
                            <button type="button" class="btn btn-outline-primary" onclick="setPeriod('month')">Bulan Ini</button>
                            <button type="button" class="btn btn-outline-primary" onclick="setPeriod('quarter')">Kuartal</button>
                        </div>
                        <button class="btn btn-success" onclick="exportDashboard()">
                            <i class="fas fa-file-excel"></i> Export
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Date Range Filter -->
            <div class="card mt-3">
                <div class="card-body">
                    <form id="dateFilterForm" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="mulai" value="<?= $tanggal_mulai ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" name="selesai" value="<?= $tanggal_selesai ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Spesifik</label>
                            <input type="date" class="form-control" name="tanggal" value="<?= $hariini ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Tampilkan
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="resetFilter()">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="row mb-4">
            <!-- Total Pendapatan -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Pendapatan
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    Rp <?= number_format($financial_summary['total_pendapatan']) ?>
                                </div>
                                <div class="text-xs text-muted">
                                    <?= $financial_summary['pendapatan_growth'] ?>% dari periode sebelumnya
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Pengeluaran -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Pengeluaran
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    Rp <?= number_format($financial_summary['total_pengeluaran']) ?>
                                </div>
                                <div class="text-xs text-muted">
                                    <?= $financial_summary['pengeluaran_growth'] ?>% dari periode sebelumnya
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profit Margin -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Profit Margin
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?= number_format($financial_summary['profit_margin'], 1) ?>%
                                </div>
                                <div class="text-xs text-muted">
                                    Target: 25%
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-percent fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jumlah Transaksi -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Jumlah Transaksi
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?= number_format($financial_summary['jumlah_transaksi']) ?>
                                </div>
                                <div class="text-xs text-muted">
                                    Rata-rata: <?= $financial_summary['avg_transaction'] ?> per hari
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Revenue Trend Chart -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Trend Pendapatan vs Pengeluaran</h6>
                        <div class="dropdown no-arrow">
                            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown">
                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                                <a class="dropdown-item" href="#" onclick="updateChart('daily')">Harian</a>
                                <a class="dropdown-item" href="#" onclick="updateChart('weekly')">Mingguan</a>
                                <a class="dropdown-item" href="#" onclick="updateChart('monthly')">Bulanan</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="revenueTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Methods Pie Chart -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Distribusi Metode Pembayaran</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2">
                            <canvas id="paymentMethodsChart"></canvas>
                        </div>
                        <div class="mt-4 text-center small">
                            <span class="mr-2">
                                <i class="fas fa-circle text-success"></i> Cash
                            </span>
                            <span class="mr-2">
                                <i class="fas fa-circle text-primary"></i> Transfer
                            </span>
                            <span class="mr-2">
                                <i class="fas fa-circle text-warning"></i> EDC
                            </span>
                            <span class="mr-2">
                                <i class="fas fa-circle text-info"></i> E-Wallet
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Analysis -->
        <div class="row">
            <!-- Cash Flow Analysis -->
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Analisis Cash Flow</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Kategori</th>
                                        <th>Harian</th>
                                        <th>Bulanan</th>
                                        <th>Trend</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cash_flow as $flow): ?>
                                    <tr>
                                        <td><?= $flow['kategori'] ?></td>
                                        <td>Rp <?= number_format($flow['harian']) ?></td>
                                        <td>Rp <?= number_format($flow['bulanan']) ?></td>
                                        <td>
                                            <?php if ($flow['trend'] > 0): ?>
                                                <span class="badge bg-success">+<?= $flow['trend'] ?>%</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger"><?= $flow['trend'] ?>%</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Performers -->
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Top Performers</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Kategori</th>
                                        <th>Nilai</th>
                                        <th>Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($top_performers as $index => $performer): ?>
                                    <tr>
                                        <td>
                                            <?php if ($index == 0): ?>
                                                <i class="fas fa-trophy text-warning"></i>
                                            <?php elseif ($index == 1): ?>
                                                <i class="fas fa-medal text-secondary"></i>
                                            <?php elseif ($index == 2): ?>
                                                <i class="fas fa-award text-bronze"></i>
                                            <?php else: ?>
                                                <?= $index + 1 ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $performer['kategori'] ?></td>
                                        <td>Rp <?= number_format($performer['nilai']) ?></td>
                                        <td><?= number_format($performer['persentase'], 1) ?>%</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="<?= base_url('laporan/rekap_metode_pembayaran') ?>" class="btn btn-outline-primary btn-block">
                                    <i class="fas fa-credit-card"></i><br>
                                    Rekap Metode Pembayaran
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="<?= base_url('laporan/pemasukan_harian') ?>" class="btn btn-outline-success btn-block">
                                    <i class="fas fa-chart-line"></i><br>
                                    Pemasukan Harian
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="<?= base_url('laporan/laba_rugi') ?>" class="btn btn-outline-info btn-block">
                                    <i class="fas fa-file-alt"></i><br>
                                    Laporan Laba Rugi
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="<?= base_url('laporan/export_dashboard') ?>" class="btn btn-outline-warning btn-block">
                                    <i class="fas fa-download"></i><br>
                                    Export Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript untuk Dashboard -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize charts
document.addEventListener('DOMContentLoaded', function() {
    initializeRevenueTrendChart();
    initializePaymentMethodsChart();
});

// Revenue Trend Chart
function initializeRevenueTrendChart() {
    const ctx = document.getElementById('revenueTrendChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($trend_analysis['labels']) ?>,
            datasets: [{
                label: 'Pendapatan',
                data: <?= json_encode($trend_analysis['pendapatan']) ?>,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }, {
                label: 'Pengeluaran',
                data: <?= json_encode($trend_analysis['pengeluaran']) ?>,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Trend Keuangan'
                }
            }
        }
    });
}

// Payment Methods Chart
function initializePaymentMethodsChart() {
    const ctx = document.getElementById('paymentMethodsChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Cash', 'Transfer', 'EDC', 'E-Wallet'],
            datasets: [{
                data: [
                    <?= (float)($payment_methods['cash']['total'] ?? 0) ?>,
                    <?= (float)($payment_methods['transfer']['total'] ?? 0) ?>,
                    <?= (float)($payment_methods['edc']['total'] ?? 0) ?>,
                    <?= (float)($payment_methods['ewallet']['total'] ?? 0) ?>
                ],
                backgroundColor: [
                    '#28a745',
                    '#007bff',
                    '#ffc107',
                    '#17a2b8'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
}

// Period functions
function setPeriod(period) {
    const today = new Date();
    let startDate, endDate;
    
    switch(period) {
        case 'today':
            startDate = endDate = today.toISOString().split('T')[0];
            break;
        case 'week':
            const weekStart = new Date(today.setDate(today.getDate() - today.getDay()));
            const weekEnd = new Date(today.setDate(today.getDate() - today.getDay() + 6));
            startDate = weekStart.toISOString().split('T')[0];
            endDate = weekEnd.toISOString().split('T')[0];
            break;
        case 'month':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
            endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().split('T')[0];
            break;
        case 'quarter':
            const quarter = Math.floor(today.getMonth() / 3);
            startDate = new Date(today.getFullYear(), quarter * 3, 1).toISOString().split('T')[0];
            endDate = new Date(today.getFullYear(), (quarter + 1) * 3, 0).toISOString().split('T')[0];
            break;
    }
    
    document.querySelector('input[name="mulai"]').value = startDate;
    document.querySelector('input[name="selesai"]').value = endDate;
    document.getElementById('dateFilterForm').submit();
}

function resetFilter() {
    document.getElementById('dateFilterForm').reset();
    window.location.href = '<?= base_url('laporan/dashboard_terpadu') ?>';
}

function exportDashboard() {
    const mulai = document.querySelector('input[name="mulai"]').value;
    const selesai = document.querySelector('input[name="selesai"]').value;
    window.location.href = `<?= base_url('laporan/export_dashboard') ?>?mulai=${mulai}&selesai=${selesai}`;
}
</script>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}

.chart-area {
    position: relative;
    height: 20rem;
    width: 100%;
}

.chart-pie {
    position: relative;
    height: 15rem;
    width: 100%;
}

.btn-block {
    display: block;
    width: 100%;
}

.gap-2 {
    gap: 0.5rem;
}

@media (max-width: 768px) {
    .chart-area {
        height: 15rem;
    }
    
    .chart-pie {
        height: 12rem;
    }
}
</style>
